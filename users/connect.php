<?php
$conn = new mysqli('localhost', 'root', '', 'athena');
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
