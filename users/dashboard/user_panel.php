<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="panelstyle.css">
</head>
<body>
  <div class="user-dashboard">
    <header>
      <h1>Hello, <?php echo $_SESSION['user_name']; ?> 👋</h1>
      <a href="../user_logout.php" class="logout-btn">Logout</a>
    </header>

    <section class="dashboard-welcome">
      <h2>Welcome to NoteXchange</h2>
      <p>Here's what you can do as a user on the platform:</p>
    </section>

    <section class="dashboard-features">
      <div class="feature-card">
        <img src="../../jpeg_files/joingrp.jpeg" alt="Study Group">
        <h3>Create or Join Study Groups</h3>
        <p>Start your own group or join existing ones to collaborate on study material.</p>
      </div>

      <div class="feature-card">
        <img src="chat.png" alt="Group Chat">
        <h3>Group Chat</h3>
        <p>Communicate in real-time with your group members to discuss ideas and notes.</p>
      </div>

      <div class="feature-card">
        <img src="upload.png" alt="Upload Notes">
        <h3>Upload & Download Notes</h3>
        <p>Share your notes and download resources uploaded by others.</p>
      </div>

      <div class="feature-card">
        <img src="todo.png" alt="To-Do List">
        <h3>To-Do List</h3>
        <p>Keep track of your academic tasks with a personalized to-do feature.</p>
      </div>
    </section>
  </div>
</body>
</html>