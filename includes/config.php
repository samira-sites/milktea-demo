<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

/* =========================
   LOAD .ENV FILE
========================= */
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');  // ← fix is here
$dotenv->load();

/* =========================
   APP CONFIG
========================= */
define('APP_NAME', $_ENV['APP_NAME'] ?? 'Milk Tea Shop');
define('APP_ENV',  $_ENV['APP_ENV']  ?? 'local');
define('APP_DEBUG', filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN));

/* =========================
   DATABASE CONFIG
========================= */
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? '');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');

/* =========================
   SITE URL
========================= */
define('BASE_URL', $_ENV['BASE_URL'] ?? 'http://localhost/milktea');

/* =========================
   ERROR HANDLING
========================= */
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(0);
}

/* =========================
   DATABASE CONNECTION
========================= */
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed',
        'error'   => $conn->connect_error
    ]);
    exit;
}

$conn->set_charset("utf8mb4");