<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$title = trim($_POST['title'] ?? '');

if (empty($title)) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $conn->prepare("INSERT INTO tasks (user_id, title) VALUES (?, ?)");
if ($stmt) {
    $stmt->bind_param("is", $user_id, $title);
    $stmt->execute();
    $stmt->close();
}

header('Location: dashboard.php');
exit;
?>
