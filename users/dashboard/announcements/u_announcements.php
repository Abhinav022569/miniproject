<?php
session_start();
require '../../connect.php'; // Path to the database connection script

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch announcements targeted to the specific user OR all users (user_id IS NULL)
$announcements_sql = "
    SELECT title, content, created_at 
    FROM announcements 
    WHERE user_id = ? OR user_id IS NULL
    ORDER BY created_at DESC
";
$stmt = $conn->prepare($announcements_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$announcements_result = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Athena</title>
    <link rel="stylesheet" href="../panelstyle.css"> <!-- Main dashboard style -->
    <link rel="stylesheet" href="u_announcements_style.css"> <!-- Page-specific style -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="dashboard-wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header"><div class="logo">ATHENA</div></div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="../user_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="../study_groups/study_group.php"><i class="fas fa-users"></i> Study Groups</a></li>
                <li><a href="../notes/notes.php"><i class="fas fa-book"></i> Notes</a></li>
                <li><a href="../chat_box/group_chat.php"><i class="fas fa-comments"></i> Group Chat</a></li>
                <li><a href="../to-do/to-do.php"><i class="fas fa-clipboard-list"></i> To-Do List</a></li>
                <li class="active"><a href="u_announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                <li><a href="../profile/profile_page.php"><i class="fas fa-user-circle"></i> Profile</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer"><a href="../../user_logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="announcements-container">
            <h2 class="page-header">Announcements</h2>

            <?php if ($announcements_result->num_rows > 0): ?>
                <?php while ($row = $announcements_result->fetch_assoc()): ?>
                    <div class="announcement-card">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p class="content"><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                        <p class="post-date">Posted on: <?= date('F d, Y, h:i A', strtotime($row['created_at'])) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">No announcements at this time.</p>
            <?php endif; ?>
        </div>
    </main>
</div>
</body>
</html>
