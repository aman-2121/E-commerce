<?php
session_start();

$host = 'localhost';
$dbname = 'ecomerce_php';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle purchase
if (isset($_GET['buy']) && isset($_SESSION['email'])) {
    $product_id = intval($_GET['buy']);
    $email = $_SESSION['email'];

    // Fetch product
    $product_sql = "SELECT * FROM product WHERE id = $product_id";
    $product_result = mysqli_query($conn, $product_sql);
    $product = mysqli_fetch_assoc($product_result);

    if ($product && $product['quantity'] > 0) {
        // Dummy data for now — you'd collect these from a form in real apps
        $username = $_SESSION['email'];
        $phone = 'N/A'; // Replace with actual phone logic
        $address = 'N/A';

        // Insert order
        $order_sql = "INSERT INTO orders (title, price, image, username, email, phone, address)
                      VALUES (
                          '{$product['title']}',
                          '{$product['price']}',
                          '{$product['image']}',
                          '$username',
                          '$email',
                          '$phone',
                          '$address')";
        mysqli_query($conn, $order_sql);

        // Decrement product quantity
        $update_sql = "UPDATE product SET quantity = quantity - 1 WHERE id = $product_id";
        mysqli_query($conn, $update_sql);

        header("Location: index.php?success=1");
        exit();
    } else {
        header("Location: index.php?outofstock=1");
        exit();
    }
}

// Search feature
if (isset($_GET['search'])) {
    $search_value = $_GET['my_search'];
    $sql = "SELECT * FROM product WHERE CONCAT(title, discription) LIKE '%$search_value%'";
    $result = mysqli_query($conn, $sql);
} else {
    $sql = "SELECT * FROM product";
    $result = mysqli_query($conn, $sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Commerce Store</title>
    <link rel="stylesheet" href="userstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<nav>
    <input type="checkbox" id="check">
    <label for="check" class="checkbtn"><i class="fas fa-bars"></i></label>
    <a href="index.php" class="logo">EcomStore</a>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php">Products</a></li>
        <li><a href="contact.php">Contact</a></li>
        <?php if (isset($_SESSION['email'])): ?>
            <li><a class="rigi" href="my_orders.php?email=<?php echo $_SESSION['email'] ?>">My Orders</a></li>
            <li><a class="rigi" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a class="rigi" href="register.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<?php if (isset($_GET['success'])): ?>
    <p style="color: green; text-align:center;">✔️ Product purchased successfully!</p>
<?php elseif (isset($_GET['outofstock'])): ?>
    <p style="color: red; text-align:center;">❌ Sorry, product is out of stock!</p>
<?php endif; ?>

<div class="hero">
    <img class="cover" src="https://images.unsplash.com/photo-1557821552-17105176677c?w=1600&auto=format&fit=crop&q=80" alt="E-commerce Store">
    <div class="hero-content">
        <h1>Welcome to Our Store</h1>
        <p>Discover amazing products at unbeatable prices</p>
    </div>
</div>

<div class="search-container">
    <form action="" method="GET" class="search-form">
        <input type="text" name="my_search" placeholder="Search products..." class="search-input"
               value="<?php echo isset($_GET['my_search']) ? htmlspecialchars($_GET['my_search']) : '' ?>">
        <button type="submit" name="search" class="search-button"><i class="fas fa-search"></i> Search</button>
    </form>
</div>

<h2 class="section-title">Featured Products</h2>
<div class="card-container">
    <div class="card-grid">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="product-card">
                <img class="product-image" src="<?php echo htmlspecialchars($row['image']); ?>"
                     alt="<?php echo htmlspecialchars($row['title']); ?>">
                <h3 class="product-title"><?php echo htmlspecialchars($row['title']); ?></h3>
                <p class="product-description"><?php echo htmlspecialchars($row['discription']); ?></p>
                <p class="product-price">ETB <?php echo number_format($row['price'], 2); ?></p>
                <p class="product-quantity">Available: <?php echo $row['quantity']; ?></p>
                <?php if ($row['quantity'] <= 0): ?>
                    <button class="buy-button disabled" disabled style="background-color: grey;">Out of Stock</button>
                <?php elseif (isset($_SESSION['email'])): ?>
                    <a href="index.php?buy=<?php echo $row['id']; ?>" class="buy-button">Buy Now</a>
                <?php else: ?>
                    <a href="register.php" class="buy-button">Login to Buy</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div>
            <div class="footer-logo">EcomStore</div>
            <p>Your one-stop shop for all your needs.</p>
        </div>
        <div>
            <h3 class="footer-heading">Quick Links</h3>
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <a href="contact.php">Contact</a>
                <a href="register.php">Login</a>
            </div>
        </div>
        <div>
            <h3 class="footer-heading">Customer Service</h3>
            <div class="footer-links">
                <a href="#">FAQs</a>
                <a href="#">Shipping Policy</a>
                <a href="#">Returns & Refunds</a>
                <a href="#">Track Order</a>
            </div>
        </div>
        <div class="footer-contact">
            <h3 class="footer-heading">Contact Us</h3>
            <p><i class="fas fa-map-marker-alt"></i> Debre Birhan, Ethiopia</p>
            <p><i class="fas fa-phone"></i> +251 961965837</p>
            <p><i class="fas fa-envelope"></i> amanmarkos582@gmail.com</p>
            <div class="social-links">
                <a href="https://linkedin.com/in/amanuel-neby-b38275367" class="social-link" target="_blank"><i class="fab fa-linkedin"></i></a>
                <a href="https://github.com/aman-2121" class="social-link" target="_blank"><i class="fab fa-github"></i></a>
                <a href="https://facebook.com/Aman" class="social-link" target="_blank"><i class="fab fa-facebook"></i></a>
                <a href="https://t.me/Aman_vx" class="social-link" target="_blank"><i class="fab fa-telegram"></i></a>
                <a href="mailto:amanmarkos582@gmail.com" class="social-link"><i class="fas fa-envelope"></i></a>
            </div>
        </div>
    </div>
</footer>

<div class="copyright">
    &copy; <?php echo date('Y'); ?> EcomStore. All rights reserved.
</div>
</body>
</html>
