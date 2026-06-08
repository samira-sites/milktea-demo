<?php
require_once '../includes/config.php';

if ($conn->connect_error) {
    die("Database error");
}


$resultTable = $conn->query("SELECT * FROM orders ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>MilkTea Admin</title>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #f5f6fa;
}

/* LAYOUT */
.dashboard {
    display: flex;
    min-height: 100vh;
}

/* SIDEBAR */
.sidebar {
    width: 220px;
    background: #111827;
    color: white;
    padding: 20px;
}

.sidebar h2 {
    margin-bottom: 20px;
}

.sidebar a {
    display: block;
    color: #cbd5e1;
    text-decoration: none;
    padding: 10px;
    border-radius: 8px;
    margin-bottom: 8px;
}

.sidebar a:hover {
    background: #1f2937;
    color: white;
}

/* CONTENT */
.content {
    flex: 1;
    padding: 20px;
}

/* ORDER CARD */
.order {
    background: white;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 12px;
    overflow: hidden;
}

th {
    background: #111827;
    color: white;
    padding: 12px;
}

td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

/* STATUS */
.status {
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    display: inline-block;
}

.pending { background: #fef3c7; color: #92400e; }
.preparing { background: #dbeafe; color: #1e40af; }
.done { background: #dcfce7; color: #166534; }

select {
    padding: 6px;
    border-radius: 6px;
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