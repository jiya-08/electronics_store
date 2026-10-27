<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_error("Method not allowed", 405);
}

$rawIds = isset($_GET['ids']) ? trim($_GET['ids']) : '';
if (empty($rawIds)) {
    json_error("Please specify product IDs to compare (e.g., ?ids=1,2)", 400);
}

$idArray = array_unique(array_filter(array_map('intval', explode(',', $rawIds))));

if (count($idArray) < 1) {
    json_error("At least one valid product ID required for comparison", 400);
}

if (count($idArray) > 4) {
    $idArray = array_slice($idArray, 0, 4); // Max 4 products for comparison
}

$placeholders = implode(',', array_fill(0, count($idArray), '?'));

// 1. Fetch products
$productStmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.id IN ($placeholders)
");
$productStmt->execute($idArray);
$products = $productStmt->fetchAll();

// Reorder products according to the requested order
$orderedProducts = [];
$productMap = [];
foreach ($products as $p) {
    $productMap[$p['id']] = $p;
}
foreach ($idArray as $id) {
    if (isset($productMap[$id])) {
        $orderedProducts[] = $productMap[$id];
    }
}

if (empty($orderedProducts)) {
    json_error("No matching products found for the provided IDs", 404);
}

// 2. Fetch all specifications for these products
$specStmt = $pdo->prepare("
    SELECT product_id, spec_group, spec_name, spec_value, sort_order 
    FROM product_specifications 
    WHERE product_id IN ($placeholders)
    ORDER BY sort_order ASC, spec_name ASC
");
$specStmt->execute($idArray);
$allSpecs = $specStmt->fetchAll();

// Group specifications: spec_group -> spec_name -> [product_id => spec_value]
$matrix = [];
$groupOrder = [
    'Processor & OS' => 1,
    'Memory & Storage' => 2,
    'Display' => 3,
    'Camera' => 4,
    'Battery & Charging' => 5,
    'Connectivity & Ports' => 6,
    'Audio & Acoustics' => 7,
    'Display & Sensor' => 8,
    'Build & Warranty' => 9,
    'General' => 10
];

foreach ($allSpecs as $row) {
    $group = $row['spec_group'] ?: 'General';
    $name = $row['spec_name'];
    $pId = $row['product_id'];
    $val = $row['spec_value'];

    if (!isset($matrix[$group])) {
        $matrix[$group] = [
            'group_name' => $group,
            'specs' => []
        ];
    }

    if (!isset($matrix[$group]['specs'][$name])) {
        $matrix[$group]['specs'][$name] = [
            'name' => $name,
            'values' => []
        ];
    }

    $matrix[$group]['specs'][$name]['values'][$pId] = $val;
}

// Ensure every product has a value (or '—') and flag differences
$comparisonGroups = [];
foreach ($matrix as $groupName => $groupData) {
    $specsList = [];
    foreach ($groupData['specs'] as $specName => $specData) {
        $values = [];
        $distinctVals = [];
        foreach ($orderedProducts as $prod) {
            $val = isset($specData['values'][$prod['id']]) ? $specData['values'][$prod['id']] : '—';
            $values[$prod['id']] = $val;
            if ($val !== '—') {
                $distinctVals[trim(strtolower($val))] = true;
            }
        }
        $isDifferent = count($distinctVals) > 1;

        $specsList[] = [
            'spec_name' => $specName,
            'values' => $values,
            'is_different' => $isDifferent
        ];
    }
    $comparisonGroups[] = [
        'group_name' => $groupName,
        'specs' => $specsList
    ];
}

// Quick overview highlights
$summary = [
    'product_count' => count($orderedProducts),
    'same_category' => count(array_unique(array_column($orderedProducts, 'category_id'))) === 1,
    'price_range' => [
        'min' => min(array_map(function($p) { return (float)($p['sale_price'] ?? $p['price']); }, $orderedProducts)),
        'max' => max(array_map(function($p) { return (float)($p['sale_price'] ?? $p['price']); }, $orderedProducts)),
    ]
];

json_success([
    'products' => $orderedProducts,
    'matrix' => $comparisonGroups,
    'summary' => $summary
], "Comparison matrix generated successfully");
?>
