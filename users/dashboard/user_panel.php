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
                <img src="group.png" alt="Groups" class="card-bg">
                <div class="card-overlay">
                    <h3>Create or Join Study Groups</h3>
                    <p>Start your own group or join existing ones to collaborate on study material.</p>
                </div>
            </div>
            <div class="feature-card">
                <img src="chat.png" alt="Chat" class="card-bg">
                <div class="card-overlay">
                    <h3>Group Chat</h3>
                    <p>Communicate with group members to discuss notes in real-time.</p>
                </div>
            </div>
            <div class="feature-card">
                <img src="upload.png" alt="Upload" class="card-bg">
                <div class="card-overlay">
                    <h3>Upload & Download Notes</h3>
                    <p>Share and download study material in your groups.</p>
                </div>
            </div>
            <div class="feature-card">
                <img src="todo.png" alt="To-Do" class="card-bg">
                <div class="card-overlay">
                    <h3>To-Do List</h3>
                    <p>Keep track of your personal academic tasks inside your group.</p>
                </div>
            </div>
        </section>
    </div>
</body>
</html>