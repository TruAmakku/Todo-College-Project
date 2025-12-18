<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "todo_app";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database Error: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>






?>

