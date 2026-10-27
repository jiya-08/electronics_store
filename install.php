<?php
require_once __DIR__ . '/config/config.php';

$message = '';
$error = '';
$dbCreated = false;

if (isset($_POST['run_install'])) {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Run Schema
        $schemaSql = file_get_contents(__DIR__ . '/database/schema.sql');
        $pdo->exec($schemaSql);

        // Run Seed
        $seedSql = file_get_contents(__DIR__ . '/database/seed.sql');
        $pdo->exec($seedSql);

        $message = "Database 'electronics_store_db' created, tables built, and demo tech products & serials seeded successfully!";
        $dbCreated = true;
    } catch (Exception $e) {
        $error = "Installation failed: " . $e->getMessage();
    }
}

// Check current database status
$currentStatus = "Not Connected";
$tableCounts = [];
try {
    $testPdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT
    ]);
    if ($testPdo) {
        $res = $testPdo->query("SHOW TABLES");
        if ($res) {
            $currentStatus = "Connected & Active";
            $tables = ['categories', 'products', 'product_specifications', 'product_serials', 'orders', 'order_items', 'warranties', 'return_requests', 'invoices', 'stock_logs'];
            foreach ($tables as $t) {
                $q = $testPdo->query("SELECT COUNT(*) as c FROM `$t`");
                $tableCounts[$t] = $q ? $q->fetch(PDO::FETCH_ASSOC)['c'] : 0;
            }
        }
    }
} catch (Exception $e) {
    $currentStatus = "Database not initialized yet";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Installer | ElectroPulse</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-xl w-full bg-slate-800 border border-slate-700 rounded-3xl p-8 space-y-6 shadow-2xl">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center text-white text-xl shadow-lg shadow-blue-500/30">
                <i class="fa-solid fa-database"></i>
            </div>
            <div>
                <h1 class="text-2xl font-extrabold text-white">Database Setup & Installer</h1>
                <p class="text-xs text-slate-400">Electronics & Gadget Store Environment</p>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="p-4 bg-emerald-500/20 border border-emerald-500/40 text-emerald-300 rounded-2xl text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5"><i class="fa-solid fa-circle-check"></i> Success!</div>
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="p-4 bg-red-500/20 border border-red-500/40 text-red-300 rounded-2xl text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> Error</div>
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>

        <!-- Database Connection Health -->
        <div class="bg-slate-900/60 rounded-2xl p-4 border border-slate-700 text-xs space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-slate-400">Database Name:</span>
                <span class="font-mono font-bold text-cyan-400"><?php echo DB_NAME; ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-400">Connection Host:</span>
                <span class="font-mono"><?php echo DB_HOST; ?>:<?php echo DB_PORT; ?></span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-slate-400">Status:</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold <?php echo $currentStatus === 'Connected & Active' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400'; ?>">
                    <?php echo $currentStatus; ?>
                </span>
            </div>
        </div>

        <!-- Current Table Statistics if active -->
        <?php if (!empty($tableCounts)): ?>
        <div class="space-y-2">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Seeded Tables & Record Counts:</h4>
            <div class="grid grid-cols-2 gap-2 text-xs font-mono">
                <?php foreach ($tableCounts as $tbl => $cnt): ?>
                <div class="flex justify-between p-2 bg-slate-900/40 rounded-xl border border-slate-700/60">
                    <span class="text-slate-400"><?php echo $tbl; ?></span>
                    <span class="font-bold text-white"><?php echo $cnt; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <form method="POST" class="pt-2">
            <button type="submit" name="run_install" class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl text-sm transition shadow-lg shadow-blue-500/25 flex items-center justify-center gap-2">
                <i class="fa-solid fa-bolt"></i>
                <span><?php echo !empty($tableCounts) ? 'Re-install / Reset Demo Database' : 'Install & Seed Electronics Store Database'; ?></span>
            </button>
        </form>

        <div class="flex justify-between items-center pt-2 text-xs text-slate-400 border-t border-slate-700">
            <a href="<?php echo BASE_URL; ?>/index.php" class="text-blue-400 hover:underline flex items-center gap-1">
                <i class="fa-solid fa-arrow-left"></i> Go to Storefront
            </a>
            <a href="<?php echo BASE_URL; ?>/admin.php" class="text-cyan-400 hover:underline flex items-center gap-1">
                Admin Dashboard <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </div>

</body>
</html>
