    <!-- Floating Compare Tray Widget -->
    <div id="compareTray" class="rounded-2xl p-3 flex flex-wrap items-center justify-between gap-4 max-w-3xl w-[94%]">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-cyan-500/20 text-cyan-400 text-xs font-bold">
                <span id="compareTrayCount">0</span>
            </span>
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-300">Compare Tray</span>
            <div id="compareTrayItems" class="flex flex-wrap items-center gap-2">
                <!-- Dynamically populated -->
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="App.clearCompare()" class="px-3 py-1.5 text-xs text-slate-400 hover:text-white transition">
                Clear
            </button>
            <a href="<?php echo BASE_URL; ?>/compare.php" class="px-4 py-2 bg-gradient-to-r from-blue-600 to-cyan-500 hover:from-blue-500 hover:to-cyan-400 text-white rounded-xl text-xs font-bold shadow-md transition flex items-center gap-1.5">
                <i class="fa-solid fa-code-compare"></i> Compare Now
            </a>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer"></div>

    <!-- Main Footer -->
    <footer class="bg-slate-900 text-slate-400 text-sm mt-auto border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 mb-10">
                <!-- Col 1: Store Intro -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center text-white font-bold">
                            <i class="fa-solid fa-bolt-lightning"></i>
                        </div>
                        <span class="text-xl font-bold text-white tracking-tight">Electro<span class="text-blue-500">Pulse</span></span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                        Enterprise-grade electronics store equipped with real-time stock telemetry, hardware serial number registration, official warranty tracking, and seamless RMA processing.
                    </p>
                    <div class="flex items-center gap-3 text-slate-400">
                        <span class="text-xs bg-slate-800 px-2.5 py-1 rounded border border-slate-700 text-emerald-400 font-mono">
                            <i class="fa-solid fa-shield-check"></i> Serial Verified
                        </span>
                        <span class="text-xs bg-slate-800 px-2.5 py-1 rounded border border-slate-700 text-cyan-400 font-mono">
                            <i class="fa-solid fa-receipt"></i> Instant Invoicing
                        </span>
                    </div>
                </div>

                <!-- Col 2: Navigation -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Shop & Explore</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="<?php echo BASE_URL; ?>/products.php?category=smartphones" class="hover:text-white transition">Smartphones</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/products.php?category=laptops" class="hover:text-white transition">Laptops & Ultrabooks</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/products.php?category=audio" class="hover:text-white transition">Audio & ANC Earbuds</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/products.php?category=wearables" class="hover:text-white transition">Wearables & Watches</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/compare.php" class="hover:text-white transition">Multi-Device Comparison</a></li>
                    </ul>
                </div>

                <!-- Col 3: Services & Support -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Customer Care</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="<?php echo BASE_URL; ?>/warranty.php" class="hover:text-white transition">Warranty Registration</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/returns.php" class="hover:text-white transition">Return & Replacement (RMA)</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/warranty.php?tab=lookup" class="hover:text-white transition">Serial Status Check</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/cart.php" class="hover:text-white transition">Checkout & Billing</a></li>
                    </ul>
                </div>

                <!-- Col 4: Store Management -->
                <div>
                    <h4 class="text-xs font-bold text-white uppercase tracking-wider mb-3">Management</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="<?php echo BASE_URL; ?>/admin.php?tab=analytics" class="hover:text-white transition">Sales Analytics</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/admin.php?tab=inventory" class="hover:text-white transition">Stock & Low-Stock Alerts</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/admin.php?tab=serials" class="hover:text-white transition">Serial Numbers Registry</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/admin.php?tab=returns" class="hover:text-white transition">RMA Ticket Resolution</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/install.php" class="hover:text-white transition text-slate-500">Database Installer / Reset</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright & Stack Info -->
            <div class="pt-8 border-t border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4 text-xs">
                <p>&copy; <?php echo date('Y'); ?> ElectroPulse Electronics Store. All rights reserved.</p>
                <div class="flex items-center gap-3 font-mono text-[11px] text-slate-500">
                    <span>Stack: PHP 8.0</span>
                    <span>•</span>
                    <span>MySQL 8.0</span>
                    <span>•</span>
                    <span>REST APIs</span>
                    <span>•</span>
                    <span>ES6 JS</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Core Scripts -->
    <script src="<?php echo BASE_URL; ?>/assets/js/app.js"></script>
</body>
</html>
