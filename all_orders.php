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
    header("Location: registere.php"); // Redirect to login if not logged in or not an admin
    exit();
}



$sql="SELECT * from orders";
$result=mysqli_query($conn,$sql);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
   <link rel="stylesheet" href="admin.css">
</head>
<style>

</style>
<body>
    <div class="admin">
        <div class="add">
            <h1>Ecommerce Admin</h1>
            <ul>
                <li>
                    <a href="admin.php">dashboard</a>
                </li>
                <li>
                    <a href="users.php">users</a>
                </li>
                <li>
                    <a href="add_product.php">Add Products</a>
                </li>
                <li>
                    <a href="display_product.php">views products</a>
                </li>
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
           <h1>All orders</h1>
           <table>
            <tr>
                <th>Custumer Name</th>
                <th>Email</th>
                <th>Address</th>
                <th>Phone</th>
                <th>Product Title</th>
                <th>Price</th>
                <th>Image</th>
                <th>Status</th>
                <th>Change Status</th>
            </tr>
            <?php
            while ($row=mysqli_fetch_assoc($result)) 
            {
                ?>
                <tr>
                <td><?php echo $row['username'] ?></td>
                <td><?php echo $row['email'] ?></td>
                <td><?php echo $row['address'] ?></td> 
                <td><?php echo $row['phone'] ?></td>
                <td><?php echo $row['title'] ?></td>
                <td><?php echo $row['price'] ?></td>
                <td>
                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" width="100">
                </td>
                <td><?php echo $row['status'] ?></td>
               <td>
                <a class="update" href="update_order.php?id=<?php echo $row['id'] ?>">Delivered</a>
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