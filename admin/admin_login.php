<?php
session_start();
require 'connect.php';

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);

$sql = "SELECT * FROM Admin WHERE user_name = '$username'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    if ($password === $admin['password']) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['user_name'];
        header("Location: ./dashboard/admin_panel.php");
        exit();
    } else {
        echo "<script>alert('❌ Incorrect password'); window.location='login_page.php';</script>";
    }
} else {
    echo "<script>alert('❌ Admin not found'); window.location='login_page.php';</script>";
}

$conn->close();
?>
