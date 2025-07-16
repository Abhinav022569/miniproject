<?php
session_start();
require 'connect.php'; // you mentioned it's already in /users

// Sanitize input
$username = trim($_POST['username']);
$password = trim($_POST['password']);

// Escape special characters for security
$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);

// Attempt to find user by username or email
$sql = "SELECT * FROM Users WHERE user_name = '$username' OR email = '$username'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Validate password (plain text version — for secure systems, use password_verify)
    if ($password === $user['password']) {
        // Store user data in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['status'] = $user['status'];

        // Check if user is banned
        if ($user['status'] === 'banned') {
            echo "<script>alert('Your account is banned. Please contact admin.'); window.location='login_page.php';</script>";
            exit();
        }

        // Redirect to user dashboard
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
