<?php
session_start();
header('Content-Type: application/json');
require '../../connect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['group_id'])) {
    echo json_encode(['error' => 'Unauthorized or missing group ID']);
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'];

$check_member_sql = "SELECT members_id FROM group_members WHERE group_id = ? AND user_id = ?";
$stmt_check = $conn->prepare($check_member_sql);
$stmt_check->bind_param("ii", $group_id, $user_id);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows == 0) {
    echo json_encode(['error' => 'Access denied. You are not a member of this group.']);
    exit();
}
$stmt_check->close();

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
    // MODIFIED: Check if the message content contains a file link and update it
    if (preg_match('/<a href=\'..\/..\/user_files\/notes\/(.*?)\' target=\'_blank\'>(.*?)<\/a>/', $row['content'], $matches)) {
        $file_path_in_link = 'user_files/notes/' . $matches[1];
        $note_title = $matches[2];

        // Find the note_id from the file_path
        $note_id_stmt = $conn->prepare("SELECT note_id FROM notes WHERE file_path = ?");
        $note_id_stmt->bind_param("s", $file_path_in_link);
        $note_id_stmt->execute();
        $note_id_result = $note_id_stmt->get_result();
        if ($note_id_result->num_rows > 0) {
            $note_data = $note_id_result->fetch_assoc();
            $note_id = $note_data['note_id'];
            // Replace the old direct link with the new logging link
            $row['content'] = "Shared a file: <a href='log_download.php?note_id={$note_id}' target='_blank'>{$note_title}</a>";
        }
        $note_id_stmt->close();
    }
    $messages[] = $row;
}

$stmt_messages->close();
$conn->close();

echo json_encode($messages);
?>
