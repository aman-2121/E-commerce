<?php
session_start();
$_SESSION['u_email']=$_GET['email'];


$host = 'localhost';
$dbname = 'ecomerce_php';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$p_id=$_GET['id'];
$u_email=$_GET['email'];




    $sql="SELECT * from product where id='$p_id'";

    $p_result =mysqli_query($conn,$sql);
    $p_row=mysqli_fetch_assoc($p_result);
   

    $p_title =$p_row['title'];
    $p_price =$p_row['price'];
    $p_image =$p_row['image'];

    $u_sql="SELECT * from register_table where email='$u_email'";
    $u_result =mysqli_query($conn,$u_sql);
    $u_row=mysqli_fetch_assoc($u_result);


    $u_name=$u_row['name'];
    $u_email=$u_row['email'];
    $u_phone=$u_row['phone'];
    $u_address=$u_row['address'];
    $status="in progress";
   


$order_sql="INSERT into orders(title,price,image,username,email,phone,address,status)
VALUES('$p_title','$p_price','$p_image','   $u_name','$u_email',' $u_phone','$u_address','$status')";

$order_result =mysqli_query($conn,$order_sql);

if($order_result)
{
    $_SESSION['u_email']=$_GET['email'];
    
    header("location:user_order.php");



}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - E-Commerce Store</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        <li><a href="contact.php" class="active">Contact</a></li>
        <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
            <li><a class="rigi" href="my_orders.php?email=<?php echo $_SESSION['email'] ?>">Orders</a></li>
            <li><a class="rigi" href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a class="rigi" href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
 </nav>
        </body>
        </html>