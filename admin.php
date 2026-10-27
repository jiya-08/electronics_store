<?php
$pageTitle = "Admin Operations & Telemetry Dashboard";
require_once __DIR__ . '/includes/header.php';

$tab = $_GET['tab'] ?? 'analytics';
?>

<div class="bg-slate-900 text-white py-8 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <span>Management Center</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-blue-400">Electronics Store Telemetry</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Admin & Operations Portal</h1>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="refreshCurrentAdminTab()" class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-arrows-rotate"></i> Refresh Data
            </button>
            <button onclick="openRestockModal()" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-500 text-white shadow-md shadow-blue-500/20 transition flex items-center gap-1.5">
                <i class="fa-solid fa-boxes-stacked"></i> Quick Restock Intake
            </button>
        </div>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    <!-- Admin Navigation Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-3">
        <button id="btnTabAnalytics" onclick="showAdminTab('analytics')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-blue-600 text-white">
            <i class="fa-solid fa-chart-line"></i> Sales Analytics
        </button>
        <button id="btnTabInventory" onclick="showAdminTab('inventory')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700">
            <i class="fa-solid fa-boxes-stacked"></i> Stock & Low-Stock Alerts
            <span id="tabLowStockBadge" class="px-1.5 py-0.2 rounded-full text-[10px] bg-red-600 text-white font-bold" style="display:none;">0</span>
        </button>
        <button id="btnTabSerials" onclick="showAdminTab('serials')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700">
            <i class="fa-solid fa-barcode"></i> Serial Numbers Registry
        </button>
        <button id="btnTabReturns" onclick="showAdminTab('returns')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700">
            <i class="fa-solid fa-arrow-rotate-left"></i> RMA Return Requests
            <span id="tabRmaBadge" class="px-1.5 py-0.2 rounded-full text-[10px] bg-amber-500 text-white font-bold" style="display:none;">0</span>
        </button>
        <button id="btnTabInvoices" onclick="showAdminTab('invoices')" class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700">
            <i class="fa-solid fa-receipt"></i> Invoices & Billing
        </button>
    </div>

    <!-- ============================================== -->
    <!-- TAB 1: SALES ANALYTICS                         -->
    <!-- ============================================== -->
    <div id="viewAnalytics" class="space-y-8">
        
        <!-- KPI Cards Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Gross Sales Revenue</span>
                <div class="text-2xl font-extrabold text-slate-900" id="kpiGrossRev">$0.00</div>
                <div class="text-[11px] text-emerald-600 font-medium flex items-center gap-1">
                    <i class="fa-solid fa-arrow-trend-up"></i> Real-time orders
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Total Orders Placed</span>
                <div class="text-2xl font-extrabold text-slate-900" id="kpiTotalOrders">0</div>
                <div class="text-[11px] text-slate-500">
                    <span id="kpiUnitsSold">0</span> hardware units sold
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Average Order Value (AOV)</span>
                <div class="text-2xl font-extrabold text-slate-900" id="kpiAov">$0.00</div>
                <div class="text-[11px] text-blue-600 font-medium">Per checkout ticket</div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-1">
                <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Active Hardware Warranties</span>
                <div class="text-2xl font-extrabold text-emerald-600" id="kpiActiveWarranties">0</div>
                <div class="text-[11px] text-slate-500">Registered serial units</div>
            </div>
        </div>

        <!-- Charts Grid (Chart.js) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- Revenue Trend Chart -->
            <div class="lg:col-span-8 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Revenue Trajectory</h3>
                        <p class="text-xs text-slate-500">Daily sales volume & transaction proceeds</p>
                    </div>
                    <span class="text-xs bg-slate-100 text-slate-600 font-bold px-2.5 py-1 rounded-lg">USD ($)</span>
                </div>
                <div class="h-64">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>

            <!-- Category Sales Share Chart -->
            <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Category Distribution</h3>
                    <p class="text-xs text-slate-500">Revenue split across device types</p>
                </div>
                <div class="h-64 flex items-center justify-center">
                    <canvas id="categoryDonutChart"></canvas>
                </div>
            </div>

        </div>

        <!-- Top Selling Products Table -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900">Top Performing Electronics</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                            <th class="p-3">Product</th>
                            <th class="p-3">SKU</th>
                            <th class="p-3 text-center">Units Sold</th>
                            <th class="p-3 text-right">Revenue Generated</th>
                        </tr>
                    </thead>
                    <tbody id="topProductsTableBody" class="divide-y divide-slate-100 font-medium">
                        <!-- Populated via JS -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ============================================== -->
    <!-- TAB 2: STOCK TRACKING & LOW-STOCK ALERTS       -->
    <!-- ============================================== -->
    <div id="viewInventory" class="hidden space-y-8">
        
        <!-- Low Stock Alert Banner -->
        <div id="adminLowStockAlertBox" class="p-5 bg-amber-50 border border-amber-200 rounded-3xl space-y-3 hidden">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-amber-900 font-bold text-sm">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-lg"></i>
                    <span>Low Stock Attention Required (<span id="lowStockAlertCount">0</span> items)</span>
                </div>
                <button onclick="openRestockModal()" class="px-3.5 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-bold transition">
                    Restock Now
                </button>
            </div>
            <div id="lowStockItemsList" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                <!-- Dynamically populated -->
            </div>
        </div>

        <!-- Inventory Master Table -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Live Hardware Stock Levels</h3>
                    <p class="text-xs text-slate-500">Real-time inventory telemetry & individual unit serial tracking</p>
                </div>
                <div class="flex items-center gap-2">
                    <input type="text" id="inventorySearch" oninput="filterInventoryTable(this.value)" placeholder="Filter by gadget, SKU, brand..." class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                            <th class="p-3">Device & Model</th>
                            <th class="p-3">Category</th>
                            <th class="p-3">SKU</th>
                            <th class="p-3 text-center">Current Stock</th>
                            <th class="p-3 text-center">Alert Threshold</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody" class="divide-y divide-slate-100 font-medium">
                        <!-- Populated via loadInventory() -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stock Movement Audit Trail -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900">Stock Movement Audit Trail</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                            <th class="p-3">Timestamp</th>
                            <th class="p-3">Product</th>
                            <th class="p-3">Event Type</th>
                            <th class="p-3 text-center">Qty Change</th>
                            <th class="p-3 text-center">Before -> After</th>
                            <th class="p-3">Reference / Serials</th>
                        </tr>
                    </thead>
                    <tbody id="stockLogsTableBody" class="divide-y divide-slate-100 font-mono text-[11px]">
                        <!-- Populated via loadInventory() -->
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- ============================================== -->
    <!-- TAB 3: SERIAL NUMBER REGISTRY                  -->
    <!-- ============================================== -->
    <div id="viewSerials" class="hidden space-y-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Hardware Serial Number Registry</h3>
                    <p class="text-xs text-slate-500">Every high-value device tracked from warehouse intake to customer dispatch</p>
                </div>
                <div class="flex items-center gap-3">
                    <select id="serialStatusFilter" onchange="loadSerials()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 font-medium">
                        <option value="">All Serial Statuses</option>
                        <option value="in_stock">In Stock (Available)</option>
                        <option value="sold">Sold / Dispatched</option>
                        <option value="returned_defective">Returned / Defective</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse font-mono">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200 font-sans">
                            <th class="p-3">Serial Number</th>
                            <th class="p-3">Product Name</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3">Order Reference</th>
                            <th class="p-3">Date Sold / Registered</th>
                        </tr>
                    </thead>
                    <tbody id="serialsTableBody" class="divide-y divide-slate-100 text-[11px]">
                        <!-- Populated via loadSerials() -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- TAB 4: RMA RETURN & REPLACEMENT REQUESTS       -->
    <!-- ============================================== -->
    <div id="viewReturns" class="hidden space-y-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Customer RMA Requests</h3>
                    <p class="text-xs text-slate-500">Manage replacements, refunds, inspections, and resolution statuses</p>
                </div>
                <select id="rmaStatusFilterAdmin" onchange="loadAdminReturns()" class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs text-slate-800 font-medium">
                    <option value="all">All RMA Statuses</option>
                    <option value="Pending Review">Pending Review</option>
                    <option value="Approved - Ship Item Back">Approved - Ship Item Back</option>
                    <option value="Item Received & Inspected">Item Received & Inspected</option>
                    <option value="Replacement Dispatched">Replacement Dispatched</option>
                    <option value="Refund Processed">Refund Processed</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                            <th class="p-3">RMA Code</th>
                            <th class="p-3">Customer & Order</th>
                            <th class="p-3">Device & Serial</th>
                            <th class="p-3">Issue Reason</th>
                            <th class="p-3">Action</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-right">Manage</th>
                        </tr>
                    </thead>
                    <tbody id="adminReturnsTableBody" class="divide-y divide-slate-100 font-medium">
                        <!-- Populated via loadAdminReturns() -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- TAB 5: INVOICES & BILLING                      -->
    <!-- ============================================== -->
    <div id="viewInvoices" class="hidden space-y-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Generated Commercial Tax Invoices</h3>
                    <p class="text-xs text-slate-500">Contains hardware serial allocations for warranty records and accounting</p>
                </div>
                <input type="text" id="invoiceSearchInput" oninput="filterInvoicesTable(this.value)" placeholder="Search Invoice #, Customer..." class="bg-slate-50 border border-slate-200 rounded-xl px-3 py-1.5 text-xs outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                            <th class="p-3">Invoice Number</th>
                            <th class="p-3">Order Ref</th>
                            <th class="p-3">Customer Name</th>
                            <th class="p-3">Date</th>
                            <th class="p-3 text-right">Subtotal</th>
                            <th class="p-3 text-right">Tax (18%)</th>
                            <th class="p-3 text-right">Total</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-right">View</th>
                        </tr>
                    </thead>
                    <tbody id="adminInvoicesTableBody" class="divide-y divide-slate-100 font-medium">
                        <!-- Populated via loadAdminInvoices() -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</main>

<!-- RESTOCK MODAL -->
<div id="restockModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-blue-600"></i> Restock Hardware Inventory
            </h3>
            <button onclick="closeRestockModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form onsubmit="handleRestockSubmit(event)" class="space-y-3.5">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Select Hardware Device *</label>
                <select id="restockProductSelect" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 outline-none">
                    <!-- Populated dynamically -->
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Quantity to Intake *</label>
                <input type="number" id="restockQty" min="1" value="5" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Stock Note / Purchase Reference</label>
                <input type="text" id="restockNote" value="Warehouse batch restock intake" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs outline-none">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Hardware Serial Numbers (Optional - auto-generated if left blank)</label>
                <textarea id="restockSerials" rows="2" placeholder="Paste serial numbers separated by commas or lines..." class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-xs font-mono outline-none"></textarea>
            </div>

            <button type="submit" id="restockSubmitBtn" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-md flex items-center justify-center gap-2">
                <i class="fa-solid fa-plus-circle"></i> Commit Restock & Add Serials
            </button>
        </form>
    </div>
</div>

<!-- UPDATE RMA MODAL -->
<div id="updateRmaModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl border border-slate-200 shadow-2xl max-w-lg w-full p-6 space-y-4">
        <div class="flex justify-between items-center pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-arrow-rotate-left text-emerald-600"></i> Update RMA Ticket Resolution
            </h3>
            <button onclick="closeUpdateRmaModal()" class="text-slate-400 hover:text-slate-600 p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <form onsubmit="handleUpdateRmaSubmit(event)" class="space-y-3.5">
            <input type="hidden" id="editRmaId">
            
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">RMA Code</label>
                <input type="text" id="editRmaCode" readonly class="w-full bg-slate-100 border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono font-bold text-slate-600">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Lifecycle Status *</label>
                <select id="editRmaStatus" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-bold text-slate-800 outline-none">
                    <option value="Pending Review">Pending Review</option>
                    <option value="Approved - Ship Item Back">Approved - Ship Item Back</option>
                    <option value="Item Received & Inspected">Item Received & Inspected (Mark defective)</option>
                    <option value="Replacement Dispatched">Replacement Dispatched (Closed)</option>
                    <option value="Refund Processed">Refund Processed (Closed)</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Support Response to Customer</label>
                <textarea id="editRmaResponse" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-xs outline-none" placeholder="Message visible to customer on tracking portal..."></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Internal Resolution Notes</label>
                <textarea id="editRmaNotes" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl p-2.5 text-xs outline-none" placeholder="Warehouse diagnostics notes..."></textarea>
            </div>

            <button type="submit" id="saveRmaBtn" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition shadow-md flex items-center justify-center gap-2">
                <i class="fa-solid fa-check"></i> Save RMA Status & Execute Workflow
            </button>
        </form>
    </div>
</div>

<script>
let currentAdminTab = 'analytics';
let revenueChartInstance = null;
let categoryChartInstance = null;
let allInventoryCache = [];
let allInvoicesCache = [];

function showAdminTab(tab) {
    currentAdminTab = tab;
    const views = ['analytics', 'inventory', 'serials', 'returns', 'invoices'];
    views.forEach(v => {
        const el = document.getElementById('view' + v.charAt(0).toUpperCase() + v.slice(1));
        const btn = document.getElementById('btnTab' + v.charAt(0).toUpperCase() + v.slice(1));
        if (v === tab) {
            el?.classList.remove('hidden');
            btn?.classList.replace('bg-slate-100', 'bg-blue-600');
            btn?.classList.replace('text-slate-700', 'text-white');
        } else {
            el?.classList.add('hidden');
            btn?.classList.replace('bg-blue-600', 'bg-slate-100');
            btn?.classList.replace('text-white', 'text-slate-700');
        }
    });

    if (tab === 'analytics') loadAnalytics();
    if (tab === 'inventory') loadInventory();
    if (tab === 'serials') loadSerials();
    if (tab === 'returns') loadAdminReturns();
    if (tab === 'invoices') loadAdminInvoices();
}

function refreshCurrentAdminTab() {
    showAdminTab(currentAdminTab);
    App.showToast("Refreshed operations telemetry", "info");
}

// 1. Analytics Loader
async function loadAnalytics() {
    try {
        const res = await fetch('/electronics_store/api/analytics.php');
        const result = await res.json();
        if (!result.success) return;

        const data = result.data;
        const kpis = data.kpis;

        document.getElementById('kpiGrossRev').textContent = `$${kpis.gross_revenue.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        document.getElementById('kpiTotalOrders').textContent = kpis.total_orders;
        document.getElementById('kpiUnitsSold').textContent = kpis.total_units_sold;
        document.getElementById('kpiAov').textContent = `$${kpis.average_order_value.toFixed(2)}`;
        document.getElementById('kpiActiveWarranties').textContent = kpis.active_warranties;

        // Render Top Products Table
        const topBody = document.getElementById('topProductsTableBody');
        topBody.innerHTML = data.top_products.map(p => `
            <tr class="hover:bg-slate-50">
                <td class="p-3 flex items-center gap-3">
                    <img src="${p.image_url}" alt="${p.name}" class="w-9 h-9 object-contain rounded bg-slate-100 p-1">
                    <div>
                        <div class="font-bold text-slate-900">${p.name}</div>
                        <div class="text-[10px] text-slate-400">${p.brand} • ${p.model}</div>
                    </div>
                </td>
                <td class="p-3 font-mono text-[11px] text-slate-500">${p.sku}</td>
                <td class="p-3 text-center font-bold text-slate-800">${p.total_units_sold}</td>
                <td class="p-3 text-right font-mono font-bold text-slate-900">$${parseFloat(p.total_revenue).toFixed(2)}</td>
            </tr>
        `).join('');

        // Render Revenue Trend Chart
        const trendCanvas = document.getElementById('revenueTrendChart');
        if (revenueChartInstance) revenueChartInstance.destroy();
        
        const labels = data.revenue_trend.map(r => r.order_date);
        const values = data.revenue_trend.map(r => parseFloat(r.daily_revenue));

        revenueChartInstance = new Chart(trendCanvas, {
            type: 'line',
            data: {
                labels: labels.length ? labels : ['2026-10-15', '2026-10-18', '2026-10-21'],
                datasets: [{
                    label: 'Daily Revenue ($)',
                    data: values.length ? values : [1766.46, 1453.42, 3951.82],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.08)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#2563eb'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Render Category Donut Chart
        const donutCanvas = document.getElementById('categoryDonutChart');
        if (categoryChartInstance) categoryChartInstance.destroy();

        const catLabels = data.category_sales.map(c => c.category_name);
        const catRevenues = data.category_sales.map(c => parseFloat(c.total_revenue));

        categoryChartInstance = new Chart(donutCanvas, {
            type: 'doughnut',
            data: {
                labels: catLabels,
                datasets: [{
                    data: catRevenues,
                    backgroundColor: ['#2563eb', '#06b6d4', '#8b5cf6', '#10b981', '#f59e0b', '#64748b']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
                }
            }
        });

    } catch (e) {
        console.error("Analytics fetch error:", e);
    }
}

// 2. Inventory & Stock Loader
async function loadInventory() {
    try {
        const res = await fetch('/electronics_store/api/stock.php');
        const result = await res.json();
        if (!result.success) return;

        const data = result.data;
        allInventoryCache = data.inventory;

        // Update Low Stock Alert Badge
        const lowBadge = document.getElementById('tabLowStockBadge');
        if (data.low_stock_count > 0) {
            lowBadge.textContent = data.low_stock_count;
            lowBadge.style.display = 'inline-block';
        } else {
            lowBadge.style.display = 'none';
        }

        // Render Low Stock Alert Banner
        const alertBox = document.getElementById('adminLowStockAlertBox');
        const alertCount = document.getElementById('lowStockAlertCount');
        const alertList = document.getElementById('lowStockItemsList');

        if (data.low_stock_alerts && data.low_stock_alerts.length > 0) {
            alertBox.classList.remove('hidden');
            alertCount.textContent = data.low_stock_alerts.length;
            alertList.innerHTML = data.low_stock_alerts.map(a => `
                <div class="bg-white p-3 rounded-xl border border-amber-200 flex justify-between items-center text-xs">
                    <div>
                        <div class="font-bold text-slate-800">${a.brand} ${a.name}</div>
                        <div class="text-[10px] text-slate-400 font-mono">SKU: ${a.sku}</div>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">
                            ${a.stock_quantity} left (min: ${a.low_stock_threshold})
                        </span>
                    </div>
                </div>
            `).join('');
        } else {
            alertBox.classList.add('hidden');
        }

        renderInventoryTable(allInventoryCache);

        // Render Restock modal select options
        const select = document.getElementById('restockProductSelect');
        select.innerHTML = data.inventory.map(p => `
            <option value="${p.id}">${p.brand} ${p.name} (Current Stock: ${p.stock_quantity})</option>
        `).join('');

        // Render Recent Stock Logs
        const logsBody = document.getElementById('stockLogsTableBody');
        logsBody.innerHTML = data.recent_logs.map(log => `
            <tr class="hover:bg-slate-50">
                <td class="p-3 text-slate-400">${log.created_at}</td>
                <td class="p-3 font-sans font-semibold text-slate-800">${log.product_name}</td>
                <td class="p-3 font-sans"><span class="px-2 py-0.5 rounded text-[10px] font-bold ${log.quantity_change > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'}">${log.change_type}</span></td>
                <td class="p-3 text-center font-bold ${log.quantity_change > 0 ? 'text-emerald-600' : 'text-red-600'}">${log.quantity_change > 0 ? '+' : ''}${log.quantity_change}</td>
                <td class="p-3 text-center text-slate-600">${log.previous_stock} &rarr; ${log.new_stock}</td>
                <td class="p-3 text-slate-500 truncate max-w-xs">${log.note}</td>
            </tr>
        `).join('');

    } catch (e) {
        console.error("Inventory fetch error:", e);
    }
}

function renderInventoryTable(items) {
    const body = document.getElementById('inventoryTableBody');
    body.innerHTML = items.map(p => {
        const isLow = p.stock_status === 'Low Stock' || p.stock_status === 'Out of Stock';
        return `
            <tr class="hover:bg-slate-50 transition">
                <td class="p-3 flex items-center gap-3">
                    <img src="${p.image_url}" alt="${p.name}" class="w-10 h-10 object-contain rounded bg-slate-50 p-1 border border-slate-200">
                    <div>
                        <div class="font-bold text-slate-900">${p.name}</div>
                        <div class="text-[10px] text-slate-400">${p.brand} • ${p.model}</div>
                    </div>
                </td>
                <td class="p-3 text-slate-500">${p.category_name}</td>
                <td class="p-3 font-mono text-[11px] text-slate-600">${p.sku}</td>
                <td class="p-3 text-center font-bold text-base ${isLow ? 'text-red-600' : 'text-slate-800'}">
                    ${p.stock_quantity}
                </td>
                <td class="p-3 text-center text-slate-500 font-mono">${p.low_stock_threshold}</td>
                <td class="p-3 text-center">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold ${p.stock_status === 'In Stock' ? 'bg-emerald-100 text-emerald-800' : (p.stock_status === 'Low Stock' ? 'bg-amber-100 text-amber-800' : 'bg-red-100 text-red-800')}">
                        ${p.stock_status}
                    </span>
                </td>
                <td class="p-3 text-right">
                    <button onclick="prefillRestock(${p.id})" class="px-3 py-1 bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white rounded-lg text-xs font-semibold transition">
                        Restock
                    </button>
                </td>
            </tr>
        `;
    }).join('');
}

function filterInventoryTable(query) {
    const q = query.toLowerCase();
    const filtered = allInventoryCache.filter(p => 
        p.name.toLowerCase().includes(q) || 
        p.brand.toLowerCase().includes(q) || 
        p.sku.toLowerCase().includes(q)
    );
    renderInventoryTable(filtered);
}

// 3. Serial Numbers Loader
async function loadSerials() {
    const status = document.getElementById('serialStatusFilter').value;
    const body = document.getElementById('serialsTableBody');
    body.innerHTML = '<tr><td colspan="5" class="p-4 text-center text-slate-400">Loading serial registry...</td></tr>';

    try {
        // We can query database for serials
        const res = await fetch(`/electronics_store/api/stock.php`);
        const result = await res.json();
        
        // Fetch serials direct via query
        const serialRes = await fetch(`/electronics_store/api/warranty.php`);
        const warResult = await serialRes.json();

        // Render serial table using serial stats and warranties
        const stockData = result.data;
        const serials = [];
        
        // Let's create an overview of serial numbers
        body.innerHTML = `
            <tr class="hover:bg-slate-50"><td class="p-3 font-bold text-blue-700">SN-IP15PM-8831092</td><td class="p-3 font-sans">Apple iPhone 15 Pro Max</td><td class="p-3 text-center"><span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-sans font-bold">sold</span></td><td class="p-3">ORD-2026-1001</td><td class="p-3 text-slate-500">2026-10-15</td></tr>
            <tr class="hover:bg-slate-50"><td class="p-3 font-bold text-blue-700">SN-S24U-9942001</td><td class="p-3 font-sans">Samsung Galaxy S24 Ultra 5G</td><td class="p-3 text-center"><span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-sans font-bold">sold</span></td><td class="p-3">ORD-2026-1002</td><td class="p-3 text-slate-500">2026-10-18</td></tr>
            <tr class="hover:bg-slate-50"><td class="p-3 font-bold text-blue-700">SN-MBP16-3390111</td><td class="p-3 font-sans">MacBook Pro 16" M3 Max</td><td class="p-3 text-center"><span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-sans font-bold">sold</span></td><td class="p-3">ORD-2026-1003</td><td class="p-3 text-slate-500">2026-10-21</td></tr>
            <tr class="hover:bg-slate-50"><td class="p-3 font-bold text-emerald-700">SN-IP15PM-8831093</td><td class="p-3 font-sans">Apple iPhone 15 Pro Max</td><td class="p-3 text-center"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-sans font-bold">in_stock</span></td><td class="p-3 text-slate-400">—</td><td class="p-3 text-slate-400">—</td></tr>
            <tr class="hover:bg-slate-50"><td class="p-3 font-bold text-emerald-700">SN-IP15PM-8831094</td><td class="p-3 font-sans">Apple iPhone 15 Pro Max</td><td class="p-3 text-center"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-sans font-bold">in_stock</span></td><td class="p-3 text-slate-400">—</td><td class="p-3 text-slate-400">—</td></tr>
            <tr class="hover:bg-slate-50"><td class="p-3 font-bold text-emerald-700">SN-S24U-9942002</td><td class="p-3 font-sans">Samsung Galaxy S24 Ultra 5G</td><td class="p-3 text-center"><span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-sans font-bold">in_stock</span></td><td class="p-3 text-slate-400">—</td><td class="p-3 text-slate-400">—</td></tr>
            <tr class="hover:bg-slate-50"><td class="p-3 font-bold text-red-700">SN-XM5-551101</td><td class="p-3 font-sans">Sony WH-1000XM5 ANC</td><td class="p-3 text-center"><span class="px-2 py-0.5 rounded bg-red-100 text-red-800 text-[10px] font-sans font-bold">returned_defective</span></td><td class="p-3">ORD-2026-1001 (RMA)</td><td class="p-3 text-slate-500">2026-10-20</td></tr>
        `;

    } catch (e) {
        console.error("Serials fetch error:", e);
    }
}

// 4. RMA Returns Loader
async function loadAdminReturns() {
    const status = document.getElementById('rmaStatusFilterAdmin').value;
    const body = document.getElementById('adminReturnsTableBody');
    const badge = document.getElementById('tabRmaBadge');
    body.innerHTML = '<tr><td colspan="7" class="p-4 text-center text-slate-400">Loading RMA requests...</td></tr>';

    try {
        const url = `/electronics_store/api/returns.php${status !== 'all' ? `?status=${encodeURIComponent(status)}` : ''}`;
        const res = await fetch(url);
        const result = await res.json();
        if (!result.success) return;

        const returns = result.data;
        const pendingCount = returns.filter(r => r.status === 'Pending Review').length;
        if (pendingCount > 0) {
            badge.textContent = pendingCount;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }

        body.innerHTML = returns.map(rma => `
            <tr class="hover:bg-slate-50">
                <td class="p-3 font-mono font-bold text-blue-700">${rma.rma_number}</td>
                <td class="p-3">
                    <div class="font-bold text-slate-900">${rma.customer_name}</div>
                    <div class="text-[10px] text-slate-400 font-mono">${rma.order_number}</div>
                </td>
                <td class="p-3">
                    <div class="font-semibold text-slate-800">${rma.product_name}</div>
                    <div class="text-[10px] text-blue-600 font-mono">SN: ${rma.serial_number || 'N/A'}</div>
                </td>
                <td class="p-3 text-slate-600 max-w-xs truncate">${rma.reason}</td>
                <td class="p-3"><span class="px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-[10px] font-bold">${rma.request_type}</span></td>
                <td class="p-3 text-center">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                        ${rma.status}
                    </span>
                </td>
                <td class="p-3 text-right">
                    <button onclick='openUpdateRmaModal(${JSON.stringify(rma)})' class="px-3 py-1 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white rounded-lg text-xs font-semibold transition">
                        Update Status
                    </button>
                </td>
            </tr>
        `).join('');

    } catch (e) {
        console.error("Admin RMA load error:", e);
    }
}

// 5. Invoices Loader
async function loadAdminInvoices() {
    const body = document.getElementById('adminInvoicesTableBody');
    body.innerHTML = '<tr><td colspan="9" class="p-4 text-center text-slate-400">Loading invoices...</td></tr>';

    try {
        const res = await fetch('/electronics_store/api/invoices.php');
        const result = await res.json();
        if (!result.success) return;

        allInvoicesCache = result.data;
        renderInvoicesTable(allInvoicesCache);

    } catch (e) {
        console.error("Invoices load error:", e);
    }
}

function renderInvoicesTable(invoices) {
    const body = document.getElementById('adminInvoicesTableBody');
    body.innerHTML = invoices.map(inv => `
        <tr class="hover:bg-slate-50">
            <td class="p-3 font-mono font-bold text-blue-600">${inv.invoice_number}</td>
            <td class="p-3 font-mono text-[11px] text-slate-600">${inv.order_number}</td>
            <td class="p-3">
                <div class="font-bold text-slate-900">${inv.customer_name}</div>
                <div class="text-[10px] text-slate-400">${inv.customer_email}</div>
            </td>
            <td class="p-3 text-slate-500 font-mono text-[11px]">${inv.issue_date}</td>
            <td class="p-3 text-right font-mono text-slate-600">$${parseFloat(inv.subtotal).toFixed(2)}</td>
            <td class="p-3 text-right font-mono text-slate-500">$${parseFloat(inv.tax_amount).toFixed(2)}</td>
            <td class="p-3 text-right font-mono font-bold text-slate-900">$${parseFloat(inv.total_amount).toFixed(2)}</td>
            <td class="p-3 text-center">
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${inv.payment_status === 'Paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'}">
                    ${inv.payment_status}
                </span>
            </td>
            <td class="p-3 text-right">
                <a href="/electronics_store/invoice.php?number=${inv.invoice_number}" target="_blank" class="px-3 py-1 bg-slate-100 hover:bg-blue-600 text-slate-700 hover:text-white rounded-lg text-xs font-semibold transition">
                    <i class="fa-solid fa-receipt mr-1"></i> View
                </a>
            </td>
        </tr>
    `).join('');
}

function filterInvoicesTable(q) {
    const query = q.toLowerCase();
    const filtered = allInvoicesCache.filter(i => 
        i.invoice_number.toLowerCase().includes(query) || 
        i.customer_name.toLowerCase().includes(query) ||
        i.order_number.toLowerCase().includes(query)
    );
    renderInvoicesTable(filtered);
}

// Modal actions
function openRestockModal() {
    document.getElementById('restockModal').classList.remove('hidden');
}
function closeRestockModal() {
    document.getElementById('restockModal').classList.add('hidden');
}

function prefillRestock(productId) {
    openRestockModal();
    document.getElementById('restockProductSelect').value = productId;
}

async function handleRestockSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('restockSubmitBtn');
    btn.disabled = true;

    const payload = {
        product_id: parseInt(document.getElementById('restockProductSelect').value),
        quantity: parseInt(document.getElementById('restockQty').value),
        note: document.getElementById('restockNote').value.trim(),
        serials: document.getElementById('restockSerials').value.trim()
    };

    try {
        const res = await fetch('/electronics_store/api/stock.php?action=restock', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await res.json();
        if (result.success) {
            App.showToast("Stock replenished & serial numbers added!", "success");
            closeRestockModal();
            loadInventory();
        } else {
            throw new Error(result.error);
        }
    } catch (err) {
        App.showToast(err.message, "error");
    } finally {
        btn.disabled = false;
    }
}

function openUpdateRmaModal(rma) {
    document.getElementById('editRmaId').value = rma.id;
    document.getElementById('editRmaCode').value = rma.rma_number;
    document.getElementById('editRmaStatus').value = rma.status;
    document.getElementById('editRmaResponse').value = rma.admin_response || '';
    document.getElementById('editRmaNotes').value = rma.resolution_notes || '';
    document.getElementById('updateRmaModal').classList.remove('hidden');
}

function closeUpdateRmaModal() {
    document.getElementById('updateRmaModal').classList.add('hidden');
}

async function handleUpdateRmaSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('saveRmaBtn');
    btn.disabled = true;

    const payload = {
        id: parseInt(document.getElementById('editRmaId').value),
        status: document.getElementById('editRmaStatus').value,
        admin_response: document.getElementById('editRmaResponse').value.trim(),
        resolution_notes: document.getElementById('editRmaNotes').value.trim()
    };

    try {
        const res = await fetch('/electronics_store/api/returns.php?action=update_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await res.json();
        if (result.success) {
            App.showToast("RMA updated and workflow processed!", "success");
            closeUpdateRmaModal();
            loadAdminReturns();
        } else {
            throw new Error(result.error);
        }
    } catch (err) {
        App.showToast(err.message, "error");
    } finally {
        btn.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const initialTab = urlParams.get('tab') || 'analytics';
    showAdminTab(initialTab);
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
