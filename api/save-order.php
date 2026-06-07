<?php

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/db.php';

/* FORCE CHECK DB */
if (!$conn) {
    echo json_encode([
        "success" => false,
        "message" => "DB object not created"
    ]);
    exit;
}

/* READ INPUT */
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (!$data) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON",
        "raw" => $raw
    ]);
    exit;
}

$cart = $data["cart"] ?? [];
$name = $data["name"] ?? "Guest";
$phone = $data["phone"] ?? "";

/* TEST DB CONNECTION FIRST */
if ($conn->connect_error) {
    echo json_encode([
        "success" => false,
        "message" => "DB connection error",
        "error" => $conn->connect_error
    ]);
    exit;
}

/* CALCULATE TOTAL */
$total = 0;
foreach ($cart as $item) {
    $total += $item["price"] * $item["qty"];
}

/* INSERT ORDER */
$stmt = $conn->prepare("INSERT INTO orders (customer_name, total, phone) VALUES (?, ?, ?)");

if (!$stmt) {
    echo json_encode([
        "success" => false,
        "message" => "Prepare failed (orders)",
        "error" => $conn->error
    ]);
    exit;
}

$stmt->bind_param("sds", $name, $total, $phone);

if (!$stmt->execute()) {
    echo json_encode([
        "success" => false,
        "message" => "Execute failed (orders)",
        "error" => $stmt->error
    ]);
    exit;
}

$orderId = $conn->insert_id;

/* INSERT ITEMS */
foreach ($cart as $item) {

    $stmt2 = $conn->prepare("
        INSERT INTO order_items (order_id, drink_name, price, quantity)
        VALUES (?, ?, ?, ?)
    ");

    if (!$stmt2) {
        echo json_encode([
            "success" => false,
            "message" => "Prepare failed (items)",
            "error" => $conn->error
        ]);
        exit;
    }

    $stmt2->bind_param(
        "isdi",
        $orderId,
        $item["name"],
        $item["price"],
        $item["qty"]
    );

    if (!$stmt2->execute()) {
        echo json_encode([
            "success" => false,
            "message" => "Execute failed (items)",
            "error" => $stmt2->error
        ]);
        exit;
    }
}

echo json_encode([
    "success" => true,
    "orderId" => $orderId
]);