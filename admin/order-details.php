<?php

require_once '../includes/db.php';
if ($conn->connect_error) {
    die("Database error");
}

$orderId = $_GET['id'];

$order = $conn->query("
    SELECT * FROM orders
    WHERE id = $orderId
")->fetch_assoc();

echo "<h1>Order #" . $order['id'] . "</h1>";

echo "<p>Name: " . $order['customer_name'] . "</p>";

echo "<p>Phone: " . $order['phone'] . "</p>";

echo "<p>Total: $" . $order['total'] . "</p>";

echo "<p>Date: " . $order['created_at'] . "</p>";

$items = $conn->query("
    SELECT * FROM order_items
    WHERE order_id = $orderId
");

echo "<h2>Items</h2>";

echo "<ul>";

while($item = $items->fetch_assoc()){

    echo "<li>";

    echo $item['drink_name'];
    echo " × ";
    echo $item['quantity'];

    echo "</li>";
}

echo "</ul>";