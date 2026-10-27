<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = get_db_connection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $rmaNumber = trim($_GET['rma'] ?? '');
    $orderNumber = trim($_GET['order'] ?? '');
    $email = trim($_GET['email'] ?? '');
    $statusFilter = trim($_GET['status'] ?? '');

    $conditions = ["1=1"];
    $params = [];

    if (!empty($rmaNumber)) {
        $conditions[] = "r.rma_number = ?";
        $params[] = $rmaNumber;
    }
    if (!empty($orderNumber)) {
        $conditions[] = "o.order_number = ?";
        $params[] = $orderNumber;
    }
    if (!empty($email)) {
        $conditions[] = "r.customer_email = ?";
        $params[] = $email;
    }
    if (!empty($statusFilter) && $statusFilter !== 'all') {
        $conditions[] = "r.status = ?";
        $params[] = $statusFilter;
    }

    $whereSql = implode(" AND ", $conditions);

    $stmt = $pdo->prepare("
        SELECT r.*, p.name as product_name, p.brand, p.model, p.image_url,
               o.order_number, o.created_at as order_date, o.total_amount as order_total
        FROM return_requests r
        JOIN products p ON r.product_id = p.id
        JOIN orders o ON r.order_id = o.id
        WHERE $whereSql
        ORDER BY r.id DESC
    ");
    $stmt->execute($params);
    $returns = $stmt->fetchAll();

    $stepsMap = [
        'Pending Review' => 1,
        'Approved - Ship Item Back' => 2,
        'Item Received & Inspected' => 3,
        'Replacement Dispatched' => 4,
        'Refund Processed' => 4,
        'Rejected' => 0
    ];

    foreach ($returns as &$ret) {
        $st = $ret['status'];
        $ret['current_step'] = $stepsMap[$st] ?? 1;
        $ret['is_closed'] = in_array($st, ['Replacement Dispatched', 'Refund Processed', 'Rejected']);
    }

    if (!empty($rmaNumber) && empty($returns)) {
        json_error("RMA request '{$rmaNumber}' not found", 404);
    }

    json_success($returns, "Return requests fetched");
}

if ($method === 'POST') {
    $action = $_GET['action'] ?? '';
    $data = get_request_data();

    // Admin updates RMA status
    if ($action === 'update_status') {
        $rmaId = intval($data['id'] ?? 0);
        $newStatus = trim($data['status'] ?? '');
        $adminResponse = trim($data['admin_response'] ?? '');
        $resolutionNotes = trim($data['resolution_notes'] ?? '');

        if ($rmaId <= 0 || empty($newStatus)) {
            json_error("RMA ID and new status are required", 400);
        }

        // Fetch existing RMA
        $getStmt = $pdo->prepare("SELECT * FROM return_requests WHERE id = ?");
        $getStmt->execute([$rmaId]);
        $rma = $getStmt->fetch();

        if (!$rma) {
            json_error("RMA not found", 404);
        }

        $upd = $pdo->prepare("
            UPDATE return_requests 
            SET status = ?, admin_response = ?, resolution_notes = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $upd->execute([$newStatus, $adminResponse, $resolutionNotes, $rmaId]);

        // If marked inspected or replaced, update serial status
        if (!empty($rma['serial_number'])) {
            if ($newStatus === 'Item Received & Inspected' || $newStatus === 'Replacement Dispatched') {
                $updSerial = $pdo->prepare("UPDATE product_serials SET status = 'returned_defective' WHERE serial_number = ?");
                $updSerial->execute([$rma['serial_number']]);

                // Log stock movement
                $logStmt = $pdo->prepare("
                    INSERT INTO stock_logs (product_id, change_type, quantity_change, previous_stock, new_stock, reference_id, note)
                    SELECT id, 'Defective Scrap', 0, stock_quantity, stock_quantity, ?, ?
                    FROM products WHERE id = ?
                ");
                $logNote = "RMA {$rma['rma_number']}: Serial {$rma['serial_number']} received & marked defective";
                $logStmt->execute([$rma['rma_number'], $logNote, $rma['product_id']]);
            }
        }

        // If refunded, mark order refunded
        if ($newStatus === 'Refund Processed') {
            $pdo->prepare("UPDATE orders SET payment_status = 'Refunded', order_status = 'Returned' WHERE id = ?")->execute([$rma['order_id']]);
            $pdo->prepare("UPDATE invoices SET payment_status = 'Refunded' WHERE order_id = ?")->execute([$rma['order_id']]);
        }

        json_success([
            'id' => $rmaId,
            'status' => $newStatus,
            'admin_response' => $adminResponse
        ], "RMA status updated to '$newStatus'");
    }

    // Customer initiates new Return / Replacement request
    $orderNumber = trim($data['order_number'] ?? '');
    $customerEmail = trim($data['customer_email'] ?? '');
    $productId = intval($data['product_id'] ?? 0);
    $serialNumber = trim($data['serial_number'] ?? '');
    $requestType = trim($data['request_type'] ?? 'Replacement');
    $reason = trim($data['reason'] ?? '');
    $description = trim($data['description'] ?? '');
    $photoUrl = trim($data['photo_url'] ?? '');

    if (empty($orderNumber) || empty($customerEmail) || empty($reason) || empty($description)) {
        json_error("Please supply Order Number, Email, Reason, and Problem Description", 400);
    }

    // Verify order
    $orderStmt = $pdo->prepare("SELECT * FROM orders WHERE order_number = ? AND customer_email = ?");
    $orderStmt->execute([$orderNumber, $customerEmail]);
    $order = $orderStmt->fetch();

    if (!$order) {
        json_error("Order '{$orderNumber}' matching email '{$customerEmail}' was not found. Please verify your order receipt.", 404);
    }

    // If product_id not provided, look in order_items
    if ($productId <= 0) {
        $itemStmt = $pdo->prepare("SELECT product_id, assigned_serial_numbers FROM order_items WHERE order_id = ? LIMIT 1");
        $itemStmt->execute([$order['id']]);
        $firstItem = $itemStmt->fetch();
        if ($firstItem) {
            $productId = (int)$firstItem['product_id'];
            if (empty($serialNumber) && !empty($firstItem['assigned_serial_numbers'])) {
                $serials = explode(',', $firstItem['assigned_serial_numbers']);
                $serialNumber = trim($serials[0]);
            }
        } else {
            json_error("No products associated with this order", 400);
        }
    }

    // Generate unique RMA Number
    $rmaNumber = 'RMA-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));

    $insertStmt = $pdo->prepare("
        INSERT INTO return_requests (
            rma_number, order_id, product_id, serial_number, customer_name,
            customer_email, request_type, reason, description, photo_url,
            status, admin_response, created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending Review', 'Your RMA ticket is submitted and under technical review.', NOW())
    ");
    $insertStmt->execute([
        $rmaNumber, $order['id'], $productId, $serialNumber, $order['customer_name'],
        $customerEmail, $requestType, $reason, $description, $photoUrl
    ]);
    $rmaId = (int)$pdo->lastInsertId();

    json_success([
        'rma_id' => $rmaId,
        'rma_number' => $rmaNumber,
        'order_number' => $orderNumber,
        'request_type' => $requestType,
        'status' => 'Pending Review',
        'message' => "Return/Replacement request submitted successfully! Your RMA tracking code is {$rmaNumber}."
    ], "RMA ticket created successfully", 201);
}

json_error("Method not allowed", 405);
?>
