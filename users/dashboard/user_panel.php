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
  <div class="dashboard-container">

    <!-- Sidebar (flush left) -->
    <aside class="sidebar">
      <h2>NoteXchange</h2>
      <div class="sidebar-option">📁 Study Groups</div>
      <div class="sidebar-option">💬 Group Chat</div>
      <div class="sidebar-option">📥 My Notes</div>
      <div class="sidebar-option">⚙️ Account Settings</div>
      <div class="sidebar-option"><a href="../user_logout.php">🔒 Logout</a></div>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-content">
      <h1>Hello, <?php echo $_SESSION['user_name']; ?> 👋</h1>
      <p>Here’s what you can do on NoteXchange:</p>

      <div class="dashboard-widgets">
        <div class="feature-card">
          <img src="../../jpeg_files/stdgroups.jpeg" class="card-bg" alt="Groups">
          <div class="card-text-panel">
            <h3>Study Groups</h3>
            <p>Create or join collaborative groups to share and discuss notes.</p>
          </div>
        </div>

        <div class="feature-card">
          <img src="../../jpeg_files/grpchat.jpeg" class="card-bg" alt="Chat">
          <div class="card-text-panel">
            <h3>Group Chat</h3>
            <p>Talk to your group members in real time using built-in chat.</p>
          </div>
        </div>

        <div class="feature-card">
          <img src="../../jpeg_files/downotes.jpeg" class="card-bg" alt="Upload">
          <div class="card-text-panel">
            <h3>My Notes</h3>
            <p>Contribute your class notes to help others study.</p>
          </div>
        </div>

        <div class="feature-card">
          <img src="../../jpeg_files/todo.jpeg" class="card-bg" alt="To-Do">
          <div class="card-text-panel">
            <h3>To-Do List</h3>
            <p>Organize your tasks and stay focused on what matters.</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>