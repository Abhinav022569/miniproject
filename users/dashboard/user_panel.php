<?php
session_start();
require '../connect.php'; // Ensure this path is correct relative to user_panel.php

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_page.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];
// Fetch the user's 'name' from the session, fallback to 'user_name', then 'User'
$user_display_name = $_SESSION['name'] ?? $_SESSION['user_name'] ?? 'User';

// Fetch user's profile picture path
$profile_pic_query = "SELECT profile_pic FROM users WHERE user_id = ?";
$stmt_profile_pic = $conn->prepare($profile_pic_query);
$stmt_profile_pic->bind_param("i", $user_id);
$stmt_profile_pic->execute();
$profile_pic_result = $stmt_profile_pic->get_result();
$profile_pic_data = $profile_pic_result->fetch_assoc();
$user_profile_pic = $profile_pic_data['profile_pic'] ?? 'https://placehold.co/100x100/0f0f2c/00ffd5?text=DP'; // Default placeholder

// Function to calculate time ago for notes
function time_ago($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}


// Fetch Study Groups created by or joined by the user, including member count
$groups_query = "
    SELECT sg.group_id, sg.group_name, COUNT(gm.members_id) AS member_count
    FROM study_group sg
    LEFT JOIN group_members gm ON sg.group_id = gm.group_id
    WHERE sg.user_id = ? OR gm.user_id = ?
    GROUP BY sg.group_id, sg.group_name
    ORDER BY sg.created_at DESC
    LIMIT 3
";
$stmt_groups = $conn->prepare($groups_query);
$stmt_groups->bind_param("ii", $user_id, $user_id);
$stmt_groups->execute();
$groups_result = $stmt_groups->get_result();


// Fetch Upcoming Tasks for the user
$tasks_query = "SELECT task, due_date FROM to_do WHERE user_id = ? AND status IN ('Open', 'in progress') ORDER BY due_date ASC LIMIT 3";
$stmt_tasks = $conn->prepare($tasks_query);
$stmt_tasks->bind_param("i", $user_id);
$stmt_tasks->execute();
$tasks_result = $stmt_tasks->get_result();

// Fetch Recently Downloaded Notes by the user, including uploader's name and time since upload
$notes_query = "
  SELECT n.title AS note_title, dn.downloaded_at AS download_date, u.user_name AS uploader_name, n.upload_time
  FROM downloaded_notes dn
  JOIN notes n ON dn.note_id = n.note_id
  JOIN users u ON n.user_id = u.user_id
  WHERE dn.user_id = ?
  ORDER BY dn.downloaded_at DESC
  LIMIT 3
";
$stmt_notes = $conn->prepare($notes_query);
$stmt_notes->bind_param("i", $user_id);
$stmt_notes->execute();
$notes_result = $stmt_notes->get_result();

// Close all statements and connection
$stmt_profile_pic->close();
$stmt_groups->close();
$stmt_tasks->close();
$stmt_notes->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Dashboard - Athena</title>
  <link rel="stylesheet" href="panelstyle.css" />
  <!-- Font Awesome for icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="logo">ATHENA</div>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li class="active"><a href="user_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
          <li><a href="./study_groups/study_group.php"><i class="fas fa-users"></i> Study Groups</a></li>
          <li><a href="#"><i class="fas fa-book"></i> Notes</a></li>
          <li><a href="./chat_box/group_chat.php"><i class="fas fa-comments"></i> Group Chat</a></li>
          <li><a href="#"><i class="fas fa-clipboard-list"></i> To-Do List</a></li>
          <li><a href="./profile/profile_page.php"><i class="fas fa-user-circle"></i> Profile</a></li>
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
          <h2>Welcome, <?php echo htmlspecialchars($user_display_name); ?>!</h2>
        </div>
        <div class="header-actions">
          <div class="search-bar">
            <input type="text" id="group-search-input" placeholder="Search for groups..." />
            <button id="group-search-btn" class="search-action-btn">Search</button>
          </div>
        </div>
      </header>

      <!-- Search Results Section (Initially Hidden) - Placed outside the grid -->
      <div id="search-results-container" style="display: none; margin-bottom: 30px;">
          <div class="card">
              <h3>Search Results</h3>
              <div id="search-results-content" class="card-content">
                  <!-- Search results will be injected here by JavaScript -->
              </div>
          </div>
      </div>

      <section class="dashboard-grid">
        <!-- Profile Picture Card -->
        <div class="card profile-overview-card">
            <h3>Your Profile</h3>
            <div class="card-content profile-card-content">
                <div class="profile-card-avatar">
                    <img src="../../<?= htmlspecialchars($user_profile_pic) ?>" alt="Profile Picture" class="profile-card-img">
                </div>
                <p class="profile-card-name"><?= htmlspecialchars($user_display_name) ?></p>
                <p class="profile-card-username">@<?= htmlspecialchars($_SESSION['user_name'] ?? 'N/A') ?></p>
                <button class="view-btn profile-card-button" onclick="location.href='./profile/profile_page.php'">View Profile</button>
            </div>
        </div>

        <!-- Your Study Groups Section -->
        <div class="card study-groups-card">
          <h3>Your Study Groups</h3>
          <div class="card-content">
            <?php if ($groups_result && $groups_result->num_rows > 0): ?>
              <?php while($row = $groups_result->fetch_assoc()): ?>
                <div class="group-item">
                  <div class="group-details">
                    <h4><?= htmlspecialchars($row['group_name']) ?></h4>
                    <p><?= htmlspecialchars($row['member_count'] ?? 0) ?> Members</p>
                  </div>
                  <!-- MODIFIED: Changed button to a link -->
                  <a href="./chat_box/group_chat.php" class="view-btn">View</a>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <p class="no-data">No study groups found. <a href="./study_groups/study_group.php">Create or join one!</a></p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Upcoming Tasks Section -->
        <div class="card upcoming-tasks-card">
          <h3>Upcoming Tasks</h3>
          <div class="card-content">
            <?php if ($tasks_result && $tasks_result->num_rows > 0): ?>
              <?php while($row = $tasks_result->fetch_assoc()): ?>
                <div class="task-item">
                  <div class="task-details">
                    <h4><?= htmlspecialchars($row['task']) ?></h4>
                    <p>Due: <?= htmlspecialchars(date('M d, Y', strtotime($row['due_date']))) ?></p>
                  </div>
                  <input type="checkbox" class="task-checkbox" />
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <p class="no-data">No upcoming tasks. <a href="#">Add a new task!</a></p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Recent Notes Section -->
        <div class="card recent-notes-card full-width">
          <h3>Recent Notes</h3>
          <div class="card-content">
            <?php if ($notes_result && $notes_result->num_rows > 0): ?>
              <?php while($row = $notes_result->fetch_assoc()): ?>
                <div class="note-item">
                  <div class="note-details">
                    <h4><?= htmlspecialchars($row['note_title']) ?></h4>
                    <p>Uploaded by <?= htmlspecialchars($row['uploader_name']) ?>, <?= time_ago($row['upload_time']) ?></p>
                  </div>
                  <i class="fas fa-download download-icon"></i>
                </div>
              <?php endwhile; ?>
            <?php else: ?>
              <p class="no-data">No notes downloaded recently. <a href="#">Browse notes!</a></p>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </main>
  </div>
  <div id="notification-container"></div>
   <script src="dscript.js"></script>
</body>
</html>
