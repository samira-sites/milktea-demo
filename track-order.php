<?php
$conn = new mysqli("localhost", "root", "", "milktea_shop");

$order = null;
$items = [];

if (isset($_GET['phone']) && $_GET['phone'] !== '') {

    $phone = $_GET['phone'];

    // SAFE QUERY
    $stmt = $conn->prepare("
        SELECT * FROM orders 
        WHERE phone = ?
        ORDER BY id DESC
        LIMIT 1
    ");

    $stmt->bind_param("s", $phone);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $order = $result->fetch_assoc();
        $orderId = $order['id'];

        $itemsResult = $conn->query("
            SELECT * FROM order_items 
            WHERE order_id = $orderId
        ");

        while ($row = $itemsResult->fetch_assoc()) {
            $items[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Track My Order</title>

    <style>
        body{
            font-family: Arial;
            background:#f5f6fa;
            padding:30px;
        }

        .box{
            max-width:500px;
            margin:auto;
            background:white;
            padding:20px;
            border-radius:12px;
            box-shadow:0 5px 15px rgba(0,0,0,0.1);
        }

        input{
            width:100%;
            padding:12px;
            margin:10px 0;
        }

        button{
            width:100%;
            padding:12px;
            background:#111827;
            color:white;
            border:none;
            border-radius:8px;
            cursor:pointer;
        }

        .status{
            margin-top:20px;
            padding:15px;
            border-radius:10px;
            background:#f3f4f6;
        }

        /* STATUS BADGES */
        .badge{
            padding:5px 10px;
            border-radius:20px;
            font-size:12px;
        }

        .pending{
            background:#fef3c7;
            color:#92400e;
        }

        .preparing{
            background:#dbeafe;
            color:#1e40af;
        }

        .done{
            background:#dcfce7;
            color:#166534;
        }
    </style>
</head>

<body>

<div class="box">

    <h2>📦 Track Your Order</h2>

    <form method="GET">
        <input type="text" name="phone" placeholder="Enter your phone number" required>
        <button type="submit">Track Order</button>
    </form>

    <div id="orderBox" class="status"></div>

</div>

<script>
let phone = "";
let interval = null;

document.querySelector("form").addEventListener("submit", function(e){
    e.preventDefault();

    phone = document.querySelector("#phone").value;

    loadOrder();

    if (interval) clearInterval(interval);

    interval = setInterval(loadOrder, 5000);
});

function loadOrder() {

    fetch("get-order.php?phone=" + phone)
        .then(res => res.json())
        .then(data => {

            console.log("API RESPONSE:", data); // 🔥 DEBUG

            const box = document.getElementById("orderBox");

            if (!data.success) {
                box.innerHTML = "❌ No order found";
                return;
            }

            let o = data.order;
            let items = data.items;

            let html = `
                <div class="status">
                    <h3>📦 Order #${o.id}</h3>
                    <p><b>Name:</b> ${o.customer_name}</p>
                    <p><b>Status:</b> ${o.status}</p>
                    <p><b>Total:</b> $${o.total}</p>
                    <p><b>Date:</b> ${o.created_at}</p>
                    <h4>Items:</h4>
                    <ul>
            `;

            items.forEach(i => {
                html += `<li>${i.drink_name} × ${i.quantity}</li>`;
            });

            html += "</ul></div>";

            box.innerHTML = html;
        });
}
</script>
</body>
</html>