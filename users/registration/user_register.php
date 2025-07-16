<?php
require '../connect.php';

// Collect and sanitize inputs
$user_name = trim($_POST['user_name']);
$email     = trim($_POST['email']);
$phone_no  = trim($_POST['phone_no']);
$password  = trim($_POST['password']);

// Escape inputs
$user_name = $conn->real_escape_string($user_name);
$email     = $conn->real_escape_string($email);
$phone_no  = $conn->real_escape_string($phone_no);
$password  = $conn->real_escape_string($password);

// Optional: hash password for security
$hashed_password = $password; // Change to: password_hash($password, PASSWORD_DEFAULT);

// Insert into DB
$sql = "INSERT INTO Users (user_name, email, phone_no, password)
        VALUES ('$user_name', '$email', '$phone_no', '$hashed_password')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('🎉 Registration successful! Please login.'); window.location='../login_page.php';</script>";
} else {
    echo "<script>alert('❌ Registration failed: " . $conn->error . "'); window.location='./user_register.php';</script>";
}

$conn->close();
?>
