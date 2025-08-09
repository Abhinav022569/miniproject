<?php
session_start();
// The connect.php file is located two directories above.
require '../connect.php';

// Redirect non-logged-in users to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_page.php");
    exit();
}

// Ensure the form was submitted via POST.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: first_time_setup.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$full_name = trim($_POST['full_name'] ?? '');
$profile_pic_path = trim($_POST['profile_pic_path'] ?? '');

// --- Data Validation ---
// Check if the required fields are filled.
if (empty($full_name) || empty($profile_pic_path)) {
    // If data is missing, redirect back to the setup page with an error.
    $_SESSION['error_message'] = "Please fill out all required fields.";
    header("Location: first_time_setup.php");
    exit();
}

// --- Database Update ---
// Prepare a SQL statement to update the user's name and profile picture.
$update_query = "UPDATE users SET name = ?, profile_pic = ? WHERE user_id = ?";
$stmt = $conn->prepare($update_query);
// Bind the parameters to the statement to prevent SQL injection.
$stmt->bind_param("ssi", $full_name, $profile_pic_path, $user_id);

if ($stmt->execute()) {
    // If the update is successful, update the session and redirect to the main dashboard.
    $_SESSION['name'] = $full_name; // Update the name in the session.
    header("Location: ../dashboard/user_panel.php");
    exit();
} else {
    // If the database update fails, redirect back with an error message.
    $_SESSION['error_message'] = "An error occurred while saving your profile. Please try again.";
    header("Location: first_time_setup.php");
    exit();
}

$stmt->close();
$conn->close();
?>
