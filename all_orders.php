<?php
session_start();

// Verify admin authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: registere.php");
    exit();
}

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecomerce_php';

// Handle cancel action
if (isset($_GET['cancel_id'])) {
    $conn = mysqli_connect($host, $username, $password, $database);
    if (!$conn) {
        die("Database connection failed: " . mysqli_connect_error());
    }
    
    $cancel_id = (int)$_GET['cancel_id'];
    $update_query = "UPDATE orders SET status = 'cancelled' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "i", $cancel_id);
    mysqli_stmt_execute($stmt);
    mysqli_close($conn);
    
    header("Location: all_orders.php");
    exit();
}

// Main database connection
$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Enhanced query to filter valid orders
$query = "SELECT 
            id,
            username,
            email,
            title,
            price,
            image,
            status,
            address,
            phone
          FROM orders
          WHERE price > 0 
          AND title IS NOT NULL 
          AND title != ''
          AND image IS NOT NULL
          AND image != ''
          ORDER BY id DESC";

$result = mysqli_query($conn, $query);
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
    <style>
        .status {
            padding: 5px 10px;
            border-radius: 3px;
            font-weight: 500;
            display: inline-block;
            min-width: 90px;
            text-align: center;
        }
        .pending { background-color: #fff3cd; color: #856404; }
        .processing { background-color: #cce5ff; color: #004085; }
        .shipped { background-color: #e2e3e5; color: #383d41; }
        .delivered { background-color: #d4edda; color: #155724; }
        .cancelled { background-color: #f8d7da; color: #721c24; }
        .action-btns { display: flex; gap: 10px; flex-wrap: wrap; }
        .update { 
            color: #fff;
            background-color: #28a745;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
        }
        .cancel { 
            color: #fff;
            background-color: #dc3545;
            padding: 5px 10px;
            border-radius: 4px;
            text-decoration: none;
        }
        .product-img-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .product-img {
            max-width: 50px;
            max-height: 50px;
            border-radius: 4px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <!-- Navigation remains unchanged -->
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
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): 
                        $price = isset($row['price']) ? (float)$row['price'] : 0.00;
                        $status = strtolower($row['status'] ?? 'pending');
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($row['username']); ?></strong><br>
                            <?php echo htmlspecialchars($row['email']); ?><br>
                            <?php echo htmlspecialchars($row['phone']); ?>
                        </td>
                        <td>
                            <div class="product-img-container">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['image']); ?>" 
                                         alt="<?php echo htmlspecialchars($row['title']); ?>" 
                                         class="product-img">
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($row['title']); ?></span>
                            </div>
                        </td>
                        <td>$<?php echo number_format($price, 2); ?></td>
                        <td>
                            <span class="status <?php echo $status; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td class="action-btns">
                            <?php if ($status !== 'cancelled' && $status !== 'delivered'): ?>
                                <a href="update_order.php?action=ship&id=<?php echo $row['id']; ?>" class="update">
                                    <i class="fas fa-truck"></i> Deliver
                                </a>
                                <a href="all_orders.php?cancel_id=<?php echo $row['id']; ?>" 
                                   class="cancel"
                                   onclick="return confirm('Are you sure you want to cancel order #<?php echo $row['id']; ?>?')">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            <?php elseif ($status === 'shipped'): ?>
                                <a href="update_order.php?action=deliver&id=<?php echo $row['id']; ?>" class="update">
                                    <i class="fas fa-check"></i> Deliver
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>