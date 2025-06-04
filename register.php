<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = 'localhost';
$dbname = 'ecomerce_php';
$username = 'root';
$password = '';

// Create connection with error handling
try {
    $conn = new mysqli($host, $username, $password, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

// Handle registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Validate and sanitize inputs
    $name = htmlspecialchars(trim($_POST["name"] ?? ''));
    $email = filter_var(trim($_POST["email"] ?? ''), FILTER_SANITIZE_EMAIL);
    $phone = preg_replace('/[^0-9]/', '', $_POST["phone"] ?? '');
    $raw_password = $_POST["password"] ?? '';
    $id = filter_var(trim($_POST["id"] ?? ''), FILTER_SANITIZE_NUMBER_INT);
    $address = htmlspecialchars(trim($_POST["address"] ?? ''));
    $usertype = 'user';

    // Validate required fields
    $errors = [];
    if (empty($name)) $errors[] = "Name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (empty($phone)) $errors[] = "Phone is required";
    if (empty($raw_password)) $errors[] = "Password is required";
    if (empty($id)) $errors[] = "ID is required";
    if (empty($address)) $errors[] = "Address is required";

    // Additional validations
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (strlen($phone) < 10 || strlen($phone) > 15) {
        $errors[] = "Phone number must be 10-15 digits";
    }
    
    if (strlen($raw_password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }

    // Check if email already exists
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM register_table WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();
            
            if ($stmt->num_rows > 0) {
                $errors[] = "Email already registered";
            }
            $stmt->close();
        }
    }

    // If no errors, proceed with registration
    if (empty($errors)) {
        $password = password_hash($raw_password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO register_table (name, email, phone, password, id, address, usertype) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt === false) {
            $errors[] = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("ssssiss", $name, $email, $phone, $password, $id, $address, $usertype);
            
            if ($stmt->execute()) {
                $_SESSION['registration_success'] = true;
                header("Location: ".$_SERVER['PHP_SELF']);
                exit();
            } else {
                $errors[] = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors = [];

    if (empty($email) || empty($password)) {
        $errors[] = "Email and password are required";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT * FROM register_table WHERE email = ?");
        if ($stmt === false) {
            $errors[] = "Prepare failed: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['usertype'];

                header("Location: " . ($user['usertype'] == 'admin' ? "admin.php" : "user.php"));
                exit();
            } else {
                $errors[] = "Invalid email or password!";
            }
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="stylesheet" href="register.css">

</head>
<body>
    <div class="form-container">
        <?php if (isset($_SESSION['registration_success'])): ?>
            <div class="success-message">
                <i class="fas fa-check-circle"></i> Registration successful! Please login.
            </div>
            <?php unset($_SESSION['registration_success']); ?>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="form" id="login">
            <h1>Login</h1>
            <form action="" method="POST">
                <div class="input">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input">
                    <input type="password" name="password" id="login-password" placeholder="Password" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('login-password', this)"></i>
                </div>
                <div class="input">
                    <input type="submit" name="login" value="Login">
                </div>
                <div class="toggle-btn">
                    <button type="button" onclick="toggleform('register')">Don't have an account? <span class="aman">Register</span></button>
                </div>
            </form>
        </div>

        <div class="form" id="register" style="display: none;">
            <h1>Register</h1>
            <form action="" method="POST">
                <div class="input">
                    <input type="text" name="name" placeholder="Full Name" required>
                </div>
                <div class="input">
                    <input type="email" name="email" placeholder="Email" required>
                </div>
                <div class="input">
                    <input type="tel" name="phone" placeholder="Phone Number" required>
                </div>
                <div class="input">
                    <input type="password" name="password" id="reg-password" placeholder="Password (min 8 characters)" required>
                    <i class="fas fa-eye password-toggle" onclick="togglePassword('reg-password', this)"></i>
                </div>
                <div class="input">
                    <input type="text" name="id" placeholder="ID" required>
                </div>
                <div class="input">
                    <input type="text" name="address" placeholder="Address" required>
                </div>
                <div class="input">
                    <input type="submit" name="register" value="Register">
                </div>
                <div class="toggle-btn">
                    <button type="button" onclick="toggleform('login')">Already have an account? <span class="aman">Login</span></button>
                </div>
            </form>
        </div>
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
        
        function togglePassword(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>