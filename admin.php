<?php
$conn = new mysqli("localhost", "root", "", "milktea_shop");

if ($conn->connect_error) {
    die("Database error");
}

$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");

while($order = $result->fetch_assoc()):

$orderId = $order['id'];

$items = $conn->query("
  SELECT * FROM order_items WHERE order_id = $orderId
");
?>

<div class="order">
  <h3>Order #<?= $order['id'] ?></h3>
  <p>Name: <?= $order['customer_name'] ?></p>
  <p>Phone: <?= $order['phone'] ?></p>
  <p>Total: $<?= $order['total'] ?></p>

  <ul>
    <?php while($item = $items->fetch_assoc()): ?>
      <li>
        <?= $item['drink_name'] ?> × <?= $item['quantity'] ?>
      </li>
    <?php endwhile; ?>
  </ul>
</div>

<hr>

<?php endwhile; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Orders</title>
    <style>
        body {
            font-family: Arial;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #f4f4f4;
        }
    </style>
</head>
<body>

<h2>🧾 All Orders</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Total</th>
        <th>Date</th>
    </tr>

   <!--Loop orders-->
    <?php while($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['customer_name'] ?></td>
    <td><?= $row['phone'] ?></td>
    <td>$<?= $row['total'] ?></td>
    <td><?= $row['created_at'] ?></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>
