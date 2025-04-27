<?php
session_start(); // Start the session
$host = 'localhost';
$dbname = 'ecomerce_php';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: registere.php"); // Redirect to login if not logged in or not an admin
    exit();
}

$is_user="user";

$sql="SELECT * from register_table where usertype= '$is_user'";
$result =mysqli_query($conn,$sql);
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
           <h1>All Users</h1>
           <table>
            <tr>
                <th>User Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
            </tr>
            <?php
            while($row=mysqli_fetch_assoc($result)){
                ?>

            <tr>
                <td><?php echo $row['name'] ?></td>
                <td><?php echo $row['email'] ?></td>
                <td><?php echo $row['phone'] ?></td>
                <td><?php echo $row['address'] ?></td>
            </tr>


                <?php
            }
            ?>
           
           </table>
        </div>
    </div>
</body>
</html>