<?php 
// File: delete_task.php
session_start();
require '../../connect.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false]);
    exit();
}

$user_id = $_SESSION['user_id'];
$task_id = $_POST['task_id'] ?? 0;

if (empty($task_id)) {
    echo json_encode(['success' => false]);
    exit();
}

$sql = "DELETE FROM to_do WHERE to_do_id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $task_id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>