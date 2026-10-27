<?php
$pageTitle = "Order Returns & Replacement (RMA) Portal";
require_once __DIR__ . '/includes/header.php';

$initialRma = $_GET['rma'] ?? '';
$initialOrder = $_GET['order'] ?? '';
$initialSerial = $_GET['serial'] ?? '';
?>

<div class="bg-gradient-to-r from-slate-900 to-slate-800 text-white py-12 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-400/30 text-emerald-300 text-xs font-semibold">
                <i class="fa-solid fa-arrow-rotate-left"></i> RMA Service & Exchange Hub
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Returns & Hardware Replacement</h1>
            <p class="text-sm text-slate-300 leading-relaxed">
                Experiencing a defect or shipping damage? Submit an RMA request for a quick unit replacement or refund, and monitor live progress across technical inspection and dispatch.
            </p>
        </div>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    <!-- Tab Navigation: Track Existing RMA vs Submit New Claim -->
    <div class="flex border-b border-slate-200">
        <button id="tabTrackBtn" onclick="switchRmaTab('track')" class="px-6 py-3 text-sm font-bold border-b-2 border-emerald-600 text-emerald-600 transition flex items-center gap-2">
            <i class="fa-solid fa-timeline"></i> Track RMA Ticket
        </button>
        <button id="tabSubmitBtn" onclick="switchRmaTab('submit')" class="px-6 py-3 text-sm font-bold border-b-2 border-transparent text-slate-500 hover:text-slate-800 transition flex items-center gap-2">
            <i class="fa-solid fa-plus-circle"></i> File Return / Replacement Request
        </button>
    </div>

    <!-- TAB 1: TRACK EXISTING RMA -->
    <div id="tabTrackArea" class="space-y-8">
        
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm max-w-2xl mx-auto space-y-4">
            <div class="text-center space-y-1">
                <h3 class="text-lg font-bold text-slate-900">Check Return / Replacement Progress</h3>
                <p class="text-xs text-slate-500">Enter your RMA Number (e.g. RMA-2026-0041) or Order Number</p>
            </div>

            <form onsubmit="handleTrackRma(event)" class="flex flex-col sm:flex-row gap-2 pt-2">
                <div class="relative flex-1">
                    <input type="text" id="trackRmaInput" value="<?php echo htmlspecialchars($initialRma ?: $initialOrder); ?>" placeholder="RMA-2026-0041 or ORD-2026-1001" class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2.5 text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white" required>
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                </div>
                <button type="submit" id="trackRmaBtn" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-2.5 rounded-xl text-xs sm:text-sm transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="fa-solid fa-location-crosshairs"></i> Track Status
                </button>
            </form>

            <div class="flex items-center justify-center gap-2 text-[11px] text-slate-500 pt-1">
                <span>Try active RMA:</span>
                <span class="font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded cursor-pointer hover:bg-emerald-100 font-bold" onclick="quickTrack('RMA-2026-0041')">RMA-2026-0041 (Sony Headphones)</span>
                <span class="font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded cursor-pointer hover:bg-emerald-100 font-bold" onclick="quickTrack('RMA-2026-0042')">RMA-2026-0042 (S24 Ultra)</span>
            </div>
        </div>

        <!-- RMA Detailed Status Display Container -->
        <div id="rmaDetailDisplay" class="max-w-4xl mx-auto hidden space-y-6">
            <!-- Dynamically populated via JS -->
        </div>

    </div>

    <!-- TAB 2: SUBMIT NEW RETURN / REPLACEMENT CLAIM -->
    <div id="tabSubmitArea" class="hidden max-w-2xl mx-auto">
        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Customer Support Service</span>
                <h3 class="text-2xl font-extrabold text-slate-900 mt-0.5">Submit Return / Replacement Request</h3>
                <p class="text-xs text-slate-500 mt-1">Our hardware logistics team reviews and authorizes RMA claims within 24 hours.</p>
            </div>

            <form onsubmit="handleSubmitRma(event)" class="space-y-4">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Order Number *</label>
                        <input type="text" id="rmaOrderNumber" value="<?php echo htmlspecialchars($initialOrder ?: 'ORD-2026-1001'); ?>" placeholder="ORD-2026-XXXX" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono uppercase focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Customer Email *</label>
                        <input type="email" id="rmaEmail" value="alex.vance@techcorp.io" placeholder="customer@example.com" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Hardware Serial Number</label>
                        <input type="text" id="rmaSerial" value="<?php echo htmlspecialchars($initialSerial ?: 'SN-IP15PM-8831092'); ?>" placeholder="e.g. SN-IP15PM-8831092" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-mono uppercase focus:ring-2 focus:ring-emerald-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Requested Action *</label>
                        <select id="rmaActionType" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none">
                            <option value="Replacement">Replacement (Dispatch New Unit)</option>
                            <option value="Refund">Refund (Credit Original Payment)</option>
                            <option value="Store Credit">Store Credit (Wallet Balance)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Primary Issue Reason *</label>
                    <select id="rmaReason" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500 outline-none">
                        <option value="Screen / Display / Audio static defect">Hardware Defect / Screen or Audio malfunction</option>
                        <option value="Damaged during shipping">Physical damage occurred during transit</option>
                        <option value="Device not powering on / Battery fault">Device won't power on / Battery draining excessively</option>
                        <option value="Wrong item delivered">Received wrong product or specification variant</option>
                        <option value="Performance issue / Overheating">Extreme overheating / CPU thermal throttling</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Detailed Defect Description *</label>
                    <textarea id="rmaDescription" rows="3" placeholder="Please describe exactly what happens, when the issue started, and any troubleshooting attempted..." required class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs focus:ring-2 focus:ring-emerald-500 outline-none">Earphone audio buzzes with static sound when ANC mode is toggled on.</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Photo / Proof URL (Optional)</label>
                    <input type="url" id="rmaPhotoUrl" placeholder="https://..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 outline-none">
                </div>

                <button type="submit" id="submitRmaBtn" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition shadow-md shadow-emerald-500/20 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Submit RMA Request & Generate Tracking Code
                </button>
            </form>
        </div>
    </div>

</main>

<script>
function switchRmaTab(tab) {
    const trackArea = document.getElementById('tabTrackArea');
    const submitArea = document.getElementById('tabSubmitArea');
    const trackBtn = document.getElementById('tabTrackBtn');
    const submitBtn = document.getElementById('tabSubmitBtn');

    if (tab === 'track') {
        trackArea.classList.remove('hidden');
        submitArea.classList.add('hidden');
        trackBtn.classList.add('border-emerald-600', 'text-emerald-600');
        trackBtn.classList.remove('border-transparent', 'text-slate-500');
        submitBtn.classList.remove('border-emerald-600', 'text-emerald-600');
        submitBtn.classList.add('border-transparent', 'text-slate-500');
    } else {
        trackArea.classList.add('hidden');
        submitArea.classList.remove('hidden');
        submitBtn.classList.add('border-emerald-600', 'text-emerald-600');
        submitBtn.classList.remove('border-transparent', 'text-slate-500');
        trackBtn.classList.remove('border-emerald-600', 'text-emerald-600');
        trackBtn.classList.add('border-transparent', 'text-slate-500');
    }
}

function quickTrack(rma) {
    document.getElementById('trackRmaInput').value = rma;
    handleTrackRma();
}

async function handleTrackRma(e) {
    if (e) e.preventDefault();
    const query = document.getElementById('trackRmaInput').value.trim();
    if (!query) return;

    const btn = document.getElementById('trackRmaBtn');
    const display = document.getElementById('rmaDetailDisplay');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Locating...';

    const isRma = query.toUpperCase().startsWith('RMA');
    const url = isRma 
        ? `/electronics_store/api/returns.php?rma=${encodeURIComponent(query)}`
        : `/electronics_store/api/returns.php?order=${encodeURIComponent(query)}`;

    try {
        const res = await fetch(url);
        const result = await res.json();

        display.classList.remove('hidden');

        if (!result.success || !result.data || result.data.length === 0) {
            display.innerHTML = `
                <div class="bg-red-50 border border-red-200 text-red-700 p-6 rounded-3xl text-center space-y-2">
                    <i class="fa-solid fa-circle-xmark text-2xl text-red-500"></i>
                    <h4 class="font-bold text-sm">RMA Request Not Found</h4>
                    <p class="text-xs text-red-600">No active RMA requests were found matching "${query}".</p>
                </div>
            `;
            return;
        }

        const rmaList = result.data;
        display.innerHTML = rmaList.map(rma => {
            const step = rma.current_step;
            const isClosed = rma.is_closed;

            return `
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-8">
                    
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 font-mono">
                                Return Merchandise Authorization
                            </span>
                            <h3 class="text-2xl font-extrabold text-slate-900 font-mono">${rma.rma_number}</h3>
                            <div class="text-xs text-slate-400 mt-0.5">Order Ref: <strong class="text-slate-700 font-mono">${rma.order_number}</strong> • Requested on ${rma.created_at}</div>
                        </div>

                        <div class="text-right">
                            <span class="inline-block px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                ${rma.status}
                            </span>
                        </div>
                    </div>

                    <!-- RMA 4-Stage Visual Timeline Tracker -->
                    <div class="py-4">
                        <div class="flex items-center justify-between relative">
                            <!-- Step 1 -->
                            <div class="timeline-step ${step >= 1 ? (step === 1 ? 'active' : 'completed') : ''}">
                                <div class="timeline-icon">
                                    <i class="fa-solid fa-file-lines text-xs"></i>
                                </div>
                                <div class="mt-2">
                                    <h5 class="text-[11px] font-bold text-slate-800">1. Submitted</h5>
                                    <p class="text-[10px] text-slate-400">Review in progress</p>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="timeline-step ${step >= 2 ? (step === 2 ? 'active' : 'completed') : ''}">
                                <div class="timeline-icon">
                                    <i class="fa-solid fa-truck-ramp-box text-xs"></i>
                                </div>
                                <div class="mt-2">
                                    <h5 class="text-[11px] font-bold text-slate-800">2. Approved</h5>
                                    <p class="text-[10px] text-slate-400">Ship hardware back</p>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="timeline-step ${step >= 3 ? (step === 3 ? 'active' : 'completed') : ''}">
                                <div class="timeline-icon">
                                    <i class="fa-solid fa-microscope text-xs"></i>
                                </div>
                                <div class="mt-2">
                                    <h5 class="text-[11px] font-bold text-slate-800">3. Inspected</h5>
                                    <p class="text-[10px] text-slate-400">Diagnostics verified</p>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="timeline-step ${step >= 4 ? 'completed' : ''}">
                                <div class="timeline-icon">
                                    <i class="fa-solid ${rma.request_type === 'Refund' ? 'fa-wallet' : 'fa-box-check'} text-xs"></i>
                                </div>
                                <div class="mt-2">
                                    <h5 class="text-[11px] font-bold text-slate-800">4. Resolved</h5>
                                    <p class="text-[10px] text-slate-400">${rma.request_type}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hardware & Defect Breakdown -->
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 bg-slate-50 p-5 rounded-2xl border border-slate-100 text-xs">
                        <div class="md:col-span-3 h-24 bg-white rounded-xl p-2 flex items-center justify-center border border-slate-200">
                            <img src="${rma.image_url}" alt="${rma.product_name}" class="max-h-full max-w-full object-contain">
                        </div>

                        <div class="md:col-span-9 space-y-2">
                            <div class="flex justify-between items-center">
                                <h4 class="font-bold text-slate-900 text-sm">${rma.product_name}</h4>
                                <span class="bg-blue-100 text-blue-800 font-bold px-2 py-0.5 rounded text-[10px]">
                                    Action: ${rma.request_type}
                                </span>
                            </div>
                            <div class="text-slate-600">
                                <strong>Tracked Serial:</strong> <span class="font-mono text-blue-700 font-semibold">${rma.serial_number || 'N/A'}</span>
                            </div>
                            <div class="text-slate-600">
                                <strong>Reported Defect:</strong> <span class="text-slate-800">${rma.reason}</span>
                            </div>
                            <p class="text-slate-500 italic bg-white p-2.5 rounded-lg border border-slate-200">
                                "${rma.description}"
                            </p>
                        </div>
                    </div>

                    <!-- Admin / Warehouse Diagnostics Response -->
                    ${rma.admin_response ? `
                        <div class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-2xl text-xs space-y-1">
                            <div class="font-bold text-emerald-900 flex items-center gap-1.5">
                                <i class="fa-solid fa-headset text-emerald-600"></i> Technical Support Response:
                            </div>
                            <p class="text-emerald-800 leading-relaxed">${rma.admin_response}</p>
                        </div>
                    ` : ''}

                    <div class="flex items-center justify-between text-xs text-slate-400 pt-2 border-t border-slate-100">
                        <span>Need updates? Contact support at support@electropulse.io</span>
                        <a href="/electronics_store/admin.php?tab=returns" class="text-blue-600 font-semibold hover:underline">
                            Admin Status View <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>

                </div>
            `;
        }).join('');

    } catch (err) {
        console.error("RMA track error:", err);
        display.innerHTML = `<div class="p-6 text-center text-red-500 text-xs bg-white rounded-2xl border border-red-200">Error retrieving RMA ticket: ${err.message}</div>`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Track Status';
    }
}

async function handleSubmitRma(e) {
    e.preventDefault();
    const btn = document.getElementById('submitRmaBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Submitting RMA Ticket...';

    const payload = {
        order_number: document.getElementById('rmaOrderNumber').value.trim(),
        customer_email: document.getElementById('rmaEmail').value.trim(),
        serial_number: document.getElementById('rmaSerial').value.trim(),
        request_type: document.getElementById('rmaActionType').value,
        reason: document.getElementById('rmaReason').value,
        description: document.getElementById('rmaDescription').value.trim(),
        photo_url: document.getElementById('rmaPhotoUrl').value.trim()
    };

    try {
        const res = await fetch('/electronics_store/api/returns.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await res.json();

        if (result.success && result.data) {
            App.showToast(`RMA ticket ${result.data.rma_number} created successfully!`, "success");
            switchRmaTab('track');
            document.getElementById('trackRmaInput').value = result.data.rma_number;
            handleTrackRma();
        } else {
            throw new Error(result.error || "RMA submission failed");
        }
    } catch (err) {
        console.error("RMA submission error:", err);
        App.showToast(err.message, "error", "Submission Failed");
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit RMA Request & Generate Tracking Code';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('trackRmaInput').value) {
        handleTrackRma();
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
