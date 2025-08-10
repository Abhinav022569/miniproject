<?php
require '../connect.php';

// --- Data Retrieval and Sanitization ---
$user_name = trim($_POST['user_name']);
$email     = trim($_POST['email']);
$password  = trim($_POST['password']);

// --- Server-Side Validation ---

// 1. Validate Email Format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>alert('❌ Invalid email format. Please enter a valid email address.'); window.location='./registeration.php';</script>";
    exit();
}

// 2. Validate Password Length (must be at least 8 characters)
if (strlen($password) < 8) {
    echo "<script>alert('❌ Password is too short. It must be at least 8 characters long.'); window.location='./registeration.php';</script>";
    exit();
}

// --- Database Interaction ---

// Escape strings to prevent SQL injection
$user_name = $conn->real_escape_string($user_name);
$email     = $conn->real_escape_string($email);
$password  = $conn->real_escape_string($password);

// Using plain text password as per the existing project structure.
$hashed_password = $password; 

// Check if the username or email already exists in the database.
$check_query = "SELECT * FROM Users WHERE user_name='$user_name' OR email='$email'";
$result = $conn->query($check_query);

if ($result->num_rows > 0) {
    // If a duplicate is found, alert the user and redirect to the login page.
    echo "<script>alert('⚠️ An account with the same username or email already exists. Please login.'); 
    window.location='../login_page.php';</script>";
} else {
    // If no duplicates, insert the new user into the database.
    // MODIFIED: The 'phone_no' is now inserted with a placeholder value.
    $sql = "INSERT INTO Users (name, user_name, email, phone_no, password)
            VALUES ('$user_name', '$user_name', '$email', '0000000000', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        // On successful registration, redirect to the login page.
        echo "<script>alert('🎉 Registration successful! Please login.'); window.location='../login_page.php';</script>";
    } else {
        // If the query fails, show an error and redirect back to the registration page.
        echo "<script>alert('❌ Registration failed: " . $conn->error . "'); window.location='./registeration.php';</script>";
    }
}

$conn->close();
?>
