<?php
// Vercel Serverless Gateway Router
$uri = $_SERVER['REQUEST_URI'] ?? '/';
$parsed = parse_url($uri);
$path = $parsed['path'] ?? '/';

// Decode URL path
$path = rawurldecode($path);

// If root path or index
if ($path === '/' || $path === '/index.php' || $path === '') {
    require __DIR__ . '/../index.php';
    exit;
}

$cleanPath = ltrim($path, '/');
$baseDir = realpath(__DIR__ . '/..');

// 1. Check if exact file exists in root (e.g. products.php, compare.php, cart.php)
$directFile = realpath($baseDir . '/' . $cleanPath);
if ($directFile && strpos($directFile, $baseDir) === 0 && is_file($directFile)) {
    if (pathinfo($directFile, PATHINFO_EXTENSION) === 'php') {
        require $directFile;
        exit;
    }
}

// 2. Check if clean path with .php extension exists (e.g. /products -> products.php)
$phpFile = realpath($baseDir . '/' . $cleanPath . '.php');
if ($phpFile && strpos($phpFile, $baseDir) === 0 && is_file($phpFile)) {
    require $phpFile;
    exit;
}

// 3. Fallback to index.php
require __DIR__ . '/../index.php';
?>
