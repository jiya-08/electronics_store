<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_error("Method not allowed", 405);
}

// 1. Single Invoice by ID or Invoice Number
if (isset($_GET['id']) || isset($_GET['number'])) {
    $where = isset($_GET['id']) ? "i.id = ?" : "i.invoice_number = ?";
    $param = isset($_GET['id']) ? intval($_GET['id']) : trim($_GET['number']);

    $stmt = $pdo->prepare("
        SELECT i.*, o.order_number, o.payment_method, o.order_status, o.created_at as order_created_at
        FROM invoices i
        JOIN orders o ON i.order_id = o.id
        WHERE $where
    ");
    $stmt->execute([$param]);
    $invoice = $stmt->fetch();

    if (!$invoice) {
        json_error("Invoice not found", 404);
    }

    // Fetch order items with assigned serial numbers
    $itemsStmt = $pdo->prepare("
        SELECT oi.*, p.brand, p.model, p.sku, p.image_url, p.warranty_months, p.has_serial_tracking
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $itemsStmt->execute([$invoice['order_id']]);
    $items = $itemsStmt->fetchAll();

    foreach ($items as &$item) {
        $item['serials_array'] = !empty($item['assigned_serial_numbers']) 
            ? array_map('trim', explode(',', $item['assigned_serial_numbers']))
            : [];
    }
    $invoice['items'] = $items;

    // Fetch associated warranties
    $warStmt = $pdo->prepare("
        SELECT warranty_code, serial_number, expiry_date, warranty_type, status
        FROM warranties
        WHERE order_id = ?
    ");
    $warStmt->execute([$invoice['order_id']]);
    $invoice['warranties'] = $warStmt->fetchAll();

    json_success($invoice, "Invoice details retrieved");
}

// 2. Invoices List with filters
$search = trim($_GET['search'] ?? '');
$status = trim($_GET['status'] ?? '');

$conditions = ["1=1"];
$params = [];

if (!empty($search)) {
    $conditions[] = "(i.invoice_number LIKE ? OR i.customer_name LIKE ? OR i.customer_email LIKE ? OR o.order_number LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if (!empty($status) && $status !== 'all') {
    $conditions[] = "i.payment_status = ?";
    $params[] = $status;
}

$whereSql = implode(" AND ", $conditions);

$stmt = $pdo->prepare("
    SELECT i.*, o.order_number, o.payment_method,
           (SELECT COUNT(*) FROM order_items WHERE order_id = o.id) as item_count
    FROM invoices i
    JOIN orders o ON i.order_id = o.id
    WHERE $whereSql
    ORDER BY i.id DESC
");
$stmt->execute($params);
$invoices = $stmt->fetchAll();

json_success($invoices, "Invoices list fetched");
?>
