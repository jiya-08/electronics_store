<?php
// Store Configuration
define('STORE_NAME', 'ElectroPulse');
define('STORE_TAGLINE', 'Premium Electronics & Next-Gen Gadgets');
define('STORE_EMAIL', 'support@electropulse.io');
define('STORE_PHONE', '+1 (800) 892-PULSE');
define('STORE_ADDRESS', '100 Innovation Parkway, Suite 500, Tech District, CA 94016');
define('STORE_CURRENCY', '$');
define('STORE_TAX_RATE', 0.18); // 18% standard VAT/Sales Tax
define('LOW_STOCK_GLOBAL_THRESHOLD', 5);

// Dynamic Base URL detection (works on localhost/electronics_store AND on Vercel root)
$isVercel = !empty($_ENV['VERCEL']) || !empty($_SERVER['VERCEL']) || (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'vercel.app') !== false);
if ($isVercel) {
    define('BASE_URL', '');
} else {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    if (strpos($scriptName, '/electronics_store') !== false) {
        define('BASE_URL', '/electronics_store');
    } else {
        define('BASE_URL', '');
    }
}

// Database Credentials from Environment or Localhost Defaults
define('DB_HOST', getenv('DB_HOST') ?: (getenv('MYSQL_HOST') ?: 'localhost'));
define('DB_USER', getenv('DB_USER') ?: (getenv('MYSQL_USER') ?: 'root'));
define('DB_PASS', getenv('DB_PASS') ?: (getenv('MYSQL_PASSWORD') ?: ''));
define('DB_NAME', getenv('DB_NAME') ?: (getenv('MYSQL_DATABASE') ?: 'electronics_store_db'));
define('DB_PORT', getenv('DB_PORT') ?: (getenv('MYSQL_PORT') ?: '3306'));
?>
