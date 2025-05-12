<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$dbname = 'ecomerce_php';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone = filter_var(trim($_POST["phone"]), FILTER_SANITIZE_NUMBER_INT);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); 
    $id = filter_var(trim($_POST["id"]), FILTER_SANITIZE_NUMBER_INT);
    $address = htmlspecialchars(trim($_POST["address"]));
    $usertype = 'user';

    if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($id) || empty($address)) {
        die("All fields are required");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    $stmt = $conn->prepare("INSERT INTO register_table (name, email, phone, password, id, address, usertype) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssiss", $name, $email, $phone, $password, $id, $address, $usertype);

    if ($stmt->execute()) {
        echo "Registration successful";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

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
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['usertype'];

        if ($user['usertype'] == 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: user.php");
        }
        exit();
    } else {
        echo "Invalid email or password!";
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
    <title>Login/Register</title>
    <link rel="stylesheet" href="v.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

   <div class="form" id="login">
    <h1>Login</h1>
    <form action="" method="POST">
        <div class="input">
            <input type="email" name="email" id="" placeholder="Email" required>
        </div>
        <div class="input">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="input">
            <input type="submit" name="login" value="Login">
        </div>
        <div class="toggle-btn">
            <button type="button" onclick="toggleform('register')">you don't have an account? <span class="aman">Register</span></button>
        </div>
    </form>
   </div>

   <div class="form" id="register" style="display: none;">
    <h1>Register Form</h1>
   <form action="" method="POST">
        <div class="input">
            <input type="text" name="name" id="" placeholder="Name" required>
        </div>
        <div class="input">
            <input type="email" name="email" id="" placeholder="Email" required>
        </div>
        <div class="input">
            <input type="text" name="phone" placeholder="Phone" id="" required>
        </div>
        <div class="input">
            <input type="password" name="password" placeholder="Password" required>
        </div>
        <div class="input">
            <input type="text" name="id" placeholder="ID" id="" required>
        </div>
        <div class="input">
            <input type="text" name="address" placeholder="Address" id="" required><br>
        </div>
        <div class="input">
            <input type="submit" name="register" value="Register">
        </div>
        <div class="toggle-btn">
            <button type="button" onclick="toggleform('login')">already have an account? <span class="aman">Login</span></button>
        </div>
   </form>
   </div>

<script>
    function toggleform(form) {
        if (form === 'register') {
            document.getElementById('login').style.display = "none"; 
            document.getElementById('register').style.display = "block"; 
        } else {
            document.getElementById('login').style.display = "block";
            document.getElementById('register').style.display = "none";
        }
    }
</script>

</body>
</html>