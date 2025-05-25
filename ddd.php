


<!-- home -->


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

if(isset($_GET['search']))

{
    $search_value=$_GET['my_search'];

    $sql="SELECT * from product where concat(title,discription) LIKE '%$search_value%'";

    $result =mysqli_query($conn,$sql);
}
else{
  $sql="SELECT * from product";
$result = mysqli_query($conn,$sql);  
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="hme.css">
    <style>

    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    
</style>
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
            <a href="homme.php">home</a>
        <li> 
         <li>  
            <a href="product.php">products</a>
        </li> 
        <li>
            <a href="contact.php">contact</a>
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
 <div>
    <img class="cover" src="https://images.unsplash.com/photo-1557821552-17105176677c?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTB8fGUlMjBjb21tZXJjZXxlbnwwfHwwfHx8MA%3D%3D" alt="">
 </div> 
   <h3>products</h3>
   <div class="search-container">
    <form action="" method="GET" class="search-form">
        <input type="text" name="my_search" placeholder="Search your products..." class="search-input">
        <button type="submit" name="search" class="search-button">Search</button>
    </form>
</div>
 <div class="card">
  <?php
  while($row=mysqli_fetch_assoc($result))
  {
    ?>

<div class="shose">
<img class="img" src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" height="70" width="100">

      <h4><?php echo $row['title']?></h4>
      <p><?php echo $row['discription']?></p>
        <p class="price">price:<?php echo $row['price']?></p>
        <?php
if(isset($_SESSION['email']) && !empty($_SESSION['email']))
{
?>

           
           <div class="aman"> <a href="my_orders.php?id=<?php echo $row['id']?> & email=<?php echo $_SESSION['email'] ?>">buy now</a></div> 
           <?php 
        }


        else{
            ?>
            <div class="aman"> <a href="registere.php">buy now</a></div> 
            <?Php
        }
?>

       
</div>

<?php
  }
  ?>
 </div>
   

 <div>
    <div class="eco"><h3>E-comerce</h3></div>
    <div class="foot">
        <div class="footer_content">
            <div>
                <h4>service</h4>
                <p><a href="#">wed developer</a></p>
                <p><a href="#">app developer</a></p>
                <p><a href="#">degital market</a></p>
            </div>
            
            <div>
                <h4>social link</h4>
                <p><a href="#">face book</a></p>
                <p><a href="#">instagram</a></p>
                <p><a href="#">twitter</a></p>
            </div>
            
            <div>
                <h4>quick link</h4>
                <p><a href="homme.php">home</a></p>
                <p><a href="product.php">products</a></p>
                <p><a href="contact.php">contact</a></p>
                <p><a href="registere.php">register</a></p>
               
            </div>

            <div>
                <h4>location</h4>
                <p>addis abeba</p>
                <p>debre markos</p>
                <p>debre birhan</p>
                <p>Email: amanmarkos582@gmail.com</p>
                <p>Phone: +251961965837</p>
            </div>
        </div>
    </div>
</div>

<footer class="markos">
© 2017 E-commerce. All rights reserved
</footer>
</body>
</html>




<!-- product -->
 
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

if(isset($_GET['search']))

{
    $search_value=$_GET['my_search'];

    $sql="SELECT * from product where concat(title,discription) LIKE '%$search_value%'";

    $result =mysqli_query($conn,$sql);
}
else{
  $sql="SELECT * from product";
$result = mysqli_query($conn,$sql);  
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="home.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
            <a href="homme.php">home</a>
        <li> 
         <li>  
            <a href="product.php">products</a>
        </li> 
        <li>
            <a href="contact.php">contact</a>
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
 
   <h3>products</h3>
   <div class="search-container">
    <form action="" method="GET" class="search-form">
        <input type="text" name="my_search" placeholder="Search your products..." class="search-input">
        <button type="submit" name="search" class="search-button">Search</button>
    </form>
</div>
 <div class="card">
  <?php
  while($row=mysqli_fetch_assoc($result))
  {
    ?>

<div class="shose">
<img class="img" src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" height="70" width="100">

      <h4><?php echo $row['title']?></h4>
      <p><?php echo $row['discription']?></p>
        <p class="price">price:<?php echo $row['price']?></p>
        <?php
if(isset($_SESSION['email']) && !empty($_SESSION['email']))
{
?>

           
           <div class="aman"> <a href="my_orders.php?id=<?php echo $row['id']?> & email=<?php echo $_SESSION['email'] ?>">buy now</a></div> 
           <?php 
        }


        else{
            ?>
            <div class="aman"> <a href="registere.php">buy now</a></div> 
            <?Php
        }
?>

       
</div>

<?php
  }
  ?>
 </div>
   

 <div>
    <div class="eco"><h3>E-comerce</h3></div>
    <div class="foot">
        <div class="footer_content">
            <div>
                <h4>service</h4>
                <p><a href="#">wed developer</a></p>
                <p><a href="#">app developer</a></p>
                <p><a href="#">degital market</a></p>
            </div>
            
            <div>
                <h4>social link</h4>
                <p><a href="#">face book</a></p>
                <p><a href="#">instagram</a></p>
                <p><a href="#">twitter</a></p>
            </div>
            
            <div>
                <h4>quick link</h4>
                <p><a href="homme.php">home</a></p>
                <p><a href="product.php">products</a></p>
                <p><a href="contact.php">contact</a></p>
                <p><a href="registere.php">register</a></p>
              
            </div>

            <div>
                <h4>location</h4>
                <p>addis abeba</p>
                <p>debre markos</p>
                <p>debre birhan</p>
                <p>Email: amanmarkos582@gmail.com</p>
                <p>Phone: +251961965837</p>
            </div>
        </div>
    </div>
</div>

<footer class="markos">
© 2017 E-commerce. All rights reserved
</footer>
</body>
</html>

<!-- contact -->
<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecomerce_php';

$conn = mysqli_connect($host, $username, $password, $database);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO messages (name, email, message) VALUES ('$name', '$email', '$message')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Message sent successfully!');</script>";
    } else {
        echo "<script>alert('Error sending message');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Contact Us</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>

    <link rel="stylesheet" href="homjjje.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
            <a href="product.php">products</a>
        </li> 
        <li>
            <a href="contact.php">contact</a>
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

<div class="contact-container">
    <h2>Contact us</h2>

  

    <form method="POST" action="contact.php">
        <label>Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Message:</label><br>
        <textarea name="message" rows="5" required></textarea><br><br>

        <button type="submit">Send Message</button>
    </form> 
    
    <div class="admin-info">
        <p><strong>Phone:</strong> +251 961965837</p>
        <p><strong>Email:</strong> amanmarkos582@gmail.com</p>
        <p><strong>Location:</strong> D/markos, Ethiopia</p>

        <div class="social-links">
            <a href="https://facebook.com" target="_blank">Facebook</a> |
            <a href="https://twitter.com" target="_blank">Twitter</a> |
            <a href="https://instagram.com" target="_blank">Instagram</a>
        </div>
    </div>
</div>

</body>
</html>

