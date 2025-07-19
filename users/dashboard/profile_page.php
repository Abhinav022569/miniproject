<?php
session_start();
require '../connect.php'; // Ensure this path is correct relative to profile_page.php

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_page.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Fetch user data from the Users table
$user_query = "SELECT user_name, name, email, phone_no, status, reputation_score, profile_pic, created_at FROM Users WHERE user_id = ?";
$stmt_user = $conn->prepare($user_query);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user_data = $user_result->fetch_assoc();

// Close the statement
$stmt_user->close();

// Handle form submission for updating profile (example for future implementation)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // This is where you would handle updates to personal info or password
    // For now, it's just a placeholder.
    // You'd typically have separate forms/logic for profile pic, personal info, and password.
    echo "<script>alert('Profile update functionality not yet implemented!');</script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Profile - Athena</title>
  <link rel="stylesheet" href="profile_style.css" />
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
          <li><a href="user_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
          <li><a href="#"><i class="fas fa-users"></i> Study Groups</a></li>
          <li><a href="#"><i class="fas fa-book"></i> Notes</a></li>
          <li><a href="#"><i class="fas fa-comments"></i> Group Chat</a></li>
          <li><a href="#"><i class="fas fa-clipboard-list"></i> To-Do List</a></li>
          <li class="active"><a href="#"><i class="fas fa-user-circle"></i> Profile</a></li>
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
          <h2>Profile Settings</h2>
        </div>
        <div class="header-actions">
          <button class="save-changes-btn"><i class="fas fa-save"></i> Save Changes</button>
        </div>
      </header>

      <section class="profile-grid">
        <!-- Account Overview -->
        <div class="card account-overview-card">
          <h3>Account Overview</h3>
          <div class="card-content">
            <div class="info-item">
              <span class="info-label">Status</span>
              <span class="info-value"><?= htmlspecialchars($user_data['status'] ?? 'N/A') ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Created At</span>
              <span class="info-value"><?= htmlspecialchars(date('Y-m-d', strtotime($user_data['created_at'] ?? ''))) ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Reputation Score</span>
              <span class="info-value"><?= htmlspecialchars($user_data['reputation_score'] ?? '0') ?></span>
            </div>
          </div>
        </div>

        <!-- Personal Information -->
        <div class="card personal-info-card">
          <h3>Personal Information</h3>
          <div class="card-content">
            <div class="profile-avatar">
              <img src="<?= htmlspecialchars($user_data['profile_pic'] ?? 'https://placehold.co/100x100/0f0f2c/00ffd5?text=DP') ?>" alt="Profile Picture" class="avatar-img">
              <button class="change-avatar-btn">Change Avatar</button>
            </div>
            <div class="info-item">
              <span class="info-label">Full Name</span>
              <span class="info-value"><?= htmlspecialchars($user_data['name'] ?? 'N/A') ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Email Address</span>
              <span class="info-value"><?= htmlspecialchars($user_data['email'] ?? 'N/A') ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Phone No.</span>
              <span class="info-value"><?= htmlspecialchars($user_data['phone_no'] ?? 'N/A') ?></span>
            </div>
          </div>
        </div>

        <!-- Password Settings -->
        <div class="card password-settings-card full-width">
          <h3>Password Settings</h3>
          <div class="card-content">
            <form action="" method="POST">
              <div class="input-group">
                <label for="current-password">Current Password</label>
                <input type="password" id="current-password" name="current_password" placeholder="Enter current password">
              </div>
              <div class="input-group">
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new_password" placeholder="Enter new password">
              </div>
              <div class="input-group">
                <label for="confirm-password">Confirm New Password</label>
                <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm new password">
              </div>
              <!-- The Save Changes button from the header will apply to this form conceptually -->
            </form>
          </div>
        </div>
      </section>
    </main>
  </div>
</body>
</html>
