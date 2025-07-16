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
                    <img src="../../jpeg_files/joingrp.jpeg" alt="Groups" class="card-bg">
                    <div class="card-text-panel">
                        <h3>Create or Join Study Groups</h3>
                        <p>Start your own group or join existing ones to collaborate on study material.</p>
                    </div>
                </div>
                <div class="feature-card">
                    <img src="../../jpeg_files/grpchat.jpeg" alt="Chat" class="card-bg">
                    <div class="card-text-panel">
                        <h3>Group Chat</h3>
                        <p>Communicate with group members to discuss notes in real-time.</p>
                    </div>
                </div>
                <div class="feature-card">
                    <img src="../../jpeg_files/downotes.jpeg" alt="Upload" class="card-bg">
                    <div class="card-text-panel">
                        <h3>Uploaded & Downloaded Notes</h3>
                        <p>Share and download study material </p><p>in your groups.</p>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>