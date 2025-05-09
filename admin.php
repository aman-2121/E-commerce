<?php
session_start(); // Start the session
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecomerce_php';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: register.php"); // Redirect to login if not logged in or not an admin
    exit();
}


 $user_sql="SELECT * from register_table";
 $u_result=mysqli_query($conn,$user_sql);
 $total_user =mysqli_num_rows($u_result);


 /*total products*/
 $product_sql="SELECT * from product";
 $p_result=mysqli_query($conn,$user_sql);
 $total_product =mysqli_num_rows($p_result);

 
 /*total orders*/
 $order_sql="SELECT * from orders";
 $o_result=mysqli_query($conn,$order_sql);
 $total_order =mysqli_num_rows($o_result);

 /*total delivered*/
 $delivered_sql="SELECT * from orders where status ='delivered'";
 $d_result=mysqli_query($conn,$delivered_sql);
 $total_delivered =mysqli_num_rows($d_result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
                    <li>
                        <a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li>
                        <a href="users.php"><i class="fas fa-users"></i> Users</a>
                    </li>
                    <li>
                        <a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Products</a>
                    </li>
                    <li>
                        <a href="display_product.php"><i class="fas fa-eye"></i> View Products</a>
                    </li>
                    <li>
                        <a href="all_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                    </li>
                    <li>
                        <a href="admin_messages.php"><i class="fas fa-envelope"></i> Messages</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="logout">
        <div class="login_header">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <div class="info">
            <h1>Dashboard Overview</h1>
            
            <div class="card-container">
                <div class="card">
                    <div class="tot">
                        <h3>Total Users</h3>
                        <p><?php echo $total_user ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="tot">
                        <h3>Total Products</h3>
                        <p><?php echo $total_product ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="tot">
                        <h3>Total Orders</h3>
                        <p><?php echo $total_order ?></p>
                    </div>
                </div>

                <div class="card">
                    <div class="tot">
                        <h3>Total Delivered</h3>
                        <p><?php echo $total_delivered ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>