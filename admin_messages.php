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
    <title>Admin Page</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin">
        <div class="add">
            <h1>Ecommerce Admin</h1>
            <ul>
                <li><a href="admin.php">dashboard</a></li>
                <li><a href="users.php">users</a></li>
                <li><a href="add_product.php">Add Products</a></li>
                <li><a href="display_product.php">views products</a></li>
                <li>
                    <a href="all_orders.php">orders</a>
                </li>
                <li>
                    <a href="admin_messages.php">messages</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="logout">
        <div class="login_header">
            <a href="logout.php">logout</a>
        </div>
        <div class="info">
            
        <div class="card">
    <div class="tot">
        <h3>User Messages</h3>
        <?php while ($msg = mysqli_fetch_assoc($msg_result)) { ?>
            <div style="border:1px solid #ccc; padding:10px; margin:10px;">
                <strong><?php echo htmlspecialchars($msg['name']); ?></strong> (<?php echo htmlspecialchars($msg['email']); ?>)<br>
                <small><?php echo $msg['sent_at']; ?></small>
                <p><?php echo nl2br(htmlspecialchars($msg['message'])); ?></p>
            </div>
        <?php } ?>
    </div>
</div>

        </div>
    </div>
</body>
</html>

