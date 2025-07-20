<?php
session_start();
require '../connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_POST['group_id'] ?? 0;

if ($group_id == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid group ID']);
    exit();
}

// Check if user is already a member
$check_query = "SELECT members_id FROM group_members WHERE group_id = ? AND user_id = ?";
$stmt_check = $conn->prepare($check_query);
$stmt_check->bind_param("ii", $group_id, $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You are already a member of this group.']);
    $stmt_check->close();
    $conn->close();
    exit();
}
$stmt_check->close();

// If not a member, add the user
$insert_query = "INSERT INTO group_members (group_id, user_id, role, status) VALUES (?, ?, 'member', 'joined')";
$stmt_insert = $conn->prepare($insert_query);
$stmt_insert->bind_param("ii", $group_id, $user_id);

if ($stmt_insert->execute()) {
    echo json_encode(['success' => true, 'message' => 'Successfully joined the group!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to join the group.']);
}

$stmt_insert->close();
$conn->close();
