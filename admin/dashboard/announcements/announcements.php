<?php
session_start();
require '../../connect.php'; // Connect to the database

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
// Use separate variables for create and delete actions
$create_success_message = '';
$create_error_message = '';
$delete_success_message = '';
$delete_error_message = '';

// --- Handle Form Submission for Creating a New Announcement ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_announcement'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $target_user_id = $_POST['target_user'] === 'all' ? NULL : $_POST['target_user'];

    if (empty($title) || empty($content)) {
        $create_error_message = "Title and Content fields cannot be empty.";
    } else {
        $sql = "INSERT INTO announcements (admin_id, title, content, user_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issi", $admin_id, $title, $content, $target_user_id);

        if ($stmt->execute()) {
            $create_success_message = "Announcement created successfully!";
        } else {
            $create_error_message = "Error creating announcement: " . $stmt->error;
        }
        $stmt->close();
    }
}

// --- Handle Deletion of an Announcement ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_announcement'])) {
    $announcement_id_to_delete = $_POST['announcement_id'];

    $sql_delete = "DELETE FROM announcements WHERE announcement_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $announcement_id_to_delete);

    if ($stmt_delete->execute()) {
        $delete_success_message = "Announcement deleted successfully!";
    } else {
        $delete_error_message = "Error deleting announcement: " . $stmt_delete->error;
    }
    $stmt_delete->close();
}


// --- Fetch all existing announcements ---
$announcements_sql = "
    SELECT a.announcement_id, a.title, a.content, a.created_at, u.user_name AS target_user
    FROM announcements a
    LEFT JOIN users u ON a.user_id = u.user_id
    ORDER BY a.created_at DESC
";
$announcements_result = $conn->query($announcements_sql);

// --- Fetch all users for the target dropdown ---
$users_sql = "SELECT user_id, user_name FROM users ORDER BY user_name ASC";
$users_result = $conn->query($users_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements - Admin Dashboard</title>
    <link rel="stylesheet" href="../panelstyle.css">
    <link rel="stylesheet" href="announcements_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="dashboard-container">
    <aside class="sidebar">
        <div class="sidebar-header"><div class="logo">ATHENA</div></div>
        <nav class="sidebar-nav">
            <ul>
                <li><a href="../admin_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                <li><a href="../approve_group/approve_group.php"><i class="fas fa-users"></i> Approve Groups</a></li>
                <li><a href="../reports/reports.php"><i class="fas fa-flag"></i> Reports</a></li>
                <li><a href="../user_management/user_management.php"><i class="fas fa-user-friends"></i> User Management</a></li>
                <li class="active"><a href="announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
            </ul>
        </nav>
        <div class="sidebar-footer"><a href="../../admin_logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </aside>

    <main class="main-content">
        <div class="announcements-container">
            <header class="announcements-header">
                <h2>Announcements</h2>
                <button id="create-announcement-btn" class="create-announcement-btn">Create New Announcement</button>
            </header>

            <!-- Display messages for DELETE action here -->
            <?php if ($delete_success_message): ?><p class="message success-message"><?= htmlspecialchars($delete_success_message) ?></p><?php endif; ?>
            <?php if ($delete_error_message): ?><p class="message error-message"><?= htmlspecialchars($delete_error_message) ?></p><?php endif; ?>

            <div id="create-announcement-card" class="create-announcement-card">
                <h3>New Announcement</h3>
                <!-- Display messages for CREATE action inside the card -->
                <?php if ($create_success_message): ?><p class="message success-message"><?= htmlspecialchars($create_success_message) ?></p><?php endif; ?>
                <?php if ($create_error_message): ?><p class="message error-message"><?= htmlspecialchars($create_error_message) ?></p><?php endif; ?>
                
                <form action="announcements.php" method="POST">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="target_user">Target</label>
                        <select id="target_user" name="target_user">
                            <option value="all">All Users</option>
                            <?php while($user = $users_result->fetch_assoc()): ?>
                                <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['user_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" name="create_announcement" class="submit-announcement-btn">Publish</button>
                </form>
            </div>

            <div class="announcements-list">
                <div class="announcement-header-row">
                    <div class="announcement-col title">TITLE</div>
                    <div class="announcement-col">DATE</div>
                    <div class="announcement-col">TARGET</div>
                    <div class="announcement-col actions">ACTIONS</div>
                </div>
                <?php if ($announcements_result->num_rows > 0): ?>
                    <?php while($row = $announcements_result->fetch_assoc()): ?>
                        <div class="announcement-item">
                            <div class="announcement-title-row">
                                <div class="announcement-col title"><?= htmlspecialchars($row['title']) ?></div>
                                <div class="announcement-col"><?= date('Y-m-d', strtotime($row['created_at'])) ?></div>
                                <div class="announcement-col"><?= htmlspecialchars($row['target_user'] ?? 'All Users') ?></div>
                                <div class="announcement-col actions">
                                    <form action="announcements.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                        <input type="hidden" name="announcement_id" value="<?= $row['announcement_id'] ?>">
                                        <button type="submit" name="delete_announcement" class="delete-btn">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <div class="announcement-content">
                                <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p style="padding: 20px; text-align: center;">No announcements found.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<script src="announcements_script.js"></script>
</body>
</html>
