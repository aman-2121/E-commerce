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
$my_email = $_SESSION['u_email'] ?? null;
$user_email = $_GET['email'] ?? null;

if ($my_email) {
    // User viewing their own orders (from session)
    $sql = "SELECT * FROM orders WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $my_email);
    $stmt->execute();
    $result = $stmt->get_result();
} elseif ($user_email) {
    // Admin viewing specific user's orders (from URL parameter)
    $sql = "SELECT * FROM orders WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // No email provided - redirect to register page
    header("Location: registere.php");
    exit();
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="home.css">

</head>
<body>
<nav>
    <input type="checkbox"  id="check">
    <label for="check"  class="checkbtn" >
        <i class="fa fa-bars"></i>
    </label>
    <label class="logo">Ecom</label>
    <ul class="w">
        <li>
            <a href="index.php">home</a>
        <li> 
         <li>  
            <a href="products.php">products</a>
        </li> 
        <li>
            <a href="contact">contact</a>
        </li>
   <?php
    if(isset($_SESSION['email']) && !empty($_SESSION['email']))
    {
    ?>  
    <p>
    <a class="rigi" href="user_order.php?email=<?php echo $_SESSION['email'] ?>">Orders</a>
        <a class="rigi" href="logout.php">Logout</a>
    </p>
    <?php
    }
    else
    {
    ?>
    <a class="rigi" href="registere.php">register</a>
    <?php
    }
    ?>
        

    </ul>
 </nav>
 <table>
    <tr>
        <th>product title</th>
        <th>price </th>
        <th>image </th>
    </tr>
    <?php
while($row=mysqli_fetch_assoc($result))
{
?>
<tr>
        <td><?php echo $row['title']?> </td>
        <td><?php echo $row['price']?></td>
        <td>
        <img src="<?php echo htmlspecialchars
        ($row['image']); ?>" alt="Product Image" width="100">
        </td>
    </tr>

<?php
}

    ?>
    
 </table>
 
</body>
</html>