<?php
session_start();
include "db.php";


if (!isset($_SESSION['user_id'])) {
die("Please login first");
}


$user_id = $_SESSION['user_id'];


$result = $conn->query("SELECT * FROM tasks WHERE user_id=$user_id");
?>


<!DOCTYPE html>
<html>
<head>
<title>My To‑Do List</title>
</head>
<body>


<h2>My Tasks</h2>


<!-- Add Task Form -->
<form action="add_task.php" method="post">
<input type="text" name="title" placeholder="New task" required>
<button type="submit">Add</button>
</form>


<hr>


<ul>
<?php while ($row = $result->fetch_assoc()): ?>
<li>
<?php echo $row['title']; ?>


<?php if ($row['is_completed'] == 0): ?>
<a href="complete_task.php?id=<?php echo $row['id']; ?>">صح</a>
<?php else: ?>
(Done)
<?php endif; ?>


<a href="delete_task.php?id=<?php echo $row['id']; ?>">غلط</a>
</li>
<?php endwhile; ?>
</ul>


</body>
</html>