<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About & Queries</title>
    <link rel="stylesheet" href="aboutstyles.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">
        <a href="index.php">🛒 Grocery Store</a>
    </div>
    <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="myorder.php">My Orders</a></li>
        <li><a href="about.php">About/Queries</a></li>
    </ul>
</nav>

<div class="about-container">
    <h2>About Our Grocery Store</h2>
    <p>Welcome to our Online Grocery Store! We aim to provide the best quality groceries at affordable prices.</p>

    <h3>Contact Us</h3>
    <p>If you have any queries, feel free to contact us:</p>
    <ul>
        <li>Email: support@groceryshop.com</li>
        <li>Phone: +91 8688971199</li>
        <li>Address: 123, Main Street, Your City, India</li>
    </ul>

    <h3>Submit Your Query</h3>
    <form action="submit_query.php" method="POST">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" placeholder="Enter your query" required></textarea>
        <button type="submit">Submit</button>
    </form>
</div>

</body>
</html>
