<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = get_db_connection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // 1. Fetch products with stock metrics
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.brand, p.model, p.sku, p.price, p.sale_price,
               p.stock_quantity, p.low_stock_threshold, p.has_serial_tracking,
               p.image_url, c.name as category_name,
               CASE 
                   WHEN p.stock_quantity <= 0 THEN 'Out of Stock'
                   WHEN p.stock_quantity <= p.low_stock_threshold THEN 'Low Stock'
                   ELSE 'In Stock'
               END as stock_status,
               (SELECT COUNT(*) FROM product_serials WHERE product_id = p.id AND status = 'in_stock') as available_serials
        FROM products p
        JOIN categories c ON p.category_id = c.id
        ORDER BY (p.stock_quantity <= p.low_stock_threshold) DESC, p.stock_quantity ASC
    ");
    $stmt->execute();
    $inventory = $stmt->fetchAll();

    // Low stock warnings list
    $lowStockItems = [];
    $totalInventoryUnits = 0;
    $totalInventoryValue = 0.0;

    foreach ($inventory as $item) {
        $totalInventoryUnits += (int)$item['stock_quantity'];
        $totalInventoryValue += ((float)($item['sale_price'] ?? $item['price'])) * (int)$item['stock_quantity'];

        if ($item['stock_status'] === 'Low Stock' || $item['stock_status'] === 'Out of Stock') {
            $lowStockItems[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'brand' => $item['brand'],
                'sku' => $item['sku'],
                'stock_quantity' => (int)$item['stock_quantity'],
                'low_stock_threshold' => (int)$item['low_stock_threshold'],
                'category_name' => $item['category_name'],
                'urgency' => ($item['stock_quantity'] <= 0) ? 'Critical' : 'Warning'
            ];
        }
    }

    // 2. Fetch recent stock movement logs
    $logStmt = $pdo->query("
        SELECT sl.*, p.name as product_name, p.sku
        FROM stock_logs sl
        JOIN products p ON sl.product_id = p.id
        ORDER BY sl.id DESC
        LIMIT 25
    ");
    $logs = $logStmt->fetchAll();

    // 3. Serial summary counts
    $serialStats = $pdo->query("
        SELECT status, COUNT(*) as count 
        FROM product_serials 
        GROUP BY status
    ")->fetchAll(PDO::FETCH_KEY_PAIR);

    json_success([
        'inventory' => $inventory,
        'low_stock_count' => count($lowStockItems),
        'low_stock_alerts' => $lowStockItems,
        'total_units' => $totalInventoryUnits,
        'total_inventory_value' => round($totalInventoryValue, 2),
        'recent_logs' => $logs,
        'serial_stats' => $serialStats
    ], "Stock tracking data retrieved");
}

if ($method === 'POST') {
    $action = $_GET['action'] ?? 'restock';
    $data = get_request_data();

    if ($action === 'restock') {
        $productId = intval($data['product_id'] ?? 0);
        $qty = intval($data['quantity'] ?? 0);
        $note = trim($data['note'] ?? 'Warehouse restock intake');
        $serialsRaw = trim($data['serials'] ?? '');

        if ($productId <= 0 || $qty <= 0) {
            json_error("Valid Product ID and positive restock quantity are required", 400);
        }

        try {
            $pdo->beginTransaction();

            // Lock product row
            $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? FOR UPDATE");
            $stmt->execute([$productId]);
            $prod = $stmt->fetch();

            if (!$prod) {
                $pdo->rollBack();
                json_error("Product not found", 404);
            }

            $prevStock = (int)$prod['stock_quantity'];
            $newStock = $prevStock + $qty;

            // Update product stock
            $upd = $pdo->prepare("UPDATE products SET stock_quantity = ? WHERE id = ?");
            $upd->execute([$newStock, $productId]);

            // Add stock log
            $refId = 'PO-' . date('Ymd') . '-' . rand(100, 999);
            $log = $pdo->prepare("
                INSERT INTO stock_logs (product_id, change_type, quantity_change, previous_stock, new_stock, reference_id, note)
                VALUES (?, 'Restock', ?, ?, ?, ?, ?)
            ");
            $log->execute([$productId, $qty, $prevStock, $newStock, $refId, $note]);

            // If product has serial tracking, insert serials
            $addedSerials = [];
            if ($prod['has_serial_tracking']) {
                if (!empty($serialsRaw)) {
                    $serialList = array_filter(array_map('trim', preg_split('/[\r\n,]+/', $serialsRaw)));
                } else {
                    $serialList = [];
                }

                // If user didn't provide enough serials, auto-generate remaining
                $remainingNeeded = $qty - count($serialList);
                for ($i = 0; $i < $remainingNeeded; $i++) {
                    $serialList[] = 'SN-' . strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $prod['brand']), 0, 3)) . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8));
                }

                $sInsert = $pdo->prepare("
                    INSERT INTO product_serials (product_id, serial_number, status, created_at)
                    VALUES (?, ?, 'in_stock', NOW())
                    ON DUPLICATE KEY UPDATE status = 'in_stock'
                ");

                foreach ($serialList as $sNum) {
                    $sInsert->execute([$productId, $sNum]);
                    $addedSerials[] = $sNum;
                }
            }

            $pdo->commit();

            json_success([
                'product_id' => $productId,
                'product_name' => $prod['name'],
                'previous_stock' => $prevStock,
                'added_quantity' => $qty,
                'new_stock' => $newStock,
                'reference_id' => $refId,
                'serials_added' => count($addedSerials)
            ], "Stock successfully replenished for '{$prod['name']}'");

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            json_error("Failed to restock: " . $e->getMessage(), 500);
        }
    }

    if ($action === 'update_threshold') {
        $productId = intval($data['product_id'] ?? 0);
        $threshold = intval($data['threshold'] ?? 5);

        if ($productId <= 0 || $threshold < 1) {
            json_error("Valid Product ID and threshold >= 1 required", 400);
        }

        $upd = $pdo->prepare("UPDATE products SET low_stock_threshold = ? WHERE id = ?");
        $upd->execute([$threshold, $productId]);

        json_success([
            'product_id' => $productId,
            'new_threshold' => $threshold
        ], "Low stock threshold updated");
    }
}

json_error("Method not allowed", 405);
?>
