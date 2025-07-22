<?php
session_start();
require_once '../../connect.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['group_id'])) {
    $group_id = $_POST['group_id'];

    if (isset($_POST['approve'])) {
        // --- Approve Logic ---
        $update_query = "UPDATE study_group SET approved = 1 WHERE group_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("i", $group_id);
        if ($stmt->execute()) {
            $creator_query = "SELECT user_id FROM study_group WHERE group_id = ?";
            $stmt_creator = $conn->prepare($creator_query);
            $stmt_creator->bind_param("i", $group_id);
            $stmt_creator->execute();
            $creator_result = $stmt_creator->get_result();
            if($creator_data = $creator_result->fetch_assoc()) {
                $creator_id = $creator_data['user_id'];
                $insert_member_query = "INSERT INTO group_members (group_id, user_id, role) VALUES (?, ?, 'moderator')";
                $stmt_member = $conn->prepare($insert_member_query);
                $stmt_member->bind_param("ii", $group_id, $creator_id);
                $stmt_member->execute();
                $stmt_member->close();
            }
            $stmt_creator->close();
            $message = "Group approved successfully.";
            $message_type = 'success';
        } else {
            $message = "Error approving group: " . $conn->error;
            $message_type = 'error';
        }
        $stmt->close();

    } elseif (isset($_POST['reject'])) {
        // --- Reject Logic (for pending groups) ---
        $delete_query = "DELETE FROM study_group WHERE group_id = ? AND approved = 0";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $group_id);
        if ($stmt->execute()) {
            $message = "Group request rejected and removed successfully.";
            $message_type = 'success';
        } else {
            $message = "Error rejecting group: " . $conn->error;
            $message_type = 'error';
        }
        $stmt->close();

    } elseif (isset($_POST['disband'])) {
        // --- Disband Logic (for approved groups) ---
        $conn->begin_transaction();
        try {
            $sql_dn = "DELETE dn FROM downloaded_notes dn JOIN notes n ON dn.note_id = n.note_id WHERE n.group_id = ?";
            $stmt_dn = $conn->prepare($sql_dn);
            $stmt_dn->bind_param("i", $group_id);
            $stmt_dn->execute();
            $stmt_dn->close();

            $sql_notes = "DELETE FROM notes WHERE group_id = ?";
            $stmt_notes = $conn->prepare($sql_notes);
            $stmt_notes->bind_param("i", $group_id);
            $stmt_notes->execute();
            $stmt_notes->close();

            $sql_msgs = "DELETE FROM group_messages WHERE group_id = ?";
            $stmt_msgs = $conn->prepare($sql_msgs);
            $stmt_msgs->bind_param("i", $group_id);
            $stmt_msgs->execute();
            $stmt_msgs->close();

            $sql_members = "DELETE FROM group_members WHERE group_id = ?";
            $stmt_members = $conn->prepare($sql_members);
            $stmt_members->bind_param("i", $group_id);
            $stmt_members->execute();
            $stmt_members->close();

            $sql_group = "DELETE FROM study_group WHERE group_id = ?";
            $stmt_group = $conn->prepare($sql_group);
            $stmt_group->bind_param("i", $group_id);
            $stmt_group->execute();
            $stmt_group->close();

            $conn->commit();
            $message = "Group disbanded successfully. All associated data has been removed.";
            $message_type = 'success';

        } catch (Exception $e) {
            $conn->rollback();
            $message = "Error disbanding group: " . $e->getMessage();
            $message_type = 'error';
        }
    }
}

// --- Fetch Pending Group Requests into an array ---
$pending_groups_query = "
    SELECT sg.group_id, sg.group_name, sg.description, u.user_name AS creator_name
    FROM study_group sg
    JOIN users u ON sg.user_id = u.user_id
    WHERE sg.approved = 0
    ORDER BY sg.created_at ASC
";
$pending_groups_result = $conn->query($pending_groups_query);
$pending_groups = [];
if ($pending_groups_result) {
    while ($row = $pending_groups_result->fetch_assoc()) {
        $pending_groups[] = $row;
    }
}
$num_pending = count($pending_groups);


// --- Fetch Approved Groups for Management ---
$approved_groups_query = "
    SELECT sg.group_id, sg.group_name, sg.description, u.user_name AS creator_name,
           (SELECT COUNT(*) FROM group_members gm WHERE gm.group_id = sg.group_id) AS member_count
    FROM study_group sg
    JOIN users u ON sg.user_id = u.user_id
    WHERE sg.approved = 1
    ORDER BY sg.group_name ASC
";
$approved_groups_result = $conn->query($approved_groups_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Approve Groups - Admin Dashboard</title>
  <link rel="stylesheet" href="../panelstyle.css">
  <link rel="stylesheet" href="approve_group.css">
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
          <li class="active"><a href="approve_group.php"><i class="fas fa-check-circle"></i> Approve Groups</a></li>
          <li><a href="#"><i class="fas fa-flag"></i> Reports</a></li>
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
          <h2>Study Group Management</h2>
        </div>
      </header>

      <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <div class="content-container">
        <section class="management-section">
          <h2>Creation Requests</h2>
          <!-- MODIFIED: Conditional container class -->
          <div class="<?php echo ($num_pending === 1) ? 'single-item-container' : 'card-grid'; ?>">
            <?php if ($num_pending > 0): ?>
              <?php foreach ($pending_groups as $group): ?>
                <div class="request-card">
                  <div class="card-content">
                    <h3><?php echo htmlspecialchars($group['group_name']); ?></h3>
                    <p><?php echo htmlspecialchars($group['description'] ?: 'No description provided.'); ?></p>
                    <p style="font-size: 13px; color: #a0a0b0;">Requested by: <?php echo htmlspecialchars($group['creator_name']); ?></p>
                  </div>
                  <div class="card-actions">
                    <form method="POST" action="">
                      <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                      <button type="submit" name="approve" class="action-btn approve-btn">Approve</button>
                    </form>
                    <form method="POST" action="">
                      <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                      <button type="submit" name="reject" class="action-btn reject-btn" onclick="return confirm('Are you sure you want to reject this group?');">Reject</button>
                    </form>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="no-requests">No pending group requests.</p>
            <?php endif; ?>
          </div>
        </section>

        <section class="management-section">
            <h2>Approved Groups</h2>
            <div class="card-grid">
                <?php if ($approved_groups_result && $approved_groups_result->num_rows > 0): ?>
                    <?php while($group = $approved_groups_result->fetch_assoc()): ?>
                        <div class="request-card">
                            <div class="card-content">
                                <h3><?php echo htmlspecialchars($group['group_name']); ?></h3>
                                <p><?php echo htmlspecialchars($group['description'] ?: 'No description provided.'); ?></p>
                                <p style="font-size: 13px; color: #a0a0b0;">
                                    Creator: <?php echo htmlspecialchars($group['creator_name']); ?> | 
                                    Members: <?php echo $group['member_count']; ?>
                                </p>
                            </div>
                            <div class="card-actions">
                                <form method="POST" action="" style="width: 100%;">
                                    <input type="hidden" name="group_id" value="<?php echo $group['group_id']; ?>">
                                    <button type="submit" name="disband" class="action-btn reject-btn" style="width: 100%;" onclick="return confirm('Are you sure you want to PERMANENTLY disband this group? This cannot be undone.');">Disband Group</button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="no-requests">No approved groups yet.</p>
                <?php endif; ?>
            </div>
        </section>
      </div>
    </main>
  </div>
</body>
</html>
