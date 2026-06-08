<?php
require_once '../includes/config.php';

$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    die("Missing order ID");
}

$order = $conn->query("
    SELECT * FROM orders WHERE id = $orderId
")->fetch_assoc();

if (!$order) {
    die("Order not found");
}

$items = $conn->query("
    SELECT * FROM order_items WHERE order_id = $orderId
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order #<?= $order['id'] ?></title>
</head>
<body>
    <h1>Order #<?= $order['id'] ?></h1>
    <p><strong>Name:</strong> <?= $order['customer_name'] ?></p>
    <p><strong>Phone:</strong> <?= $order['phone'] ?></p>
    <p><strong>Total:</strong> $<?= $order['total'] ?></p>
    <p><strong>Status:</strong> <?= $order['status'] ?></p>
    <p><strong>Date:</strong> <?= $order['created_at'] ?></p>

    <h2>Items</h2>
    <ul>
        <?php while ($item = $items->fetch_assoc()): ?>
            <li><?= $item['drink_name'] ?> × <?= $item['quantity'] ?></li>
        <?php endwhile; ?>
    </ul>

    <a href="index.php">← Back to Orders</a>
</body>
</html>