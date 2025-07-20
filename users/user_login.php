<?php
session_start();
require 'connect.php';

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);

// MODIFIED: Select 'name' column in the query
$sql = "SELECT user_id, user_name, name, password, status FROM users WHERE user_name = '$username' OR email = '$username'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($password === $user['password']) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['status'] = $user['status'];
        // NEW: Store the user's full name in the session
        $_SESSION['name'] = $user['name'];

        if ($user['status'] === 'banned') {
            echo "<script>alert('Your account is banned. Please contact admin.'); window.location='login_page.php';</script>";
            exit();
        }

        header("Location: ./dashboard/user_panel.php");
        exit();
    } else {
        echo "<script>alert('❌ Incorrect password'); window.location='login_page.php';</script>";
    }
} else {
    echo "<script>alert('❌ User not found'); window.location='login_page.php';</script>";
}

$conn->close();
?>
