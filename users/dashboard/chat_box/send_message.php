<?php
session_start();
header('Content-Type: application/json');
// Path updated to go up two directories
require '../../connect.php';

// Basic validation
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_POST['group_id'] ?? null;
$message = trim($_POST['message'] ?? '');

if (empty($group_id) || empty($message)) {
    echo json_encode(['success' => false, 'error' => 'Group ID and message are required.']);
    exit();
}

// Security check: Ensure the user is a member of the group they're trying to post in
$check_member_sql = "SELECT members_id FROM group_members WHERE group_id = ? AND user_id = ?";
$stmt_check = $conn->prepare($check_member_sql);
$stmt_check->bind_param("ii", $group_id, $user_id);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows == 0) {
    echo json_encode(['success' => false, 'error' => 'Access denied.']);
    exit();
}
$stmt_check->close();

// Insert the new message
$insert_sql = "INSERT INTO group_messages (group_id, user_id, content) VALUES (?, ?, ?)";
$stmt_insert = $conn->prepare($insert_sql);
$stmt_insert->bind_param("iis", $group_id, $user_id, $message);

if ($stmt_insert->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to send message.']);
}

$stmt_insert->close();
$conn->close();
?>
