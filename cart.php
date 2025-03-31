<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for logged-in user
$query = "SELECT cart.id, cart.product_id, products.name, products.price, products.image, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.id 
          WHERE cart.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total price
$total_amount = 0;
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $total_amount += $row['price'] * $row['quantity'];
    $cart_items[] = $row;
}

// If cart is empty, disable payment
$cart_empty = empty($cart_items);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Cart</title>
    <link rel="stylesheet" href="cartstyles.css">
</head>
<body>

<h2>My Cart</h2>
<div class="cart-container">
    <?php if ($cart_empty): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <?php foreach ($cart_items as $row): ?>
            <div class="cart-item">
                <img src="<?= $row['image'] ?>" alt="<?= $row['name'] ?>">
                <p><?= $row['name'] ?> - ₹<?= $row['price'] ?> x <?= $row['quantity'] ?></p>
                <a href="remove_from_cart.php?id=<?= $row['id'] ?>" class="remove-btn">Remove</a>
            </div>
        <?php endforeach; ?>
        
        <p class="total-price">Total Amount: ₹<?= number_format($total_amount, 2) ?></p>
        
        <!-- Redirect to payment page -->
        <a href="payment.php" class="buy-btn <?= $cart_empty ? 'disabled' : '' ?>">Proceed to Payment</a>
    <?php endif; ?>
</div>

<a href="index.php" class="btn">Continue Shopping</a>

</body>
</html>
