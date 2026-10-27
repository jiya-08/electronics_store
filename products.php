<?php
$pageTitle = "Product Catalog & Specifications";
require_once __DIR__ . '/includes/header.php';

$pdo = get_db_connection();

// Categories and Brands for Sidebar
$categories = $pdo->query("SELECT id, name, slug, (SELECT COUNT(*) FROM products WHERE category_id = categories.id) as count FROM categories ORDER BY name ASC")->fetchAll();
$brands = $pdo->query("SELECT DISTINCT brand, COUNT(*) as count FROM products GROUP BY brand ORDER BY brand ASC")->fetchAll();

// Get active filters from URL
$currentCat = $_GET['category'] ?? '';
$currentBrand = $_GET['brand'] ?? '';
$currentSort = $_GET['sort'] ?? 'featured';
$currentSearch = $_GET['search'] ?? '';
$minPrice = $_GET['min_price'] ?? '';
$maxPrice = $_GET['max_price'] ?? '';
?>

<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="<?php echo BASE_URL; ?>/index.php" class="hover:text-blue-600">Home</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-800 font-medium">Electronics Catalog</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900">Electronics, Gadgets & Hardware</h1>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <a href="<?php echo BASE_URL; ?>/compare.php" class="px-4 py-2 bg-white border border-slate-300 hover:border-blue-500 rounded-xl text-xs font-semibold text-slate-700 transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-code-compare text-blue-600"></i> Open Comparison Matrix
            </a>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Filter Sidebar -->
        <aside class="lg:col-span-1 space-y-6">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                
                <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-filter text-blue-600"></i> Filters
                    </h3>
                    <a href="<?php echo BASE_URL; ?>/products.php" class="text-xs text-blue-600 hover:underline">Reset All</a>
                </div>

                <!-- Category Filter -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2.5">Category</h4>
                    <div class="space-y-1.5">
                        <a href="<?php echo BASE_URL; ?>/products.php<?php echo $currentBrand ? '?brand='.urlencode($currentBrand) : ''; ?>" class="flex justify-between items-center text-xs py-1 px-2 rounded-lg transition <?php echo empty($currentCat) ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                            <span>All Categories</span>
                        </a>
                        <?php foreach ($categories as $cat): ?>
                        <a href="<?php echo BASE_URL; ?>/products.php?category=<?php echo urlencode($cat['slug']); ?><?php echo $currentBrand ? '&brand='.urlencode($currentBrand) : ''; ?>" class="flex justify-between items-center text-xs py-1 px-2 rounded-lg transition <?php echo $currentCat === $cat['slug'] ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                            <span><?php echo htmlspecialchars($cat['name']); ?></span>
                            <span class="text-[11px] text-slate-400 font-mono">(<?php echo $cat['count']; ?>)</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Brand Filter -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2.5">Brand</h4>
                    <div class="space-y-1 max-h-48 overflow-y-auto pr-1">
                        <?php foreach ($brands as $b): ?>
                        <a href="<?php echo BASE_URL; ?>/products.php?brand=<?php echo urlencode($b['brand']); ?><?php echo $currentCat ? '&category='.urlencode($currentCat) : ''; ?>" class="flex justify-between items-center text-xs py-1 px-2 rounded-lg transition <?php echo $currentBrand === $b['brand'] ? 'bg-blue-50 text-blue-600 font-bold' : 'text-slate-600 hover:bg-slate-50'; ?>">
                            <span><?php echo htmlspecialchars($b['brand']); ?></span>
                            <span class="text-[11px] text-slate-400 font-mono">(<?php echo $b['count']; ?>)</span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Price Filter Form -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2.5">Price Range ($)</h4>
                    <form action="<?php echo BASE_URL; ?>/products.php" method="GET" class="space-y-2">
                        <?php if ($currentCat): ?><input type="hidden" name="category" value="<?php echo htmlspecialchars($currentCat); ?>"><?php endif; ?>
                        <?php if ($currentBrand): ?><input type="hidden" name="brand" value="<?php echo htmlspecialchars($currentBrand); ?>"><?php endif; ?>
                        <div class="flex items-center gap-2">
                            <input type="number" name="min_price" placeholder="Min" value="<?php echo htmlspecialchars($minPrice); ?>" class="w-1/2 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs">
                            <span class="text-slate-400">-</span>
                            <input type="number" name="max_price" placeholder="Max" value="<?php echo htmlspecialchars($maxPrice); ?>" class="w-1/2 bg-slate-50 border border-slate-200 rounded-lg px-2.5 py-1.5 text-xs">
                        </div>
                        <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white rounded-lg py-1.5 text-xs font-semibold transition">
                            Apply Price
                        </button>
                    </form>
                </div>

                <!-- Serial Tracking & Guarantee Box -->
                <div class="p-3 bg-blue-50/70 border border-blue-200 rounded-xl space-y-1.5 text-xs text-blue-900">
                    <div class="font-bold flex items-center gap-1.5">
                        <i class="fa-solid fa-barcode text-blue-600"></i> Serial-Tracked Units
                    </div>
                    <p class="text-[11px] text-blue-700 leading-normal">
                        Every phone, laptop, and audio device receives an individual hardware serial number upon dispatch.
                    </p>
                </div>

            </div>
        </aside>

        <!-- Product Listing Grid Area -->
        <main class="lg:col-span-3 space-y-6">
            
            <!-- Controls bar: Search, Sort & Results count -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="text-xs text-slate-500" id="resultsCount">
                    Loading gadgets...
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <!-- Sorting Dropdown -->
                    <label class="text-xs text-slate-500 whitespace-nowrap">Sort by:</label>
                    <select id="sortSelect" onchange="applySort(this.value)" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="featured" <?php echo $currentSort === 'featured' ? 'selected' : ''; ?>>Featured First</option>
                        <option value="price_asc" <?php echo $currentSort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo $currentSort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="rating" <?php echo $currentSort === 'rating' ? 'selected' : ''; ?>>Highest Rated</option>
                        <option value="newest" <?php echo $currentSort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    </select>
                </div>
            </div>

            <!-- Products Grid Container -->
            <div id="productGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                <!-- Dynamically loaded via JavaScript -->
            </div>

            <!-- Empty State Container -->
            <div id="emptyState" class="hidden p-12 bg-white rounded-2xl border border-slate-200 text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-2xl">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">No electronics match your filters</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto">Try clearing selected filters or searching for another term like iPhone, M3, Dell or Sony.</p>
                <a href="<?php echo BASE_URL; ?>/products.php" class="inline-block px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-semibold hover:bg-blue-700 transition">
                    Clear Filters
                </a>
            </div>

        </main>

    </div>
</div>

<script>
const activeCategory = <?php echo json_encode($currentCat); ?>;
const activeBrand = <?php echo json_encode($currentBrand); ?>;
const activeSort = <?php echo json_encode($currentSort); ?>;
const activeSearch = <?php echo json_encode($currentSearch); ?>;
const minPriceVal = <?php echo json_encode($minPrice); ?>;
const maxPriceVal = <?php echo json_encode($maxPrice); ?>;

async function loadProducts() {
    const grid = document.getElementById('productGrid');
    const empty = document.getElementById('emptyState');
    const countLabel = document.getElementById('resultsCount');

    grid.innerHTML = '<div class="col-span-3 text-center py-12 text-slate-400 text-sm"><i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Loading tech catalog...</div>';

    const params = new URLSearchParams();
    if (activeCategory) params.append('category', activeCategory);
    if (activeBrand) params.append('brand', activeBrand);
    if (activeSort) params.append('sort', activeSort);
    if (activeSearch) params.append('search', activeSearch);
    if (minPriceVal) params.append('min_price', minPriceVal);
    if (maxPriceVal) params.append('max_price', maxPriceVal);

    try {
        const res = await fetch(`/electronics_store/api/products.php?${params.toString()}`);
        const data = await res.json();

        if (data.success && data.data && data.data.products) {
            const products = data.data.products;
            countLabel.textContent = `Showing ${products.length} of ${data.data.total} electronics`;

            if (products.length === 0) {
                grid.innerHTML = '';
                empty.classList.remove('hidden');
                return;
            }

            empty.classList.add('hidden');
            grid.innerHTML = products.map(p => {
                const isLow = p.is_low_stock == 1;
                const isOut = parseInt(p.stock_quantity) <= 0;
                const effectivePrice = p.sale_price ? parseFloat(p.sale_price) : parseFloat(p.price);

                return `
                    <div class="tech-card overflow-hidden flex flex-col justify-between group">
                        <div>
                            <!-- Image Frame -->
                            <div class="relative h-52 bg-white overflow-hidden p-6 flex items-center justify-center border-b border-slate-100">
                                <img src="${p.image_url}" alt="${p.name}" class="max-h-full max-w-full object-contain group-hover:scale-105 transition duration-300" onerror="this.src='https://placehold.co/400x300?text=Gadget'">

                                <div class="absolute top-3 left-3 flex flex-col gap-1.5">
                                    <span class="bg-slate-900/80 backdrop-blur-sm text-white px-2.5 py-0.5 rounded text-[10px] font-semibold">
                                        ${p.brand}
                                    </span>
                                    ${p.has_serial_tracking == 1 ? `
                                        <span class="badge-serial px-2 py-0.5 rounded text-[10px] font-semibold flex items-center gap-1">
                                            <i class="fa-solid fa-barcode text-[9px]"></i> Serial
                                        </span>
                                    ` : ''}
                                </div>

                                <div class="absolute top-3 right-3">
                                    ${isOut ? `
                                        <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded">Out of Stock</span>
                                    ` : (isLow ? `
                                        <span class="badge-low-stock px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1">
                                            <i class="fa-solid fa-fire text-red-500"></i> ${p.stock_quantity} Left
                                        </span>
                                    ` : `
                                        <span class="badge-in-stock px-2 py-0.5 rounded text-[10px] font-semibold flex items-center gap-1">
                                            <i class="fa-solid fa-check text-emerald-600"></i> In Stock (${p.stock_quantity})
                                        </span>
                                    `)}
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="p-4 space-y-2">
                                <div class="flex items-center justify-between text-[11px] text-slate-400">
                                    <span>${p.category_name}</span>
                                    <span class="flex items-center gap-1 text-amber-500 font-semibold">
                                        <i class="fa-solid fa-star text-[10px]"></i> ${p.rating}
                                    </span>
                                </div>

                                <a href="/electronics_store/product-detail.php?id=${p.id}" class="block group-hover:text-blue-600 transition">
                                    <h3 class="font-bold text-slate-900 text-sm leading-snug line-clamp-1">${p.name}</h3>
                                </a>

                                <p class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                                    ${p.description}
                                </p>

                                <div class="text-[10px] text-slate-400 flex items-center gap-1 font-mono pt-1">
                                    <i class="fa-solid fa-shield text-blue-500"></i> ${p.warranty_months}-Mo Warranty
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="p-4 pt-0 mt-2">
                            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                                <div>
                                    <span class="text-base font-extrabold text-slate-900">$${effectivePrice.toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                                    ${p.sale_price ? `<span class="text-[11px] text-slate-400 line-through ml-1">$${parseFloat(p.price).toFixed(2)}</span>` : ''}
                                </div>

                                <div class="flex items-center gap-1.5">
                                    <button onclick='App.toggleCompare(${JSON.stringify({id: p.id, name: p.name, image_url: p.image_url, price: effectivePrice, brand: p.brand})})' class="p-2 rounded-xl border border-slate-200 hover:border-cyan-500 hover:text-cyan-600 text-slate-600 bg-white transition shadow-sm" title="Add to Compare">
                                        <i class="fa-solid fa-code-compare text-xs"></i>
                                    </button>

                                    <button onclick="App.addToCart(${p.id}, 1)" class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs transition shadow-sm flex items-center gap-1" ${isOut ? 'disabled' : ''}>
                                        <i class="fa-solid fa-cart-shopping text-xs"></i> Add
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }
    } catch (e) {
        console.error("Error loading products:", e);
        grid.innerHTML = '<div class="col-span-3 text-center text-red-500 text-xs py-8">Failed to load electronics. Please refresh.</div>';
    }
}

function applySort(val) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', val);
    window.location.href = url.toString();
}

document.addEventListener('DOMContentLoaded', loadProducts);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
