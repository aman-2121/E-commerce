<?php
session_start();

// Redirect if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: register.php");
    exit();
}

// Database connection
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ecomerce_php';

$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Initialize variables
$message = '';
$product = [];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product data
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    
    if (!$product) {
        $message = 'Product not found';
        header("Location: display_product.php");
        exit();
    }
} else {
    header("Location: display_product.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    // Input validation and sanitization
    $id = intval($_POST['id']);
    $title = trim(mysqli_real_escape_string($conn, $_POST['title'] ?? ''));
    $description = trim(mysqli_real_escape_string($conn, $_POST['discription'] ?? ''));
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $current_image = $product['image'] ?? '';
    $new_image = $current_image;

    // Validate inputs
    $errors = [];
    
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
    
    if ($quantity < 0) {
        $errors[] = 'Quantity cannot be negative';
    }

    // Handle image upload if provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
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
                            // Delete old image if it exists and is different from new one
                            if (!empty($current_image) && file_exists($current_image) && $current_image !== $targetPath) {
                                unlink($current_image);
                            }
                            $new_image = $targetPath;
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
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Error in file upload: ' . $_FILES['image']['error'];
    }

    // Process update if no errors
    if (empty($errors)) {
        $conn->begin_transaction();
        
        try {
            $stmt = $conn->prepare("UPDATE product SET 
                                  title = ?, 
                                  discription = ?, 
                                  price = ?, 
                                  quantity = ?, 
                                  image = ? 
                                  WHERE id = ?");
            
            if ($stmt === false) {
                throw new Exception("SQL Error: " . $conn->error);
            }
            
            $stmt->bind_param("ssdisi", $title, $description, $price, $quantity, $new_image, $id);
            
            if ($stmt->execute()) {
                $conn->commit();
                $message = 'Product updated successfully!';
                // Refresh product data
                $stmt = $conn->prepare("SELECT * FROM product WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $product = $result->fetch_assoc();
                $stmt->close();
            } else {
                throw new Exception($stmt->error);
            }
        } catch (Exception $e) {
            $conn->rollback();
            // Delete new image if transaction failed
            if (isset($targetPath) && file_exists($targetPath) && $targetPath !== $current_image) {
                unlink($targetPath);
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
    <title>Update Product</title>
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
        .image-preview {
            margin: 15px 0;
        }
        .current-image {
            margin-top: 10px;
        }
        .current-image img {
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .image-text {
            display: block;
            margin-top: 5px;
            font-size: 0.9em;
            color: #666;
        }
        .file-hint {
            display: block;
            margin-top: 5px;
            font-size: 0.8em;
            color: #666;
        }
        .form-actions {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .update-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .update-btn:hover {
            background-color: #45a049;
        }
        .cancel-btn {
            background-color: #f44336;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
        }
        .cancel-btn:hover {
            background-color: #d32f2f;
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
            <h1>Update Product</h1>
            <?php if (!empty($message)): ?>
                <div class="message <?php echo (strpos($message, 'successfully') !== false) ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" class="update-form" onsubmit="return validateForm()">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id'] ?? ''); ?>">
                
                <div class="products">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($product['title'] ?? ''); ?>" required maxlength="255">
                </div>
                
                <div class="products">
                    <label for="description">Description</label>
                    <textarea name="discription" id="description" required><?php echo htmlspecialchars($product['discription'] ?? ''); ?></textarea>
                </div>
                
                <div class="products">
                    <label for="price">Price</label>
                    <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" step="0.01" min="0.01" required>
                </div>
                
                <div class="products">
                    <label for="quantity">Quantity</label>
                    <input type="number" name="quantity" id="quantity" value="<?php echo htmlspecialchars($product['quantity'] ?? '0'); ?>" min="0" required>
                </div>
                
                <?php if (!empty($product['image'])): ?>
                <div class="products image-preview">
                    <label>Current Image</label>
                    <div class="current-image">
                        <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Current Product Image">
                        <span class="image-text">Current Image</span>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="products">
                    <label for="new_image">Change Image (Optional)</label>
                    <input type="file" name="image" id="new_image" accept="image/jpeg,image/png,image/gif">
                    <small class="file-hint">Leave blank to keep current image </small>
                </div>
                
                <div class="form-actions">
                    <input type="submit" name="update_product" value="Update Product" class="update-btn">
                    <a href="display_product.php" class="cancel-btn">Cancel</a>
                </div>
            </form> 
        </div>
    </div>

    <script>
    function validateForm() {
        const price = parseFloat(document.getElementById('price').value);
        const quantity = parseInt(document.getElementById('quantity').value);
        const fileInput = document.getElementById('new_image');
        
        // Client-side validation
        if (isNaN(price)) {
            alert('Please enter a valid price');
            return false;
        }
        
        if (price <= 0) {
            alert('Price must be greater than 0');
            return false;
        }
        
        if (isNaN(quantity) || quantity < 0) {
            alert('Quantity must be a positive number');
            return false;
        }
        
        if (fileInput.files.length > 0) {
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
        }
        
        return true;
    }
    </script>
</body>
</html>