<?php
include "db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: index.html");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$user_id = (int) $_SESSION['user_id'];
if ($id <= 0) {
    header("Location: todo.php");
    exit;
}

$stmt = $conn->prepare("UPDATE tasks SET is_completed = 1 WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$stmt->close();

header("Location: todo.php");
exit;
?>
