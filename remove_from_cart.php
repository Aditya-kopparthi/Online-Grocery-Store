<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart_id = $_GET['id'];

$query = "DELETE FROM cart WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $cart_id);

if ($stmt->execute()) {
    header("Location: cart.php");
} else {
    echo "Error removing item from cart.";
}

$stmt->close();
$conn->close();
?>
