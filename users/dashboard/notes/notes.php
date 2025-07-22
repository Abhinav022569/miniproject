<?php
session_start();
require '../../connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all notes downloaded by the user, joined with group information
$notes_query = "
    SELECT
        dn.note_id,
        dn.title,
        n.upload_time,
        sg.group_name
    FROM
        downloaded_notes dn
    JOIN
        notes n ON dn.note_id = n.note_id
    JOIN
        study_group sg ON n.group_id = sg.group_id
    WHERE
        dn.user_id = ?
    ORDER BY
        sg.group_name ASC, n.upload_time DESC
";

$stmt = $conn->prepare($notes_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Group notes by study group
$grouped_notes = [];
while ($row = $result->fetch_assoc()) {
    $grouped_notes[$row['group_name']][] = $row;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Notes Management - Athena</title>
    <link rel="stylesheet" href="../panelstyle.css" />
    <link rel="stylesheet" href="notes_style.css" />
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
                    <li><a href="../user_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                    <li><a href="../study_groups/study_group.php"><i class="fas fa-users"></i> Study Groups</a></li>
                    <li class="active"><a href="notes.php"><i class="fas fa-book"></i> Notes</a></li>
                    <li><a href="../chat_box/group_chat.php"><i class="fas fa-comments"></i> Group Chat</a></li>
                    <li><a href="#"><i class="fas fa-clipboard-list"></i> To-Do List</a></li>
                    <li><a href="../profile/profile_page.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../../user_logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <div class="welcome-section">
                    <h2>Notes Management</h2>
                </div>
            </header>

            <section class="notes-container">
                <div class="notes-toolbar">
                    <div class="search-notes">
                        <i class="fas fa-search"></i>
                        <!-- MODIFIED: Added ID for JavaScript -->
                        <input type="text" id="notes-search-input" placeholder="Search notes...">
                    </div>
                    <div class="filter-groups">
                        <!-- MODIFIED: Added ID for JavaScript -->
                        <select id="group-filter-select">
                            <option value="all">All Study Groups</option>
                            <?php foreach (array_keys($grouped_notes) as $group_name): ?>
                                <option value="<?= htmlspecialchars($group_name) ?>"><?= htmlspecialchars($group_name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="notes-list">
                    <?php if (empty($grouped_notes)): ?>
                        <p class="no-data">You haven't downloaded any notes yet.</p>
                    <?php else: ?>
                        <?php foreach ($grouped_notes as $group_name => $notes): ?>
                            <div class="study-group-section">
                                <h3 class="study-group-header">Study Group: <?= htmlspecialchars($group_name) ?></h3>
                                <?php foreach ($notes as $note): ?>
                                    <a href="../chat_box/log_download.php?note_id=<?= $note['note_id'] ?>" class="note-item-link">
                                        <div class="note-item">
                                            <div class="note-icon">
                                                <i class="fas fa-file-alt"></i>
                                            </div>
                                            <div class="note-info">
                                                <p class="note-title"><?= htmlspecialchars($note['title']) ?></p>
                                                <p class="note-date">Created: <?= date('M d, Y', strtotime($note['upload_time'])) ?></p>
                                            </div>
                                            <div class="note-action">
                                                <i class="fas fa-chevron-right"></i>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>
    <!-- MODIFIED: Added the script tag to load the JavaScript file -->
    <script src="notes_script.js"></script>
</body>
</html>
