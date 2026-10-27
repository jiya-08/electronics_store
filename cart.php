<?php
$pageTitle = "Shopping Cart & Checkout";
require_once __DIR__ . '/includes/header.php';
?>

<div class="bg-slate-100 py-6 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
            <a href="<?php echo BASE_URL; ?>/index.php" class="hover:text-blue-600">Home</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-800 font-medium">Cart & Checkout</span>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900">Your Shopping Cart & Hardware Order</h1>
    </div>
</div>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Active Cart & Checkout View -->
    <div id="cartContent" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Cart Items List -->
        <div class="lg:col-span-7 space-y-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                
                <div class="flex justify-between items-center pb-3 border-b border-slate-100">
                    <h2 class="font-bold text-slate-900 text-base flex items-center gap-2">
                        <i class="fa-solid fa-box text-blue-600"></i> Selected Electronics (<span id="cartCountLabel">0</span>)
                    </h2>
                    <button onclick="App.clearCart()" class="text-xs text-slate-400 hover:text-red-500 transition">
                        Clear Cart
                    </button>
                </div>

                <!-- Shipping threshold progress bar -->
                <div class="p-3 bg-blue-50/70 border border-blue-200 rounded-2xl text-xs space-y-1.5" id="shippingBanner">
                    <div class="flex justify-between text-blue-900 font-semibold" id="shippingBannerText">
                        <span>Free Express Shipping on orders over $500</span>
                        <span id="shippingRemaining">$500.00 away</span>
                    </div>
                    <div class="w-full bg-blue-200 rounded-full h-2 overflow-hidden">
                        <div id="shippingBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Items Container -->
                <div id="cartItemList" class="divide-y divide-slate-100">
                    <!-- Populated via loadCart() -->
                </div>

            </div>

            <!-- Serial & Warranty Assurance Notice -->
            <div class="p-5 bg-gradient-to-r from-blue-900 to-indigo-900 text-white rounded-3xl space-y-2 shadow-sm">
                <div class="flex items-center gap-2 font-bold text-sm text-cyan-300">
                    <i class="fa-solid fa-shield-check text-lg"></i> Official Hardware Warranty Guarantee
                </div>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Upon checkout completion, our warehouse system immediately allocates authenticated hardware serial numbers to your items. A printable tax invoice and warranty certificates with digital verification codes are issued in real time.
                </p>
            </div>
        </div>

        <!-- Right: Order Summary & Checkout Form -->
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <h3 class="text-lg font-bold text-slate-900 pb-3 border-b border-slate-100">Order Summary & Billing</h3>

                <!-- Subtotal Breakdown -->
                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Items Subtotal:</span>
                        <span class="font-semibold text-slate-800" id="subtotalVal">$0.00</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Sales Tax / VAT (18%):</span>
                        <span class="font-semibold text-slate-800" id="taxVal">$0.00</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Express Insured Shipping:</span>
                        <span class="font-semibold text-slate-800" id="shippingVal">$0.00</span>
                    </div>
                    <div class="flex justify-between text-base font-extrabold text-slate-900 pt-3 border-t border-slate-200">
                        <span>Estimated Total:</span>
                        <span class="text-blue-600" id="totalVal">$0.00</span>
                    </div>
                </div>

                <!-- Checkout Form -->
                <form id="checkoutForm" onsubmit="handleCheckout(event)" class="space-y-4 pt-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Customer Details</h4>
                    
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name *</label>
                        <input type="text" id="custName" value="Marcus Sterling" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address *</label>
                            <input type="email" id="custEmail" value="m.sterling@cyberworks.dev" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number *</label>
                            <input type="text" id="custPhone" value="+1 (555) 948-2201" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Shipping & Billing Address *</label>
                        <input type="text" id="custAddress" value="450 Silicon Avenue, Floor 12" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">City *</label>
                            <input type="text" id="custCity" value="Seattle" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Postal / ZIP Code *</label>
                            <input type="text" id="custPostal" value="98101" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:bg-white outline-none">
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Payment Method</label>
                        <select id="custPaymentMethod" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-blue-500 outline-none">
                            <option value="Credit Card (Visa / Mastercard)">Credit Card (Visa / Mastercard)</option>
                            <option value="PayPal">PayPal</option>
                            <option value="Bank Wire Transfer">Bank Wire Transfer</option>
                            <option value="Cash on Delivery">Cash on Delivery</option>
                        </select>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submitOrderBtn" class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold rounded-xl text-sm transition shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2">
                        <i class="fa-solid fa-lock"></i>
                        <span>Complete Order & Assign Serials</span>
                    </button>
                    <p class="text-[10px] text-center text-slate-400">
                        <i class="fa-solid fa-shield"></i> 256-bit Encrypted Checkout • Tax Invoice & Serials Generated Instantly
                    </p>
                </form>

            </div>
        </div>

    </div>

    <!-- Empty Cart State -->
    <div id="emptyCartView" class="hidden bg-white p-12 rounded-3xl border border-slate-200 text-center space-y-4 max-w-lg mx-auto">
        <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 mx-auto flex items-center justify-center text-2xl">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800">Your cart is currently empty</h3>
        <p class="text-xs text-slate-500 max-w-sm mx-auto">
            Browse our catalog of flagship phones, laptops, and smart tech accessories to add products to your cart.
        </p>
        <div class="pt-2">
            <a href="<?php echo BASE_URL; ?>/products.php" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow-sm inline-flex items-center gap-2">
                <span>Explore Products</span>
                <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Order Success Receipt Screen -->
    <div id="orderSuccessView" class="hidden max-w-3xl mx-auto bg-white p-8 sm:p-10 rounded-3xl border border-emerald-200 shadow-xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center text-3xl">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Payment & Stock Confirmed</span>
            <h2 class="text-3xl font-extrabold text-slate-900">Order Placed Successfully!</h2>
            <p class="text-xs text-slate-500">
                Order <strong class="text-slate-800 font-mono" id="successOrderNum">ORD-XXXX</strong> has been processed. Stock has been deducted and authentic serial numbers have been allocated.
            </p>
        </div>

        <!-- Order & Invoice Badges -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-center text-xs">
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-bold">Order Number</span>
                <div class="font-bold font-mono text-slate-800 mt-0.5" id="resOrderNumber">-</div>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-bold">Tax Invoice</span>
                <div class="font-bold font-mono text-blue-600 mt-0.5" id="resInvoiceNumber">-</div>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-bold">Total Paid</span>
                <div class="font-bold text-slate-800 mt-0.5" id="resTotalAmount">-</div>
            </div>
            <div>
                <span class="text-slate-400 text-[10px] uppercase font-bold">Payment Method</span>
                <div class="font-bold text-slate-800 mt-0.5" id="resPaymentMethod">-</div>
            </div>
        </div>

        <!-- Assigned Hardware Serial Numbers & Warranties Box -->
        <div class="p-5 bg-blue-50/70 border border-blue-200 rounded-2xl space-y-3">
            <h4 class="font-bold text-xs uppercase tracking-wider text-blue-900 flex items-center gap-2">
                <i class="fa-solid fa-barcode text-blue-600"></i> Assigned Serial Numbers & Activated Warranties
            </h4>
            <div id="successWarrantiesList" class="space-y-2">
                <!-- Dynamically populated -->
            </div>
        </div>

        <!-- Action Links -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-slate-200">
            <a href="<?php echo BASE_URL; ?>/products.php" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition">
                <i class="fa-solid fa-arrow-left mr-1"></i> Continue Shopping
            </a>

            <div class="flex items-center gap-2">
                <a id="viewInvoiceBtn" href="#" class="px-5 py-2.5 bg-white border border-slate-300 hover:border-blue-600 text-blue-600 rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-receipt"></i> View Official Invoice
                </a>
                <a href="<?php echo BASE_URL; ?>/warranty.php" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition flex items-center gap-1.5 shadow-sm">
                    <i class="fa-solid fa-shield-halved"></i> Open Warranty Hub
                </a>
            </div>
        </div>
    </div>

</main>

<script>
let currentCartData = null;

async function loadCart() {
    const cart = App.getCart();
    const cartContent = document.getElementById('cartContent');
    const emptyView = document.getElementById('emptyCartView');
    const successView = document.getElementById('orderSuccessView');
    const itemList = document.getElementById('cartItemList');
    const countLabel = document.getElementById('cartCountLabel');

    if (!cart || cart.length === 0) {
        cartContent.classList.add('hidden');
        emptyView.classList.remove('hidden');
        successView.classList.add('hidden');
        return;
    }

    cartContent.classList.remove('hidden');
    emptyView.classList.add('hidden');
    successView.classList.add('hidden');

    try {
        const res = await fetch('${(window.APP_BASE_URL !== undefined ? window.APP_BASE_URL : (window.location.pathname.startsWith('/electronics_store') ? '/electronics_store' : ''))}/api/cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items: cart })
        });
        const result = await res.json();

        if (result.success && result.data) {
            currentCartData = result.data;
            countLabel.textContent = currentCartData.item_count;

            // Render Items
            itemList.innerHTML = currentCartData.items.map(item => `
                <div class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <img src="${item.image_url}" alt="${item.name}" class="w-16 h-16 object-contain rounded-xl bg-slate-50 border border-slate-200 p-1" onerror="this.src='https://placehold.co/100x100?text=Hardware'">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600">${item.brand}</span>
                            <h4 class="text-xs font-bold text-slate-900 leading-snug">${item.name}</h4>
                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">SKU: ${item.sku}</div>
                            ${item.has_serial_tracking ? `
                                <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-blue-600 font-mono mt-1">
                                    <i class="fa-solid fa-barcode"></i> Serial Tracked (${item.warranty_months}m warranty)
                                </span>
                            ` : ''}
                            ${item.stock_warning ? `
                                <div class="text-[11px] text-red-600 font-semibold mt-1">
                                    <i class="fa-solid fa-triangle-exclamation"></i> ${item.stock_warning}
                                </div>
                            ` : ''}
                        </div>
                    </div>

                    <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto">
                        <!-- Quantity Controller -->
                        <div class="flex items-center border border-slate-200 rounded-xl bg-slate-50 overflow-hidden">
                            <button onclick="changeQty(${item.product_id}, ${item.quantity - 1})" class="px-2.5 py-1 text-slate-500 hover:bg-slate-200 transition">
                                <i class="fa-solid fa-minus text-[10px]"></i>
                            </button>
                            <span class="w-8 text-center text-xs font-bold text-slate-800">${item.quantity}</span>
                            <button onclick="changeQty(${item.product_id}, ${item.quantity + 1})" class="px-2.5 py-1 text-slate-500 hover:bg-slate-200 transition">
                                <i class="fa-solid fa-plus text-[10px]"></i>
                            </button>
                        </div>

                        <!-- Subtotal -->
                        <div class="text-right">
                            <div class="text-xs font-bold text-slate-900">$${item.subtotal.toFixed(2)}</div>
                            <div class="text-[10px] text-slate-400">$${item.unit_price.toFixed(2)} ea</div>
                        </div>

                        <!-- Remove Button -->
                        <button onclick="App.removeFromCart(${item.product_id})" class="text-slate-400 hover:text-red-500 p-1" title="Remove Item">
                            <i class="fa-solid fa-trash-can text-xs"></i>
                        </button>
                    </div>
                </div>
            `).join('');

            // Totals
            document.getElementById('subtotalVal').textContent = `$${currentCartData.subtotal.toFixed(2)}`;
            document.getElementById('taxVal').textContent = `$${currentCartData.tax_amount.toFixed(2)}`;
            document.getElementById('shippingVal').textContent = currentCartData.shipping_amount === 0 ? 'FREE' : `$${currentCartData.shipping_amount.toFixed(2)}`;
            document.getElementById('totalVal').textContent = `$${currentCartData.total_amount.toFixed(2)}`;

            // Shipping Progress
            const sub = currentCartData.subtotal;
            const threshold = 500.00;
            const bar = document.getElementById('shippingBar');
            const remLabel = document.getElementById('shippingRemaining');

            if (sub >= threshold) {
                bar.style.width = '100%';
                bar.classList.replace('bg-blue-600', 'bg-emerald-500');
                remLabel.textContent = 'Free Shipping Unlocked!';
                remLabel.classList.add('text-emerald-700');
            } else {
                const pct = Math.min(100, Math.round((sub / threshold) * 100));
                bar.style.width = `${pct}%`;
                bar.classList.replace('bg-emerald-500', 'bg-blue-600');
                const diff = threshold - sub;
                remLabel.textContent = `$${diff.toFixed(2)} away from Free Shipping`;
                remLabel.classList.remove('text-emerald-700');
            }
        }
    } catch (e) {
        console.error("Cart calculation error:", e);
    }
}

function changeQty(pId, newQty) {
    App.updateCartQty(pId, newQty);
    loadCart();
}

async function handleCheckout(event) {
    event.preventDefault();
    const btn = document.getElementById('submitOrderBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Processing & Allocating Serials...';

    const cart = App.getCart();
    const payload = {
        customer_name: document.getElementById('custName').value.trim(),
        customer_email: document.getElementById('custEmail').value.trim(),
        customer_phone: document.getElementById('custPhone').value.trim(),
        shipping_address: document.getElementById('custAddress').value.trim(),
        city: document.getElementById('custCity').value.trim(),
        postal_code: document.getElementById('custPostal').value.trim(),
        payment_method: document.getElementById('custPaymentMethod').value,
        items: cart
    };

    try {
        const res = await fetch('${(window.APP_BASE_URL !== undefined ? window.APP_BASE_URL : (window.location.pathname.startsWith('/electronics_store') ? '/electronics_store' : ''))}/api/checkout.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const result = await res.json();

        if (result.success && result.data) {
            const data = result.data;

            // Clear Cart
            App.clearCart();

            // Populate success screen
            document.getElementById('cartContent').classList.add('hidden');
            document.getElementById('orderSuccessView').classList.remove('hidden');

            document.getElementById('successOrderNum').textContent = data.order_number;
            document.getElementById('resOrderNumber').textContent = data.order_number;
            document.getElementById('resInvoiceNumber').textContent = data.invoice_number;
            document.getElementById('resTotalAmount').textContent = `$${data.total_amount.toFixed(2)}`;
            document.getElementById('resPaymentMethod').textContent = data.payment_method;

            document.getElementById('viewInvoiceBtn').href = `/electronics_store/invoice.php?number=${data.invoice_number}`;

            // Render assigned serials
            const warContainer = document.getElementById('successWarrantiesList');
            if (data.warranties && data.warranties.length > 0) {
                warContainer.innerHTML = data.warranties.map(w => `
                    <div class="bg-white p-3 rounded-xl border border-blue-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div>
                            <span class="font-bold text-slate-800">${w.product_name}</span>
                            <div class="text-[11px] text-slate-500 font-mono">Serial #: <strong class="text-blue-700">${w.serial_number}</strong></div>
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Warranty Code: ${w.warranty_code}
                            </span>
                            <div class="text-[10px] text-slate-400 mt-0.5">Valid until: ${w.expiry_date}</div>
                        </div>
                    </div>
                `).join('');
            } else {
                warContainer.innerHTML = '<div class="text-xs text-slate-500">Order successfully placed without serial-tracked units.</div>';
            }

            App.showToast("Order placed successfully! Serials assigned.", "success");
            window.scrollTo({ top: 0, behavior: 'smooth' });

        } else {
            throw new Error(result.error || "Order placement failed");
        }

    } catch (err) {
        console.error("Checkout error:", err);
        App.showToast(err.message, "error", "Checkout Failed");
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-lock"></i> <span>Complete Order & Assign Serials</span>';
    }
}

window.addEventListener('cart-updated', loadCart);
document.addEventListener('DOMContentLoaded', loadCart);
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

