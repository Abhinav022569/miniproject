<?php
session_start();
require '../../connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// --- Handle Profile Picture Upload ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic_upload'])) {
    if (!$conn || $conn->connect_error) {
        require '../../connect.php';
    }

    $target_dir = "../../../user_files/profile_pics/";
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

    if ($_FILES["profile_pic_upload"]["size"] > 5000000) {
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
            $stmt_update->bind_param("si", $db_file_path, $user_id);
            if ($stmt_update->execute()) {
                $success_message = "Profile picture updated successfully!";
            } else {
                $error_message = "Error updating database: " . $stmt_update->error;
            }
            $stmt_update->close();
        } else {
            $error_message = "Sorry, there was an error uploading your file.";
        }
    }
}

// --- Handle Personal Information Update ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_personal_info_submit'])) {
    if (!$conn || $conn->connect_error) {
        require '../../connect.php';
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
        $stmt_check->bind_param("ssi", $new_email, $new_phone_no, $user_id);
        $stmt_check->execute();
        $duplicate_result = $stmt_check->get_result();

        if ($duplicate_result->num_rows > 0) {
            $error_message = "Email or Phone number already in use by another account.";
        } else {
            $update_query = "UPDATE users SET name = ?, email = ?, phone_no = ? WHERE user_id = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param("sssi", $new_name, $new_email, $new_phone_no, $user_id);
            if ($stmt_update->execute()) {
                $success_message = "Personal information updated successfully!";
                $_SESSION['name'] = $new_name;
            } else {
                $error_message = "Error updating personal information: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
        $stmt_check->close();
    }
}

// --- Handle Password Change ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password_submit'])) {
    if (!$conn || $conn->connect_error) {
        require '../../connect.php';
    }

    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $get_password_query = "SELECT password FROM users WHERE user_id = ?";
    $stmt_get_password = $conn->prepare($get_password_query);
    $stmt_get_password->bind_param("i", $user_id);
    $stmt_get_password->execute();
    $password_result = $stmt_get_password->get_result();
    $user_db_password = $password_result->fetch_assoc()['password'] ?? '';
    $stmt_get_password->close();

    if ($current_password !== $user_db_password) {
        $error_message = "Current password is incorrect.";
    } elseif (empty($new_password) || empty($confirm_password)) {
        $error_message = "New password and confirm password fields cannot be empty.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New password and confirm password do not match.";
    } elseif (strlen($new_password) < 6) {
        $error_message = "New password must be at least 6 characters long.";
    } else {
        $hashed_new_password = $new_password;
        $update_password_query = "UPDATE users SET password = ? WHERE user_id = ?";
        $stmt_update_password = $conn->prepare($update_password_query);
        $stmt_update_password->bind_param("si", $hashed_new_password, $user_id);
        if ($stmt_update_password->execute()) {
            $success_message = "Password updated successfully!";
        } else {
            $error_message = "Error updating password: " . $stmt_update_password->error;
        }
        $stmt_update_password->close();
    }
}


if (!$conn || $conn->connect_error) {
    require '../../connect.php';
}
$user_query = "SELECT user_name, name, email, phone_no, status, reputation_score, profile_pic, created_at FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($user_query);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user_data = $user_result->fetch_assoc();
$stmt_user->close();

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
  <link rel="stylesheet" href="../panelstyle.css" />
  <link rel="stylesheet" href="profile_style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
  <div class="dashboard-wrapper">
    <aside class="sidebar">
      <div class="sidebar-header">
        <div class="logo">ATHENA</div>
      </div>
      <nav class="sidebar-nav">
        <ul>
          <li><a href="../user_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
          <li><a href="../study_groups/study_group.php"><i class="fas fa-users"></i> Study Groups</a></li>
          <li><a href="../notes/notes.php"><i class="fas fa-book"></i> Notes</a></li>
          <li><a href="../chat_box/group_chat.php"><i class="fas fa-comments"></i> Group Chat</a></li>
          <li><a href="../to-do/to-do.php"><i class="fas fa-clipboard-list"></i> To-Do List</a></li>
          <li><a href="../announcements/u_announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
          <li class="active"><a href="profile_page.php"><i class="fas fa-user-circle"></i> Profile</a></li>
        </ul>
      </nav>
      <div class="sidebar-footer">
        <a href="../../user_logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </aside>

    <main class="main-content">
      <header class="main-header">
        <div class="welcome-section">
          <h2>Profile Settings</h2>
        </div>
        <!-- NEW: Header actions container for the delete button -->
        <div class="header-actions">
            <button id="delete-account-trigger-btn" class="delete-account-btn-header"><i class="fas fa-trash-alt"></i> Delete Account</button>
        </div>
      </header>

      <section class="profile-grid">
        <?php if ($success_message): ?>
            <div class="message success-message"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="message error-message"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>

        <div class="card personal-info-card">
          <h3>Personal Information</h3>
          <div class="card-content">
            <div class="profile-avatar">
              <img src="../../../<?= htmlspecialchars($user_data['profile_pic'] ?? 'https://placehold.co/100x100/0f0f2c/00ffd5?text=DP') ?>" alt="Profile Picture" class="avatar-img">
              <form action="" method="POST" enctype="multipart/form-data" id="profile-pic-form" style="display: none;">
                <input type="file" name="profile_pic_upload" id="profile_pic_input" accept="image/*">
                <input type="submit" value="Upload" style="display: none;">
              </form>
              <button class="change-avatar-btn" id="trigger-pic-upload">Change Avatar</button>
            </div>
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
              <button type="submit" name="change_password_submit" class="save-changes-btn-local" style="margin-top: auto;"><i class="fas fa-key"></i> Change Password</button>
            </form>
          </div>
        </div>

        <div class="card account-overview-card">
          <h3>Account Overview</h3>
          <div class="card-content">
            <div class="reputation-score-container">
                <div class="reputation-score-label">Reputation Score</div>
                <div class="reputation-score-value"><?= htmlspecialchars($user_data['reputation_score'] ?? '0') ?></div>
            </div>
            <div class="info-item">
              <span class="info-label">Status</span>
              <span class="info-value"><?= htmlspecialchars($user_data['status'] ?? 'N/A') ?></span>
            </div>
            <div class="info-item">
              <span class="info-label">Created At</span>
              <span class="info-value"><?= htmlspecialchars(date('Y-m-d', strtotime($user_data['created_at'] ?? ''))) ?></span>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
  
  <!-- NEW: Confirmation Modal for Account Deletion -->
  <div id="delete-confirm-modal" class="modal-overlay" style="display: none;">
      <div class="modal-content">
          <h3>Confirm Account Deletion</h3>
          <p>Are you sure you want to permanently delete your account? This action cannot be undone.</p>
          <div class="modal-actions">
              <button id="cancel-delete-btn" class="modal-btn cancel-btn">Cancel</button>
              <form action="delete_account.php" method="POST" style="display: inline;">
                  <button type="submit" id="confirm-delete-btn" class="modal-btn confirm-btn">Delete Account</button>
              </form>
          </div>
      </div>
  </div>

  <script src="../dscript.js"></script>
</body>
</html>
