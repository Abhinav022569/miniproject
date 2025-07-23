<?php
session_start();
require_once '../../connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

$message = '';
$message_type = '';

// Handle POST request to ban or activate a user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $new_status = '';
    
    if (isset($_POST['ban_user'])) {
        $new_status = 'banned';
    } elseif (isset($_POST['activate_user'])) {
        $new_status = 'active';
    }

    if ($new_status) {
        $update_query = "UPDATE users SET status = ? WHERE user_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("si", $new_status, $user_id);
        if ($stmt->execute()) {
            $message = "User status updated successfully.";
            $message_type = 'success';
        } else {
            $message = "Error updating user status.";
            $message_type = 'error';
        }
        $stmt->close();
    }
}


// MODIFIED: Fetch all users from the database including reputation_score
$users_query = "SELECT user_id, user_name, name, email, status, reputation_score FROM users ORDER BY user_name ASC";
$users_result = $conn->query($users_query);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Management - Admin Dashboard</title>
  <link rel="stylesheet" href="../panelstyle.css">
  <link rel="stylesheet" href="user_management_style.css">
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
          <li><a href="../approve_group/approve_group.php"><i class="fas fa-check-circle"></i> Approve Groups</a></li>
          <li><a href="../reports/reports.php"><i class="fas fa-flag"></i> Reports</a></li>
          <li class="active"><a href="user_management.php"><i class="fas fa-user-friends"></i> User Management</a></li>
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
          <h2>User Management</h2>
        </div>
      </header>

      <?php if ($message): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo htmlspecialchars($message); ?></div>
      <?php endif; ?>

      <div class="search-container">
        <input type="text" id="user-search-input" placeholder="Search users by name, username, or email...">
      </div>

      <div class="table-container">
        <table class="user-table">
          <thead>
            <tr>
              <th>User Name</th>
              <th>Name</th>
              <th>Email</th>
              <!-- NEW: Added Reputation Score Header -->
              <th>Reputation</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody id="user-table-body">
            <?php if ($users_result && $users_result->num_rows > 0): ?>
              <?php while($user = $users_result->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($user['user_name']) ?></td>
                  <td><?= htmlspecialchars($user['name']) ?></td>
                  <td><?= htmlspecialchars($user['email']) ?></td>
                  <!-- NEW: Display Reputation Score -->
                  <td><?= htmlspecialchars($user['reputation_score']) ?></td>
                  <td>
                    <?php if ($user['status'] === 'active'): ?>
                      <span class="status-tag status-active">Active</span>
                    <?php else: ?>
                      <span class="status-tag status-banned">Banned</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <form method="POST" action="" style="display: inline;">
                      <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                      <?php if ($user['status'] === 'active'): ?>
                        <button type="submit" name="ban_user" class="action-btn ban-btn">Ban</button>
                      <?php else: ?>
                        <button type="submit" name="activate_user" class="action-btn activate-btn">Activate</button>
                      <?php endif; ?>
                    </form>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align: center;">No users found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
  <script src="user_management_script.js"></script>
</body>
</html>
