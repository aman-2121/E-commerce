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

if(isset($_GET['id']))
{
    $id=$_GET['id'];
    $del="DELETE from product where id='$id'";
    $data=mysqli_query($conn, $del);
    if($data){
     header("location:display_product.php");

    }
}


$query = "SELECT * FROM product";
$result = mysqli_query($conn, $query);
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
            <h2>All Products</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Image</th>
                    <th>Delete</th>
                    <th>Update</th>
                </tr> 
                <?php
                while($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['discription']); ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['quantity']; ?></td>
                        <td>
                            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" width="100">
                        </td>
                        <td>
                            <a onclick="return confirm('are you sure to delete this');" class="delete" href="display_product.php?id=<?php echo $row['id']; ?>" >Delete</a>
                        </td>
                        <td >
                            <a class="update" href="update_product.php?id=<?php echo $row['id'] ?>">Update</a>
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