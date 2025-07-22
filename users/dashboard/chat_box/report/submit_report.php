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
// The 'target_id' in your schema corresponds to the user being reported
$sql = "INSERT INTO reports (user_id, target_id, reason, status) VALUES (?, ?, ?, 'open')";
$stmt = $conn->prepare($sql);
// 'user_id' is the person making the report, 'target_id' is the person being reported
$stmt->bind_param("iis", $reporter_user_id, $reported_user_id, $reason);

if ($stmt->execute()) {
    // On success, set a success message and redirect to the group chat page
    $_SESSION['report_success'] = "Your report has been submitted successfully. An admin will review it shortly.";
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
