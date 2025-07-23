<?php
session_start();
require_once '../../connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

// Get group_id and report_id from URL
$group_id = $_GET['group_id'] ?? 0;
$report_id = $_GET['report_id'] ?? 0;

if ($group_id == 0 || $report_id == 0) {
    die("Invalid request.");
}

// Fetch group name
$group_query = "SELECT group_name FROM study_group WHERE group_id = ?";
$stmt_group = $conn->prepare($group_query);
$stmt_group->bind_param("i", $group_id);
$stmt_group->execute();
$group_result = $stmt_group->get_result();
$group_name = $group_result->fetch_assoc()['group_name'] ?? 'Unknown Group';
$stmt_group->close();

// Fetch all messages for the group
$messages_query = "
    SELECT m.content, m.time_stamp, u.user_name 
    FROM group_messages m
    JOIN users u ON m.user_id = u.user_id
    WHERE m.group_id = ?
    ORDER BY m.time_stamp ASC
";
$stmt_messages = $conn->prepare($messages_query);
$stmt_messages->bind_param("i", $group_id);
$stmt_messages->execute();
$messages_result = $stmt_messages->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Review Chat - <?= htmlspecialchars($group_name) ?></title>
    <link rel="stylesheet" href="../panelstyle.css">
    <link rel="stylesheet" href="reports_style.css">
    <style>
        /* Simple chat log styles */
        .chat-log {
            background-color: #1a1a2e;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            max-height: 60vh;
            overflow-y: auto;
        }
        .chat-message {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .chat-message:last-child {
            border-bottom: none;
        }
        .chat-meta {
            font-size: 14px;
            color: #00ffd5;
            margin-bottom: 5px;
        }
        .chat-meta .timestamp {
            color: rgba(255, 255, 255, 0.5);
            font-size: 12px;
            margin-left: 10px;
        }
        .chat-content {
            color: rgba(255, 255, 255, 0.9);
        }
    </style>
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
                <h2>Reviewing Chat for "<?= htmlspecialchars($group_name) ?>"</h2>
            </div>
        </header>

        <div class="chat-log">
            <?php if ($messages_result && $messages_result->num_rows > 0): ?>
                <?php while($message = $messages_result->fetch_assoc()): ?>
                    <div class="chat-message">
                        <div class="chat-meta">
                            <strong><?= htmlspecialchars($message['user_name']) ?></strong>
                            <span class="timestamp"><?= date('M d, Y g:i A', strtotime($message['time_stamp'])) ?></span>
                        </div>
                        <div class="chat-content">
                            <?= nl2br(htmlspecialchars($message['content'])) ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-reports">No messages found in this group.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
