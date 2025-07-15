<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login_page.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="panelstyle.css">
</head>
<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <h2>NoteXChange</h2>
      <div class="sidebar-option">📁 Approve Groups</div>
      <div class="sidebar-option">🚩 Flagged Reports</div>
      <div class="sidebar-option">👥 Manage Users</div>
      <div class="sidebar-option">📢 Announcements</div>
      <div class="sidebar-option"><a href="../admin_logout.php">🔒 Logout</a></div>
    </aside>

    <main class="dashboard-content">
      <h1>Welcome, <?php echo $_SESSION['admin_name']; ?> 👋</h1>
      <p>Here’s what’s happening on your platform:</p>

      <div class="dashboard-widgets">
        <div class="widget">
          <img src="../../jpeg_files/stdgroups.jpeg" alt="Groups" class="card-bg">
          <div class="widget-overlay">
            <h3>Pending Groups</h3>
            <p>5 group requests awaiting approval</p>
          </div>
        </div>
        <div class="widget">
          <img src="../../jpeg_files/flgrep.jpeg" alt="Groups" class="card-bg">
          <div class="widget-overlay">
            <h3>Flagged Reports</h3>
            <p>3 new items reported</p>
          </div>
        </div>
        <div class="widget">
          <img src="../../jpeg_files/actuser.jpeg" alt="Groups" class="card-bg">
          <div class="widget-overlay">
            <h3>Active Users</h3>
            <p>112 currently active users</p>
          </div>
        </div>
        <div class="widget">
          <img src="../../jpeg_files/anounce.jpeg" alt="Groups" class="card-bg">
          <div class="widget-overlay">
            <h3>Announcements</h3>
            <p>2 announcements sent this week</p>
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>
