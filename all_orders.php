<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: registere.php");
    exit();
}

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecomerce_php';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$query = "SELECT * FROM orders"; // Make sure this is your correct table name
$result = mysqli_query($conn, $query);

// Check if query was successful
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
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
            <h1>All Orders</h1>
            <table>
                <tr>
                    <th>Customer Name</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php
                while ($row = mysqli_fetch_assoc($result)) {
                    // Safely handle the price formatting
                    $price = isset($row['price']) && is_numeric($row['price']) ? (float)$row['price'] : 0.00;
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['address'] ?? ''); ?></td> 
                    <td><?php echo htmlspecialchars($row['phone'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($row['title'] ?? ''); ?></td>
                    <td>$<?php echo number_format($price, 2); ?></td>
                    <td>
                        <?php if (!empty($row['image'])): ?>
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
                        <?php else: ?>
                            <span>No Image</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="status <?php echo isset($row['status']) ? strtolower($row['status']) : 'pending'; ?>">
                            <?php echo htmlspecialchars($row['status'] ?? 'Pending'); ?>
                        </span>
                    </td>
                    <td>
                        <a class="update" href="update_order.php?id=<?php echo $row['id'] ?? ''; ?>">
                            <i class="fas fa-truck"></i> Delivered
                        </a>
                    </td>
                </tr>
                <?php
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>