<?php
require '../connect.php';

$user_name = trim($_POST['user_name']);
$email     = trim($_POST['email']);
$phone_no  = trim($_POST['phone_no']);
$password  = trim($_POST['password']);

$user_name = $conn->real_escape_string($user_name);
$email     = $conn->real_escape_string($email);
$phone_no  = $conn->real_escape_string($phone_no);
$password  = $conn->real_escape_string($password);

$hashed_password = $password;

$check_query = "SELECT * FROM Users WHERE user_name='$user_name' OR email='$email' OR phone_no='$phone_no'";
$result = $conn->query($check_query);

if ($result->num_rows > 0) {
    echo "<script>alert('⚠️ An account with the same username, email or phone number already exists. Please login.'); 
    window.location='../login_page.php';</script>";
}
else{
    $sql = "INSERT INTO Users (user_name, email, phone_no, password)
            VALUES ('$user_name', '$email', '$phone_no', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('🎉 Registration successful! Please login.'); window.location='../login_page.php';</script>";
    } else {
        echo "<script>alert('❌ Registration failed: " . $conn->error . "'); window.location='./user_register.php';</script>";
    }
}

$conn->close();
?>
