<?php
session_start();
require '../../connect.php';

// 1. Check for authentication and valid input
if (!isset($_SESSION['user_id']) || !isset($_GET['note_id'])) {
    die("Access denied or invalid request.");
}

$user_id = $_SESSION['user_id'];
$note_id = $_GET['note_id'];

// 2. Fetch the note's file path and title from the database
$stmt_get_note = $conn->prepare("SELECT file_path, title FROM notes WHERE note_id = ?");
$stmt_get_note->bind_param("i", $note_id);
$stmt_get_note->execute();
$result = $stmt_get_note->get_result();

if ($result->num_rows === 0) {
    die("Note not found.");
}
$note = $result->fetch_assoc();
$file_path_from_db = $note['file_path'];
$note_title = $note['title']; // Get the title to save it later
$stmt_get_note->close();

// The file path in the DB is relative to the project root.
// We need to make it relative to the server's file system.
$full_file_path = __DIR__ . '/../../../' . $file_path_from_db;


// 3. Log the download in the 'downloaded_notes' table
// First, check if this user has already downloaded this note to avoid duplicate entries
$stmt_check = $conn->prepare("SELECT download_id FROM downloaded_notes WHERE note_id = ? AND user_id = ?");
$stmt_check->bind_param("ii", $note_id, $user_id);
$stmt_check->execute();
if ($stmt_check->get_result()->num_rows === 0) {
    // If no record exists, insert one
    // MODIFIED: Added the 'title' column to the INSERT statement
    $stmt_log = $conn->prepare("INSERT INTO downloaded_notes (note_id, user_id, title) VALUES (?, ?, ?)");
    $stmt_log->bind_param("iis", $note_id, $user_id, $note_title);
    $stmt_log->execute();
    $stmt_log->close();
}
$stmt_check->close();
$conn->close();


// 4. Force file download
if (file_exists($full_file_path)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    // Use the stored title as the download filename
    header('Content-Disposition: attachment; filename="' . basename($note_title) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($full_file_path));
    readfile($full_file_path);
    exit;
} else {
    die("Error: File does not exist at path: " . htmlspecialchars($full_file_path));
}
?>
