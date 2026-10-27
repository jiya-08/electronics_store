<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error("Method not allowed", 405);
}

$pdo = get_db_connection();
$data = get_request_data();

// Basic validation
$customerName = trim($data['customer_name'] ?? '');
$customerEmail = trim($data['customer_email'] ?? '');
$customerPhone = trim($data['customer_phone'] ?? '');
$shippingAddress = trim($data['shipping_address'] ?? '');
$city = trim($data['city'] ?? '');
$postalCode = trim($data['postal_code'] ?? '');
$paymentMethod = trim($data['payment_method'] ?? 'Credit Card');
$items = $data['items'] ?? [];

if (empty($customerName) || empty($customerEmail) || empty($shippingAddress) || empty($items)) {
    json_error("Please fill in all required customer details and cart items", 400);
}

if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
    json_error("Please enter a valid email address", 400);
}

// Consolidate quantities
$itemQuantities = [];
foreach ($items as $item) {
    $pId = intval($item['product_id'] ?? 0);
    $qty = max(1, intval($item['quantity'] ?? 1));
    if ($pId > 0) {
        $itemQuantities[$pId] = ($itemQuantities[$pId] ?? 0) + $qty;
    }
}

if (empty($itemQuantities)) {
    json_error("Cart is empty or contains invalid items", 400);
}

try {
    $pdo->beginTransaction();

    // Fetch product data and check stocks with lock
    $pIds = array_keys($itemQuantities);
    $placeholders = implode(',', array_fill(0, count($pIds), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) FOR UPDATE");
    $stmt->execute($pIds);
    $products = $stmt->fetchAll();

    $productMap = [];
    foreach ($products as $p) {
        $productMap[$p['id']] = $p;
    }

    $subtotal = 0.00;
    $processedOrderItems = [];

    // Verify stock availability
    foreach ($itemQuantities as $pId => $qty) {
        if (!isset($productMap[$pId])) {
            $pdo->rollBack();
            json_error("Product ID {$pId} does not exist", 404);
        }

        $prod = $productMap[$pId];
        if ($prod['stock_quantity'] < $qty) {
            $pdo->rollBack();
            json_error("Insufficient stock for '{$prod['name']}'. Only {$prod['stock_quantity']} units remaining.", 400);
        }

        $price = floatval($prod['sale_price'] ?? $prod['price']);
        $itemSubtotal = $price * $qty;
        $subtotal += $itemSubtotal;

        $processedOrderItems[] = [
            'product' => $prod,
            'unit_price' => $price,
            'quantity' => $qty,
            'subtotal' => $itemSubtotal
        ];
    }

    $taxAmount = round($subtotal * STORE_TAX_RATE, 2);
    $shippingAmount = ($subtotal >= 500.00) ? 0.00 : 15.00;
    $totalAmount = round($subtotal + $taxAmount + $shippingAmount, 2);

    // Generate Order Number
    $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

    // Create Order Record
    $orderInsert = $pdo->prepare("
        INSERT INTO orders (
            order_number, customer_name, customer_email, customer_phone,
            shipping_address, city, postal_code, subtotal, tax_amount,
            shipping_amount, total_amount, payment_method, payment_status,
            order_status, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Paid', 'Processing', NOW())
    ");
    $orderInsert->execute([
        $orderNumber, $customerName, $customerEmail, $customerPhone,
        $shippingAddress, $city, $postalCode, $subtotal, $taxAmount,
        $shippingAmount, $totalAmount, $paymentMethod
    ]);
    $orderId = (int)$pdo->lastInsertId();

    // Generate Invoice Record
    $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($orderId + 8000, 5, '0', STR_PAD_LEFT);
    $invInsert = $pdo->prepare("
        INSERT INTO invoices (
            invoice_number, order_id, issue_date, due_date, customer_name,
            customer_email, customer_phone, billing_address, subtotal,
            tax_rate, tax_amount, shipping_amount, total_amount, payment_status,
            notes, created_at
        ) VALUES (?, ?, CURDATE(), CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Paid', ?, NOW())
    ");
    $invNotes = "Electronic Tax Invoice issued with tracked hardware serial numbers. Retain this invoice for warranty verification.";
    $invInsert->execute([
        $invoiceNumber, $orderId, $customerName, $customerEmail, $customerPhone,
        $shippingAddress . ', ' . $city . ' ' . $postalCode,
        $subtotal, (STORE_TAX_RATE * 100), $taxAmount, $shippingAmount, $totalAmount,
        $invNotes
    ]);
    $invoiceId = (int)$pdo->lastInsertId();

    $assignedWarranties = [];

    // Process each order item, assign serials, deduct stock, create warranty
    foreach ($processedOrderItems as &$item) {
        $prod = $item['product'];
        $qty = $item['quantity'];
        $pId = $prod['id'];

        $assignedSerials = [];

        // If product has serial tracking
        if ($prod['has_serial_tracking']) {
            // Check existing available serials
            $availSerialsStmt = $pdo->prepare("
                SELECT id, serial_number FROM product_serials 
                WHERE product_id = ? AND status = 'in_stock' 
                LIMIT ?
            ");
            $availSerialsStmt->bindValue(1, $pId, PDO::PARAM_INT);
            $availSerialsStmt->bindValue(2, $qty, PDO::PARAM_INT);
            $availSerialsStmt->execute();
            $foundSerials = $availSerialsStmt->fetchAll();

            foreach ($foundSerials as $sRow) {
                $assignedSerials[] = $sRow['serial_number'];
                // Update serial status to sold
                $updSerial = $pdo->prepare("
                    UPDATE product_serials 
                    SET status = 'sold', order_id = ?, sold_at = NOW() 
                    WHERE id = ?
                ");
                $updSerial->execute([$orderId, $sRow['id']]);
            }

            // If we needed more serials than existed in DB, generate valid new ones
            $neededMore = $qty - count($foundSerials);
            for ($k = 0; $k < $neededMore; $k++) {
                $newSerial = 'SN-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $prod['brand']), 0, 3)) . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
                $newSerialInsert = $pdo->prepare("
                    INSERT INTO product_serials (product_id, serial_number, status, order_id, sold_at)
                    VALUES (?, ?, 'sold', ?, NOW())
                ");
                $newSerialInsert->execute([$pId, $newSerial, $orderId]);
                $assignedSerials[] = $newSerial;
            }
        }

        $serialString = !empty($assignedSerials) ? implode(', ', $assignedSerials) : null;

        // Insert Order Item
        $itemInsert = $pdo->prepare("
            INSERT INTO order_items (
                order_id, product_id, product_name, unit_price, quantity, subtotal, assigned_serial_numbers
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $itemInsert->execute([
            $orderId, $pId, $prod['name'], $item['unit_price'], $qty, $item['subtotal'], $serialString
        ]);
        $orderItemId = (int)$pdo->lastInsertId();

        // Deduct Stock
        $previousStock = $prod['stock_quantity'];
        $newStock = $previousStock - $qty;
        $updStock = $pdo->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?");
        $updStock->execute([$newStock, $pId]);

        // Stock log entry
        $logInsert = $pdo->prepare("
            INSERT INTO stock_logs (
                product_id, change_type, quantity_change, previous_stock, new_stock, reference_id, note
            ) VALUES (?, 'Order Deduction', ?, ?, ?, ?, ?)
        ");
        $logNote = "Customer order {$orderNumber}" . ($serialString ? " (Serials: {$serialString})" : "");
        $logInsert->execute([
            $pId, -$qty, $previousStock, $newStock, $orderNumber, $logNote
        ]);

        // Auto-Register Warranty for each assigned serial number
        if ($prod['has_serial_tracking'] && !empty($assignedSerials)) {
            $warrantyMonths = max(12, intval($prod['warranty_months']));
            foreach ($assignedSerials as $sNum) {
                $warrantyCode = 'WAR-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
                $expiryDate = date('Y-m-d', strtotime("+{$warrantyMonths} months"));

                $warInsert = $pdo->prepare("
                    INSERT INTO warranties (
                        warranty_code, product_id, serial_number, order_id, customer_name,
                        customer_email, customer_phone, purchase_date, expiry_date, warranty_type,
                        status, notes, created_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, CURDATE(), ?, 'Standard Official Coverage', 'Active', ?, NOW())
                ");
                $warNotes = "Auto-activated on order {$orderNumber}. Covers hardware failure, component faults and manufacturer warranty.";
                $warInsert->execute([
                    $warrantyCode, $pId, $sNum, $orderId, $customerName, $customerEmail,
                    $customerPhone, $expiryDate, $warNotes
                ]);

                $assignedWarranties[] = [
                    'warranty_code' => $warrantyCode,
                    'serial_number' => $sNum,
                    'product_name' => $prod['name'],
                    'expiry_date' => $expiryDate,
                    'months' => $warrantyMonths
                ];
            }
        }
    }

    $pdo->commit();

    json_success([
        'order_id' => $orderId,
        'order_number' => $orderNumber,
        'invoice_id' => $invoiceId,
        'invoice_number' => $invoiceNumber,
        'total_amount' => $totalAmount,
        'customer_name' => $customerName,
        'customer_email' => $customerEmail,
        'payment_method' => $paymentMethod,
        'warranties' => $assignedWarranties,
        'message' => 'Order placed successfully and hardware warranties activated!'
    ], "Order successfully placed and serial numbers assigned");

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    json_error("Failed to complete checkout: " . $e->getMessage(), 500);
}
?>
