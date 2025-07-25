<?php
session_start();
// The path to connect.php is now three levels up
require '../../../connect.php';

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../../login_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'] ?? 0;

// Validate that a group ID was passed
if ($group_id == 0) {
    // A simple error message if no group is specified
    die("Invalid group specified. Please go back and select a group to report.");
}

// Fetch the group name for display
$group_name_query = "SELECT group_name FROM study_group WHERE group_id = ?";
$stmt_group = $conn->prepare($group_name_query);
$stmt_group->bind_param("i", $group_id);
$stmt_group->execute();
$group_result = $stmt_group->get_result();
$group = $group_result->fetch_assoc();
$group_name = $group['group_name'] ?? 'Unknown Group';
$stmt_group->close();


// Fetch group members to populate the dropdown, excluding the current user
$members_query = "SELECT u.user_id, u.user_name FROM users u JOIN group_members gm ON u.user_id = gm.user_id WHERE gm.group_id = ? AND u.user_id != ?";
$stmt_members = $conn->prepare($members_query);
$stmt_members->bind_param("ii", $group_id, $user_id);
$stmt_members->execute();
$members_result = $stmt_members->get_result();
$members = [];
while ($row = $members_result->fetch_assoc()) {
    $members[] = $row;
}
$stmt_members->close();
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report a User - Athena</title>
    <!-- Path to panelstyle.css is now two levels up -->
    <link rel="stylesheet" href="../../panelstyle.css">
    <!-- This file will be in the same folder -->
    <link rel="stylesheet" href="report_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="dashboard-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header"><div class="logo">ATHENA</div></div>
            <nav class="sidebar-nav">
                <ul>
                    <!-- Paths are adjusted to be relative from the new folder -->
                    <li><a href="../../user_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                    <li><a href="../../study_groups/study_group.php"><i class="fas fa-users"></i> Study Groups</a></li>
                    <li><a href="../../notes/notes.php"><i class="fas fa-book"></i> Notes</a></li>
                    <li class="active"><a href="../group_chat.php"><i class="fas fa-comments"></i> Group Chat</a></li>
                    <li><a href="../../to-do/to-do.php"><i class="fas fa-clipboard-list"></i> To-Do List</a></li>
                    <li><a href="../../announcements/u_announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                    <li><a href="../../profile/profile_page.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer"><a href="../../../user_logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <div class="welcome-section">
                    <h2>Report a User in "<?= htmlspecialchars($group_name) ?>"</h2>
                </div>
            </header>

            <div class="report-container">
                <div class="card">
                    <h3>File a Report</h3>
                    <div class="card-content">
                        <?php if (isset($_SESSION['report_error'])): ?>
                            <p class="message error-message"><?= $_SESSION['report_error'] ?></p>
                            <?php unset($_SESSION['report_error']); ?>
                        <?php endif; ?>
                        <form action="submit_report.php" method="POST">
                            <input type="hidden" name="group_id" value="<?= htmlspecialchars($group_id) ?>">

                            <div class="input-group">
                                <label for="reported_user_id">User to Report</label>
                                <select name="reported_user_id" id="reported_user_id" required>
                                    <option value="">-- Select a user --</option>
                                    <?php foreach ($members as $member): ?>
                                        <option value="<?= $member['user_id'] ?>"><?= htmlspecialchars($member['user_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="input-group">
                                <label for="reason">Reason for Reporting</label>
                                <textarea name="reason" id="reason" rows="6" placeholder="Please provide specific details about the issue, including quotes or descriptions of behavior..." required></textarea>
                            </div>

                            <button type="submit" class="submit-report-btn">
                                <i class="fas fa-paper-plane"></i> Submit Report
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
