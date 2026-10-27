<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = get_db_connection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $search = trim($_GET['query'] ?? $_GET['serial'] ?? $_GET['code'] ?? '');
    
    if (empty($search)) {
        // Return latest registered warranties for public or dashboard overview
        $stmt = $pdo->prepare("
            SELECT w.*, p.name as product_name, p.brand, p.model, p.image_url, p.warranty_months,
                   o.order_number
            FROM warranties w
            JOIN products p ON w.product_id = p.id
            LEFT JOIN orders o ON w.order_id = o.id
            ORDER BY w.id DESC
            LIMIT 20
        ");
        $stmt->execute();
        $warranties = $stmt->fetchAll();

        foreach ($warranties as &$w) {
            $expiryTimestamp = strtotime($w['expiry_date']);
            $todayTimestamp = strtotime('today');
            $diffDays = ceil(($expiryTimestamp - $todayTimestamp) / 86400);

            $w['days_remaining'] = $diffDays;
            $w['is_valid'] = ($diffDays >= 0 && $w['status'] === 'Active');
        }

        json_success($warranties, "Latest warranties fetched");
    }

    // Search by Serial Number, Warranty Code, or Order Number
    $stmt = $pdo->prepare("
        SELECT w.*, p.name as product_name, p.brand, p.model, p.image_url, p.warranty_months,
               o.order_number, o.created_at as order_date
        FROM warranties w
        JOIN products p ON w.product_id = p.id
        LEFT JOIN orders o ON w.order_id = o.id
        WHERE w.serial_number = ? 
           OR w.warranty_code = ? 
           OR o.order_number = ?
           OR w.customer_email = ?
        ORDER BY w.id DESC
    ");
    $stmt->execute([$search, $search, $search, $search]);
    $results = $stmt->fetchAll();

    if (empty($results)) {
        // Check if the serial exists in product_serials but not yet registered
        $serialCheck = $pdo->prepare("
            SELECT ps.*, p.name as product_name, p.brand, p.warranty_months, p.id as product_id
            FROM product_serials ps
            JOIN products p ON ps.product_id = p.id
            WHERE ps.serial_number = ?
        ");
        $serialCheck->execute([$search]);
        $foundSerial = $serialCheck->fetch();

        if ($foundSerial) {
            json_success([
                'found' => false,
                'can_register' => true,
                'unregistered_device' => [
                    'serial_number' => $foundSerial['serial_number'],
                    'product_id' => $foundSerial['product_id'],
                    'product_name' => $foundSerial['product_name'],
                    'brand' => $foundSerial['brand'],
                    'warranty_months' => $foundSerial['warranty_months']
                ]
            ], "Serial number is authentic but not yet registered under a warranty. You can register it now.");
        }

        json_error("No warranty records found for '$search'. Please check your serial number, warranty code, or order ID.", 404);
    }

    foreach ($results as &$item) {
        $expiryTimestamp = strtotime($item['expiry_date']);
        $todayTimestamp = strtotime('today');
        $diffDays = ceil(($expiryTimestamp - $todayTimestamp) / 86400);

        $item['days_remaining'] = $diffDays;
        $item['is_valid'] = ($diffDays >= 0 && $item['status'] === 'Active');
        $item['coverage_percentage'] = max(0, min(100, round(($diffDays / ($item['warranty_months'] * 30)) * 100)));
    }

    json_success($results, "Warranty details found");
}

if ($method === 'POST') {
    $data = get_request_data();

    $serialNumber = trim($data['serial_number'] ?? '');
    $productId = intval($data['product_id'] ?? 0);
    $customerName = trim($data['customer_name'] ?? '');
    $customerEmail = trim($data['customer_email'] ?? '');
    $customerPhone = trim($data['customer_phone'] ?? '');
    $purchaseDate = trim($data['purchase_date'] ?? date('Y-m-d'));
    $warrantyType = trim($data['warranty_type'] ?? 'Standard Manufacturer Coverage');

    if (empty($serialNumber) || empty($customerName) || empty($customerEmail) || empty($customerPhone)) {
        json_error("Please fill in all required warranty registration fields", 400);
    }

    // Check if already registered
    $checkStmt = $pdo->prepare("SELECT id, warranty_code FROM warranties WHERE serial_number = ?");
    $checkStmt->execute([$serialNumber]);
    $existing = $checkStmt->fetch();
    if ($existing) {
        json_error("This serial number is already registered under warranty code: {$existing['warranty_code']}", 409);
    }

    // Verify product or infer from serial
    if ($productId <= 0) {
        $findP = $pdo->prepare("SELECT product_id FROM product_serials WHERE serial_number = ?");
        $findP->execute([$serialNumber]);
        $pRow = $findP->fetch();
        if ($pRow) {
            $productId = (int)$pRow['product_id'];
        } else {
            // Default to first product if unlinked
            $productId = 1;
        }
    }

    // Fetch product info to get warranty period
    $prodStmt = $pdo->prepare("SELECT name, warranty_months FROM products WHERE id = ?");
    $prodStmt->execute([$productId]);
    $prod = $prodStmt->fetch();
    $warrantyMonths = $prod ? (int)$prod['warranty_months'] : 12;

    $expiryDate = date('Y-m-d', strtotime("{$purchaseDate} +{$warrantyMonths} months"));
    $warrantyCode = 'WAR-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

    $insert = $pdo->prepare("
        INSERT INTO warranties (
            warranty_code, product_id, serial_number, order_id, customer_name,
            customer_email, customer_phone, purchase_date, expiry_date,
            warranty_type, status, notes, created_at
        ) VALUES (?, ?, ?, NULL, ?, ?, ?, ?, ?, ?, 'Active', ?, NOW())
    ");
    $note = "Self-registered online warranty certificate. Authorized manufacturer protection.";
    $insert->execute([
        $warrantyCode, $productId, $serialNumber, $customerName, $customerEmail,
        $customerPhone, $purchaseDate, $expiryDate, $warrantyType, $note
    ]);
    $warrantyId = (int)$pdo->lastInsertId();

    // Make sure serial exists in product_serials
    $ensureSerial = $pdo->prepare("
        INSERT INTO product_serials (product_id, serial_number, status, sold_at)
        VALUES (?, ?, 'sold', NOW())
        ON DUPLICATE KEY UPDATE status = 'sold', sold_at = NOW()
    ");
    $ensureSerial->execute([$productId, $serialNumber]);

    json_success([
        'warranty_id' => $warrantyId,
        'warranty_code' => $warrantyCode,
        'serial_number' => $serialNumber,
        'product_name' => $prod ? $prod['name'] : 'Registered Device',
        'purchase_date' => $purchaseDate,
        'expiry_date' => $expiryDate,
        'warranty_type' => $warrantyType,
        'status' => 'Active',
        'message' => 'Warranty successfully activated and registered in the national hardware database!'
    ], "Warranty registration completed successfully", 201);
}

json_error("Method not allowed", 405);
?>
