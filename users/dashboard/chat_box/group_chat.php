<?php
session_start();
require '../../connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$groups_query = "
    SELECT sg.group_id, sg.group_name
    FROM study_group sg
    JOIN group_members gm ON sg.group_id = gm.group_id
    WHERE gm.user_id = ? AND sg.approved = 1
    ORDER BY sg.group_name ASC
";
$stmt_groups = $conn->prepare($groups_query);
$stmt_groups->bind_param("i", $user_id);
$stmt_groups->execute();
$groups_result = $stmt_groups->get_result();
$joined_groups = [];
while ($row = $groups_result->fetch_assoc()) {
    $joined_groups[] = $row;
}
$stmt_groups->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Group Chat - Athena</title>
  <link rel="stylesheet" href="../panelstyle.css">
  <link rel="stylesheet" href="chat_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="dashboard-wrapper">
    <aside class="sidebar">
      <div class="sidebar-header"><div class="logo">ATHENA</div></div>
      <nav class="sidebar-nav">
        <ul>
          <li><a href="../user_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
          <li><a href="../study_groups/study_group.php"><i class="fas fa-users"></i> Study Groups</a></li>
          <li><a href="../notes/notes.php"><i class="fas fa-book"></i> Notes</a></li>
          <li class="active"><a href="group_chat.php"><i class="fas fa-comments"></i> Group Chat</a></li>
          <li><a href="../to-do/to-do.php"><i class="fas fa-clipboard-list"></i> To-Do List</a></li>
          <li><a href="../profile/profile_page.php"><i class="fas fa-user-circle"></i> Profile</a></li>
        </ul>
      </nav>
      <div class="sidebar-footer"><a href="../../user_logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a></div>
    </aside>

    <main class="main-content">
        <div class="chat-container">
            <div class="chat-sidebar">
                <div class="chat-sidebar-header"><h3>Group Chats</h3></div>
                <ul class="group-list">
                    <?php if (!empty($joined_groups)): ?>
                        <?php foreach ($joined_groups as $group): ?>
                            <li class="group-item" data-group-id="<?= $group['group_id'] ?>" data-group-name="<?= htmlspecialchars($group['group_name']) ?>">
                                <div class="group-info">
                                    <p class="group-name"><?= htmlspecialchars($group['group_name']) ?></p>
                                    <p class="group-last-message">Click to view chat...</p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="no-groups">You haven't joined any groups yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="chat-main">
                <div class="chat-header">
                    <h3 id="current-group-name">Select a group to start chatting</h3>
                    <!-- ADDED: Report button, initially hidden -->
                    <a href="#" id="report-group-btn" class="report-btn" style="display: none;">
                        <i class="fas fa-flag"></i> Report
                    </a>
                </div>
                <div class="chat-messages" id="chat-messages-container">
                    <div class="no-group-selected">
                        <i class="fas fa-comments"></i>
                        <p>Your messages will appear here.</p>
                    </div>
                </div>
                <div class="chat-input-area" id="chat-input-container" style="display: none;">
                    <input type="file" id="note-file-input" style="display: none;">
                    <button type="button" id="upload-file-btn" class="upload-btn"><i class="fas fa-plus"></i></button>
                    
                    <form id="message-form">
                        <input type="hidden" id="group-id-input" name="group_id">
                        <input type="text" id="message-input" name="message" placeholder="Type your message..." autocomplete="off" required>
                        <button type="submit"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </main>
  </div>
  <script>const currentUserId = <?php echo json_encode($_SESSION['user_id']); ?>;</script>
  <script src="chat_script.js"></script>
</body>
</html>
