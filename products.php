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

// Buy logic
if (isset($_GET['buy']) && isset($_SESSION['email'])) {
    $productId = intval($_GET['buy']);
    $email = $_SESSION['email'];

    // Get product details
    $stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();

    if ($product && $product['quantity'] > 0) {
        // Get user details from session or database
        $username = $_SESSION['username'] ?? 'User';
        $phone = $_SESSION['phone'] ?? 'Unknown';
        $address = $_SESSION['address'] ?? 'Unknown';
        $status = 'pending';

        // Start transaction
        $conn->begin_transaction();

        try {
            // Insert order
            $insert = $conn->prepare("INSERT INTO orders (title, price, image, username, email, phone, address, status) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $insert->bind_param("sdssssss", $product['title'], $product['price'], $product['image'], 
                              $username, $email, $phone, $address, $status);
            $insert->execute();

            // Decrease quantity
            $update = $conn->prepare("UPDATE product SET quantity = quantity - 1 WHERE id = ? AND quantity > 0");
            $update->bind_param("i", $productId);
            $update->execute();

            if ($update->affected_rows === 0) {
                throw new Exception("Failed to update product quantity");
            }

            $conn->commit();
            echo "<script>alert('Order placed successfully!'); window.location='products.php';</script>";
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Error processing your order. Please try again.'); window.location='products.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('This product is out of stock!'); window.location='products.php';</script>";
        exit;
    }
}

// Product search or all
if (isset($_GET['search'])) {
    $search_value = $_GET['my_search'];
    $sql = "SELECT * FROM product WHERE CONCAT(title, discription) LIKE '%$search_value%'";
    $result = $conn->query($sql);
} else {
    $sql = "SELECT * FROM product";
    $result = $conn->query($sql);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Our Products - E-Commerce Store</title>
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
        <li><a href="products.php" class="active">Products</a></li>
        <li><a href="contact.php">Contact</a></li>
        <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
            <li><a class="rigi" href="my_orders.php?email=<?php echo $_SESSION['email'] ?>">My Orders</a></li>
            <li><a class="rigi" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a class="rigi" href="register.php">Login</a></li>
        <?php endif; ?>
    </ul>
</nav>

<div class="search-container" style="margin-top: 100px;">
    <form action="" method="GET" class="search-form">
        <input type="text" name="my_search" placeholder="Search products..." class="search-input" value="<?php echo isset($_GET['my_search']) ? htmlspecialchars($_GET['my_search']) : '' ?>">
        <button type="submit" name="search" class="search-button"><i class="fas fa-search"></i> Search</button>
    </form>
</div>

<h2 class="section-title">Our Products</h2>
<div class="card-container">
    <div class="card-grid">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="product-card">
            <img class="product-image" src="<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
            <h3 class="product-title"><?php echo htmlspecialchars($row['title']); ?></h3>
            <p class="product-description"><?php echo htmlspecialchars($row['discription']); ?></p>
            <p class="product-price">ETB <?php echo number_format($row['price'], 2); ?></p>
            <p class="product-stock">Available: <?php echo $row['quantity']; ?></p>
            <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
                <?php if($row['quantity'] > 0): ?>
                    <a href="products.php?buy=<?php echo $row['id'] ?>" class="buy-button">Buy Now</a>
                <?php else: ?>
                    <button class="buy-button" disabled>Out of Stock</button>
                <?php endif; ?>
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
            <p>Your one-stop shop for all your needs. Quality products at affordable prices.</p>
        </div>
        <div>
            <h3 class="footer-heading">Quick Links</h3>
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="products.php">Products</a>
                <a href="contact.php">Contact</a>
                <a href="register.php">Register</a>
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
            <p><i class="fas fa-map-marker-alt"></i> Addis Ababa, Ethiopia</p>
            <p><i class="fas fa-phone"></i> +251 961965837</p>
            <p><i class="fas fa-envelope"></i> amanmarkos@gmail.com</p>
            <div class="social-links">
                <a href="https://www.linkedin.com/in/amanuel-neby-b38275367" target="_blank" class="social-link"><i class="fab fa-linkedin"></i></a>
                <a href="https://github.com/aman-2121" target="_blank" class="social-link"><i class="fab fa-github"></i></a>
                <a href="https://facebook.com/Aman" target="_blank" class="social-link"><i class="fab fa-facebook"></i></a>
                <a href="https://t.me/Aman_vx" target="_blank" class="social-link"><i class="fab fa-telegram"></i></a>
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