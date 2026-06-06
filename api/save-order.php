<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

file_put_contents("debug.txt", file_get_contents("php://input"));

require_once '../includes/db.php';

$conn = new mysqli(
    $_ENV['DB_HOST'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS'],
    $_ENV['DB_NAME']
);
if($conn->connect_error){
    die("Database error");
}


/* READ JSON */
$data = json_decode(file_get_contents("php://input"), true);

header('Content-Type: application/json'); // IMPORTANT

if(!$data){
    echo json_encode([
        "success" => false,
        "message" => "No data received"
    ]);
    exit;
}

$cart = $data["cart"] ?? [];
$name = $data["name"] ?? "Guest";
$phone = $data["phone"] ?? "";

$total = 0; // MUST exist

foreach($cart as $item){
    $total += $item["price"] * $item["qty"];
}

/* INSERT ORDER */
$stmt = $conn->prepare("
INSERT INTO orders(customer_name, total, phone)
VALUES(?, ?, ?)
");

$stmt->bind_param("sds", $name, $total, $phone);
$stmt->execute();

$orderId = $conn->insert_id;

/* INSERT ITEMS */
foreach($cart as $item){

    $drinkName = $item["name"];
    $price = $item["price"];
    $qty = $item["qty"];

    $stmt2 = $conn->prepare("
    INSERT INTO order_items(order_id, drink_name, price, quantity)
    VALUES(?, ?, ?, ?)
    ");

    $stmt2->bind_param("isdi", $orderId, $drinkName, $price, $qty);
    $stmt2->execute();
}

/* RESPONSE */
echo json_encode([
    "success" => true,
    "orderId" => $orderId
]);

$conn->close();