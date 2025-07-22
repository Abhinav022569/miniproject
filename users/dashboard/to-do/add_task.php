<?php 
// File: add_task.php
session_start();
require '../../connect.php';

if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$task = trim($_POST['task'] ?? '');
$day = $_POST['day'] ?? '';
$month = $_POST['month'] ?? '';
$year = $_POST['year'] ?? '';

$due_date = "$year-$month-$day";

if (empty($task) || !checkdate($month, $day, $year)) {
    echo json_encode(['success' => false, 'message' => 'Invalid task or date.']);
    exit();
}

// REVERTED: The SQL query no longer includes group_id
$sql = "INSERT INTO to_do (user_id, task, due_date, status) VALUES (?, ?, ?, 'in progress')";
$stmt = $conn->prepare($sql);
// REVERTED: The binding now only includes user_id, task, and due_date
$stmt->bind_param("iss", $user_id, $task, $due_date);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'task_id' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add task.']);
}

$stmt->close();
$conn->close();
?>