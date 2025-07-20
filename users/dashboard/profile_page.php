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

// Initialize message variables
$success_message = '';
$error_message = '';

// --- Handle Profile Picture Upload ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic_upload'])) {
    $target_dir = "../../user_files/profile_pics/"; // Directory to save uploaded pictures
    // Create the directory if it doesn't exist
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Create recursively with full permissions
    }

    $file_name = basename($_FILES["profile_pic_upload"]["name"]);
    $target_file = $target_dir . $file_name;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Generate a unique filename to prevent overwriting and conflicts
    $unique_file_name = uniqid('profile_') . '.' . $imageFileType;
    $target_file_unique = $target_dir . $unique_file_name;
    $db_file_path = "user_files/profile_pics/" . $unique_file_name; // Path to save in DB

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["profile_pic_upload"]["tmp_name"]);
    if($check !== false) {
        // file is an image
        $uploadOk = 1;
    } else {
        $error_message = "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (e.g., max 5MB)
    if ($_FILES["profile_pic_upload"]["size"] > 5000000) { // 5MB
        $error_message = "Sorry, your file is too large (max 5MB).";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        // Error message already set
    } else {
        // if everything is ok, try to upload file
        if (move_uploaded_file($_FILES["profile_pic_upload"]["tmp_name"], $target_file_unique)) {
            // Update profile_pic path in database
            $update_query = "UPDATE users SET profile_pic = ? WHERE user_id = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param("si", $db_file_path, $user_id);

            if ($stmt_update->execute()) {
                $success_message = "Profile picture updated successfully!";
                // Update session variable if you store profile_pic there
                // $_SESSION['profile_pic'] = $db_file_path;
            } else {
                $error_message = "Error updating database: " . $stmt_update->error;
            }
            $stmt_update->close();
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    }
}

// --- Fetch user data from the Users table (after potential update) ---
$user_query = "SELECT user_name, name, email, phone_no, status, reputation_score, profile_pic, created_at FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($user_query);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user_data = $user_result->fetch_assoc();

// Close the statement and connection
$stmt_user->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Profile - Athena</title>
  <!-- Link to the main panelstyle.css for overall dashboard layout and sidebar -->
  <link rel="stylesheet" href="panelstyle.css" />
  <!-- Link to the profile_style.css for specific profile page styling -->
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
          <li><a href="study_group.php"><i class="fas fa-users"></i> Study Groups</a></li>
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
          <!-- This button conceptually saves changes from the forms below -->
          <!-- For now, it's just a visual button, actual form submission is handled by specific forms -->
          <button class="save-changes-btn"><i class="fas fa-save"></i> Save Changes</button>
        </div>
      </header>

      <section class="profile-grid">
        <!-- Display messages -->
        <?php if ($success_message): ?>
            <div class="message success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="message error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

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
              <!-- CORRECTED: Added ../../ to the src path to correctly reference the root-level user_files directory -->
              <img src="../../<?= htmlspecialchars($user_data['profile_pic'] ?? 'https://placehold.co/100x100/0f0f2c/00ffd5?text=DP') ?>" alt="Profile Picture" class="avatar-img">
              <!-- Profile Picture Upload Form -->
              <form action="" method="POST" enctype="multipart/form-data" id="profile-pic-form" style="display: none;">
                <input type="file" name="profile_pic_upload" id="profile_pic_input" accept="image/*">
                <!-- A submit button is needed, but can be hidden and triggered by JS -->
                <input type="submit" value="Upload" style="display: none;">
              </form>
              <button class="change-avatar-btn" id="trigger-pic-upload">Change Avatar</button>
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

  <script>
    // JavaScript to trigger file input when "Change Avatar" button is clicked
    document.getElementById('trigger-pic-upload').addEventListener('click', function() {
        document.getElementById('profile_pic_input').click();
    });

    // Automatically submit the form when a file is selected
    document.getElementById('profile_pic_input').addEventListener('change', function() {
        if (this.files.length > 0) {
            document.getElementById('profile-pic-form').submit();
        }
    });
  </script>
</body>
</html>
