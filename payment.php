<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for logged-in user
$query = "SELECT cart.product_id, products.name, products.price, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.id 
          WHERE cart.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total amount
$total_amount = 0;
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $total_amount += $row['price'] * $row['quantity'];
    $cart_items[] = $row;
}

// Process payment when "Pay Now" is clicked
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pay_now'])) {
    $payment_method = $_POST['payment_method'];

    // Ensure UPI ID or Card details are provided if required
    if ($payment_method == "upi" && empty($_POST['upi_id'])) {
        $error = "Please enter your UPI ID.";
    } elseif ($payment_method == "card" && (empty($_POST['card_number']) || empty($_POST['expiry_date']) || empty($_POST['cvv']))) {
        $error = "Please fill in all card details.";
    } else {
        foreach ($cart_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];
            $stmt = $conn->prepare("INSERT INTO orders (user_id, product_id, quantity, status) VALUES (?, ?, ?, 'Paid')");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            $stmt->execute();
        }

        // Clear cart
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        // Redirect to My Orders page
        header("Location: myorder.php?success=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link rel="stylesheet" href="paymentstyles.css">
</head>
<body>

<h2>Choose Payment Method</h2>
<p>Total Amount: ₹<?= number_format($total_amount, 2) ?></p>

<?php if (isset($error)): ?>
    <p class="error"><?= $error ?></p>
<?php endif; ?>

<form method="POST">
    <div class="payment-options">
        <label>
            <input type="radio" name="payment_method" value="upi" required onclick="showUPI()">
            <img src="./uploads/gpay.png" alt="Google Pay" class="payment-logo">
            Google Pay
        </label>

        <label>
            <input type="radio" name="payment_method" value="upi" required onclick="showUPI()">
            <img src="./uploads/phonepay.jfif" alt="PhonePe" class="payment-logo">
            PhonePe
        </label>

        <label>
            <input type="radio" name="payment_method" value="upi" required onclick="showUPI()">
            <img src="./uploads/paytm.jfif" alt="Paytm" class="payment-logo">
            Paytm
        </label>

        <label>
            <input type="radio" name="payment_method" value="card" required onclick="showCard()">
            <img src="./uploads/card.jpg" alt="Credit Card" class="payment-logo">
            Credit/Debit Card
        </label>
    </div>

    <!-- UPI ID Input -->
    <div id="upi-details" class="hidden">
        <input type="text" name="upi_id" placeholder="Enter UPI ID (e.g., yourname@upi)" required>
    </div>

    <!-- Card Details -->
    <div id="card-details" class="hidden">
        <input type="text" name="card_number" placeholder="Card Number" required>
        <input type="text" name="expiry_date" placeholder="Expiry Date (MM/YY)" required>
        <input type="text" name="cvv" placeholder="CVV" required>
    </div>

    <button type="submit" name="pay_now" class="btn">Pay Now</button>
</form>

<a href="cart.php" class="btn">Cancel</a>

<script>
function showUPI() {
    document.getElementById('upi-details').classList.remove('hidden');
    document.getElementById('card-details').classList.add('hidden');
}

function showCard() {
    document.getElementById('card-details').classList.remove('hidden');
    document.getElementById('upi-details').classList.add('hidden');
}
</script>

</body>
</html>
