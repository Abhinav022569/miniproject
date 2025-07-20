<?php
session_start();
require '../connect.php'; // Ensure this path is correct relative to study_groups.php

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_page.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? $_SESSION['user_name'] ?? 'User'; // Fallback for user_name if not set in session

// Fetch study groups that the user has created OR is a member of
$study_groups = [];
$query_groups = "
    SELECT
        sg.group_id,
        sg.group_name,
        sg.description,
        sg.approved,
        u.user_name AS creator_name,
        COUNT(gm.members_id) AS member_count
    FROM
        study_group sg
    LEFT JOIN
        users u ON sg.user_id = u.user_id
    LEFT JOIN
        group_members gm ON sg.group_id = gm.group_id
    WHERE
        sg.user_id = ? OR gm.user_id = ? /* Filter by groups created by user OR where user is a member */
    GROUP BY
        sg.group_id, sg.group_name, sg.description, sg.approved, u.user_name
    ORDER BY
        sg.created_at DESC;
";

$stmt_groups = $conn->prepare($query_groups);
// Bind the user_id twice for the OR condition
$stmt_groups->bind_param("ii", $user_id, $user_id);
$stmt_groups->execute();
$result_groups = $stmt_groups->get_result();

if ($result_groups) {
    while ($row = $result_groups->fetch_assoc()) {
        $study_groups[] = $row;
    }
} else {
    error_log("Error fetching study groups: " . $conn->error);
}

$stmt_groups->close();
$conn->close(); // Close connection after fetching data
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User - Study Groups - Athena</title>
  <!-- Link to the main panelstyle.css for overall dashboard layout and sidebar -->
  <link rel="stylesheet" href="panelstyle.css">
  <!-- Link to the NEW study_groups_style.css (should come AFTER panelstyle.css to override/add specific styles) -->
  <link rel="stylesheet" href="study_group_style.css">
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="dashboard-wrapper">
    <!-- Sidebar - Copied from user_panel.php -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="logo">ATHENA</div>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li><a href="user_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
          <li class="active"><a href="#"><i class="fas fa-users"></i> Study Groups</a></li>
          <li><a href="#"><i class="fas fa-book"></i> Notes</a></li>
          <li><a href="#"><i class="fas fa-comments"></i> Group Chat</a></li>
          <li><a href="#"><i class="fas fa-clipboard-list"></i> To-Do List</a></li>
          <li><a href="profile_page.php"><i class="fas fa-user-circle"></i> Profile</a></li>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <a href="../user_logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header class="main-header">
        <div class="welcome-section">
          <h2>Study Groups</h2>
        </div>
        <div class="header-actions">
          <button class="create-new-btn"><i class="fas fa-plus"></i> Create New Group</button>
        </div>
      </header>

      <section class="dashboard-grid">
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
                <button class="view-btn">View Details</button>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="no-data">No study groups found. <a href="#">Create or join one!</a></p>
        <?php endif; ?>
      </section>
    </main>
  </div>
</body>
</html>
