<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

if ($conn->connect_error) {
    echo json_encode([
        'success' => false,
        'error'   => $conn->connect_error
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Database connected OK'
]);