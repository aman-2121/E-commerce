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
                    <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="add_product.php"><i class="fas fa-plus-circle"></i> Add Products</a></li>
                    <li><a href="display_product.php"><i class="fas fa-eye"></i> View Products</a></li>
                    <li><a href="all_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li><a href="admin_messages.php"><i class="fas fa-envelope"></i> Messages</a></li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="logout">
        <div class="login_header">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        
        <div class="info">
            <h1>Update Product</h1>
            <?php if (!empty($message)): ?>
                <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="update-form">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                
                <div class="products">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                </div>
                
                <div class="products">
                    <label for="description">Description</label>
                    <textarea name="discription" id="description" required><?php echo htmlspecialchars($row['discription']); ?></textarea>
                </div>
                
                <div class="products">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($row['price']); ?>" step="0.01" min="0" required>
                </div>
                
                <div class="products">
                    <label for="quantity">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" min="0" required>
                </div>
                
                <div class="products image-preview">
                    <label>Current Image</label>
                    <div class="current-image">
                        <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Current Product Image">
                        <span class="image-text">Current Image</span>
                    </div>
                </div>
                
                <div class="products">
                    <label for="new_image">Change Image (Optional)</label>
                    <input type="file" name="image" id="new_image" accept="image/*">
                    <small class="file-hint">Leave blank to keep current image</small>
                </div>
                
                <div class="form-actions">
                    <input type="submit" name="update_product" value="Update Product" class="update-btn">
                    <a href="display_product.php" class="cancel-btn">Cancel</a>
                </div>
            </form> 
        </div>
    </div>
</body>
</html>