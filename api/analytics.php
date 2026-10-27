<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = get_db_connection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_error("Method not allowed", 405);
}

// 1. Core KPIs
$kpiStmt = $pdo->query("
    SELECT 
        COUNT(*) as total_orders,
        COALESCE(SUM(total_amount), 0) as gross_revenue,
        COALESCE(SUM(CASE WHEN payment_status = 'Paid' THEN total_amount ELSE 0 END), 0) as net_revenue,
        COALESCE(AVG(total_amount), 0) as average_order_value,
        COALESCE(SUM(tax_amount), 0) as total_tax_collected
    FROM orders
");
$kpis = $kpiStmt->fetch();

// Total units sold
$unitsSoldStmt = $pdo->query("SELECT COALESCE(SUM(quantity), 0) as total_units FROM order_items");
$totalUnitsSold = (int)$unitsSoldStmt->fetch()['total_units'];

// Count open RMAs
$rmaCountStmt = $pdo->query("SELECT COUNT(*) as count FROM return_requests WHERE status NOT IN ('Replacement Dispatched', 'Refund Processed', 'Rejected')");
$openRmas = (int)$rmaCountStmt->fetch()['count'];

// Count low stock
$lowStockStmt = $pdo->query("SELECT COUNT(*) as count FROM products WHERE stock_quantity <= low_stock_threshold");
$lowStockCount = (int)$lowStockStmt->fetch()['count'];

// Count active warranties
$warrantyStmt = $pdo->query("SELECT COUNT(*) as count FROM warranties WHERE status = 'Active' AND expiry_date >= CURDATE()");
$activeWarranties = (int)$warrantyStmt->fetch()['count'];

// 2. Sales by Category
$catSalesStmt = $pdo->query("
    SELECT 
        c.name as category_name,
        c.slug,
        COUNT(DISTINCT o.id) as orders_count,
        COALESCE(SUM(oi.quantity), 0) as units_sold,
        COALESCE(SUM(oi.subtotal), 0) as total_revenue
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    LEFT JOIN order_items oi ON oi.product_id = p.id
    LEFT JOIN orders o ON oi.order_id = o.id
    GROUP BY c.id
    ORDER BY total_revenue DESC
");
$catSales = $catSalesStmt->fetchAll();

// 3. Top Selling Gadgets
$topProdStmt = $pdo->query("
    SELECT 
        p.id, p.name, p.brand, p.model, p.sku, p.price, p.image_url,
        COALESCE(SUM(oi.quantity), 0) as total_units_sold,
        COALESCE(SUM(oi.subtotal), 0) as total_revenue
    FROM products p
    JOIN order_items oi ON oi.product_id = p.id
    JOIN orders o ON oi.order_id = o.id
    GROUP BY p.id
    ORDER BY total_units_sold DESC, total_revenue DESC
    LIMIT 5
");
$topProducts = $topProdStmt->fetchAll();

// 4. Revenue Trend (Last 7 Days / Recent timeline)
$trendStmt = $pdo->query("
    SELECT 
        DATE(created_at) as order_date,
        COUNT(id) as order_count,
        COALESCE(SUM(total_amount), 0) as daily_revenue
    FROM orders
    GROUP BY DATE(created_at)
    ORDER BY order_date ASC
    LIMIT 30
");
$revenueTrend = $trendStmt->fetchAll();

// 5. Return & RMA Statistics
$rmaStatusStmt = $pdo->query("
    SELECT status, COUNT(*) as count 
    FROM return_requests 
    GROUP BY status
");
$rmaStatuses = $rmaStatusStmt->fetchAll(PDO::FETCH_KEY_PAIR);

$rmaReasonStmt = $pdo->query("
    SELECT reason, COUNT(*) as count 
    FROM return_requests 
    GROUP BY reason
    ORDER BY count DESC
    LIMIT 6
");
$rmaReasons = $rmaReasonStmt->fetchAll();

// 6. Payment methods breakdown
$payStmt = $pdo->query("
    SELECT payment_method, COUNT(*) as count, SUM(total_amount) as total
    FROM orders
    GROUP BY payment_method
");
$paymentMethods = $payStmt->fetchAll();

json_success([
    'kpis' => [
        'total_orders' => (int)$kpis['total_orders'],
        'gross_revenue' => round((float)$kpis['gross_revenue'], 2),
        'net_revenue' => round((float)$kpis['net_revenue'], 2),
        'average_order_value' => round((float)$kpis['average_order_value'], 2),
        'total_units_sold' => $totalUnitsSold,
        'total_tax' => round((float)$kpis['total_tax_collected'], 2),
        'open_rmas' => $openRmas,
        'low_stock_count' => $lowStockCount,
        'active_warranties' => $activeWarranties
    ],
    'category_sales' => $catSales,
    'top_products' => $topProducts,
    'revenue_trend' => $revenueTrend,
    'rma_stats' => [
        'by_status' => $rmaStatuses,
        'by_reason' => $rmaReasons
    ],
    'payment_breakdown' => $paymentMethods
], "Sales analytics data generated");
?>
