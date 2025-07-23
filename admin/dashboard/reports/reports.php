<?php
session_start();
require_once '../../connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

$message = '';
$message_type = '';

// Handle POST requests for report actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id = $_POST['report_id'];

    if (isset($_POST['view_report'])) {
        // --- Action to set a report to 'review' status ---
        $group_id = $_POST['group_id'];
        $update_query = "UPDATE reports SET status = 'review' WHERE report_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $report_id);
        $stmt->execute();
        $stmt->close();
        header("Location: review_chat.php?group_id=" . $group_id . "&report_id=" . $report_id);
        exit();

    } elseif (isset($_POST['mark_resolved'])) {
        // --- NEW: Action to set a report to 'resolved' status ---
        $update_query = "UPDATE reports SET status = 'resolved' WHERE report_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $report_id);
        if ($stmt->execute()) {
            $message = "Report marked as resolved.";
            $message_type = 'success';
        } else {
            $message = "Error updating report status.";
            $message_type = 'error';
        }
        $stmt->close();
    }
}

// Fetch all reports that are 'open' or 'review'
$reports_query = "
    SELECT 
        r.report_id,
        r.reason,
        r.group_id,
        r.status,
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
          <li><a href="../user_management/user_management.php"><i class="fas fa-user-friends"></i> User Management</a></li>
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

      <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <section class="reports-grid">
        <?php if ($reports_result && $reports_result->num_rows > 0): ?>
          <?php while($report = $reports_result->fetch_assoc()): ?>
            <div class="report-card">
              <div class="card-content">
                  <h3>Report: <?= htmlspecialchars($report['reporter_name']) ?> reported <?= htmlspecialchars($report['reported_name']) ?></h3>
                  <p>Reason: <?= htmlspecialchars($report['reason']) ?> in study group "<?= htmlspecialchars($report['group_name']) ?>"</p>
                  <p>Status: <span class="status-<?= htmlspecialchars($report['status']) ?>"><?= htmlspecialchars(ucfirst($report['status'])) ?></span></p>
              </div>
              <!-- MODIFIED: Added a container for the action buttons -->
              <div class="report-card-actions">
                  <form method="POST" action="">
                    <input type="hidden" name="report_id" value="<?= $report['report_id'] ?>">
                    <input type="hidden" name="group_id" value="<?= $report['group_id'] ?>">
                    <button type="submit" name="view_report" class="action-btn view-btn">View</button>
                  </form>
                  <!-- NEW: Form for the "Mark Resolved" button -->
                  <form method="POST" action="">
                    <input type="hidden" name="report_id" value="<?= $report['report_id'] ?>">
                    <button type="submit" name="mark_resolved" class="action-btn resolve-btn">Mark Resolved</button>
                  </form>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="no-reports">There are no open or pending reports.</p>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>
