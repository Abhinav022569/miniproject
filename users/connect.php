<?php
$conn = new mysqli('localhost', 'root', '', 'notexchange');
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
}
?>
