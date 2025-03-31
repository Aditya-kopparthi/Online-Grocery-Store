<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders for the user
$query = "SELECT orders.id, products.name, products.price, orders.quantity, orders.order_date, orders.status 
          FROM orders 
          JOIN products ON orders.product_id = products.id 
          WHERE orders.user_id = ? 
          ORDER BY orders.order_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Orders</title>
    <link rel="stylesheet" href="myorder.css">
</head>
<body>

<h2>My Orders</h2>
<?php if (isset($_GET['success'])) echo "<p class='success'>Order placed successfully!</p>"; ?>
<div class="orders-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="order-item">
            <p><strong><?= $row['name'] ?></strong> - ₹<?= $row['price'] ?> x <?= $row['quantity'] ?></p>
            <p>Ordered on: <?= $row['order_date'] ?></p>
            <p>Status: <strong><?= $row['status'] ?></strong></p>
        </div>
    <?php endwhile; ?>
</div>

<a href="index.php" class="btn">Back to Home</a>

</body>
</html>
