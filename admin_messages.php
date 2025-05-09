<?php
$host = 'localhost';
$dbname = 'ecomerce_php';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$msg_sql = "SELECT * FROM messages ORDER BY sent_at DESC";
$msg_result = mysqli_query($conn, $msg_sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Messages</title>
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <input type="checkbox" id="check">
    <label for="check" class="checkbtn">
        <i class="fas fa-bars"></i>
    </label>
    
    <nav>
        <div class="admin">
            <div class="add">
                <h1>Ecommerce Admin</h1>
                <ul>
                    <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Products</a></li>
                    <li><a href="display_product.php"><i class="fas fa-eye"></i> View Products</a></li>
                    <li><a href="all_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="admin_messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="logout">
        <div class="login_header">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="info">
            <h1>Customer Messages</h1>
            <div class="card-container">
                <div class="card">
                    <div class="tot">
                        <h3>User Messages</h3>
                        <?php while ($msg = mysqli_fetch_assoc($msg_result)) { ?>
                            <div class="message-card">
                                <strong><?php echo htmlspecialchars($msg['name']); ?></strong> 
                                <span>(<?php echo htmlspecialchars($msg['email']); ?>)</span><br>
                                <small><?php echo $msg['sent_at']; ?></small>
                                <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>