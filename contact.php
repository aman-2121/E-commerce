<?php
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecomerce_php';

$conn = mysqli_connect($host, $username, $password, $database);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO messages (name, email, message) VALUES ('$name', '$email', '$message')";
    if (mysqli_query($conn, $sql)) {
        $success = "Message sent successfully!";
    } else {
        $error = "Error sending message: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - E-Commerce Store</title>
    <link rel="stylesheet" href="stylee.css">
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
        <li><a href="products.php">Products</a></li>
        <li><a href="contact.php" class="active">Contact</a></li>
        <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
            <li><a class="rigi" href="my_orders.php?email=<?php echo $_SESSION['email'] ?>">My Orders</a></li>
            <li><a class="rigi" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a class="rigi" href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
 </nav>

<div class="contact-container">
    <h2 class="contact-title">Contact Us</h2>
    
    <?php if(isset($success)): ?>
        <div style="background: #4CAF50; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
        <div style="background: #f44336; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="contact-info">
        <p><strong><i class="fas fa-info-circle"></i> Need help or have questions?</strong> Our team is here to assist you with any inquiries you may have.</p>
        <p><i class="fas fa-phone"></i> <strong>Phone:</strong> +251 961965837</p>
        <p><i class="fas fa-envelope"></i> <strong>Email:</strong> contact@ecomstore.com</p>
        <p><i class="fas fa-map-marker-alt"></i> <strong>Address:</strong> Addis Ababa, Ethiopia</p>
   
    </div>

    <form method="POST" action="contact.php" class="contact-form">
        <div class="form-group">
            <label for="name" class="form-label">Your Name</label>
            <input type="text" id="name" name="name" class="form-input" required>
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label">Your Email</label>
            <input type="email" id="email" name="email" class="form-input" required>
        </div>
        
        <div class="form-group">
            <label for="message" class="form-label">Your Message</label>
            <textarea id="message" name="message" class="form-textarea" required></textarea>
        </div>
        
        <button type="submit" class="submit-button">
            <i class="fas fa-paper-plane"></i> Send Message
        </button>
    </form> 
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
            <p><i class="fas fa-map-marker-alt"></i> debre birhan, Ethiopia</p>
            <p><i class="fas fa-phone"></i> +251 961965837</p>
            <p><i class="fas fa-envelope"></i> amanmarkos582@gmail.com</p>
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