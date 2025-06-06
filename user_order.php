<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'ecomerce_php';
$username = 'root';
$password = '';

// Establish database connection
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->error);
}

// Initialize variables
$my_email = $_SESSION['email'] ?? null;
$user_email = $_GET['email'] ?? null;
$result = null;

// Price formatting function
function format_price($price) {
    $price = (float)$price;
    return '$' . number_format($price, 2);
}

if ($my_email || $user_email) {
    $email = $my_email ?: $user_email;
    
    // SQL query based on your actual table columns
    $sql = "SELECT 
                id,
                title,
                price,
                image,
                status
            FROM orders
            WHERE email = ? 
            AND price > 0 
            AND title IS NOT NULL 
            AND title != ''
            AND image IS NOT NULL
            AND image != ''";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    
    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $orders_exists = $result->num_rows > 0;
} else {
    header("Location: registere.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - EcomStore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6bff;
            --secondary-color: #6c757d;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --border-radius: 8px;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f5f5f5;
        }

        /* Navigation */
        nav {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        nav ul {
            display: flex;
            list-style: none;
        }

        nav ul li {
            margin-left: 1.5rem;
        }

        nav ul li a {
            padding: 0.5rem 0;
            position: relative;
            transition: var(--transition);
            text-decoration: none;
            color: #333;
        }

        nav ul li a:hover {
            color: var(--primary-color);
        }

        .rigi {
            background-color: var(--primary-color);
            color: white !important;
            padding: 0.5rem 1rem !important;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .rigi:hover {
            background-color: #3a56d4;
            transform: translateY(-2px);
            box-shadow: var(--box-shadow);
        }

        .checkbtn {
            font-size: 1.5rem;
            color: var(--primary-color);
            cursor: pointer;
            display: none;
        }

        #check {
            display: none;
        }

        /* Orders Content */
        .orders-container {
            max-width: 1200px;
            margin: 100px auto 2rem;
            padding: 2rem;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .orders-title {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
            color: var(--dark-color);
            position: relative;
            padding-bottom: 0.5rem;
        }

        .orders-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--primary-color);
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }

        .orders-table th {
            background-color: var(--primary-color);
            color: white;
            padding: 1rem;
            text-align: left;
        }

        .orders-table td {
            padding: 1rem;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }

        .orders-table tr:hover {
            background-color: var(--light-color);
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: var(--transition);
        }

        .product-image:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .order-price {
            color: var(--primary-color);
            font-weight: 600;
        }

        .no-orders {
            text-align: center;
            padding: 2rem;
            color: var(--secondary-color);
        }

        /* Status styling */
        .status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
            text-transform: capitalize;
            display: inline-block;
            min-width: 80px;
            text-align: center;
        }
        .pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .completed {
            background-color: #d4edda;
            color: #155724;
        }
        .cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            nav {
                padding: 1rem;
            }
            
            .checkbtn {
                display: block;
            }
            
            nav ul {
                position: fixed;
                width: 100%;
                height: 100vh;
                background-color: white;
                top: 70px;
                left: -100%;
                text-align: center;
                flex-direction: column;
                transition: var(--transition);
                padding-top: 2rem;
            }
            
            nav ul li {
                margin: 1rem 0;
            }
            
            #check:checked ~ ul {
                left: 0;
            }
            
            .orders-container {
                margin: 80px auto 1rem;
                padding: 1rem;
            }
            
            .orders-table {
                display: block;
                overflow-x: auto;
            }
            
            .orders-table th, 
            .orders-table td {
                padding: 0.75rem;
            }
            
            .product-image {
                width: 60px;
                height: 60px;
            }
        }
    </style>
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
            <li><a href="contact.php">Contact</a></li>
            <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
                <li><a class="rigi" href="user_order.php?email=<?php echo $_SESSION['email'] ?>">Orders</a></li>
                <li><a class="rigi" href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a class="rigi" href="registere.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="orders-container">
        <h1 class="orders-title">My Orders</h1>
        
        <?php if($orders_exists): ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title'] ?? 'Unknown Product'); ?></td>
                        <td class="order-price"><?php echo format_price($row['price'] ?? 0); ?></td>
                        <td>
                            <?php if(!empty($row['image'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($row['title'] ?? 'Product Image'); ?>" 
                                     class="product-image">
                            <?php else: ?>
                                <span>No Image</span>
                            <?php endif; ?>
                        </td>
                      <td>
          <span class="status <?php echo strtolower(htmlspecialchars($row['status'] ?? 'pending')); ?>">
           In Progress <!-- This will always show "In Progress" -->
           (<?php echo htmlspecialchars($row['status'] ?? 'Pending'); ?>) <!-- This shows the actual status in parentheses -->
                </span>
             </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-orders">
                <i class="fas fa-box-open fa-3x" style="color: var(--secondary-color); margin-bottom: 1rem;"></i>
                <h3>No Orders Found</h3>
                <p>You haven't placed any orders yet.</p>
                <a href="products.php" class="rigi" style="display: inline-block; margin-top: 1rem;">
                    <i class="fas fa-shopping-bag"></i> Shop Now
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>