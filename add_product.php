<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: register.php");
    exit();
}

$host = 'localhost';
$dbname = 'ecomerce_php';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_products'])) {
    $id= $_POST['id'];
    $title = trim($conn->real_escape_string($_POST['title']));
    $description = trim($conn->real_escape_string($_POST['description']));
    $price = $_POST['price'];
    $quantity = intval($_POST['quantity']);

    if (!is_numeric($price)) {
        $message = 'Price must be a valid number';
    }

    $image = '';
    if (isset($_FILES['image'])) {
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $check = getimagesize($_FILES['image']['tmp_name']);
            if ($check !== false) {
                $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
                
                if (in_array($fileExt, $allowedExts)) {
                    $fileName = uniqid() . '.' . $fileExt;
                    $targetPath = $uploadDir . $fileName;
                    
                    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                        $image = $targetPath;
                    } else {
                        $message = 'Error uploading file';
                    }
                } else {
                    $message = 'Invalid file type. Allowed: JPG, JPEG, PNG, GIF';
                }
            } else {
                $message = 'File is not a valid image';
            }
        } else {
            $message = 'Error in file upload: ' . $_FILES['image']['error'];
        }
    } else {
        $message = 'No image uploaded';
    }
    




    if (empty($message)) {
        if (!empty($title) && !empty($description) && !empty($price) && $quantity >= 0 && !empty($image)) {
            
            // Generate or fetch your manual ID (example: UUID)
            $id = uniqid(); // Or your custom ID logic
            
            // 1. Match placeholders (6?) to columns
            $stmt = $conn->prepare("INSERT INTO product (id, title, discription, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
            
            // 2. Critical error check
            if ($stmt === false) {
                die("SQL Error: " . $conn->error); // Reveals exact syntax issues
            }
            
            // 3. Correct type specifier (6 params: id + 5 others)
            // s=string, d=double, i=integer
            $stmt->bind_param("sssdis", $id, $title, $description, $price, $quantity, $image);
            
            if ($stmt->execute()) {
                $message = 'Product added! ID: ' . $id; // Use your manual ID
            } else {
                $message = 'Error: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $message = 'Please fill all fields correctly';
        }
    }




}
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
            <h1>Add Products</h1>
            <?php if (!empty($message)): ?>
                <p style="color: <?php echo strpos($message, 'successfully') !== false ? 'green' : 'red'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </p>
            <?php endif; ?>
            <div>
                <form action="add_product.php" method="POST" enctype="multipart/form-data">
                    <div class="products">
                        <label for="title">id</label>
                        <input type="text" name="id" id="id" required>
                    </div>
                    <div class="products">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" required>
                    </div>
                    <div class="products">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" required></textarea>
                    </div>
                    <div class="products">
                        <label for="price">Price</label>
                        <input type="number" name="price" id="price" step="0.01" min="0" required>
                    </div>
                    <div class="products">
                        <label for="quantity">Quantity</label>
                        <input type="number" name="quantity" id="quantity" min="0" required>
                    </div>
                    <div class="products">
                        <label for="image">Image</label>
                        <input type="file" name="image" id="image" accept="image/*" required>
                    </div>
                    <div class="products">
                        <input type="submit" name="add_products" value="Add Product">
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php
$conn->close();
?>