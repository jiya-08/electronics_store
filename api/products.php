<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = get_db_connection();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // 1. Single product details
    if (isset($_GET['id']) || isset($_GET['slug'])) {
        $where = isset($_GET['id']) ? "p.id = ?" : "p.slug = ?";
        $param = isset($_GET['id']) ? intval($_GET['id']) : trim($_GET['slug']);

        $stmt = $pdo->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE $where
        ");
        $stmt->execute([$param]);
        $product = $stmt->fetch();

        if (!$product) {
            json_error("Product not found", 404);
        }

        // Fetch specifications grouped by spec_group
        $specStmt = $pdo->prepare("
            SELECT spec_group, spec_name, spec_value 
            FROM product_specifications 
            WHERE product_id = ? 
            ORDER BY spec_group ASC, sort_order ASC, id ASC
        ");
        $specStmt->execute([$product['id']]);
        $specs = $specStmt->fetchAll();

        $groupedSpecs = [];
        foreach ($specs as $spec) {
            $group = $spec['spec_group'] ?: 'General';
            if (!isset($groupedSpecs[$group])) {
                $groupedSpecs[$group] = [];
            }
            $groupedSpecs[$group][] = [
                'name' => $spec['spec_name'],
                'value' => $spec['spec_value']
            ];
        }
        $product['specifications'] = $groupedSpecs;
        $product['spec_list'] = $specs;

        // Stock status
        $product['is_low_stock'] = ($product['stock_quantity'] <= $product['low_stock_threshold']);
        $product['is_out_of_stock'] = ($product['stock_quantity'] <= 0);

        // Available serials count if tracking
        if ($product['has_serial_tracking']) {
            $serialStmt = $pdo->prepare("SELECT COUNT(*) as available_serials FROM product_serials WHERE product_id = ? AND status = 'in_stock'");
            $serialStmt->execute([$product['id']]);
            $product['available_serials_count'] = (int)$serialStmt->fetch()['available_serials'];
        } else {
            $product['available_serials_count'] = null;
        }

        json_success($product, "Product details fetched");
    }

    // 2. Product listing with filters
    $conditions = ["1=1"];
    $params = [];

    // Category filter
    if (!empty($_GET['category'])) {
        if (is_numeric($_GET['category'])) {
            $conditions[] = "p.category_id = ?";
            $params[] = intval($_GET['category']);
        } else {
            $conditions[] = "c.slug = ?";
            $params[] = trim($_GET['category']);
        }
    }

    // Brand filter
    if (!empty($_GET['brand'])) {
        $conditions[] = "p.brand = ?";
        $params[] = trim($_GET['brand']);
    }

    // Price range
    if (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) {
        $conditions[] = "COALESCE(p.sale_price, p.price) >= ?";
        $params[] = floatval($_GET['min_price']);
    }
    if (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) {
        $conditions[] = "COALESCE(p.sale_price, p.price) <= ?";
        $params[] = floatval($_GET['max_price']);
    }

    // Search query
    if (!empty($_GET['search'])) {
        $search = '%' . trim($_GET['search']) . '%';
        $conditions[] = "(p.name LIKE ? OR p.brand LIKE ? OR p.model LIKE ? OR p.sku LIKE ? OR p.description LIKE ?)";
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
        $params[] = $search;
    }

    // Featured only
    if (isset($_GET['featured']) && $_GET['featured'] == '1') {
        $conditions[] = "p.is_featured = 1";
    }

    // Low stock filter (for management)
    if (isset($_GET['low_stock']) && $_GET['low_stock'] == '1') {
        $conditions[] = "p.stock_quantity <= p.low_stock_threshold";
    }

    // Sorting
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'featured';
    $orderBy = "p.is_featured DESC, p.id ASC";
    switch ($sort) {
        case 'price_asc':
            $orderBy = "COALESCE(p.sale_price, p.price) ASC";
            break;
        case 'price_desc':
            $orderBy = "COALESCE(p.sale_price, p.price) DESC";
            break;
        case 'rating':
            $orderBy = "p.rating DESC";
            break;
        case 'newest':
            $orderBy = "p.created_at DESC";
            break;
        case 'stock_asc':
            $orderBy = "p.stock_quantity ASC";
            break;
    }

    $whereClause = implode(" AND ", $conditions);

    // Count total matching
    $countSql = "SELECT COUNT(*) as total FROM products p JOIN categories c ON p.category_id = c.id WHERE $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalCount = (int)$countStmt->fetch()['total'];

    // Pagination
    $limit = isset($_GET['limit']) ? max(1, min(100, intval($_GET['limit']))) : 50;
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($page - 1) * $limit;

    $sql = "
        SELECT p.*, c.name as category_name, c.slug as category_slug, c.icon as category_icon,
               (p.stock_quantity <= p.low_stock_threshold) as is_low_stock
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE $whereClause
        ORDER BY $orderBy
        LIMIT $limit OFFSET $offset
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();

    // Get all categories and brands for filter UI
    $cats = $pdo->query("SELECT id, name, slug, icon, (SELECT COUNT(*) FROM products WHERE category_id = categories.id) as product_count FROM categories ORDER BY name ASC")->fetchAll();
    $brands = $pdo->query("SELECT DISTINCT brand FROM products ORDER BY brand ASC")->fetchAll(PDO::FETCH_COLUMN);

    json_success([
        'total' => $totalCount,
        'page' => $page,
        'limit' => $limit,
        'products' => $products,
        'categories' => $cats,
        'brands' => $brands
    ]);
}

json_error("Method not allowed", 405);
?>
