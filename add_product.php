<?php
session_start();

// Redirect if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: register.php");
    exit();
}

// Database connection
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
    // Input validation and sanitization
    $id = trim($conn->real_escape_string($_POST['id'] ?? ''));
    $title = trim($conn->real_escape_string($_POST['title'] ?? ''));
    $description = trim($conn->real_escape_string($_POST['description'] ?? ''));
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);

    // Validate inputs
    $errors = [];
    
    // ID validation
    if (empty($id)) {
        $errors[] = 'Product ID is required';
    } elseif (!is_numeric($id)) {
        $errors[] = 'Product ID must be a number';
    } elseif ($id <= 0) {
        $errors[] = 'Product ID must be a positive number';
    } else {
        // Check if ID already exists
        $check = $conn->prepare("SELECT id FROM product WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $errors[] = 'Product ID already exists';
        }
        $check->close();
    }
    
    if (empty($title)) {
        $errors[] = 'Title is required';
    } elseif (strlen($title) > 255) {
        $errors[] = 'Title must be less than 255 characters';
    }
    
    if (empty($description)) {
        $errors[] = 'Description is required';
    }
    
    if ($price <= 0) {
        $errors[] = 'Price must be greater than 0';
    }
    
    if ($quantity < 1) {
        $errors[] = 'Quantity must be at least 1';
    }

    // Image validation
    $image = '';
    if (isset($_FILES['image'])) {
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    $errors[] = 'Failed to create upload directory';
                }
            }
            
            if (empty($errors)) {
                $check = getimagesize($_FILES['image']['tmp_name']);
                if ($check !== false) {
                    $fileExt = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
                    
                    if (in_array($fileExt, $allowedExts)) {
                        if ($_FILES['image']['size'] > 2097152) {
                            $errors[] = 'File size must be less than 2MB';
                        } else {
                            $fileName = uniqid() . '.' . $fileExt;
                            $targetPath = $uploadDir . $fileName;
                            
                            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
                                $image = $targetPath;
                            } else {
                                $errors[] = 'Error uploading file';
                            }
                        }
                    } else {
                        $errors[] = 'Invalid file type. Allowed: JPG, JPEG, PNG, GIF';
                    }
                } else {
                    $errors[] = 'File is not a valid image';
                }
            }
        } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $errors[] = 'Error in file upload: ' . $_FILES['image']['error'];
        } else {
            $errors[] = 'No image uploaded';
        }
    } else {
        $errors[] = 'No image uploaded';
    }

    // Process if no errors
    if (empty($errors)) {
        $conn->begin_transaction();
        
        try {
            $stmt = $conn->prepare("INSERT INTO product (id, title, discription, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
            
            if ($stmt === false) {
                throw new Exception("SQL Error: " . $conn->error);
            }
            
            $stmt->bind_param("issdis", $id, $title, $description, $price, $quantity, $image);
            
            if ($stmt->execute()) {
                $conn->commit();
                $message = 'Product added successfully! ID: ' . $id;
                
                // Clear form fields after successful submission
                $_POST = array();
            } else {
                throw new Exception($stmt->error);
            }
            $stmt->close();
        } catch (Exception $e) {
            $conn->rollback();
            if (!empty($image) && file_exists($image)) {
                unlink($image);
            }
            $errors[] = 'Error: ' . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $message = implode('<br>', $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="adminstyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .error {
            background-color: #ffdddd;
            color: #d8000c;
        }
        .success {
            background-color: #ddffdd;
            color: #4F8A10;
        }
        .products {
            margin-bottom: 15px;
        }
        .products label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .products input[type="text"],
        .products input[type="number"],
        .products textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .products input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .products input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
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
            <h1>Add Products</h1>
            <?php if (!empty($message)): ?>
                <div class="message <?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <div>
                <form action="add_product.php" method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
                    <div class="products">
                        <label for="id">Product ID</label>
                        <input type="number" name="id" id="id" min="1" required value="<?php echo htmlspecialchars($_POST['id'] ?? ''); ?>">
                    </div>
                    <div class="products">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" required maxlength="255" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                    </div>
                    <div class="products">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="products">
                        <label for="price">Price</label>
                        <input type="number" name="price" id="price" step="0.01" min="0.01" required value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>">
                    </div>
                    <div class="products">
                        <label for="quantity">Quantity</label>
                        <input type="number" name="quantity" id="quantity" min="1" required value="<?php echo htmlspecialchars($_POST['quantity'] ?? '1'); ?>">
                    </div>
                    <div class="products">
                        <label for="image">Image</label>
                        <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/gif" required>
                    </div>
                    <div class="products">
                        <input type="submit" name="add_products" value="Add Product">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function validateForm() {
        const id = parseInt(document.getElementById('id').value);
        const price = parseFloat(document.getElementById('price').value);
        const quantity = parseInt(document.getElementById('quantity').value);
        const fileInput = document.getElementById('image');
        
        // Client-side validation
        if (isNaN(id) || id <= 0) {
            alert('Please enter a valid Product ID (positive number)');
            return false;
        }
        
        if (isNaN(price)) {
            alert('Please enter a valid price');
            return false;
        }
        
        if (price <= 0) {
            alert('Price must be greater than 0');
            return false;
        }
        
        if (isNaN(quantity) || quantity < 1) {
            alert('Quantity must be at least 1');
            return false;
        }
        
        if (fileInput.files.length === 0) {
            alert('Please select an image file');
            return false;
        }
        
        const file = fileInput.files[0];
        const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
        
        if (!validTypes.includes(file.type)) {
            alert('Invalid file type. Please upload a JPG, PNG, or GIF image.');
            return false;
        }
        
        if (file.size > 2097152) {
            alert('File size must be less than 2MB');
            return false;
        }
        
        return true;
    }
    </script>
</body>
</html>