<?php
require_once '../includes/db.php';

header('Content-Type: application/json');

if (!isset($_GET['phone'])) {
    echo json_encode(["success" => false]);
    exit;
}

$phone = $_GET['phone'];

$stmt = $conn->prepare("
    SELECT * FROM orders 
    WHERE phone = ?
    ORDER BY id DESC
    LIMIT 1
");

$stmt->bind_param("s", $phone);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false]);
    exit;
}

$order = $result->fetch_assoc();
$orderId = $order['id'];

$items = [];

$resItems = $conn->query("
    SELECT * FROM order_items 
    WHERE order_id = $orderId
");

while ($row = $resItems->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode([
    "success" => true,
    "order" => $order,
    "items" => $items
]);