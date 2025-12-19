<?php
include "db.php";
$firstname = $_POST['firstname'];
$Lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];

$password = password_hash($password, PASSWORD_DEFAULT);
$conn=query("INSERT INTO users (firstname, lastname, email,password)
VALUES ('$firstname', '$Lastname', '$email','$password')");


echo "Registered successfully";
?>
