<?php
session_start();
require_once '../connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../login_page.php");
    exit();
}

$study_groups_count = 0;
$flagged_reports_count = 0;
$announcements_count = 0;
$user_accounts_count = 0;

// Fetch Study Groups Count
$query_groups = "SELECT COUNT(*) AS count FROM study_group WHERE approved = 1";
$result_groups = $conn->query($query_groups);
if ($result_groups && $result_groups->num_rows > 0) {
    $row = $result_groups->fetch_assoc();
    $study_groups_count = $row['count'];
}

// MODIFIED: Fetch Flagged Reports Count (now includes 'open' and 'review')
$query_reports = "SELECT COUNT(*) AS count FROM reports WHERE status = 'open' OR status = 'review'";
$result_reports = $conn->query($query_reports);
if ($result_reports && $result_reports->num_rows > 0) {
    $row = $result_reports->fetch_assoc();
    $flagged_reports_count = $row['count'];
}

// Fetch Announcements Count
$query_announcements = "SELECT COUNT(*) AS count FROM announcements";
$result_announcements = $conn->query($query_announcements);
if ($result_announcements && $result_announcements->num_rows > 0) {
    $row = $result_announcements->fetch_assoc();
    $announcements_count = $row['count'];
}

// Fetch User Accounts Count
$query_users = "SELECT COUNT(*) AS count FROM users";
$result_users = $conn->query($query_users);
if ($result_users && $result_users->num_rows > 0) {
    $row = $result_users->fetch_assoc();
    $user_accounts_count = $row['count'];
}

$conn->close();
$admin_name = $_SESSION['admin_name'] ?? 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Dashboard - Athena</title>
  <link rel="stylesheet" href="panelstyle.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="dashboard-container">
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="logo">ATHENA</div>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li class="active"><a href="admin_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
          <li><a href="./approve_group/approve_group.php"><i class="fas fa-check-circle"></i> Approve Groups</a></li>
          <li><a href="./reports/reports.php"><i class="fas fa-flag"></i> Reports</a></li>
          <li><a href="#"><i class="fas fa-user-friends"></i> User Management</a></li>
          <li><a href="#"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <a href="../admin_logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </aside>

    <main class="main-content">
      <header class="main-header">
        <div class="welcome-section">
          <h2>Admin Dashboard Overview</h2>
        </div>
      </header>

      <section class="dashboard-grid">
        <div class="card">
          <h3>Study Groups</h3>
          <div class="card-content">
            <div class="metric-value"><?php echo $study_groups_count; ?></div>
            <p class="metric-description">Active groups currently running</p>
            <button class="manage-btn" onclick="location.href='./approve_group/approve_group.php'">Manage Groups</button>
          </div>
        </div>

        <div class="card">
          <h3>Flagged Reports</h3>
          <div class="card-content">
            <div class="metric-value"><?php echo $flagged_reports_count; ?></div>
            <p class="metric-description">Pending reports requiring attention</p>
            <!-- MODIFIED: Button now links to the reports page -->
            <button class="manage-btn" onclick="location.href='./reports/reports.php'">View Reports</button>
          </div>
        </div>

        <div class="card">
          <h3>Announcements</h3>
          <div class="card-content">
            <div class="metric-value"><?php echo $announcements_count; ?></div>
            <p class="metric-description">Published announcements</p>
            <button class="manage-btn">Create New</button>
          </div>
        </div>

        <div class="card">
          <h3>User Accounts</h3>
          <div class="card-content">
            <div class="metric-value"><?php echo $user_accounts_count; ?></div>
            <p class="metric-description">Total registered users</p>
            <button class="manage-btn">Manage Users</button>
          </div>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
