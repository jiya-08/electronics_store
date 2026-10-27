<?php
$pageTitle = "Hardware Warranty Center & Serial Registration";
require_once __DIR__ . '/includes/header.php';

$initialQuery = $_GET['query'] ?? $_GET['serial'] ?? '';
$activeTab = $_GET['tab'] ?? 'lookup';

$pdo = get_db_connection();
$productsList = $pdo->query("SELECT id, name, brand, model, warranty_months FROM products WHERE has_serial_tracking = 1 ORDER BY brand ASC, name ASC")->fetchAll();
?>

<div class="bg-gradient-to-r from-slate-900 to-indigo-950 text-white py-12 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-400/30 text-cyan-300 text-xs font-semibold">
                <i class="fa-solid fa-shield-halved"></i> Official Manufacturer Protection Registry
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Hardware Warranty Hub</h1>
            <p class="text-sm text-slate-300 leading-relaxed">
                Check device coverage status by serial number, print digital certificates of authenticity, or register retail and partner purchases into the central hardware database.
            </p>
        </div>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">

    <!-- Tab Selector -->
    <div class="flex border-b border-slate-200">
        <button id="tabLookupBtn" onclick="switchTab('lookup')" class="px-6 py-3 text-sm font-bold border-b-2 border-blue-600 text-blue-600 transition flex items-center gap-2">
            <i class="fa-solid fa-magnifying-glass"></i> Check Warranty by Serial / Order
        </button>
        <button id="tabRegisterBtn" onclick="switchTab('register')" class="px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex items-center gap-2">
            <i class="fa-solid fa-file-signature"></i> Register Device Warranty
        </button>
    </div>

    <!-- TAB 1: WARRANTY LOOKUP -->
    <div id="tabLookup" class="space-y-8">
        
        <!-- Search Form Card -->
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm max-w-3xl mx-auto space-y-4">
            <div class="text-center space-y-1">
                <h3 class="text-xl font-bold text-slate-900">Verify Device Hardware Coverage</h3>
                <p class="text-xs text-slate-500">Enter your device Serial Number, Warranty Code, or Order ID</p>
            </div>

            <form onsubmit="handleWarrantySearch(event)" class="flex flex-col sm:flex-row gap-2 pt-2">
                <div class="relative flex-1">
                    <input type="text" id="warrantySearchInput" value="<?php echo htmlspecialchars($initialQuery); ?>" placeholder="e.g. SN-IP15PM-8831092 or WAR-2026-88101" class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-3 text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white" required>
                    <i class="fa-solid fa-barcode absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                </div>
                <button type="submit" id="searchWarrantyBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-shield-halved"></i> Verify Status
                </button>
            </form>

            <!-- Quick Demo Serial Tags -->
            <div class="flex flex-wrap items-center justify-center gap-2 text-[11px] text-slate-500 pt-2">
                <span>Try active serials:</span>
                <span class="font-mono text-blue-600 bg-blue-50 px-2 py-0.5 rounded cursor-pointer hover:bg-blue-100" onclick="quickFillSerial('SN-IP15PM-8831092')">SN-IP15PM-8831092 (iPhone 15 PM)</span>
                <span class="font-mono text-blue-600 bg-blue-50 px-2 py-0.5 rounded cursor-pointer hover:bg-blue-100" onclick="quickFillSerial('SN-S24U-9942001')">SN-S24U-9942001 (S24 Ultra)</span>
                <span class="font-mono text-blue-600 bg-blue-50 px-2 py-0.5 rounded cursor-pointer hover:bg-blue-100" onclick="quickFillSerial('SN-MBP16-3390111')">SN-MBP16-3390111 (MacBook Pro)</span>
            </div>
        </div>

        <!-- Warranty Results Container -->
        <div id="warrantyResultsArea" class="max-w-4xl mx-auto hidden space-y-6">
            <!-- Dynamically populated via JS -->
        </div>

    </div>

    <!-- TAB 2: REGISTER NEW DEVICE -->
    <div id="tabRegister" class="hidden max-w-2xl mx-auto">
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Self-Service Portal</span>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-0.5">Register Device Hardware</h3>
                <p class="text-xs text-slate-500 mt-1">Activate authorized manufacturer coverage for offline purchases.</p>
            </div>

            <form onsubmit="handleWarrantyRegistration(event)" class="space-y-4">
                
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Hardware Serial Number *</label>
                    <div class="relative">
                        <input type="text" id="regSerial" placeholder="e.g. SN-IP15PM-8831094" required class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-xs font-mono uppercase focus:ring-2 focus:ring-blue-500 outline-none">
                        <i class="fa-solid fa-barcode absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Select Hardware Model *</label>
                    <select id="regProductId" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="">-- Choose your gadget model --</option>
                        <?php foreach ($productsList as $p): ?>
                        <option value="<?php echo $p['id']; ?>">
                            <?php echo htmlspecialchars($p['brand'] . ' ' . $p['name'] . ' (' . $p['warranty_months'] . ' Months Warranty)'); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Owner Full Name *</label>
                        <input type="text" id="regName" placeholder="John Doe" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Owner Email *</label>
                        <input type="email" id="regEmail" placeholder="john@example.com" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number *</label>
                        <input type="text" id="regPhone" placeholder="+1 (555) 000-0000" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Date of Purchase *</label>
                        <input type="date" id="regDate" value="<?php echo date('Y-m-d'); ?>" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Coverage Plan</label>
                    <select id="regWarrantyType" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="Standard Manufacturer (1 Year)">Standard Official Manufacturer Warranty</option>
                        <option value="Extended Shield Plus (2 Years)">Extended Protection Shield (+1 Year Extra)</option>
                        <option value="Enterprise Workstation Care">Enterprise Commercial Protection</option>
                    </select>
                </div>

                <button type="submit" id="regSubmitBtn" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition shadow-md shadow-blue-500/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-file-circle-check"></i> Register & Issue Warranty Certificate
                </button>
            </form>
        </div>
    </div>

</main>

<script>
function switchTab(tab) {
    const tabLookup = document.getElementById('tabLookup');
    const tabRegister = document.getElementById('tabRegister');
    const tabLookupBtn = document.getElementById('tabLookupBtn');
    const tabRegisterBtn = document.getElementById('tabRegisterBtn');

    if (tab === 'lookup') {
        tabLookup.classList.remove('hidden');
        tabRegister.classList.add('hidden');
        tabLookupBtn.classList.add('border-blue-600', 'text-blue-600');
        tabLookupBtn.classList.remove('border-transparent', 'text-slate-500');
        tabRegisterBtn.classList.remove('border-blue-600', 'text-blue-600');
        tabRegisterBtn.classList.add('border-transparent', 'text-slate-500');
    } else {
        tabLookup.classList.add('hidden');
        tabRegister.classList.remove('hidden');
        tabRegisterBtn.classList.add('border-blue-600', 'text-blue-600');
        tabRegisterBtn.classList.remove('border-transparent', 'text-slate-500');
        tabLookupBtn.classList.remove('border-blue-600', 'text-blue-600');
        tabLookupBtn.classList.add('border-transparent', 'text-slate-500');
    }
}

function quickFillSerial(sn) {
    document.getElementById('warrantySearchInput').value = sn;
    handleWarrantySearch();
}

async function handleWarrantySearch(e) {
    if (e) e.preventDefault();
    const query = document.getElementById('warrantySearchInput').value.trim();
    if (!query) return;

    const btn = document.getElementById('searchWarrantyBtn');
    const container = document.getElementById('warrantyResultsArea');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Checking...';

    try {
        const res = await fetch(`${(window.APP_BASE_URL !== undefined ? window.APP_BASE_URL : (window.location.pathname.startsWith('/electronics_store') ? '/electronics_store' : ''))}/api/warranty.php?query=${encodeURIComponent(query)}`);
        const result = await res.json();

        container.classList.remove('hidden');

        if (!result.success) {
            container.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-3xl text-center space-y-2">
                    <i class="fa-solid fa-circle-xmark text-2xl text-red-500"></i>
                    <h4 class="font-bold text-sm">Warranty Record Not Found</h4>
                    <p class="text-xs text-red-600">${result.error}</p>
                </div>
            `;
            return;
        }

        const data = result.data;

        // If unregistered device found in warehouse
        if (data.can_register && data.unregistered_device) {
            const dev = data.unregistered_device;
            container.innerHTML = `
                <div class="bg-amber-50 border border-amber-200 p-6 rounded-3xl space-y-3">
                    <div class="flex items-center gap-2 text-amber-800 font-bold text-sm">
                        <i class="fa-solid fa-circle-info text-amber-600"></i> Genuine Hardware Found - Pending Registration
                    </div>
                    <p class="text-xs text-amber-700 leading-relaxed">
                        Serial Number <strong class="font-mono text-slate-900">${dev.serial_number}</strong> is an authentic unit of <strong>${dev.product_name}</strong>, but has not yet been assigned to a customer warranty profile.
                    </p>
                    <button onclick="prefillAndRegister('${dev.serial_number}', ${dev.product_id})" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold transition">
                        Register This Serial Now
                    </button>
                </div>
            `;
            return;
        }

        const warranties = Array.isArray(data) ? data : [data];

        container.innerHTML = warranties.map(w => {
            const isValid = w.is_valid;
            const days = w.days_remaining;

            return `
                <div class="bg-white rounded-3xl border ${isValid ? 'border-emerald-200 shadow-md shadow-emerald-500/5' : 'border-red-200'} p-6 sm:p-8 space-y-6">
                    
                    <!-- Header with Badge -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl ${isValid ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'} flex items-center justify-center text-xl">
                                <i class="fa-solid ${isValid ? 'fa-shield-check' : 'fa-triangle-exclamation'}"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider ${isValid ? 'text-emerald-600' : 'text-red-500'}">
                                    ${isValid ? 'Active Manufacturer Coverage' : 'Warranty Expired'}
                                </span>
                                <h3 class="text-lg font-extrabold text-slate-900 font-mono">${w.warranty_code}</h3>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold ${isValid ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-red-50 text-red-700 border border-red-200'}">
                                ${isValid ? `${days} Days Remaining` : 'Expired'}
                            </span>
                        </div>
                    </div>

                    <!-- Device & Hardware Details -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                        <div class="md:col-span-3 h-28 bg-slate-50 rounded-2xl p-2 flex items-center justify-center border border-slate-100">
                            <img src="${w.image_url}" alt="${w.product_name}" class="max-h-full max-w-full object-contain" onerror="this.src='https://placehold.co/200x200?text=Gadget'">
                        </div>

                        <div class="md:col-span-9 space-y-2 text-xs">
                            <h4 class="text-base font-bold text-slate-900">${w.product_name}</h4>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 pt-1">
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Serial Number</span>
                                    <span class="font-mono font-bold text-blue-700">${w.serial_number}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Purchase Date</span>
                                    <span class="font-medium text-slate-800">${w.purchase_date}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Expiry Date</span>
                                    <span class="font-medium text-slate-800">${w.expiry_date}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Registered Owner</span>
                                    <span class="font-medium text-slate-800">${w.customer_name}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Coverage Plan</span>
                                    <span class="font-medium text-slate-800">${w.warranty_type}</span>
                                </div>
                                <div>
                                    <span class="text-slate-400 block text-[10px] uppercase font-bold">Order Ref</span>
                                    <span class="font-mono text-slate-800">${w.order_number || 'Direct Channel'}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Coverage Progress Bar -->
                    <div class="space-y-1.5 pt-2">
                        <div class="flex justify-between text-[11px] font-semibold text-slate-500">
                            <span>Coverage Lifecycle</span>
                            <span>${w.coverage_percentage || 100}% Period Elapsed</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full ${isValid ? 'bg-emerald-500' : 'bg-red-500'}" style="width: ${Math.max(5, 100 - (w.coverage_percentage || 0))}%"></div>
                        </div>
                    </div>

                    <!-- Footer Actions -->
                    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-100 text-xs">
                        <span class="text-slate-400">
                            <i class="fa-solid fa-circle-check text-blue-500"></i> Authenticated in Central Telemetry Database
                        </span>
                        <div class="flex items-center gap-2">
                            <a href="/electronics_store/returns.php?serial=${w.serial_number}&order=${w.order_number || ''}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition">
                                <i class="fa-solid fa-arrow-rotate-left mr-1"></i> File RMA Claim
                            </a>
                            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition">
                                <i class="fa-solid fa-print mr-1"></i> Print Certificate
                            </button>
                        </div>
                    </div>

                </div>
            `;
        }).join('');

    } catch (err) {
        console.error("Warranty check error:", err);
        container.innerHTML = `<div class="p-6 text-center text-red-500 text-xs bg-white rounded-2xl border border-red-200">Error verifying warranty: ${err.message}</div>`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-shield-halved"></i> Verify Status';
    }
}

function prefillAndRegister(sn, pId) {
    switchTab('register');
    document.getElementById('regSerial').value = sn;
    document.getElementById('regProductId').value = pId;
    window.scrollTo({ top: 200, behavior: 'smooth' });
}

async function handleWarrantyRegistration(e) {
    e.preventDefault();
    const btn = document.getElementById('regSubmitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Registering Hardware...';

    const payload = {
        serial_number: document.getElementById('regSerial').value.trim(),
        product_id: parseInt(document.getElementById('regProductId').value),
        customer_name: document.getElementById('regName').value.trim(),
        customer_email: document.getElementById('regEmail').value.trim(),
        customer_phone: document.getElementById('regPhone').value.trim(),
        purchase_date: document.getElementById('regDate').value,
        warranty_type: document.getElementById('regWarrantyType').value
    };

    try {
        const res = await fetch('${(window.APP_BASE_URL !== undefined ? window.APP_BASE_URL : (window.location.pathname.startsWith('/electronics_store') ? '/electronics_store' : ''))}/api/warranty.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await res.json();

        if (result.success && result.data) {
            App.showToast("Device registered and official warranty issued!", "success");
            // Switch back to lookup and view newly registered warranty
            switchTab('lookup');
            document.getElementById('warrantySearchInput').value = result.data.warranty_code;
            handleWarrantySearch();
        } else {
            throw new Error(result.error || "Warranty registration failed");
        }
    } catch (err) {
        console.error("Registration error:", err);
        App.showToast(err.message, "error", "Registration Failed");
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-file-circle-check"></i> Register & Issue Warranty Certificate';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    if (params.get('query') || params.get('serial')) {
        handleWarrantySearch();
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

