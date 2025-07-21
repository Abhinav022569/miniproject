<?php
session_start();
// IMPORTANT: Keep require_once here to ensure $conn is available for all POST handling
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
    // Re-fetch $conn if it was closed by a previous operation (though it shouldn't be with this structure)
    if (!$conn || $conn->connect_error) {
        require '../connect.php';
    }

    $target_dir = "../../user_files/profile_pics/"; // Directory to save uploaded pictures
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($_FILES["profile_pic_upload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    $unique_file_name = uniqid('profile_') . '.' . $imageFileType;
    $target_file_unique = $target_dir . $unique_file_name;
    $db_file_path = "user_files/profile_pics/" . $unique_file_name;

    $check = getimagesize($_FILES["profile_pic_upload"]["tmp_name"]);
    if($check === false) {
        $error_message = "File is not an image.";
        $uploadOk = 0;
    }

    if ($_FILES["profile_pic_upload"]["size"] > 5000000) { // 5MB
        $error_message = "Sorry, your file is too large (max 5MB).";
        $uploadOk = 0;
    }

    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        $error_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["profile_pic_upload"]["tmp_name"], $target_file_unique)) {
            $update_query = "UPDATE users SET profile_pic = ? WHERE user_id = ?";
            $stmt_update = $conn->prepare($update_query);
            if ($stmt_update === false) {
                $error_message = "Prepare failed for profile pic update: " . $conn->error;
                error_log("Profile Pic Update Prepare Error: " . $conn->error);
            } else {
                $stmt_update->bind_param("si", $db_file_path, $user_id);
                if ($stmt_update->execute()) {
                    $success_message = "Profile picture updated successfully!";
                } else {
                    $error_message = "Error updating database: " . $stmt_update->error;
                    error_log("Profile Pic Update Execute Error: " . $stmt_update->error);
                }
                $stmt_update->close();
            }
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    }
}

// --- Handle Personal Information Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_personal_info_submit'])) {
    // Re-fetch $conn if it was closed by a previous operation
    if (!$conn || $conn->connect_error) {
        require '../connect.php';
    }

    $new_name = trim($_POST['name'] ?? '');
    $new_email = trim($_POST['email'] ?? '');
    $new_phone_no = trim($_POST['phone_no'] ?? '');

    if (empty($new_name) || empty($new_email) || empty($new_phone_no)) {
        $error_message = "All personal information fields are required.";
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        $check_duplicate_query = "SELECT user_id FROM users WHERE (email = ? OR phone_no = ?) AND user_id != ?";
        $stmt_check = $conn->prepare($check_duplicate_query);
        if ($stmt_check === false) {
            $error_message = "Prepare failed for duplicate check: " . $conn->error;
            error_log("Duplicate Check Prepare Error: " . $conn->error);
        } else {
            $stmt_check->bind_param("ssi", $new_email, $new_phone_no, $user_id);
            $stmt_check->execute();
            $duplicate_result = $stmt_check->get_result();

            if ($duplicate_result->num_rows > 0) {
                $error_message = "Email or Phone number already in use by another account.";
            } else {
                $update_query = "UPDATE users SET name = ?, email = ?, phone_no = ? WHERE user_id = ?";
                $stmt_update = $conn->prepare($update_query);
                if ($stmt_update === false) {
                    $error_message = "Prepare failed for personal info update: " . $conn->error;
                    error_log("Personal Info Update Prepare Error: " . $conn->error);
                } else {
                    $stmt_update->bind_param("sssi", $new_name, $new_email, $new_phone_no, $user_id);
                    if ($stmt_update->execute()) {
                        $success_message = "Personal information updated successfully!";
                        $_SESSION['name'] = $new_name;
                    } else {
                        $error_message = "Error updating personal information: " . $stmt_update->error;
                        error_log("Personal Info Update Execute Error: " . $stmt_update->error);
                    }
                    $stmt_update->close();
                }
            }
            $stmt_check->close();
        }
    }
}

// --- Handle Password Change ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password_submit'])) {
    // Re-fetch $conn if it was closed by a previous operation
    if (!$conn || $conn->connect_error) {
        require '../connect.php';
    }

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Fetch current password from the database
    $get_password_query = "SELECT password FROM users WHERE user_id = ?";
    $stmt_get_password = $conn->prepare($get_password_query);
    if ($stmt_get_password === false) {
        $error_message = "Prepare failed for get password: " . $conn->error;
        error_log("Get Password Prepare Error: " . $conn->error);
    } else {
        $stmt_get_password->bind_param("i", $user_id);
        $stmt_get_password->execute();
        $password_result = $stmt_get_password->get_result();
        $user_db_password = $password_result->fetch_assoc()['password'] ?? '';
        $stmt_get_password->close();

        // Validate current password (plain text comparison as per your DB)
        if ($current_password !== $user_db_password) {
            $error_message = "Current password is incorrect.";
        } elseif (empty($new_password) || empty($confirm_password)) {
            $error_message = "New password and confirm password fields cannot be empty.";
        } elseif ($new_password !== $confirm_password) {
            $error_message = "New password and confirm password do not match.";
        } elseif (strlen($new_password) < 6) {
            $error_message = "New password must be at least 6 characters long.";
        } else {
            // Update password in database
            // IMPORTANT: Still storing plain text. Hash passwords in a real app!
            $hashed_new_password = $new_password;

            $update_password_query = "UPDATE users SET password = ? WHERE user_id = ?";
            $stmt_update_password = $conn->prepare($update_password_query);
            if ($stmt_update_password === false) {
                $error_message = "Prepare failed for password update: " . $conn->error;
                error_log("Password Update Prepare Error: " . $conn->error);
            } else {
                $stmt_update_password->bind_param("si", $hashed_new_password, $user_id);
                if ($stmt_update_password->execute()) {
                    $success_message = "Password updated successfully!";
                } else {
                    $error_message = "Error updating password: " . $stmt_update_password->error;
                    error_log("Password Update Execute Error: " . $stmt_update_password->error);
                }
                $stmt_update_password->close();
            }
        }
    }
}


// --- Fetch user data from the Users table (after potential updates) ---
// This query is run after all POST handling to get the latest data
// Re-fetch $conn if it was closed by a previous operation (e.g., if a POST handler closed it)
if (!$conn || $conn->connect_error) {
    require '../connect.php';
}
$user_query = "SELECT user_name, name, email, phone_no, status, reputation_score, profile_pic, created_at FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($user_query);
if ($stmt_user === false) {
    error_log("User Data Fetch Prepare Error: " . $conn->error);
    // Handle error, maybe redirect or show a generic message
} else {
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $user_result = $stmt_user->get_result();
    $user_data = $user_result->fetch_assoc();
    $stmt_user->close();
}

// Close the database connection at the very end of the script
if ($conn && !$conn->connect_error) {
    $conn->close();
}
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
          <li><a href="./chat_box/group_chat.php"><i class="fas fa-comments"></i> Group Chat</a></li>
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
          <!-- The "Save Changes" button has been removed from here -->
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

        <!-- Personal Information -->
        <div class="card personal-info-card">
          <h3>Personal Information</h3>
          <div class="card-content">
            <div class="profile-avatar">
              <img src="../../<?= htmlspecialchars($user_data['profile_pic'] ?? 'https://placehold.co/100x100/0f0f2c/00ffd5?text=DP') ?>" alt="Profile Picture" class="avatar-img">
              <!-- Profile Picture Upload Form -->
              <form action="" method="POST" enctype="multipart/form-data" id="profile-pic-form" style="display: none;">
                <input type="file" name="profile_pic_upload" id="profile_pic_input" accept="image/*">
                <input type="submit" value="Upload" style="display: none;">
              </form>
              <button class="change-avatar-btn" id="trigger-pic-upload">Change Avatar</button>
            </div>
            <!-- Personal Information Update Form -->
            <form action="" method="POST" id="personal-info-form">
              <div class="input-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user_data['name'] ?? '') ?>" placeholder="Enter full name" required>
              </div>
              <div class="input-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email'] ?? '') ?>" placeholder="Enter email address" required>
              </div>
              <div class="input-group">
                <label for="phone_no">Phone No.</label>
                <input type="tel" id="phone_no" name="phone_no" value="<?= htmlspecialchars($user_data['phone_no'] ?? '') ?>" placeholder="Enter phone number" required>
              </div>
              <button type="submit" name="update_personal_info_submit" class="save-changes-btn-local"><i class="fas fa-save"></i> Save Personal Info</button>
            </form>
          </div>
        </div>

        <!-- Password Settings -->
        <div class="card password-settings-card">
          <h3>Password Settings</h3>
          <div class="card-content">
            <form action="" method="POST" id="password-change-form">
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
              <button type="submit" name="change_password_submit" class="save-changes-btn-local"><i class="fas fa-key"></i> Change Password</button>
            </form>
          </div>
        </div>

        <!-- Account Overview -->
        <div class="card account-overview-card full-width">
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
      </section>
    </main>
  </div>

  <!-- Link to the new dscript.js file -->
  <script src="dscript.js"></script>
</body>
</html>
