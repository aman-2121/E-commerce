<?php
session_start();

// Regenerate session ID for security
session_regenerate_id(true);

// Database configuration
$host = 'localhost';
$dbname = 'ecomerce_php';
$username = 'root';
$password = '';

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    header("Location: error.php?code=db_connect");
    exit();
}

// Validate CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    error_log("CSRF token validation failed");
    header("Location: error.php?code=csrf");
    exit();
}

// Validate required parameters
if (!isset($_POST['id']) || !isset($_SESSION['email'])) {
    error_log("Missing required parameters");
    header("Location: error.php?code=missing_params");
    exit();
}

// Validate and sanitize input
$p_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if ($p_id === false || $p_id < 1) {
    error_log("Invalid product ID");
    header("Location: error.php?code=invalid_product");
    exit();
}

$u_email = filter_var($_SESSION['email'], FILTER_VALIDATE_EMAIL);
if ($u_email === false) {
    error_log("Invalid email in session");
    header("Location: error.php?code=invalid_email");
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Get product details with row lock
    $p_sql = "SELECT id, title, price, image FROM product WHERE id = ? FOR UPDATE";
    $p_stmt = $conn->prepare($p_sql);
    if (!$p_stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $p_stmt->bind_param("i", $p_id);
    if (!$p_stmt->execute()) {
        throw new Exception("Execute failed: " . $p_stmt->error);
    }
    $p_result = $p_stmt->get_result();

    if ($p_result->num_rows === 0) {
        throw new Exception("Product not found");
    }
    $p_row = $p_result->fetch_assoc();

    // Get user details
    $u_sql = "SELECT name, email, phone, address FROM register_table WHERE email = ?";
    $u_stmt = $conn->prepare($u_sql);
    if (!$u_stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $u_stmt->bind_param("s", $u_email);
    if (!$u_stmt->execute()) {
        throw new Exception("Execute failed: " . $u_stmt->error);
    }
    $u_result = $u_stmt->get_result();

    if ($u_result->num_rows === 0) {
        throw new Exception("User not found");
    }
    $u_row = $u_result->fetch_assoc();

    // Check for existing order
    $check_sql = "SELECT id, quantity FROM orders WHERE email = ? AND product_id = ? AND status = 'in progress'";
    $check_stmt = $conn->prepare($check_sql);
    if (!$check_stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $check_stmt->bind_param("si", $u_email, $p_id);
    if (!$check_stmt->execute()) {
        throw new Exception("Execute failed: " . $check_stmt->error);
    }
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing order quantity
        $existing = $check_result->fetch_assoc();
        $new_quantity = $existing['quantity'] + 1;
        
        $update_sql = "UPDATE orders SET quantity = ?, updated_at = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $update_stmt->bind_param("ii", $new_quantity, $existing['id']);
        if (!$update_stmt->execute()) {
            throw new Exception("Update failed: " . $update_stmt->error);
        }
        if ($update_stmt->affected_rows === 0) {
            throw new Exception("No rows updated");
        }
        $update_stmt->close();
    } else {
        // Insert new order
        $status = "in progress";
        $order_sql = "INSERT INTO orders (
            product_id, 
            title, 
            price, 
            image, 
            username, 
            email, 
            phone, 
            address, 
            status, 
            quantity,
            created_at,
            updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
        
        $order_stmt = $conn->prepare($order_sql);
        if (!$order_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $order_stmt->bind_param(
            "issssssss", 
            $p_id,
            $p_row['title'], 
            $p_row['price'], 
            $p_row['image'], 
            $u_row['name'], 
            $u_row['email'], 
            $u_row['phone'], 
            $u_row['address'], 
            $status
        );
        if (!$order_stmt->execute()) {
            throw new Exception("Insert failed: " . $order_stmt->error);
        }
        if ($order_stmt->affected_rows === 0) {
            throw new Exception("No rows inserted");
        }
        $order_stmt->close();
    }

    // Commit transaction
    if (!$conn->commit()) {
        throw new Exception("Commit failed: " . $conn->error);
    }
    
    // Regenerate CSRF token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    
    // Redirect to order confirmation
    header("Location: user_order.php?success=1");
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Order processing error: " . $e->getMessage());
    header("Location: error.php?code=order_failed&message=" . urlencode($e->getMessage()));
    exit();
} finally {
    // Close statements
    if (isset($p_stmt)) $p_stmt->close();
    if (isset($u_stmt)) $u_stmt->close();
    if (isset($check_stmt)) $check_stmt->close();
    if (isset($update_stmt)) $update_stmt->close();
    if (isset($order_stmt)) $order_stmt->close();
    $conn->close();
}