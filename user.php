<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: register.php");
    exit();
}

// If user is already logged in, redirect to homme.php
if (isset($_SESSION['user_id']) && !isset($_POST['login'])) {
    header("Location: index.php");
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

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        die("Email and password are required");
    }

    $stmt = $conn->prepare("SELECT * FROM register_table WHERE email = ?");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['usertype'];
        header("Location: index.php");
        exit();
    } else {
        $login_error = "Invalid email or password!";
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
</head>
<body>
    <?php if (isset($login_error)): ?>
        <div style="color: red;"><?php echo $login_error; ?></div>
    <?php endif; ?>
    
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['email']); ?>!</h1>
    <p>This is your user dashboard.</p>
    <a href="logout.php">Logout</a>
</body>
</html>