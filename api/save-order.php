<?php
require_once __DIR__ . '/../includes/config.php';

header('Content-Type: application/json');

// Block non-POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Read JSON body from JavaScript fetch()
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid or empty request body']);
    exit;
}

// Sanitize inputs
$name  = trim($data['name'] ?? '');
$phone = trim($data['phone'] ?? '');
$cart  = $data['cart'] ?? [];

// Validate
if (!$name || !$phone || empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Name, phone, and cart are required']);
    exit;
}

// Calculate total
$total = 0;
foreach ($cart as $item) {
    $total += floatval($item['price']) * intval($item['qty']);
}

// Insert into orders table
$stmt = $conn->prepare(
    "INSERT INTO orders (customer_name, phone, total, status, created_at)
     VALUES (?, ?, ?, 'Pending', NOW())"
);
$stmt->bind_param("ssd", $name, $phone, $total);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to save order']);
    exit;
}

$orderId = $conn->insert_id;

// Insert each item into order_items table
$itemStmt = $conn->prepare(
    "INSERT INTO order_items (order_id, drink_name, quantity, price)
     VALUES (?, ?, ?, ?)"
);

foreach ($cart as $item) {
    $drinkName = trim($item['name']);
    $qty       = intval($item['qty']);
    $price     = floatval($item['price']);

    $itemStmt->bind_param("isid", $orderId, $drinkName, $qty, $price);

    if (!$itemStmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Failed to save order items']);
        exit;
    }
}

echo json_encode(['success' => true, 'order_id' => $orderId]);