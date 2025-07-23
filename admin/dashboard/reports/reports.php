<?php
session_start();
require_once '../../connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

// Handle the action to set a report to 'review' status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['view_report'])) {
    $report_id = $_POST['report_id'];
    $group_id = $_POST['group_id'];

    // Update the status in the database
    $update_query = "UPDATE reports SET status = 'review' WHERE report_id = ?";
    $stmt = $conn->prepare($update_query);
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $stmt->close();

    // Redirect to the chat review page
    header("Location: review_chat.php?group_id=" . $group_id . "&report_id=" . $report_id);
    exit();
}

// Fetch all reports that are 'open' or 'review'
$reports_query = "
    SELECT 
        r.report_id,
        r.reason,
        r.group_id,
        reporter.user_name AS reporter_name,
        reported.user_name AS reported_name,
        sg.group_name
    FROM reports r
    JOIN users AS reporter ON r.user_id = reporter.user_id
    JOIN users AS reported ON r.target_id = reported.user_id
    JOIN study_group AS sg ON r.group_id = sg.group_id
    WHERE r.status = 'open' OR r.status = 'review'
    ORDER BY r.created_at DESC
";
$reports_result = $conn->query($reports_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Reports - Admin Dashboard</title>
  <link rel="stylesheet" href="../panelstyle.css">
  <link rel="stylesheet" href="reports_style.css">
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
          <li><a href="../admin_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
          <li><a href="../approve_group/approve_group.php"><i class="fas fa-check-circle"></i> Approve Groups</a></li>
          <li class="active"><a href="reports.php"><i class="fas fa-flag"></i> Reports</a></li>
          <li><a href="#"><i class="fas fa-user-friends"></i> User Management</a></li>
          <li><a href="#"><i class="fas fa-bullhorn"></i> Announcements</a></li>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <a href="../../admin_logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </aside>

    <main class="main-content">
      <header class="main-header">
        <div class="welcome-section">
          <h2>User Reports</h2>
        </div>
      </header>

      <section class="reports-grid">
        <?php if ($reports_result && $reports_result->num_rows > 0): ?>
          <?php while($report = $reports_result->fetch_assoc()): ?>
            <div class="report-card">
              <h3>Report: <?= htmlspecialchars($report['reporter_name']) ?> reported <?= htmlspecialchars($report['reported_name']) ?></h3>
              <p>Reason: <?= htmlspecialchars($report['reason']) ?> in study group "<?= htmlspecialchars($report['group_name']) ?>"</p>
              <form method="POST" action="">
                <input type="hidden" name="report_id" value="<?= $report['report_id'] ?>">
                <input type="hidden" name="group_id" value="<?= $report['group_id'] ?>">
                <button type="submit" name="view_report" class="view-btn">View</button>
              </form>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="no-reports">There are no open reports.</p>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>
