<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' | ' . STORE_NAME : STORE_NAME . ' | ' . STORE_TAGLINE; ?></title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                            900: '#1e3a8a',
                        },
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen">

    <!-- Top Announcement Bar -->
    <div class="bg-slate-900 text-slate-300 text-xs py-2 px-4 border-b border-slate-800">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5"><i class="fa-solid fa-shield-halved text-emerald-400"></i> Official Manufacturer Warranty & Tracked Serials</span>
                <span class="hidden md:inline text-slate-600">|</span>
                <span class="hidden md:flex items-center gap-1.5"><i class="fa-solid fa-truck-fast text-blue-400"></i> Free Express Shipping on orders over $500</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="<?php echo BASE_URL; ?>/warranty.php" class="hover:text-white transition flex items-center gap-1">
                    <i class="fa-solid fa-barcode text-cyan-400"></i> Check Warranty
                </a>
                <span class="text-slate-600">|</span>
                <a href="<?php echo BASE_URL; ?>/returns.php" class="hover:text-white transition flex items-center gap-1">
                    <i class="fa-solid fa-arrow-rotate-left text-amber-400"></i> RMA & Returns
                </a>
                <span class="text-slate-600">|</span>
                <a href="<?php echo BASE_URL; ?>/admin.php" class="hover:text-white transition flex items-center gap-1 font-semibold text-blue-400">
                    <i class="fa-solid fa-gauge-high"></i> Admin Portal
                    <span class="stock-alert-badge ml-1 px-1.5 py-0.2 bg-red-600 text-white rounded-full text-[10px] font-bold" style="display:none;">0</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar -->
    <header class="bg-white/95 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">
                
                <!-- Brand Logo -->
                <div class="flex items-center gap-8">
                    <a href="<?php echo BASE_URL; ?>/index.php" class="flex items-center gap-2.5 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-500 flex items-center justify-center text-white shadow-md shadow-blue-500/20 group-hover:scale-105 transition">
                            <i class="fa-solid fa-bolt-lightning text-xl"></i>
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight text-slate-900 group-hover:text-blue-600 transition">Electro<span class="text-blue-600">Pulse</span></span>
                            <span class="block text-[10px] uppercase tracking-wider text-slate-400 font-semibold -mt-1">Gadgets & Tech</span>
                        </div>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden lg:flex items-center gap-1">
                        <a href="<?php echo BASE_URL; ?>/index.php" class="px-3 py-2 rounded-lg text-sm font-medium transition <?php echo $currentPage === 'index' ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'; ?>">
                            Home
                        </a>
                        <a href="<?php echo BASE_URL; ?>/products.php" class="px-3 py-2 rounded-lg text-sm font-medium transition <?php echo $currentPage === 'products' ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'; ?>">
                            Catalog & Specs
                        </a>
                        <a href="<?php echo BASE_URL; ?>/compare.php" class="px-3 py-2 rounded-lg text-sm font-medium transition flex items-center gap-1.5 <?php echo $currentPage === 'compare' ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'; ?>">
                            Compare
                            <span class="compare-badge bg-blue-600 text-white rounded-full px-1.5 py-0.5 text-[10px] font-bold" style="display:none;">0</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/warranty.php" class="px-3 py-2 rounded-lg text-sm font-medium transition <?php echo $currentPage === 'warranty' ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'; ?>">
                            Warranty Hub
                        </a>
                        <a href="<?php echo BASE_URL; ?>/returns.php" class="px-3 py-2 rounded-lg text-sm font-medium transition <?php echo $currentPage === 'returns' ? 'text-blue-600 bg-blue-50' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100'; ?>">
                            Returns & RMA
                        </a>
                    </nav>
                </div>

                <!-- Global Search Input -->
                <div class="flex-1 max-w-md hidden md:block">
                    <form action="<?php echo BASE_URL; ?>/products.php" method="GET" class="relative">
                        <input type="text" name="search" placeholder="Search phones, laptops, chips, SKUs..." class="w-full bg-slate-100 border border-slate-200 rounded-xl pl-10 pr-4 py-2 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                    </form>
                </div>

                <!-- Right Action Icons -->
                <div class="flex items-center gap-3">
                    <!-- Compare Button Shortcut -->
                    <a href="<?php echo BASE_URL; ?>/compare.php" class="relative p-2.5 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded-xl transition" title="Compare Gadgets">
                        <i class="fa-solid fa-code-compare text-lg"></i>
                        <span class="compare-badge absolute -top-1 -right-1 bg-cyan-600 text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center" style="display:none;">0</span>
                    </a>

                    <!-- Cart Drawer / Link -->
                    <a href="<?php echo BASE_URL; ?>/cart.php" class="relative flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition shadow-sm shadow-blue-500/20">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="hidden sm:inline">Cart</span>
                        <span class="cart-badge bg-white text-blue-700 text-xs font-bold rounded-full px-2 py-0.5 ml-0.5" style="display:none;">0</span>
                    </a>

                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg">
                        <i class="fa-solid fa-bars text-lg"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div id="mobileMenu" class="hidden lg:hidden border-t border-slate-200 bg-white px-4 pt-3 pb-4 space-y-2">
            <form action="<?php echo BASE_URL; ?>/products.php" method="GET" class="mb-3">
                <input type="text" name="search" placeholder="Search gadgets..." class="w-full bg-slate-100 border border-slate-200 rounded-lg px-3 py-2 text-sm">
            </form>
            <a href="<?php echo BASE_URL; ?>/index.php" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Home</a>
            <a href="<?php echo BASE_URL; ?>/products.php" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Catalog & Specs</a>
            <a href="<?php echo BASE_URL; ?>/compare.php" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Compare Matrix</a>
            <a href="<?php echo BASE_URL; ?>/warranty.php" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Warranty Hub</a>
            <a href="<?php echo BASE_URL; ?>/returns.php" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100">Returns & RMA</a>
            <a href="<?php echo BASE_URL; ?>/admin.php" class="block px-3 py-2 rounded-lg text-sm font-semibold text-blue-600 hover:bg-blue-50">Admin Dashboard</a>
        </div>
    </header>

    <script>
        document.getElementById('mobileMenuBtn')?.addEventListener('click', () => {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });
    </script>
