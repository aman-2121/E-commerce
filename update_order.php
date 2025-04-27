<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecomerce_php';

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$order_id=$_GET['id'];
$delivered="Delivered";
$sql="UPDATE orders set status= '$delivered' where id='$order_id'";
$result=mysqli_query($conn,$sql);
if($result)
{
header("location:all_orders.php");
}


?>