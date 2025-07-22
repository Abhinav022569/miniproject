<?php
session_start();
header('Content-Type: application/json');
require '../../connect.php';

// Basic validation and security checks
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_POST['group_id'] ?? null;

if (empty($group_id) || !isset($_FILES['note_file'])) {
    echo json_encode(['success' => false, 'error' => 'Group ID or file is missing.']);
    exit();
}

// Check for upload errors
if ($_FILES['note_file']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'File upload error. Code: ' . $_FILES['note_file']['error']]);
    exit();
}

// Security check: Ensure the user is a member of the group
$check_member_sql = "SELECT members_id FROM group_members WHERE group_id = ? AND user_id = ?";
$stmt_check = $conn->prepare($check_member_sql);
$stmt_check->bind_param("ii", $group_id, $user_id);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows == 0) {
    echo json_encode(['success' => false, 'error' => 'Access denied. You are not a member of this group.']);
    exit();
}
$stmt_check->close();

// --- File Handling ---
$upload_dir = '../../../user_files/notes/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

$original_filename = basename($_FILES['note_file']['name']);
$file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
// Create a unique filename to prevent overwrites
$unique_filename = uniqid('note_', true) . '.' . $file_extension;
$target_path = $upload_dir . $unique_filename;
$db_path = 'user_files/notes/' . $unique_filename; // Path to store in DB

// Move the uploaded file to the target directory
if (move_uploaded_file($_FILES['note_file']['tmp_name'], $target_path)) {
    
    $conn->begin_transaction();
    try {
        // 1. Insert the file record into the `notes` table
        $note_title = $original_filename; // Use the original filename as the title
        $sql_insert_note = "INSERT INTO notes (group_id, user_id, title, file_path) VALUES (?, ?, ?, ?)";
        $stmt_note = $conn->prepare($sql_insert_note);
        $stmt_note->bind_param("iiss", $group_id, $user_id, $note_title, $db_path);
        $stmt_note->execute();
        // Get the ID of the note we just inserted
        $new_note_id = $stmt_note->insert_id;
        $stmt_note->close();

        // 2. MODIFIED: Post a message in the chat linking to the new log_download.php script
        $chat_message_content = "Shared a file: <a href='log_download.php?note_id={$new_note_id}' target='_blank'>{$note_title}</a>";
        $sql_insert_message = "INSERT INTO group_messages (group_id, user_id, content) VALUES (?, ?, ?)";
        $stmt_message = $conn->prepare($sql_insert_message);
        $stmt_message->bind_param("iis", $group_id, $user_id, $chat_message_content);
        $stmt_message->execute();
        $stmt_message->close();
        
        $conn->commit();
        echo json_encode(['success' => true]);

    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        // Delete the uploaded file if the database transaction fails
        if (file_exists($target_path)) {
            unlink($target_path);
        }
        echo json_encode(['success' => false, 'error' => 'Database error: ' . $exception->getMessage()]);
    }

} else {
    echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file.']);
}

$conn->close();
?>
