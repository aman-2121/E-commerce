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
    die("Database connection failed");
}

$id = $_GET["id"];
$sql = "SELECT * FROM product WHERE id='$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);

if(isset($_POST["update_product"])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $description = $_POST['discription'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    
    $image = $row['image'];
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($fileExt, $allowedExts)) {
            $fileName = uniqid() . '.' . $fileExt;
            $targetPath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                if (file_exists($row['image'])) {
                    unlink($row['image']);
                }
                $image = $targetPath;
            }
        }
    }

    $update_sql = "UPDATE product SET 
                  title = '$title', 
                  discription = '$description', 
                  price = '$price', 
                  quantity = '$quantity', 
                  image = '$image' 
                  WHERE id = '$id'";
    
    if (mysqli_query($conn, $update_sql)) {
        header("Location: display_product.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
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
            <h1>Update Page</h1>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                
                <div class="products">
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo $row['title']; ?>" required>
                </div>
                
                <div class="products">
                    <label>Description</label>
                    <textarea name="discription" required><?php echo $row['discription']; ?></textarea>
                </div>
                
                <div class="products">
                    <label>Price</label>
                    <input type="number" name="price" value="<?php echo $row['price']; ?>" step="0.01" min="0" required>
                </div>
                
                <div class="products">
                    <label>Quantity</label>
                    <input type="number" name="quantity" value="<?php echo $row['quantity']; ?>" min="0" required>
                </div>
                
                <div class="products">
                    <label>Current Image</label>
                    <img src="<?php echo $row['image']; ?>" width="80" height="70">
                </div>
                
                <div class="products">
                    <label>Change Image</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                
                <div class="products">
                    <input type="submit" name="update_product" value="Update Product">
                </div>
            </form> 
        </div>
    </div>
</body>
</html>