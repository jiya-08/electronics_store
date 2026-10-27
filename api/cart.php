<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = get_db_connection();
$input = get_request_data();

$items = isset($input['items']) ? $input['items'] : [];

if (!is_array($items)) {
    json_error("Invalid items format", 400);
}

if (empty($items)) {
    json_success([
        'items' => [],
        'subtotal' => 0.00,
        'tax_amount' => 0.00,
        'shipping_amount' => 0.00,
        'total_amount' => 0.00,
        'item_count' => 0
    ], "Cart is empty");
}

$productIds = [];
$quantities = [];

foreach ($items as $item) {
    if (isset($item['product_id']) && intval($item['product_id']) > 0) {
        $pId = intval($item['product_id']);
        $qty = max(1, intval($item['quantity'] ?? 1));
        $productIds[] = $pId;
        $quantities[$pId] = ($quantities[$pId] ?? 0) + $qty;
    }
}

if (empty($productIds)) {
    json_success([
        'items' => [],
        'subtotal' => 0.00,
        'tax_amount' => 0.00,
        'shipping_amount' => 0.00,
        'total_amount' => 0.00,
        'item_count' => 0
    ], "Cart is empty");
}

$placeholders = implode(',', array_fill(0, count($productIds), '?'));
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.id IN ($placeholders)
");
$stmt->execute($productIds);
$fetchedProducts = $stmt->fetchAll();

$cartItems = [];
$subtotal = 0.0;
$totalUnits = 0;
$hasStockIssue = false;

foreach ($fetchedProducts as $prod) {
    $pId = $prod['id'];
    $requestedQty = $quantities[$pId];
    $unitPrice = floatval($prod['sale_price'] ?? $prod['price']);
    $itemSubtotal = $unitPrice * $requestedQty;
    $availableStock = intval($prod['stock_quantity']);

    $stockWarning = null;
    if ($requestedQty > $availableStock) {
        $hasStockIssue = true;
        $stockWarning = "Only {$availableStock} unit(s) available in stock";
    }

    $cartItems[] = [
        'product_id' => $pId,
        'name' => $prod['name'],
        'brand' => $prod['brand'],
        'sku' => $prod['sku'],
        'category_name' => $prod['category_name'],
        'image_url' => $prod['image_url'],
        'unit_price' => $unitPrice,
        'regular_price' => floatval($prod['price']),
        'quantity' => $requestedQty,
        'available_stock' => $availableStock,
        'has_serial_tracking' => (bool)$prod['has_serial_tracking'],
        'warranty_months' => (int)$prod['warranty_months'],
        'subtotal' => $itemSubtotal,
        'stock_warning' => $stockWarning
    ];

    $subtotal += $itemSubtotal;
    $totalUnits += $requestedQty;
}

$taxAmount = round($subtotal * STORE_TAX_RATE, 2);
$shippingAmount = ($subtotal >= 500.00 || $subtotal == 0) ? 0.00 : 15.00;
$totalAmount = round($subtotal + $taxAmount + $shippingAmount, 2);

json_success([
    'items' => $cartItems,
    'subtotal' => round($subtotal, 2),
    'tax_rate' => STORE_TAX_RATE * 100,
    'tax_amount' => $taxAmount,
    'shipping_amount' => $shippingAmount,
    'total_amount' => $totalAmount,
    'item_count' => $totalUnits,
    'has_stock_issue' => $hasStockIssue
], "Cart calculated successfully");
?>
