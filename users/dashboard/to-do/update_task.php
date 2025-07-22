<?php 
// File: update_task.php
session_start();
require '../../connect.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = $_POST['task_id'] ?? 0;
$status = $_POST['status'] ?? ''; // 'done' or 'in progress'

if (empty($task_id) || !in_array($status, ['done', 'in progress'])) {
    echo json_encode(['success' => false]);
    exit();
}

$sql = "UPDATE to_do SET status = ? WHERE to_do_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $status, $task_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>