<?php
require_once __DIR__ . '/config.php';

function get_db_connection() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Check if database doesn't exist, try to connect without dbname and create it
            if ($e->getCode() == 1049) {
                try {
                    $initDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
                    $tmpPdo = new PDO($initDsn, DB_USER, DB_PASS, $options);
                    $tmpPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
                    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                } catch (Exception $inner) {
                    die("Database connection failed: " . $inner->getMessage());
                }
            } else {
                die("Database connection error: " . $e->getMessage());
            }
        }
    }
    return $pdo;
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
