<?php
require_once __DIR__ . '/config.php';

function get_db_connection() {
    static $pdo = null;
    if ($pdo === null) {
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // 1. Try MySQL connection if configured or locally running
        $isVercel = !empty($_ENV['VERCEL']) || !empty($_SERVER['VERCEL']);
        $hasCustomHost = !empty(getenv('DB_HOST')) || !empty(getenv('MYSQL_HOST'));

        if (!$isVercel || $hasCustomHost) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            try {
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                return $pdo;
            } catch (PDOException $e) {
                // Try to create DB if missing
                if ($e->getCode() == 1049) {
                    try {
                        $initDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
                        $tmpPdo = new PDO($initDsn, DB_USER, DB_PASS, $options);
                        $tmpPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                        return $pdo;
                    } catch (Exception $inner) {}
                }
            }
        }

        // 2. Fallback to SQLite (Essential for Vercel Serverless without remote MySQL)
        try {
            $sqlitePath = is_writable('/tmp') ? '/tmp/electronics_store.sqlite' : __DIR__ . '/../database/electronics_store.sqlite';
            $isNewDb = !file_exists($sqlitePath);
            $pdo = new PDO("sqlite:" . $sqlitePath, null, null, $options);
            
            if ($isNewDb || filesize($sqlitePath) < 1000) {
                init_sqlite_schema($pdo);
            }
        } catch (Exception $sqliteErr) {
            die("Database initialization error: " . $sqliteErr->getMessage());
        }
    }
    return $pdo;
}

function init_sqlite_schema($pdo) {
    // Read schema & seed files, convert MySQL specific syntax for SQLite
    $schemaFile = __DIR__ . '/../database/schema.sql';
    $seedFile = __DIR__ . '/../database/seed.sql';
    if (file_exists($schemaFile) && file_exists($seedFile)) {
        $sql = file_get_contents($schemaFile) . "\n" . file_get_contents($seedFile);
        
        // Clean MySQL specific keywords for SQLite compatibility
        $sql = preg_replace('/CREATE DATABASE[^;]+;/i', '', $sql);
        $sql = preg_replace('/USE[^;]+;/i', '', $sql);
        $sql = preg_replace('/ENGINE=InnoDB[^;]*/i', '', $sql);
        $sql = preg_replace('/DEFAULT CHARSET=[^;]*/i', '', $sql);
        $sql = preg_replace('/CHARACTER SET [^ ]+/i', '', $sql);
        $sql = preg_replace('/COLLATE [^ ;]+/i', '', $sql);
        $sql = preg_replace('/SET FOREIGN_KEY_CHECKS[^;]+;/i', '', $sql);
        $sql = preg_replace('/INT AUTO_INCREMENT PRIMARY KEY/i', 'INTEGER PRIMARY KEY AUTOINCREMENT', $sql);
        $sql = str_ireplace('NOW()', "datetime('now')", $sql);
        $sql = str_ireplace('CURDATE()', "date('now')", $sql);

        $queries = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($queries as $q) {
            if (!empty($q)) {
                try {
                    $pdo->exec($q);
                } catch (Exception $e) {}
            }
        }
    }
}

function json_response($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}

function json_error($message, $statusCode = 400, $extra = []) {
    $payload = array_merge([
        'success' => false,
        'error' => $message,
        'timestamp' => date('c')
    ], $extra);
    json_response($payload, $statusCode);
}

function json_success($data = [], $message = 'Success', $statusCode = 200) {
    $payload = [
        'success' => true,
        'message' => $message,
        'data' => $data,
        'timestamp' => date('c')
    ];
    json_response($payload, $statusCode);
}

function format_currency($amount) {
    return STORE_CURRENCY . number_format((float)$amount, 2);
}

function get_request_data() {
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    if (strpos($contentType, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        return json_decode($raw, true) ?? [];
    }
    return $_POST;
}
?>
