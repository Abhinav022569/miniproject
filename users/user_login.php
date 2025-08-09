<?php
session_start();
require 'connect.php';

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$username = $conn->real_escape_string($username);
$password = $conn->real_escape_string($password);

$sql = "SELECT user_id, user_name, name, password, status FROM users WHERE user_name = '$username' OR email = '$username'";
$result = $conn->query($sql);

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if ($password === $user['password']) {
        // --- Start of MODIFICATION ---

        // Check if the user's account is banned before proceeding.
        if ($user['status'] === 'banned') {
            echo "<script>alert('Your account is banned. Please contact admin.'); window.location='login_page.php';</script>";
            exit();
        }

        // Set session variables immediately after successful password verification.
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_name'] = $user['user_name'];
        $_SESSION['status'] = $user['status'];
        $_SESSION['name'] = $user['name'];

        // NEW: Check if the user is part of any study groups.
        $group_check_query = "SELECT COUNT(*) as group_count FROM group_members WHERE user_id = ?";
        $stmt_group_check = $conn->prepare($group_check_query);
        $stmt_group_check->bind_param("i", $user['user_id']);
        $stmt_group_check->execute();
        $group_result = $stmt_group_check->get_result()->fetch_assoc();
        $stmt_group_check->close();

        // If the user has joined 0 groups, redirect them to the first-time setup page.
        if ($group_result['group_count'] == 0) {
            header("Location: ./first_time/first_time_setup.php");
            exit();
        } else {
            // Otherwise, proceed to the main user dashboard as usual.
            header("Location: ./dashboard/user_panel.php");
            exit();
        }
        
        // --- End of MODIFICATION ---

    } else {
        echo "<script>alert('❌ Incorrect password'); window.location='login_page.php';</script>";
    }
} else {
    echo "<script>alert('❌ User not found'); window.location='login_page.php';</script>";
}

$conn->close();
?>
