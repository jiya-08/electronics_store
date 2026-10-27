<?php
$pageTitle = "Next-Gen Electronics, Laptops & Phones";
require_once __DIR__ . '/includes/header.php';

$pdo = get_db_connection();

// Fetch Categories with counts
$categories = $pdo->query("
    SELECT c.*, COUNT(p.id) as product_count 
    FROM categories c 
    LEFT JOIN products p ON p.category_id = c.id 
    GROUP BY c.id 
    ORDER BY c.id ASC
")->fetchAll();

// Fetch Featured Products
$featuredStmt = $pdo->query("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.is_featured = 1 
    ORDER BY p.id ASC 
    LIMIT 6
");
$featuredProducts = $featuredStmt->fetchAll();

// Fetch Low-stock products for ticker alert
$lowStockStmt = $pdo->query("
    SELECT name, brand, stock_quantity, low_stock_threshold 
    FROM products 
    WHERE stock_quantity <= low_stock_threshold 
    ORDER BY stock_quantity ASC 
    LIMIT 3
");
$lowStockAlerts = $lowStockStmt->fetchAll();
?>

<!-- Low Stock Live Alert Banner (if any) -->
<?php if (!empty($lowStockAlerts)): ?>
<div class="bg-amber-500/10 border-b border-amber-500/20 px-4 py-2 text-xs text-amber-800">
    <div class="max-w-7xl mx-auto flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 overflow-hidden">
            <span class="inline-flex items-center gap-1 font-bold uppercase tracking-wider bg-amber-500 text-white px-2 py-0.5 rounded text-[10px]">
                <i class="fa-solid fa-triangle-exclamation"></i> Low Inventory Alert
            </span>
            <span class="truncate">
                Limited stock remaining: 
                <?php 
                $alertStrings = array_map(function($item) {
                    return "<strong>{$item['brand']} {$item['name']}</strong> ({$item['stock_quantity']} left)";
                }, $lowStockAlerts);
                echo implode(' • ', $alertStrings);
                ?>
            </span>
        </div>
        <a href="<?php echo BASE_URL; ?>/admin.php?tab=inventory" class="font-semibold text-amber-900 hover:underline flex-shrink-0 text-xs">
            Manage Stock <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>
</div>
<?php endif; ?>

<!-- Hero Section -->
<section class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 text-white py-16 lg:py-24 overflow-hidden">
    <!-- Decorative background glow -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-blue-500/20 blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-96 h-96 rounded-full bg-cyan-500/20 blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-7 space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-400/30 text-blue-300 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                    <span>2026 Hardware Lineup • Tracked Serial Numbers</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-tight">
                    Next-Gen Tech. <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-cyan-300 to-teal-300">
                        Detailed Specs & Precision Comparison.
                    </span>
                </h1>

                <p class="text-base sm:text-lg text-slate-300 max-w-xl font-normal leading-relaxed">
                    Explore ultra-performance smartphones, M3 laptops, and noise-cancelling acoustics. Every device is backed by official serial registration, automated tax invoicing, and seamless warranty claims.
                </p>

                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="<?php echo BASE_URL; ?>/products.php" class="px-6 py-3.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-sm shadow-lg shadow-blue-500/25 transition flex items-center gap-2 group">
                        <span>Browse Catalog</span>
                        <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition"></i>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/compare.php?ids=1,2" class="px-6 py-3.5 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-slate-200 rounded-xl font-semibold text-sm transition flex items-center gap-2">
                        <i class="fa-solid fa-code-compare text-cyan-400"></i>
                        <span>Compare Flagships</span>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/warranty.php" class="px-5 py-3.5 text-slate-300 hover:text-white text-sm font-semibold transition flex items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-emerald-400"></i>
                        <span>Check Warranty</span>
                    </a>
                </div>

                <!-- Feature badges -->
                <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-800 text-xs text-slate-400">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-fingerprint text-blue-400 text-base"></i>
                        <span>Serial Number Tracking</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-boxes-stacked text-amber-400 text-base"></i>
                        <span>Live Stock Telemetry</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-arrow-rotate-left text-emerald-400 text-base"></i>
                        <span>Online RMA Returns</span>
                    </div>
                </div>
            </div>

            <!-- Hero Interactive Gadget Card Preview -->
            <div class="lg:col-span-5">
                <div class="bg-gradient-to-b from-slate-800/90 to-slate-900/90 border border-slate-700 rounded-3xl p-6 shadow-2xl relative overflow-hidden backdrop-blur-xl">
                    <div class="flex justify-between items-start mb-4">
                        <span class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs font-semibold uppercase tracking-wider border border-blue-500/30">
                            Featured Flagship
                        </span>
                        <span class="flex items-center gap-1.5 text-xs text-emerald-400 font-mono">
                            <i class="fa-solid fa-circle-check"></i> Serial Verified
                        </span>
                    </div>

                    <div class="relative h-56 rounded-2xl overflow-hidden bg-slate-950 flex items-center justify-center mb-6 border border-slate-800">
                        <img src="https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=800&q=80" alt="iPhone 15 Pro Max" class="object-cover w-full h-full opacity-90 hover:scale-105 transition duration-500">
                        <div class="absolute bottom-3 left-3 bg-slate-900/80 backdrop-blur-md px-3 py-1 rounded-lg text-xs font-mono text-cyan-300 border border-slate-700">
                            A17 Pro • Titanium Blue
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <h3 class="text-xl font-bold text-white">Apple iPhone 15 Pro Max</h3>
                            <span class="text-xl font-extrabold text-white">$1,149.00</span>
                        </div>
                        <p class="text-xs text-slate-400">256GB NVMe • 6.7" Super Retina XDR OLED • 48MP Optical 5x Telephoto</p>

                        <div class="pt-3 flex gap-2">
                            <button onclick="App.addToCart(1, 1)" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2.5 px-4 rounded-xl text-xs transition flex items-center justify-center gap-2">
                                <i class="fa-solid fa-cart-shopping"></i> Quick Buy
                            </button>
                            <button onclick="App.toggleCompare({id: 1, name: 'Apple iPhone 15 Pro Max', image_url: 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?auto=format&fit=crop&w=800&q=80', price: 1149})" class="bg-slate-800 hover:bg-slate-700 text-slate-300 px-4 py-2.5 rounded-xl text-xs font-semibold border border-slate-700 transition flex items-center gap-1.5" title="Add to Comparison Matrix">
                                <i class="fa-solid fa-code-compare"></i> Compare
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Categories Bar -->
<section class="py-12 bg-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Explore by Category</h2>
                <p class="text-xs text-slate-500">Find devices tailored to your workflow and gaming needs</p>
            </div>
            <a href="<?php echo BASE_URL; ?>/products.php" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition flex items-center gap-1">
                View All Categories <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-4">
            <?php 
            $catIcons = [
                'smartphones' => 'fa-mobile-screen-button',
                'laptops' => 'fa-laptop',
                'audio' => 'fa-headphones-simple',
                'wearables' => 'fa-stopwatch',
                'tablets-displays' => 'fa-tablet-screen-button',
                'accessories' => 'fa-bolt'
            ];
            foreach ($categories as $cat): 
                $iconClass = $catIcons[$cat['slug']] ?? 'fa-microchip';
            ?>
            <a href="<?php echo BASE_URL; ?>/products.php?category=<?php echo urlencode($cat['slug']); ?>" class="group p-5 bg-slate-50 hover:bg-blue-50/50 border border-slate-200 hover:border-blue-300 rounded-2xl text-center transition flex flex-col items-center">
                <div class="w-12 h-12 rounded-xl bg-white group-hover:bg-blue-600 shadow-sm flex items-center justify-center text-slate-700 group-hover:text-white transition mb-3">
                    <i class="fa-solid <?php echo $iconClass; ?> text-xl"></i>
                </div>
                <h4 class="text-xs font-bold text-slate-800 group-hover:text-blue-600 transition truncate max-w-full"><?php echo htmlspecialchars($cat['name']); ?></h4>
                <span class="text-[11px] text-slate-400 mt-0.5"><?php echo (int)$cat['product_count']; ?> Products</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Grid with Live Specs & Stock Badges -->
<section class="py-14 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Premium Devices</span>
                <h2 class="text-3xl font-extrabold text-slate-900 mt-1">Featured Electronics</h2>
                <p class="text-sm text-slate-500 mt-1">Includes live stock status, serial-number tracking, and side-by-side comparison</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?php echo BASE_URL; ?>/compare.php" class="px-4 py-2 bg-white border border-slate-300 hover:border-blue-500 rounded-xl text-xs font-semibold text-slate-700 transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-code-compare text-blue-600"></i> Open Comparison Matrix
                </a>
                <a href="<?php echo BASE_URL; ?>/products.php" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition shadow-sm">
                    All Products
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($featuredProducts as $product): 
                $isLowStock = ($product['stock_quantity'] <= $product['low_stock_threshold']);
                $isOut = ($product['stock_quantity'] <= 0);
            ?>
            <div class="tech-card overflow-hidden flex flex-col justify-between group">
                <div>
                    <!-- Product Image & Top Badges -->
                    <div class="relative h-60 bg-white overflow-hidden p-6 flex items-center justify-center border-b border-slate-100">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-h-full max-w-full object-contain group-hover:scale-105 transition duration-300" onerror="this.src='https://placehold.co/400x300?text=Hardware'">

                        <!-- Category & Serial Badge -->
                        <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                            <span class="bg-slate-900/80 backdrop-blur-sm text-white px-2.5 py-1 rounded-md text-[11px] font-semibold">
                                <?php echo htmlspecialchars($product['brand']); ?>
                            </span>
                            <?php if ($product['has_serial_tracking']): ?>
                            <span class="badge-serial px-2 py-0.5 rounded text-[10px] font-semibold flex items-center gap-1">
                                <i class="fa-solid fa-barcode text-[9px]"></i> Serial Tracked
                            </span>
                            <?php endif; ?>
                        </div>

                        <!-- Stock Status Badge -->
                        <div class="absolute top-3 right-3">
                            <?php if ($isOut): ?>
                                <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded-md uppercase">
                                    Out of Stock
                                </span>
                            <?php elseif ($isLowStock): ?>
                                <span class="badge-low-stock px-2.5 py-1 rounded-md text-[10px] font-bold flex items-center gap-1">
                                    <i class="fa-solid fa-fire text-red-500"></i> Only <?php echo $product['stock_quantity']; ?> left!
                                </span>
                            <?php else: ?>
                                <span class="badge-in-stock px-2 py-1 rounded-md text-[10px] font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> In Stock (<?php echo $product['stock_quantity']; ?>)
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Details Area -->
                    <div class="p-5 space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-400">
                            <span><?php echo htmlspecialchars($product['category_name']); ?></span>
                            <span class="flex items-center gap-1 text-amber-500 font-semibold">
                                <i class="fa-solid fa-star text-[11px]"></i> <?php echo $product['rating']; ?> (<?php echo $product['review_count']; ?>)
                            </span>
                        </div>

                        <a href="<?php echo BASE_URL; ?>/product-detail.php?id=<?php echo $product['id']; ?>" class="block group-hover:text-blue-600 transition">
                            <h3 class="font-bold text-slate-900 text-base leading-snug"><?php echo htmlspecialchars($product['name']); ?></h3>
                        </a>

                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">
                            <?php echo htmlspecialchars($product['description']); ?>
                        </p>

                        <!-- Warranty tag -->
                        <div class="text-[11px] text-slate-500 flex items-center gap-1 font-mono pt-1">
                            <i class="fa-solid fa-shield text-blue-500"></i> 
                            Official <?php echo $product['warranty_months']; ?>-Month Coverage
                        </div>
                    </div>
                </div>

                <!-- Footer with Price & Actions -->
                <div class="p-5 pt-0 mt-2">
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                        <div>
                            <span class="text-lg font-extrabold text-slate-900">
                                $<?php echo number_format($product['sale_price'] ?? $product['price'], 2); ?>
                            </span>
                            <?php if (!empty($product['sale_price'])): ?>
                                <span class="text-xs text-slate-400 line-through ml-1.5 font-normal">
                                    $<?php echo number_format($product['price'], 2); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="flex items-center gap-2">
                            <!-- Add to Compare Button -->
                            <button onclick="App.toggleCompare(<?php echo htmlspecialchars(json_encode([
                                'id' => (int)$product['id'],
                                'name' => $product['name'],
                                'image_url' => $product['image_url'],
                                'price' => floatval($product['sale_price'] ?? $product['price']),
                                'brand' => $product['brand']
                            ])); ?>)" class="p-2.5 rounded-xl border border-slate-200 hover:border-cyan-500 hover:text-cyan-600 text-slate-600 bg-white transition shadow-sm" title="Add to Comparison Matrix">
                                <i class="fa-solid fa-code-compare text-xs"></i>
                            </button>

                            <!-- Add to Cart Button -->
                            <button onclick="App.addToCart(<?php echo $product['id']; ?>, 1)" class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition shadow-sm flex items-center gap-1.5" <?php echo $isOut ? 'disabled' : ''; ?>>
                                <i class="fa-solid fa-cart-shopping"></i> Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Comparison Spotlight Banner -->
<section class="py-16 bg-slate-900 text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <div class="lg:col-span-8 space-y-4">
                <span class="text-xs font-bold uppercase tracking-wider text-cyan-400">Decision Engine</span>
                <h2 class="text-3xl font-extrabold text-white">Can't decide between flagships?</h2>
                <p class="text-sm text-slate-300 max-w-2xl leading-relaxed">
                    Compare technical specifications side-by-side with automatic difference highlighting. Analyze processors, RAM benchmarks, camera sensors, and battery charging capabilities.
                </p>
                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="<?php echo BASE_URL; ?>/compare.php?ids=1,2" class="px-5 py-2.5 bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold rounded-xl text-xs transition flex items-center gap-2">
                        <i class="fa-solid fa-bolt"></i> Compare iPhone 15 Pro Max vs Galaxy S24 Ultra
                    </a>
                    <a href="<?php echo BASE_URL; ?>/compare.php?ids=5,6,7" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-xl text-xs border border-slate-700 transition flex items-center gap-2">
                        <i class="fa-solid fa-laptop"></i> Compare M3 Max vs Dell XPS 15 vs ROG G14
                    </a>
                </div>
            </div>
            <div class="lg:col-span-4 flex justify-center">
                <div class="p-6 rounded-2xl bg-slate-800/60 border border-slate-700 text-center w-full max-w-sm">
                    <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 text-cyan-400 mx-auto flex items-center justify-center text-2xl mb-4 border border-cyan-500/20">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <h4 class="font-bold text-white text-base mb-1">Up to 4 Devices Side-by-Side</h4>
                    <p class="text-xs text-slate-400 mb-4">Select any electronics from our catalog to compare full hardware specs instantly.</p>
                    <a href="<?php echo BASE_URL; ?>/products.php" class="inline-block text-xs text-cyan-400 font-semibold hover:underline">
                        Pick Gadgets to Compare <i class="fa-solid fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Warranty & RMA Customer Hub Teaser -->
<section class="py-14 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- Card 1: Warranty Lookup -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50/40 border border-blue-200/70 rounded-3xl p-8 space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-xl shadow-md shadow-blue-500/20">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900">Warranty Registration & Lookup</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Check your official warranty status, expiry date, or register hardware purchased through partner channels with your serial number.
                </p>
                <form action="<?php echo BASE_URL; ?>/warranty.php" method="GET" class="flex gap-2 pt-2">
                    <input type="text" name="query" placeholder="Enter Serial # (e.g. SN-IP15PM-8831092)" class="flex-1 bg-white border border-blue-200 rounded-xl px-4 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-semibold transition">
                        Verify
                    </button>
                </form>
                <div class="text-[11px] text-slate-400">
                    Try demo serial: <span class="font-mono text-blue-600 font-semibold cursor-pointer" onclick="document.querySelector('input[name=query]').value='SN-IP15PM-8831092'">SN-IP15PM-8831092</span>
                </div>
            </div>

            <!-- Card 2: Return & Replacement RMA -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50/40 border border-emerald-200/70 rounded-3xl p-8 space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center text-xl shadow-md shadow-emerald-500/20">
                    <i class="fa-solid fa-arrow-rotate-left"></i>
                </div>
                <h3 class="text-2xl font-bold text-slate-900">Hassle-Free Returns & RMA</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Experiencing a hardware issue, transit damage, or defect? Initiate a replacement or refund with our transparent 4-stage RMA tracking system.
                </p>
                <div class="flex flex-wrap gap-3 pt-2">
                    <a href="<?php echo BASE_URL; ?>/returns.php" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs transition shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-plus-circle"></i> File Replacement / Return
                    </a>
                    <a href="<?php echo BASE_URL; ?>/returns.php?rma=RMA-2026-0041" class="px-4 py-2.5 bg-white border border-emerald-200 text-emerald-800 hover:bg-emerald-100/50 font-semibold rounded-xl text-xs transition">
                        Track RMA-2026-0041
                    </a>
                </div>
                <div class="text-[11px] text-slate-400">
                    Track live status from initial approval to technical inspection and replacement dispatch.
                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
