<?php
include "db.php";
$firstname = $_POST['firstname'];
$Lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];

$password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $firstname, $Lastname, $email, $password);
$stmt->execute();

if($stmt){
    echo "Registered successfully";
} else {
    echo "Database Error: " . $stmt->error;
}
$stmt->close();

