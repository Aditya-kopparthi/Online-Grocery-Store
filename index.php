<?php
session_start();
include 'db_connect.php';

$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Online Grocery Store</title>
    <link rel="stylesheet" href="indexstyles.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="logo">
        <a href="index.php">🛒 Grocery Store</a>
    </div>
    
    <div class="search-bar">
        <input type="text" id="searchInput" onkeyup="searchItems()" placeholder="Search for groceries...">
    </div>

    <ul class="nav-links">
        <li><a href="about.php">About/Queries</a></li>
        <li><a href="index.php">Home</a></li>
        <li><a href="cart.php">Cart</a></li>
        <li><a href="myorder.php">My Orders</a></li>

        <?php if ($isLoggedIn): ?>
            <li class="nav-item">
                <span class="welcome-text">👋 Welcome, <?= htmlspecialchars($_SESSION['username']); ?></span>
            </li>
            <li><a class="btn logout-btn" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a class="btn login-btn" href="login.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Category Buttons -->
<div class="category-buttons">
    <button class="category-btn active" onclick="filterCategory('all')">All</button>
    <button class="category-btn" onclick="filterCategory('vegetables')">Vegetables</button>
    <button class="category-btn" onclick="filterCategory('fruits')">Fruits</button>
    <button class="category-btn" onclick="filterCategory('detergents')">Detergents</button>
    <button class="category-btn" onclick="filterCategory('dairy')">Dairy</button>

</div>

<!-- Grocery Items Section -->
<section class="content-container">
    <h2>All Grocery Items Available</h2>
    <div class="grocery-container">
        <?php
        $query = "SELECT id, name, price, image, category FROM products";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()):
        ?>
            <div class="product-card" data-category="<?= $row['category'] ?>">
                <img src="<?= $row['image'] ?>" alt="<?= $row['name'] ?>">
                <p class="product-name"><?= $row['name'] ?></p>
                <p class="product-price">₹<?= $row['price'] ?></p>
                
                <?php if ($isLoggedIn): ?>
                    <button class="add-to-cart" data-id="<?= $row['id'] ?>">Add to Cart</button>
                <?php else: ?>
                    <button class="disabled-btn" onclick="alert('Please log in to add items to the cart!')">Login to Add</button>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</section>

<script>
// Add to Cart AJAX
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', function() {
        let productId = this.getAttribute('data-id');
        
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + productId
        })
        .then(response => response.text())
        .then(data => alert(data))
        .catch(error => console.error('Error:', error));
    });
});

// Search Function
function searchItems() {
    let input = document.getElementById("searchInput").value.toLowerCase();
    let items = document.querySelectorAll(".product-card");

    items.forEach(item => {
        let name = item.querySelector(".product-name").innerText.toLowerCase();
        item.style.display = name.includes(input) ? "block" : "none";
    });
}

// Filter by Category
function filterCategory(category) {
    let items = document.querySelectorAll(".product-card");
    items.forEach(item => {
        if (category === "all" || item.dataset.category === category) {
            item.style.display = "block";
        } else {
            item.style.display = "none";
        }
    });

    document.querySelectorAll(".category-btn").forEach(btn => btn.classList.remove("active"));
    event.target.classList.add("active");
}
</script>

</body>
</html>
