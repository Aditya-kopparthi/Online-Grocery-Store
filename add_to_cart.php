<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "Please log in to add items to the cart!";
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];

// Check if the product is already in the cart
$checkQuery = "SELECT id FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // If item exists, update the quantity
    $updateQuery = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ii", $user_id, $product_id);
} else {
    // If item doesn't exist, insert new entry
    $insertQuery = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ii", $user_id, $product_id);
}

if ($stmt->execute()) {
    echo "Item added to cart successfully!";
} else {
    echo "Error adding item to cart.";
}

$stmt->close();
$conn->close();
?>
