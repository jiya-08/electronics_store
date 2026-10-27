<?php
require_once __DIR__ . '/config/database.php';

$productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

$pdo = get_db_connection();

if ($productId > 0) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$productId]);
} else if (!empty($slug)) {
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.slug = ?");
    $stmt->execute([$slug]);
} else {
    header("Location: " . BASE_URL . "/products.php");
    exit;
}

$product = $stmt->fetch();

if (!$product) {
    header("Location: " . BASE_URL . "/products.php");
    exit;
}

// Fetch Grouped Specifications
$specStmt = $pdo->prepare("SELECT spec_group, spec_name, spec_value FROM product_specifications WHERE product_id = ? ORDER BY spec_group ASC, sort_order ASC, id ASC");
$specStmt->execute([$product['id']]);
$specs = $specStmt->fetchAll();

$groupedSpecs = [];
foreach ($specs as $spec) {
    $group = $spec['spec_group'] ?: 'General Specs';
    $groupedSpecs[$group][] = $spec;
}

$pageTitle = $product['name'] . " - Specifications & Warranty";
require_once __DIR__ . '/includes/header.php';

$isLowStock = ($product['stock_quantity'] <= $product['low_stock_threshold']);
$isOut = ($product['stock_quantity'] <= 0);
$effectivePrice = floatval($product['sale_price'] ?? $product['price']);
$regularPrice = floatval($product['price']);
$hasDiscount = !empty($product['sale_price']) && ($product['sale_price'] < $product['price']);
$discountPercent = $hasDiscount ? round((($regularPrice - $effectivePrice) / $regularPrice) * 100) : 0;
?>

<div class="bg-slate-100 py-4 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-xs text-slate-500">
            <a href="<?php echo BASE_URL; ?>/index.php" class="hover:text-blue-600">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?php echo BASE_URL; ?>/products.php" class="hover:text-blue-600">Products</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="<?php echo BASE_URL; ?>/products.php?category=<?php echo urlencode($product['category_slug']); ?>" class="hover:text-blue-600"><?php echo htmlspecialchars($product['category_name']); ?></a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-medium truncate max-w-xs"><?php echo htmlspecialchars($product['name']); ?></span>
        </div>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
    
    <!-- Hero Detail Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        
        <!-- Left Column: Gallery / Hero Visual -->
        <div class="lg:col-span-5 space-y-4">
            <div class="relative bg-slate-50 border border-slate-200 rounded-2xl p-8 h-96 flex items-center justify-center overflow-hidden">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="max-h-full max-w-full object-contain hover:scale-105 transition duration-300" onerror="this.src='https://placehold.co/600x500?text=Hardware'">
                
                <div class="absolute top-4 left-4 flex flex-col gap-2">
                    <span class="bg-slate-900 text-white font-bold text-xs px-3 py-1 rounded-lg">
                        <?php echo htmlspecialchars($product['brand']); ?>
                    </span>
                    <?php if ($hasDiscount): ?>
                    <span class="bg-red-600 text-white font-bold text-xs px-2.5 py-0.5 rounded-lg">
                        SAVE <?php echo $discountPercent; ?>%
                    </span>
                    <?php endif; ?>
                </div>

                <?php if ($product['has_serial_tracking']): ?>
                <div class="absolute bottom-4 left-4 bg-blue-50 border border-blue-200 text-blue-800 text-[11px] font-semibold px-2.5 py-1 rounded-lg flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-barcode text-blue-600"></i> Hardware Serial Number Tracked
                </div>
                <?php endif; ?>
            </div>

            <!-- Highlights Pill Grid -->
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                    <div class="text-slate-400 text-[10px] uppercase font-bold">Warranty</div>
                    <div class="font-bold text-slate-800 mt-0.5"><?php echo $product['warranty_months']; ?> Months</div>
                </div>
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                    <div class="text-slate-400 text-[10px] uppercase font-bold">Return Window</div>
                    <div class="font-bold text-slate-800 mt-0.5">30 Days RMA</div>
                </div>
                <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                    <div class="text-slate-400 text-[10px] uppercase font-bold">Shipping</div>
                    <div class="font-bold text-slate-800 mt-0.5">Express Dispatch</div>
                </div>
            </div>
        </div>

        <!-- Right Column: Details, Live Stock & Purchase Actions -->
        <div class="lg:col-span-7 flex flex-col justify-between space-y-6">
            <div class="space-y-4">
                
                <div class="flex items-center justify-between text-xs text-slate-500">
                    <span class="font-mono">SKU: <strong class="text-slate-700"><?php echo htmlspecialchars($product['sku']); ?></strong></span>
                    <span class="font-mono">Model: <strong class="text-slate-700"><?php echo htmlspecialchars($product['model']); ?></strong></span>
                </div>

                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 leading-tight">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>

                <!-- Rating & Reviews -->
                <div class="flex items-center gap-3 text-sm">
                    <div class="flex text-amber-400">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-stroke"></i>
                    </div>
                    <span class="font-bold text-slate-800"><?php echo $product['rating']; ?></span>
                    <span class="text-slate-400">/ 5.0</span>
                    <span class="text-slate-300">|</span>
                    <span class="text-slate-500"><?php echo $product['review_count']; ?> Verified Buyer Ratings</span>
                </div>

                <!-- Price Area -->
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between">
                    <div>
                        <div class="text-xs text-slate-400 font-medium">Standard Price:</div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-900">
                                $<?php echo number_format($effectivePrice, 2); ?>
                            </span>
                            <?php if ($hasDiscount): ?>
                                <span class="text-sm text-slate-400 line-through">
                                    $<?php echo number_format($regularPrice, 2); ?>
                                </span>
                            <?php endif; ?>
                            <span class="text-xs text-emerald-600 font-semibold ml-2">Taxes calculated at checkout</span>
                        </div>
                    </div>

                    <!-- Live Stock Status Badge -->
                    <div class="text-right">
                        <?php if ($isOut): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-100 text-red-700 font-bold text-xs">
                                <i class="fa-solid fa-circle-xmark"></i> Out of Stock
                            </span>
                        <?php elseif ($isLowStock): ?>
                            <div class="flex flex-col items-end">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-100 text-amber-800 font-bold text-xs border border-amber-200">
                                    <i class="fa-solid fa-triangle-exclamation text-amber-600"></i> Low Stock Alert
                                </span>
                                <span class="text-[11px] text-amber-700 font-medium mt-1">Only <?php echo $product['stock_quantity']; ?> units remaining!</span>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-col items-end">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-bold text-xs border border-emerald-200">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> In Stock
                                </span>
                                <span class="text-[11px] text-emerald-700 font-medium mt-1"><?php echo $product['stock_quantity']; ?> units ready for dispatch</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Description -->
                <p class="text-sm text-slate-600 leading-relaxed">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>

                <!-- Serial tracking notice -->
                <?php if ($product['has_serial_tracking']): ?>
                <div class="p-3.5 bg-blue-50/70 border border-blue-200 rounded-2xl flex items-start gap-3 text-xs text-blue-900">
                    <i class="fa-solid fa-barcode text-blue-600 text-lg mt-0.5"></i>
                    <div>
                        <strong class="font-bold">Hardware Serial Number Guarantee:</strong>
                        <p class="text-[11px] text-blue-700 mt-0.5 leading-normal">
                            This high-value device is allocated a unique manufacturer serial number at checkout. It will be printed on your official tax invoice and automatically activated for warranty coverage.
                        </p>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Action Buttons: Quantity, Add to Cart, Add to Compare -->
            <div class="pt-6 border-t border-slate-100 space-y-4">
                <div class="flex flex-wrap items-center gap-3">
                    
                    <!-- Quantity Input -->
                    <div class="flex items-center border border-slate-300 rounded-xl bg-white overflow-hidden">
                        <button type="button" onclick="adjustQty(-1)" class="px-3 py-2.5 text-slate-500 hover:bg-slate-100 transition">
                            <i class="fa-solid fa-minus text-xs"></i>
                        </button>
                        <input type="number" id="detailQty" value="1" min="1" max="<?php echo max(1, $product['stock_quantity']); ?>" class="w-12 text-center text-sm font-bold text-slate-800 focus:outline-none">
                        <button type="button" onclick="adjustQty(1)" class="px-3 py-2.5 text-slate-500 hover:bg-slate-100 transition">
                            <i class="fa-solid fa-plus text-xs"></i>
                        </button>
                    </div>

                    <!-- Add to Cart -->
                    <button onclick="handleAddToCart()" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl text-sm transition shadow-lg shadow-blue-500/20 flex items-center justify-center gap-2" <?php echo $isOut ? 'disabled' : ''; ?>>
                        <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                    </button>

                    <!-- Add to Compare -->
                    <button onclick='App.toggleCompare(<?php echo htmlspecialchars(json_encode([
                        'id' => (int)$product['id'],
                        'name' => $product['name'],
                        'image_url' => $product['image_url'],
                        'price' => $effectivePrice,
                        'brand' => $product['brand']
                    ])); ?>)' class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-sm transition flex items-center gap-2 border border-slate-200" title="Add to Comparison Matrix">
                        <i class="fa-solid fa-code-compare text-blue-600"></i> Compare
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- Technical Specifications Matrix Section -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-200 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Hardware Deep Dive</span>
                <h2 class="text-2xl font-extrabold text-slate-900 mt-0.5">Technical Specifications</h2>
            </div>
            <a href="<?php echo BASE_URL; ?>/compare.php?ids=<?php echo $product['id']; ?>" class="px-4 py-2 bg-slate-100 hover:bg-blue-50 text-blue-600 hover:text-blue-700 rounded-xl text-xs font-semibold border border-slate-200 transition flex items-center gap-2 self-start">
                <i class="fa-solid fa-code-compare"></i> Compare with Another Gadget
            </a>
        </div>

        <?php if (!empty($groupedSpecs)): ?>
            <div class="space-y-6">
                <?php foreach ($groupedSpecs as $groupName => $specList): ?>
                    <div class="border border-slate-200 rounded-2xl overflow-hidden">
                        <div class="bg-slate-100/80 px-4 py-2.5 font-bold text-xs uppercase tracking-wider text-slate-700 border-b border-slate-200 flex items-center gap-2">
                            <i class="fa-solid fa-microchip text-blue-600"></i> <?php echo htmlspecialchars($groupName); ?>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <?php foreach ($specList as $spec): ?>
                                <div class="grid grid-cols-1 sm:grid-cols-3 p-3 text-xs hover:bg-slate-50 transition">
                                    <div class="font-semibold text-slate-600 sm:col-span-1"><?php echo htmlspecialchars($spec['spec_name']); ?></div>
                                    <div class="text-slate-900 sm:col-span-2 font-mono sm:font-sans font-medium"><?php echo htmlspecialchars($spec['spec_value']); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="p-8 text-center text-slate-400 text-xs">
                No technical specifications recorded for this model yet.
            </div>
        <?php endif; ?>
    </div>

</main>

<script>
function adjustQty(delta) {
    const input = document.getElementById('detailQty');
    let val = parseInt(input.value) || 1;
    val += delta;
    if (val < 1) val = 1;
    const max = parseInt(input.getAttribute('max')) || 99;
    if (val > max) val = max;
    input.value = val;
}

function handleAddToCart() {
    const qty = parseInt(document.getElementById('detailQty').value) || 1;
    App.addToCart(<?php echo $product['id']; ?>, qty);
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
