<?php
session_start();
include "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: todo.php");
    exit;
}

$title = trim($_POST['title'] ?? '');
if ($title === '') {
    header("Location: todo.php");
    exit;
}
$user_id = (int) $_SESSION['user_id'];

$stmt = $conn->prepare("INSERT INTO tasks (user_id, title) VALUES (?, ?)");
$stmt->bind_param("is", $user_id, $title);
$stmt->execute();
$stmt->close();

header("Location: todo.php");
exit;
?>
