<?php
session_start();
require 'db_connection.php'; // Your database connection file

header('Content-Type: application/json');

// Verify user is logged in
if (!isset($_SESSION['email'])) {
    die(json_encode(['error' => 'Unauthorized']));
}

$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$orderId) {
    die(json_encode(['error' => 'Invalid order ID']));
}

// Get order details
$stmt = $conn->prepare("
    SELECT o.*, p.image 
    FROM orders o
    JOIN product p ON o.title = p.title
    WHERE o.id = ? AND o.email = ?
");
$stmt->bind_param("is", $orderId, $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(['error' => 'Order not found']));
}

echo json_encode($result->fetch_assoc());