<?php
$pageTitle = "Gadget Specifications Comparison Matrix";
require_once __DIR__ . '/includes/header.php';

$pdo = get_db_connection();

// Get query param ids if provided
$idsParam = $_GET['ids'] ?? '';
$allProducts = $pdo->query("SELECT id, name, brand, model, price, sale_price, image_url FROM products ORDER BY brand ASC, name ASC")->fetchAll();
?>

<div class="bg-slate-900 text-white py-8 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                    <a href="<?php echo BASE_URL; ?>/index.php" class="hover:text-blue-400">Home</a>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <span class="text-slate-200">Specification Comparison</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Side-by-Side Gadget Comparison</h1>
                <p class="text-xs text-slate-400 mt-1">Compare up to 4 electronics with highlighted technical differences.</p>
            </div>

            <!-- Difference Highlighter Toggle & Add Device Button -->
            <div class="flex flex-wrap items-center gap-3">
                <button id="toggleDiffBtn" onclick="toggleDifferenceHighlight()" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-cyan-300 border border-slate-700 transition flex items-center gap-2">
                    <i class="fa-solid fa-highlighter"></i>
                    <span id="diffToggleLabel">Highlight Differences: ON</span>
                </button>

                <button onclick="openAddGadgetModal()" class="px-4 py-2 rounded-xl text-xs font-semibold bg-blue-600 hover:bg-blue-500 text-white shadow-md shadow-blue-500/20 transition flex items-center gap-1.5">
                    <i class="fa-solid fa-plus"></i> Add Device
                </button>
            </div>
        </div>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Quick Preset Comparisons if user has no selection -->
    <div id="presetComparisons" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-3">Popular Comparisons</h4>
        <div class="flex flex-wrap gap-2.5">
            <a href="<?php echo BASE_URL; ?>/compare.php?ids=1,2" class="px-3 py-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 rounded-lg text-xs font-medium border border-slate-200 transition flex items-center gap-1.5">
                <i class="fa-solid fa-mobile-screen text-blue-500"></i> iPhone 15 Pro Max vs Galaxy S24 Ultra
            </a>
            <a href="<?php echo BASE_URL; ?>/compare.php?ids=1,2,3" class="px-3 py-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 rounded-lg text-xs font-medium border border-slate-200 transition flex items-center gap-1.5">
                <i class="fa-solid fa-mobile-screen text-blue-500"></i> Apple vs Samsung vs Google Pixel 8 Pro
            </a>
            <a href="<?php echo BASE_URL; ?>/compare.php?ids=5,6,7" class="px-3 py-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 rounded-lg text-xs font-medium border border-slate-200 transition flex items-center gap-1.5">
                <i class="fa-solid fa-laptop text-amber-500"></i> MacBook Pro M3 Max vs Dell XPS 15 vs ASUS ROG G14
            </a>
            <a href="<?php echo BASE_URL; ?>/compare.php?ids=9,10" class="px-3 py-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 rounded-lg text-xs font-medium border border-slate-200 transition flex items-center gap-1.5">
                <i class="fa-solid fa-headphones text-purple-500"></i> Sony WH-1000XM5 vs Apple AirPods Pro 2
            </a>
        </div>
    </div>

    <!-- Comparison Table Container -->
    <div id="comparisonContainer" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden highlight-diff">
        <div class="p-8 text-center text-slate-400 text-sm" id="loadingState">
            <i class="fa-solid fa-circle-notch fa-spin mr-2"></i> Generating specification matrix...
        </div>

        <div id="matrixContent" class="hidden overflow-x-auto">
            <!-- Dynamically populated comparison table -->
        </div>
    </div>

    <!-- Empty State for Comparison -->
    <div id="emptyCompareState" class="hidden bg-white p-12 rounded-3xl border border-slate-200 text-center space-y-4">
        <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 mx-auto flex items-center justify-center text-2xl">
            <i class="fa-solid fa-code-compare"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800">No electronics selected for comparison</h3>
        <p class="text-xs text-slate-500 max-w-md mx-auto">
            Choose 2 to 4 products to evaluate detailed technical specifications side by side.
        </p>
        <div class="pt-2">
            <button onclick="openAddGadgetModal()" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition shadow-sm">
                Pick Gadgets to Compare
            </button>
        </div>
    </div>

</main>

<!-- Add Gadget Modal -->
<div id="addGadgetModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-4 animate-in fade-in zoom-in-95 duration-200">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-plus text-blue-600"></i> Add Gadget to Matrix
            </h3>
            <button onclick="closeAddGadgetModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="relative">
            <input type="text" id="modalSearchInput" oninput="filterModalGadgets(this.value)" placeholder="Search phone, laptop, model..." class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-4 py-2 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
        </div>

        <div id="modalGadgetList" class="max-h-72 overflow-y-auto space-y-2 pr-1">
            <?php foreach ($allProducts as $p): 
                $pEffective = floatval($p['sale_price'] ?? $p['price']);
            ?>
            <div class="modal-gadget-item flex items-center justify-between p-2.5 hover:bg-slate-50 rounded-xl border border-slate-100 transition" data-name="<?php echo strtolower($p['name'] . ' ' . $p['brand'] . ' ' . $p['model']); ?>">
                <div class="flex items-center gap-3">
                    <img src="<?php echo htmlspecialchars($p['image_url']); ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="w-10 h-10 object-contain rounded-lg bg-slate-100 p-1">
                    <div>
                        <div class="text-xs font-bold text-slate-800"><?php echo htmlspecialchars($p['name']); ?></div>
                        <div class="text-[11px] text-slate-400"><?php echo htmlspecialchars($p['brand']); ?> • $<?php echo number_format($pEffective, 2); ?></div>
                    </div>
                </div>
                <button onclick="addGadgetToMatrix(<?php echo $p['id']; ?>)" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg text-xs font-semibold transition">
                    Select
                </button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
let currentProductIds = [];
let highlightDiff = true;

function initComparison() {
    const urlParams = new URLSearchParams(window.location.search);
    const idsFromUrl = urlParams.get('ids');

    if (idsFromUrl) {
        currentProductIds = idsFromUrl.split(',').map(s => parseInt(s)).filter(n => !isNaN(n));
    } else {
        // Fallback to localStorage comparison tray if available
        const localList = App.getCompareList();
        if (localList && localList.length > 0) {
            currentProductIds = localList.map(item => parseInt(item.id));
        } else {
            // Default demo comparison: iPhone 15 Pro Max vs Galaxy S24 Ultra
            currentProductIds = [1, 2];
        }
    }

    renderMatrix();
}

async function renderMatrix() {
    const loading = document.getElementById('loadingState');
    const content = document.getElementById('matrixContent');
    const empty = document.getElementById('emptyCompareState');

    if (!currentProductIds || currentProductIds.length === 0) {
        loading.classList.add('hidden');
        content.classList.add('hidden');
        empty.classList.remove('hidden');
        return;
    }

    empty.classList.add('hidden');
    loading.classList.remove('hidden');
    content.classList.add('hidden');

    try {
        const res = await fetch(`${(window.APP_BASE_URL !== undefined ? window.APP_BASE_URL : (window.location.pathname.startsWith('/electronics_store') ? '/electronics_store' : ''))}/api/compare.php?ids=${currentProductIds.join(',')}`);
        const result = await res.json();

        if (!result.success || !result.data || !result.data.products) {
            throw new Error(result.error || "Failed to load comparison");
        }

        const data = result.data;
        const products = data.products;
        const matrix = data.matrix;

        // Build HTML Table
        let html = `
            <table class="w-full text-left text-xs border-collapse compare-table min-w-[700px]">
                <!-- Header: Product cards -->
                <thead>
                    <tr class="border-b border-slate-200">
                        <th class="p-4 w-48 bg-slate-50 text-slate-500 font-bold uppercase tracking-wider text-[11px] align-top">
                            Specification Group
                        </th>
                        ${products.map(p => `
                            <th class="p-4 bg-white align-top border-l border-slate-200">
                                <div class="flex flex-col items-center text-center space-y-2">
                                    <div class="relative w-full h-32 flex items-center justify-center p-2 bg-slate-50 rounded-2xl mb-2">
                                        <img src="${p.image_url}" alt="${p.name}" class="max-h-full max-w-full object-contain">
                                        <button onclick="removeGadgetFromMatrix(${p.id})" class="absolute top-2 right-2 text-slate-400 hover:text-red-500 bg-white shadow-sm p-1.5 rounded-lg transition" title="Remove Device">
                                            <i class="fa-solid fa-xmark text-xs"></i>
                                        </button>
                                    </div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">${p.brand}</span>
                                    <a href="/electronics_store/product-detail.php?id=${p.id}" class="font-bold text-slate-900 hover:text-blue-600 line-clamp-2 text-xs">
                                        ${p.name}
                                    </a>
                                    <div class="text-sm font-extrabold text-slate-900 pt-1">
                                        $${parseFloat(p.sale_price || p.price).toFixed(2)}
                                    </div>
                                    <div class="pt-2 w-full flex gap-1.5">
                                        <button onclick="App.addToCart(${p.id}, 1)" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white rounded-xl py-2 text-xs font-semibold shadow-sm transition">
                                            <i class="fa-solid fa-cart-shopping"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </th>
                        `).join('')}
                    </tr>
                </thead>
                <tbody>
        `;

        // Render each specification group
        matrix.forEach(group => {
            html += `
                <tr class="bg-slate-100/80 border-y border-slate-200 font-bold text-slate-800 text-xs">
                    <td colspan="${products.length + 1}" class="px-4 py-2.5 flex items-center gap-2">
                        <i class="fa-solid fa-layer-group text-blue-600"></i> ${group.group_name}
                    </td>
                </tr>
            `;

            group.specs.forEach(spec => {
                const isDiff = spec.is_different;
                const rowClass = isDiff ? 'compare-row-diff' : '';

                html += `
                    <tr class="border-b border-slate-100 hover:bg-slate-50/70 transition ${rowClass}">
                        <td class="p-3.5 font-semibold text-slate-600 bg-slate-50/50 border-r border-slate-200">
                            ${spec.spec_name}
                            ${isDiff ? '<span class="inline-block ml-1.5 px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 font-sans">Differs</span>' : ''}
                        </td>
                        ${products.map(p => {
                            const val = spec.values[p.id] || '—';
                            return `
                                <td class="p-3.5 border-l border-slate-100 font-medium text-slate-800 ${isDiff ? 'text-slate-950 font-semibold' : ''}">
                                    ${val}
                                </td>
                            `;
                        }).join('')}
                    </tr>
                `;
            });
        });

        html += `
                </tbody>
            </table>
        `;

        content.innerHTML = html;
        loading.classList.add('hidden');
        content.classList.remove('hidden');

        // Update URL query string
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('ids', currentProductIds.join(','));
        window.history.replaceState({}, '', newUrl.toString());

    } catch (err) {
        console.error("Comparison load error:", err);
        loading.innerHTML = `<div class="text-red-500 text-xs py-4">Error loading comparison: ${err.message}</div>`;
    }
}

function removeGadgetFromMatrix(id) {
    currentProductIds = currentProductIds.filter(pId => pId !== id);
    App.removeFromCompare(id);
    renderMatrix();
}

function addGadgetToMatrix(id) {
    if (currentProductIds.includes(id)) {
        App.showToast("This device is already in the comparison table", "info");
        closeAddGadgetModal();
        return;
    }

    if (currentProductIds.length >= 4) {
        App.showToast("Maximum of 4 gadgets can be compared at once", "warning");
        closeAddGadgetModal();
        return;
    }

    currentProductIds.push(id);
    closeAddGadgetModal();
    renderMatrix();
}

function toggleDifferenceHighlight() {
    const container = document.getElementById('comparisonContainer');
    const label = document.getElementById('diffToggleLabel');
    highlightDiff = !highlightDiff;

    if (highlightDiff) {
        container.classList.add('highlight-diff');
        label.textContent = "Highlight Differences: ON";
    } else {
        container.classList.remove('highlight-diff');
        label.textContent = "Highlight Differences: OFF";
    }
}

function openAddGadgetModal() {
    document.getElementById('addGadgetModal').classList.remove('hidden');
    document.getElementById('modalSearchInput').focus();
}

function closeAddGadgetModal() {
    document.getElementById('addGadgetModal').classList.add('hidden');
}

function filterModalGadgets(q) {
    const query = q.toLowerCase();
    const items = document.querySelectorAll('.modal-gadget-item');
    items.forEach(item => {
        const name = item.getAttribute('data-name');
        item.style.display = name.includes(query) ? 'flex' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', initComparison);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

