<?php
session_start();
header('Content-Type: application/json');
// Path updated to go up two directories
require '../../connect.php';

// Basic validation
if (!isset($_SESSION['user_id']) || !isset($_GET['group_id'])) {
    echo json_encode(['error' => 'Unauthorized or missing group ID']);
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'];

// Security check: Ensure the user is actually a member of the group they're trying to view
$check_member_sql = "SELECT members_id FROM group_members WHERE group_id = ? AND user_id = ?";
$stmt_check = $conn->prepare($check_member_sql);
$stmt_check->bind_param("ii", $group_id, $user_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows == 0) {
    echo json_encode(['error' => 'Access denied. You are not a member of this group.']);
    exit();
}
$stmt_check->close();

// Fetch messages for the given group
$messages_sql = "
    SELECT 
        gm.message_id,
        gm.content,
        gm.time_stamp,
        gm.user_id,
        u.user_name,
        u.profile_pic
    FROM group_messages gm
    JOIN users u ON gm.user_id = u.user_id
    WHERE gm.group_id = ?
    ORDER BY gm.time_stamp ASC
";

$stmt_messages = $conn->prepare($messages_sql);
$stmt_messages->bind_param("i", $group_id);
$stmt_messages->execute();
$result_messages = $stmt_messages->get_result();

$messages = [];
while ($row = $result_messages->fetch_assoc()) {
    $messages[] = $row;
}

$stmt_messages->close();
$conn->close();

echo json_encode($messages);
?>
