<?php

session_start();

if (!isset($_SESSION['admin_logged_in'])) {

    header("Location: login.php");
    exit;
}

require_once '../includes/config.php';

if ($conn->connect_error) {
    die("Database error");
}

$resultCards = $conn->query("SELECT * FROM orders ORDER BY id DESC");
$resultTable = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>MilkTea Admin</title>

<style>
:root{
  --bg:#f8f3ee;
  --surface:#fffaf6;
  --surface-2:#f1e4da;
  --primary:#6b422d;
  --primary-dark:#4e2d1f;
  --accent:#c58f7b;
  --text:#2e211b;
  --text-light:#6b5b55;
  --border:#eadfd7;
  --shadow:0 12px 40px rgba(0,0,0,.08);

  --radius-sm:14px;
  --radius-md:22px;
  --radius-lg:32px;
}

/* BASE */
body {
    margin: 0;
    font-family: "Segoe UI", sans-serif;
    background: var(--bg);
    color: var(--text);
}

/* LAYOUT */
.dashboard {
    display: flex;
    min-height: 100vh;
}

/* SIDEBAR */
.sidebar {
    width: 240px;
    background: var(--text);
    border-right: 1px solid var(--border);
    padding: 20px;
    
}

.sidebar h2 {
    margin-bottom: 20px;
    color: var(--bg);
}

.sidebar a {
    display: block;
    color: var(--bg);
    text-decoration: none;
    padding: 10px 12px;
    border-radius: var(--radius-sm);
    margin-bottom: 8px;
    transition: 0.2s ease;
}

.sidebar a:hover {
    background: var(--surface-2);
    color: var(--primary-dark);
}

/* CONTENT */
.content {
    flex: 1;
    padding: 20px;
}

/* ORDER CARD */
.order {
    background: var(--surface);
    padding: 15px;
    border-radius: var(--radius-md);
    margin-bottom: 15px;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    background: var(--surface);
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
}

th {
    background: var(--primary);
    color: white;
    padding: 12px;
    text-align: left;
}

td {
    padding: 12px;
    border-bottom: 1px solid var(--border);
}

/* STATUS BADGES */
.status {
    padding: 5px 10px;
    border-radius: 999px;
    font-size: 12px;
    display: inline-block;
    font-weight: 500;
}

.pending {
    background: #fef3c7;
    color: #92400e;
}

.preparing {
    background: #dbeafe;
    color: #1e40af;
}

.done {
    background: #dcfce7;
    color: #166534;
}

/* SELECT */
select {
    padding: 6px 10px;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border);
    background: var(--surface);
    color: var(--text);
    outline: none;
    cursor: pointer;
}

/* MOBILE */
@media (max-width: 768px) {
    .dashboard {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
    }
}
</style>
</head>

<body>

<div class="dashboard">

<!-- SIDEBAR -->
<aside class="sidebar">
    <h2>🧋 MilkTea Admin</h2>
    <a href="#">📦 Orders</a>
    <a href="#">⏳ Pending</a>
    <a href="#">🍹 Menu</a>
    <a href="#">⚙ Settings</a>
    
    <a href="logout.php">🚪 Logout</a>
</aside>

<!-- CONTENT -->
<main class="content">

<h2>📦 Order Details</h2>

<?php while ($order = $resultCards->fetch_assoc()): ?>

<?php
$orderId = $order['id'];
$items = $conn->query("
    SELECT * FROM order_items WHERE order_id = $orderId
");
?>

<div class="order">
    <h3>Order #<?= $order['id'] ?></h3>

    <p><strong>Name:</strong> <?= $order['customer_name'] ?></p>
    <p><strong>Phone:</strong> <?= $order['phone'] ?></p>

    <p>
        <strong>Status:</strong>
        <span class="status <?= strtolower($order['status']) ?>">
            <?= $order['status'] ?>
        </span>
    </p>

    <p><strong>Total:</strong> $<?= $order['total'] ?></p>
    <p><strong>Date:</strong> <?= $order['created_at'] ?></p>

    <ul>
        <?php while ($item = $items->fetch_assoc()): ?>
            <li><?= $item['drink_name'] ?> × <?= $item['quantity'] ?></li>
        <?php endwhile; ?>
    </ul>
</div>

<?php endwhile; ?>

<hr>

<h2>🧾 All Orders</h2>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Phone</th>
    <th>Total</th>
    <th>Status</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php while ($row = $resultTable->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['customer_name'] ?></td>
    <td><?= $row['phone'] ?></td>
    <td>$<?= $row['total'] ?></td>

    <td>
        <span class="status <?= strtolower($row['status']) ?>">
            <?= $row['status'] ?>
        </span>
    </td>

    <td><?= $row['created_at'] ?></td>

    <td>
        <form method="GET" action="update-status.php">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">

            <select name="status" onchange="this.form.submit()">
                <option value="Pending" <?= $row['status']=="Pending"?"selected":"" ?>>Pending</option>
                <option value="Preparing" <?= $row['status']=="Preparing"?"selected":"" ?>>Preparing</option>
                <option value="Done" <?= $row['status']=="Done"?"selected":"" ?>>Done</option>
            </select>
        </form>
    </td>

</tr>
<?php endwhile; ?>

</table>

</main>
</div>

</body>
</html>