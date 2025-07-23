<?php
session_start();
// The path to connect.php is now three levels up
require '../../../connect.php';

// Check if user is logged in and the request method is POST
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Redirect to home if accessed improperly
    header("Location: ../../../../index.php");
    exit();
}

$reporter_user_id = $_SESSION['user_id'];
$group_id = $_POST['group_id'] ?? 0;
$reported_user_id = $_POST['reported_user_id'] ?? 0;
$reason = trim($_POST['reason'] ?? '');

// Validate the received data
if (empty($group_id) || empty($reported_user_id) || empty($reason)) {
    // Set an error message in the session and redirect back to the form
    $_SESSION['report_error'] = "All fields are required. Please select a user and provide a reason.";
    header("Location: report.php?group_id=" . urlencode($group_id));
    exit();
}

// Prepare to insert the report into the database
// MODIFIED: Added the 'group_id' column to the INSERT statement
$sql = "INSERT INTO reports (user_id, target_id, group_id, reason, status) VALUES (?, ?, ?, ?, 'open')";
$stmt = $conn->prepare($sql);

// MODIFIED: Updated the bind_param to include the integer group_id
// 'user_id' is the reporter, 'target_id' is the reported user, 'group_id' is the context
$stmt->bind_param("iiis", $reporter_user_id, $reported_user_id, $group_id, $reason);

if ($stmt->execute()) {
    // On success, set a success message and redirect to the group chat page
    $_SESSION['report_success'] = "Your report has been submitted successfully. An admin will review it shortly.";
    // Redirecting to the main group chat page after a successful report
    header("Location: ../group_chat.php"); 
    exit();
} else {
    // On failure, set an error message and redirect back to the form
    $_SESSION['report_error'] = "A database error occurred while submitting your report. Please try again.";
    header("Location: report.php?group_id=" . urlencode($group_id));
    exit();
}

$stmt->close();
$conn->close();
?>
