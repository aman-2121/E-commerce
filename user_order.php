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

// Initialize variables
$my_email = $_SESSION['email'] ?? null;
$user_email = $_GET['email'] ?? null;
$result = null;

// Price formatting function
function format_price($price) {
    $price = (float)$price;
    return '$' . number_format($price, 2);
}

if ($my_email) {
    $sql = "SELECT * FROM orders WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $my_email);
    $stmt->execute();
    $result = $stmt->get_result();
} elseif ($user_email) {
    $sql = "SELECT * FROM orders WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    header("Location: registere.php");
    exit();
}

$orders_exists = $result ? $result->num_rows > 0 : false;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - EcomStore</title>
    <link rel="stylesheet" href="userstyle.css">
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
}

nav ul li a:hover {
  color: var(--primary-color);
}

nav ul li a.active {
  color: var(--primary-color);
  font-weight: 600;
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

.order-status {
  display: inline-block;
  padding: 0.5rem 1rem;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 500;
}

.status-pending {
  background-color: #fff3cd;
  color: #856404;
}

.status-completed {
  background-color: #d4edda;
  color: #155724;
}

.status-processing {
  background-color: #cce5ff;
  color: #004085;
}

.no-orders {
  text-align: center;
  padding: 2rem;
  color: var(--secondary-color);
}

/* Order Actions */
.order-actions {
  display: flex;
  gap: 0.5rem;
}

.action-btn {
  padding: 0.5rem 1rem;
  border-radius: var(--border-radius);
  border: none;
  cursor: pointer;
  transition: var(--transition);
  font-size: 0.85rem;
}

.btn-view {
  background-color: var(--primary-color);
  color: white;
}

.btn-cancel {
  background-color: var(--danger-color);
  color: white;
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: var(--box-shadow);
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
  
  .order-actions {
      flex-direction: column;
  }
}

/* Animations */
@keyframes fadeIn {
  from {
      opacity: 0;
      transform: translateY(20px);
  }
  to {
      opacity: 1;
      transform: translateY(0);
  }
}

.orders-table tr {
  animation: fadeIn 0.5s ease forwards;
}

.orders-table tr:nth-child(1) { animation-delay: 0.1s; }
.orders-table tr:nth-child(2) { animation-delay: 0.2s; }
.orders-table tr:nth-child(3) { animation-delay: 0.3s; }
.orders-table tr:nth-child(4) { animation-delay: 0.4s; }
.orders-table tr:nth-child(5) { animation-delay: 0.5s; }
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
                        <th>Product Title</th>
                        <th>Price</th>
                        <th>Image</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']) ?></td>
                        <td class="order-price"><?php echo format_price($row['price']) ?></td>
                        <td>
                            <img src="<?php echo htmlspecialchars($row['image']) ?>" 
                                 alt="Product Image" 
                                 class="product-image">
                        </td>
                        <td>
                            <span class="order-status status-<?php echo strtolower(str_replace(' ', '-', $row['status'])) ?>">
                                <?php echo htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="order-actions">
                                <button class="action-btn btn-view">
                                    <i class="fas fa-eye"></i> View
                                </button>
                                <button class="action-btn btn-cancel">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
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

    <script>
        document.querySelectorAll('.btn-view').forEach(btn => {
            btn.addEventListener('click', function() {
                const productTitle = this.closest('tr').querySelector('td:first-child').textContent;
                alert(`Viewing details for: ${productTitle}`);
            });
        });

        document.querySelectorAll('.btn-cancel').forEach(btn => {
            btn.addEventListener('click', function() {
                if(confirm('Are you sure you want to cancel this order?')) {
                    const row = this.closest('tr');
                    row.style.opacity = '0.5';
                    row.style.backgroundColor = '#ffebee';
                    setTimeout(() => {
                        row.style.display = 'none';
                    }, 500);
                }
            });
        });
    </script>
</body>
</html>