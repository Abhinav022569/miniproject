<?php
session_start();
require '../../connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? $_SESSION['user_name'] ?? 'User';

// --- Handle Group Creation Form Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_group_submit'])) {
    $group_name = trim($_POST['group_name'] ?? '');
    $group_description = trim($_POST['group_description'] ?? '');

    if (empty($group_name)) {
        $_SESSION['error_message'] = "Group name cannot be empty.";
    } else {
        $insert_query = "INSERT INTO study_group (group_name, description, user_id, approved) VALUES (?, ?, ?, 0)";
        $stmt_insert = $conn->prepare($insert_query);
        $stmt_insert->bind_param("ssi", $group_name, $group_description, $user_id);

        if ($stmt_insert->execute()) {
            $_SESSION['success_message'] = "Group '" . htmlspecialchars($group_name) . "' requested successfully! It will appear once approved.";
        } else {
            $_SESSION['error_message'] = "Error creating group: " . $stmt_insert->error;
        }
        $stmt_insert->close();
    }
    // Redirect to the same page to prevent form resubmission on refresh
    header("Location: study_group.php");
    exit();
}

// --- Fetch groups the user created OR is a member of ---
$study_groups = [];
$query_groups = "
    SELECT
        sg.group_id, sg.group_name, sg.description, sg.approved,
        sg.user_id AS creator_id, u.user_name AS creator_name,
        (SELECT COUNT(*) FROM group_members gm_count WHERE gm_count.group_id = sg.group_id) AS member_count
    FROM study_group sg
    JOIN users u ON sg.user_id = u.user_id
    WHERE sg.user_id = ? OR EXISTS (SELECT 1 FROM group_members gm WHERE gm.group_id = sg.group_id AND gm.user_id = ?)
    ORDER BY sg.created_at DESC;
";

$stmt_groups = $conn->prepare($query_groups);
$stmt_groups->bind_param("ii", $user_id, $user_id);
$stmt_groups->execute();
$result_groups = $stmt_groups->get_result();

if ($result_groups) {
    while ($row = $result_groups->fetch_assoc()) {
        $study_groups[] = $row;
    }
}

$stmt_groups->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User - Study Group - Athena</title>
  <link rel="stylesheet" href="../panelstyle.css">
  <link rel="stylesheet" href="study_group_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="dashboard-wrapper">
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="logo">ATHENA</div>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li><a href="../user_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
          <li class="active"><a href="study_group.php"><i class="fas fa-users"></i> Study Groups</a></li>
          <li><a href="../notes/notes.php"><i class="fas fa-book"></i> Notes</a></li>
          <li><a href="../chat_box/group_chat.php"><i class="fas fa-comments"></i> Group Chat</a></li>
          <li><a href="../to-do/to-do.php"><i class="fas fa-clipboard-list"></i> To-Do List</a></li>
          <li><a href="../announcements/u_announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
          <li><a href="../profile/profile_page.php"><i class="fas fa-user-circle"></i> Profile</a></li>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <a href="../../user_logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </aside>

    <main class="main-content">
      <header class="main-header">
        <div class="welcome-section">
          <h2>Study Groups</h2>
        </div>
        <div class="header-actions">
          <button class="create-new-btn" id="toggle-create-group-form"><i class="fas fa-plus"></i> Create New Group</button>
        </div>
      </header>

      <section class="dashboard-grid">
        <!-- MODIFIED: Display session messages for all actions -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success-message"><?= htmlspecialchars($_SESSION['success_message']) ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="message error-message"><?= htmlspecialchars($_SESSION['error_message']) ?></div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="card create-group-card" id="create-group-form-card" style="display: none;">
            <h3>Request New Study Group</h3>
            <div class="card-content">
                <form action="" method="POST" id="group-creation-form">
                    <input type="hidden" name="create_group_submit" value="1">
                    <div class="input-group">
                        <label for="group_name">Group Name</label>
                        <input type="text" id="group_name" name="group_name" placeholder="e.g., Calculus II Study" required>
                    </div>
                    <div class="input-group">
                        <label for="group_description">Description</label>
                        <textarea id="group_description" name="group_description" placeholder="Briefly describe the group's purpose..." rows="3"></textarea>
                    </div>
                </form>
            </div>
        </div>

        <?php if (!empty($study_groups)): ?>
          <?php foreach ($study_groups as $group): ?>
            <div class="card">
              <div class="card-content">
                <h3 class="group-title"><?= htmlspecialchars($group['group_name']) ?></h3>
                <p class="group-description"><?= htmlspecialchars($group['description'] ?? 'No description provided.') ?></p>
                <div class="group-meta">
                  <p><i class="fas fa-user-friends"></i> <?= htmlspecialchars($group['member_count']) ?> Members</p>
                  <p>
                    <i class="fas fa-check-circle"></i> Status:
                    <span class="status-<?= $group['approved'] ? 'active' : 'pending' ?>">
                      <?= $group['approved'] ? 'Active' : 'Pending Approval' ?>
                    </span>
                  </p>
                </div>
                <?php if ($group['creator_id'] == $user_id): ?>
                    <!-- If the user is the creator, show a 'Disband' button -->
                    <form action="./disband_group.php" method="post" onsubmit="return confirm('Are you sure you want to PERMANENTLY disband this group? This cannot be undone.');" style="display: inline;">
                        <input type="hidden" name="group_id" value="<?= $group['group_id'] ?>">
                        <!-- MODIFIED: Changed button class to 'disband-btn' -->
                        <button type="submit" class="disband-btn">Disband</button>
                    </form>
                <?php else: ?>
                    <!-- If the user is just a member, show a 'Leave Group' button -->
                    <button class="leave-btn" data-group-id="<?= $group['group_id'] ?>">Leave Group</button>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="no-data">No study groups found. <a href="#" id="trigger-create-group-from-empty">Create or join one!</a></p>
        <?php endif; ?>
      </section>
    </main>
  </div>
  <div id="notification-container"></div>
  <script src="../dscript.js"></script>
</body>
</html>
