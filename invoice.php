<?php
require_once __DIR__ . '/config/database.php';

$invoiceNumber = $_GET['number'] ?? '';
$invoiceId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$pdo = get_db_connection();

if (!empty($invoiceNumber)) {
    $stmt = $pdo->prepare("SELECT i.*, o.order_number, o.payment_method, o.order_status, o.created_at as order_created_at FROM invoices i JOIN orders o ON i.order_id = o.id WHERE i.invoice_number = ?");
    $stmt->execute([$invoiceNumber]);
} else if ($invoiceId > 0) {
    $stmt = $pdo->prepare("SELECT i.*, o.order_number, o.payment_method, o.order_status, o.created_at as order_created_at FROM invoices i JOIN orders o ON i.order_id = o.id WHERE i.id = ?");
    $stmt->execute([$invoiceId]);
} else {
    // Default to first invoice
    $stmt = $pdo->query("SELECT i.*, o.order_number, o.payment_method, o.order_status, o.created_at as order_created_at FROM invoices i JOIN orders o ON i.order_id = o.id ORDER BY i.id DESC LIMIT 1");
}

$invoice = $stmt->fetch();

if (!$invoice) {
    die("Invoice record not found.");
}

// Fetch order items with assigned serial numbers
$itemsStmt = $pdo->prepare("
    SELECT oi.*, p.brand, p.model, p.sku, p.warranty_months, p.has_serial_tracking 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$itemsStmt->execute([$invoice['order_id']]);
$items = $itemsStmt->fetchAll();

// Fetch warranties
$warStmt = $pdo->prepare("
    SELECT warranty_code, serial_number, expiry_date, warranty_type, status 
    FROM warranties 
    WHERE order_id = ?
");
$warStmt->execute([$invoice['order_id']]);
$warranties = $warStmt->fetchAll();

$pageTitle = "Invoice " . $invoice['invoice_number'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $invoice['invoice_number']; ?> - ElectroPulse Tax Invoice</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        @media print {
            .no-print { display: none !important; }
            body { background: #fff !important; }
            .invoice-box { border: none !important; box-shadow: none !important; margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; }
        }
    </style>
</head>
<body class="bg-slate-100 py-8 px-4 text-slate-800">

    <!-- Top Action Bar (No Print) -->
    <div class="max-w-4xl mx-auto mb-6 flex flex-wrap justify-between items-center gap-4 no-print">
        <a href="<?php echo BASE_URL; ?>/admin.php?tab=invoices" class="text-xs font-semibold text-slate-600 hover:text-blue-600 flex items-center gap-1.5 transition">
            <i class="fa-solid fa-arrow-left"></i> Back to Invoice Registry
        </a>

        <div class="flex items-center gap-2">
            <a href="<?php echo BASE_URL; ?>/returns.php?order=<?php echo urlencode($invoice['order_number']); ?>" class="px-4 py-2 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-arrow-rotate-left text-emerald-600"></i> Request RMA for this Order
            </a>
            <button onclick="window.print()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-blue-500/20">
                <i class="fa-solid fa-print"></i> Print / Download PDF
            </button>
        </div>
    </div>

    <!-- Official Printable Invoice Container -->
    <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-12 space-y-8 invoice-box">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 pb-6 border-b border-slate-200">
            <div class="space-y-2">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                        <i class="fa-solid fa-bolt-lightning"></i>
                    </div>
                    <span class="text-2xl font-black text-slate-900 tracking-tight">Electro<span class="text-blue-600">Pulse</span></span>
                </div>
                <p class="text-xs text-slate-500 max-w-xs leading-relaxed">
                    <?php echo STORE_ADDRESS; ?><br>
                    Email: <?php echo STORE_EMAIL; ?> • Phone: <?php echo STORE_PHONE; ?><br>
                    GSTIN / Tax ID: <strong class="font-mono text-slate-700">99AAACE2026E1Z9</strong>
                </p>
            </div>

            <div class="sm:text-right space-y-1">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider <?php echo $invoice['payment_status'] === 'Paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'; ?>">
                    <?php echo $invoice['payment_status']; ?>
                </span>
                <h2 class="text-2xl font-black font-mono text-slate-900 mt-1"><?php echo htmlspecialchars($invoice['invoice_number']); ?></h2>
                <div class="text-xs text-slate-400">Order: <strong class="font-mono text-slate-700"><?php echo htmlspecialchars($invoice['order_number']); ?></strong></div>
                <div class="text-xs text-slate-500">Date: <?php echo htmlspecialchars($invoice['issue_date']); ?></div>
            </div>
        </div>

        <!-- Billing & Shipping Details -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs pb-6 border-b border-slate-100">
            <div class="space-y-1">
                <span class="text-slate-400 uppercase font-bold text-[10px] tracking-wider">Billed & Shipped To:</span>
                <h4 class="font-bold text-sm text-slate-900"><?php echo htmlspecialchars($invoice['customer_name']); ?></h4>
                <p class="text-slate-600 leading-relaxed"><?php echo nl2br(htmlspecialchars($invoice['billing_address'])); ?></p>
                <div class="text-slate-500 pt-1">
                    Email: <span class="font-medium text-slate-700"><?php echo htmlspecialchars($invoice['customer_email']); ?></span><br>
                    Phone: <span class="font-medium text-slate-700"><?php echo htmlspecialchars($invoice['customer_phone']); ?></span>
                </div>
            </div>

            <div class="space-y-1 sm:text-right">
                <span class="text-slate-400 uppercase font-bold text-[10px] tracking-wider">Payment & Logistics:</span>
                <div class="text-slate-700 pt-1">
                    Payment Method: <strong class="text-slate-900"><?php echo htmlspecialchars($invoice['payment_method']); ?></strong><br>
                    Order Status: <strong class="text-blue-600"><?php echo htmlspecialchars($invoice['order_status']); ?></strong><br>
                    Invoice Due Date: <span class="font-mono"><?php echo htmlspecialchars($invoice['due_date']); ?></span>
                </div>
                <div class="pt-2">
                    <span class="inline-flex items-center gap-1 text-[11px] font-mono text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        <i class="fa-solid fa-shield-check"></i> Serial Verified & Tax Compliant
                    </span>
                </div>
            </div>
        </div>

        <!-- Itemized Products Table with Tracked Serials -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Purchased Hardware & Assigned Serials</h3>
            
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200">
                            <th class="p-3 w-12 text-center">#</th>
                            <th class="p-3">Hardware Item & Serial Number</th>
                            <th class="p-3 w-28 text-center">SKU</th>
                            <th class="p-3 w-20 text-center">Qty</th>
                            <th class="p-3 w-24 text-right">Unit Price</th>
                            <th class="p-3 w-28 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        <?php 
                        $i = 1;
                        foreach ($items as $item): 
                            $serials = !empty($item['assigned_serial_numbers']) ? explode(',', $item['assigned_serial_numbers']) : [];
                        ?>
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="p-3 text-center text-slate-400 font-mono"><?php echo $i++; ?></td>
                            <td class="p-3 space-y-1">
                                <div class="font-bold text-slate-900"><?php echo htmlspecialchars($item['brand'] . ' ' . $item['product_name']); ?></div>
                                <?php if (!empty($serials)): ?>
                                <div class="text-[11px] text-blue-700 bg-blue-50/80 px-2.5 py-1 rounded-lg border border-blue-100 inline-block font-mono">
                                    <i class="fa-solid fa-barcode text-blue-600 mr-1"></i>
                                    Allocated Serial(s): <strong><?php echo htmlspecialchars(implode(', ', array_map('trim', $serials))); ?></strong>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="p-3 text-center font-mono text-[11px] text-slate-500"><?php echo htmlspecialchars($item['sku']); ?></td>
                            <td class="p-3 text-center font-bold text-slate-800"><?php echo (int)$item['quantity']; ?></td>
                            <td class="p-3 text-right font-mono">$<?php echo number_format($item['unit_price'], 2); ?></td>
                            <td class="p-3 text-right font-mono font-bold text-slate-900">$<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals & Tax Breakdown -->
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-6 pt-2">
            
            <!-- Left: Associated Warranties Box -->
            <div class="sm:col-span-7 space-y-3">
                <?php if (!empty($warranties)): ?>
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 text-xs space-y-2">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5 uppercase text-[10px] tracking-wider">
                        <i class="fa-solid fa-shield text-blue-600"></i> Official Manufacturer Warranty Certificates
                    </span>
                    <div class="space-y-1.5">
                        <?php foreach ($warranties as $w): ?>
                        <div class="flex justify-between items-center text-[11px] bg-white p-2 rounded-lg border border-slate-100">
                            <div>
                                <span class="font-mono font-bold text-blue-700"><?php echo $w['warranty_code']; ?></span>
                                <span class="text-slate-400 ml-1 font-mono">(SN: <?php echo $w['serial_number']; ?>)</span>
                            </div>
                            <span class="text-emerald-700 font-semibold">Valid thru <?php echo $w['expiry_date']; ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <p class="text-[11px] text-slate-400 italic leading-relaxed">
                    <?php echo htmlspecialchars($invoice['notes']); ?>
                </p>
            </div>

            <!-- Right: Calculation Summary -->
            <div class="sm:col-span-5 space-y-2 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Hardware Subtotal:</span>
                    <span class="font-mono font-semibold text-slate-800">$<?php echo number_format($invoice['subtotal'], 2); ?></span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Sales Tax / VAT (<?php echo $invoice['tax_rate']; ?>%):</span>
                    <span class="font-mono font-semibold text-slate-800">$<?php echo number_format($invoice['tax_amount'], 2); ?></span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Insured Express Shipping:</span>
                    <span class="font-mono font-semibold text-slate-800">
                        <?php echo $invoice['shipping_amount'] == 0 ? 'FREE' : '$' . number_format($invoice['shipping_amount'], 2); ?>
                    </span>
                </div>
                <div class="flex justify-between text-base font-extrabold text-slate-900 pt-3 border-t-2 border-slate-200">
                    <span>Total Amount Paid:</span>
                    <span class="font-mono text-blue-600">$<?php echo number_format($invoice['total_amount'], 2); ?></span>
                </div>
            </div>

        </div>

        <!-- Footer Notice & QR Code -->
        <div class="pt-8 border-t border-slate-200 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-slate-400">
            <div class="space-y-1 text-center sm:text-left">
                <p class="font-semibold text-slate-600">ElectroPulse Electronic Commercial Invoice</p>
                <p class="text-[11px]">This is a computer-generated tax invoice. No physical signature is required.</p>
                <p class="text-[10px] font-mono">Invoice Hash: <?php echo hash('sha256', $invoice['invoice_number'] . $invoice['order_id']); ?></p>
            </div>

            <!-- QR Code Simulation -->
            <div class="flex items-center gap-3 bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                <div class="w-12 h-12 bg-slate-900 rounded p-1 flex items-center justify-center text-white text-xs font-mono">
                    <i class="fa-solid fa-qrcode text-2xl"></i>
                </div>
                <div class="text-[10px] text-slate-500 font-mono leading-tight">
                    <span>Scan to verify</span><br>
                    <span class="text-blue-600 font-bold"><?php echo $invoice['invoice_number']; ?></span>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
