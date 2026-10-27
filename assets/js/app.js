/**
 * ElectroPulse Core Application JavaScript
 */

const App = {
    cartKey: 'electropulse_cart',
    compareKey: 'electropulse_compare',

    init() {
        this.updateHeaderBadges();
        this.renderCompareTray();
        this.checkLowStockAlerts();
    },

    // Cart Management
    getCart() {
        try {
            return JSON.parse(localStorage.getItem(this.cartKey)) || [];
        } catch (e) {
            return [];
        }
    },

    saveCart(cart) {
        localStorage.setItem(this.cartKey, JSON.stringify(cart));
        this.updateHeaderBadges();
        window.dispatchEvent(new CustomEvent('cart-updated', { detail: cart }));
    },

    addToCart(productId, quantity = 1, productDetails = null) {
        let cart = this.getCart();
        const pId = parseInt(productId);
        const existingIndex = cart.findIndex(item => item.product_id === pId);

        if (existingIndex > -1) {
            cart[existingIndex].quantity += parseInt(quantity);
        } else {
            cart.push({
                product_id: pId,
                quantity: parseInt(quantity),
                details: productDetails || {}
            });
        }

        this.saveCart(cart);
        this.showToast('Product added to your cart!', 'success', 'Cart Updated');
    },

    removeFromCart(productId) {
        let cart = this.getCart().filter(item => item.product_id !== parseInt(productId));
        this.saveCart(cart);
        this.showToast('Item removed from cart', 'info');
    },

    updateCartQty(productId, quantity) {
        let cart = this.getCart();
        const pId = parseInt(productId);
        const item = cart.find(i => i.product_id === pId);
        if (item) {
            if (quantity <= 0) {
                this.removeFromCart(pId);
            } else {
                item.quantity = parseInt(quantity);
                this.saveCart(cart);
            }
        }
    },

    clearCart() {
        localStorage.removeItem(this.cartKey);
        this.updateHeaderBadges();
        window.dispatchEvent(new CustomEvent('cart-updated', { detail: [] }));
    },

    getCartCount() {
        return this.getCart().reduce((sum, item) => sum + (item.quantity || 1), 0);
    },

    // Comparison Manager
    getCompareList() {
        try {
            return JSON.parse(localStorage.getItem(this.compareKey)) || [];
        } catch (e) {
            return [];
        }
    },

    saveCompareList(list) {
        localStorage.setItem(this.compareKey, JSON.stringify(list));
        this.renderCompareTray();
        this.updateHeaderBadges();
        window.dispatchEvent(new CustomEvent('compare-updated', { detail: list }));
    },

    toggleCompare(product) {
        let list = this.getCompareList();
        const existingIndex = list.findIndex(p => p.id === parseInt(product.id));

        if (existingIndex > -1) {
            list.splice(existingIndex, 1);
            this.showToast(`Removed "${product.name}" from comparison`, 'info');
        } else {
            if (list.length >= 4) {
                this.showToast('You can compare a maximum of 4 gadgets at once', 'warning', 'Limit Reached');
                return false;
            }
            list.push({
                id: parseInt(product.id),
                name: product.name,
                image_url: product.image_url,
                price: product.price,
                brand: product.brand
            });
            this.showToast(`Added "${product.name}" to comparison matrix`, 'success', 'Added to Compare');
        }

        this.saveCompareList(list);
        return true;
    },

    removeFromCompare(productId) {
        let list = this.getCompareList().filter(p => p.id !== parseInt(productId));
        this.saveCompareList(list);
    },

    clearCompare() {
        localStorage.removeItem(this.compareKey);
        this.renderCompareTray();
        this.updateHeaderBadges();
        window.dispatchEvent(new CustomEvent('compare-updated', { detail: [] }));
    },

    renderCompareTray() {
        const tray = document.getElementById('compareTray');
        if (!tray) return;

        const list = this.getCompareList();
        if (list.length === 0) {
            tray.classList.remove('active');
            return;
        }

        tray.classList.add('active');
        const container = document.getElementById('compareTrayItems');
        const countSpan = document.getElementById('compareTrayCount');

        if (countSpan) countSpan.textContent = list.length;

        if (container) {
            container.innerHTML = list.map(item => `
                <div class="relative group flex items-center gap-2 bg-slate-800/80 border border-slate-700 rounded-lg p-1.5 pr-3 text-xs text-white">
                    <img src="${item.image_url}" alt="${item.name}" class="w-8 h-8 object-cover rounded bg-slate-900 border border-slate-700" onerror="this.src='https://placehold.co/100x100?text=Gadget'">
                    <span class="max-w-[120px] truncate font-medium">${item.name}</span>
                    <button onclick="App.removeFromCompare(${item.id})" class="text-slate-400 hover:text-red-400 p-0.5 rounded transition ml-1" title="Remove">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            `).join('');
        }
    },

    updateHeaderBadges() {
        const cartBadge = document.querySelectorAll('.cart-badge');
        const count = this.getCartCount();
        cartBadge.forEach(badge => {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline-flex' : 'none';
        });

        const compareBadge = document.querySelectorAll('.compare-badge');
        const compareCount = this.getCompareList().length;
        compareBadge.forEach(badge => {
            badge.textContent = compareCount;
            badge.style.display = compareCount > 0 ? 'inline-flex' : 'none';
        });
    },

    // Stock alert poll for header/admin
    async checkLowStockAlerts() {
        try {
            const res = await fetch(((window.APP_BASE_URL !== undefined ? window.APP_BASE_URL : (window.location.pathname.startsWith('/electronics_store') ? '/electronics_store' : '')) + '/api/stock.php'));
            const data = await res.json();
            if (data.success && data.data) {
                const count = data.data.low_stock_count;
                const badges = document.querySelectorAll('.stock-alert-badge');
                badges.forEach(b => {
                    b.textContent = count;
                    b.style.display = count > 0 ? 'inline-flex' : 'none';
                });
            }
        } catch (e) {
            console.warn("Stock alert check error:", e);
        }
    },

    // Toast Notifications
    showToast(message, type = 'info', title = '') {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'toast-item bg-white border border-slate-200 rounded-xl shadow-xl p-4 flex items-start gap-3 text-sm';

        let icon = '<i class="fa-solid fa-circle-info text-blue-500 text-lg"></i>';
        if (type === 'success') {
            icon = '<i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>';
        } else if (type === 'warning') {
            icon = '<i class="fa-solid fa-triangle-exclamation text-amber-500 text-lg"></i>';
        } else if (type === 'error') {
            icon = '<i class="fa-solid fa-circle-exclamation text-red-500 text-lg"></i>';
        }

        toast.innerHTML = `
            ${icon}
            <div class="flex-1">
                ${title ? `<h4 class="font-semibold text-slate-800 text-xs uppercase tracking-wider mb-0.5">${title}</h4>` : ''}
                <p class="text-slate-600">${message}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }
        }, 3500);
    },

    formatCurrency(amount) {
        return '$' + parseFloat(amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    App.init();
});

