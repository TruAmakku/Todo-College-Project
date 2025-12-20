<?php
include "db.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: main.html");
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
if (!$stmt) { die("Prepare failed: " . $conn->error); }
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    echo "Login successful";
    // يمكنك استخدام redirect:
    // header("Location: todo.php");
} else {
    echo "Wrong email or password";
}
?>

