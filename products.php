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

if(isset($_GET['search'])) {
    $search_value = $_GET['my_search'];
    $sql = "SELECT * from product where concat(title, discription) LIKE '%$search_value%'";
    $result = mysqli_query($conn, $sql);
} else {
    $sql = "SELECT * from product";
    $result = mysqli_query($conn, $sql);  
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Products - E-Commerce Store</title>
    <link rel="stylesheet" href="userstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
 <nav>
    <input type="checkbox" id="check">
    <label for="check" class="checkbtn">
        <i class="fas fa-bars"></i>
    </label>
    <a href="index.php" class="logo">EcomStore</a>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="products.php" class="active">Products</a></li>
        <li><a href="contact.php">Contact</a></li>
        <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
            <li><a class="rigi" href="my_orders.php?email=<?php echo $_SESSION['email'] ?>">My Orders</a></li>
            <li><a class="rigi" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a class="rigi" href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
 </nav>
 
 <div class="search-container" style="margin-top: 100px;">
    <form action="" method="GET" class="search-form">
        <input type="text" name="my_search" placeholder="Search products..." class="search-input" value="<?php echo isset($_GET['my_search']) ? htmlspecialchars($_GET['my_search']) : '' ?>">
        <button type="submit" name="search" class="search-button">
            <i class="fas fa-search"></i> Search
        </button>
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
            <p class="product-price">$<?php echo number_format($row['price'], 2); ?></p>
            <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
                <a href="order.php?id=<?php echo $row['id']?>&email=<?php echo $_SESSION['email'] ?>" class="buy-button">Buy Now</a>
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
            <p><i class="fas fa-envelope"></i> contact@ecomstore.com</p>
            <div class="social-links">
            <a href="https://linkedin.com/in/yourprofile" class="social-link" target="_blank" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
    <a <a href="https://github.com/aman-2121"  class="social-link"  target="_blank">
        <i class="fab fa-github"></i> 
      </a>
               
                <a href="https://facebook.com/Aman" class="social-link" target="_blank" title="Facebook"><i class="fab fa-facebook"></i></a>
    <a href="https://t.me/Aman_vx" target="_blank" class="social-link" title="Message me on Telegram">
        <i class="fab fa-telegram"></i> 
      </a>
    <a href="mailto:amanmarkos582@gmail.com" class="social-link" title="Email"><i class="fas fa-envelope"></i></a>
            </div>
        </div>
    </div>
 </footer>
 
 <div class="copyright">
    &copy; <?php echo date('Y'); ?> EcomStore. All rights reserved.
 </div>
</body>
</html>