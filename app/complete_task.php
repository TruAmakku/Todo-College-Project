<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];
$task_id = (int) ($_GET['id'] ?? 0);

if ($task_id <= 0) {
    header('Location: dashboard.php');
    exit;
}

$stmt = $conn->prepare("UPDATE tasks SET is_completed = 1 WHERE id = ? AND user_id = ?");
if ($stmt) {
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

header('Location: dashboard.php');
exit;
?>
