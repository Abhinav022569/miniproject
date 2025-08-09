<?php
session_start();
// The connect.php file is two directories up from the current location.
require '../connect.php';

// If the user is not logged in, redirect them to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's current name to pre-fill the input field.
$user_query = "SELECT name FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($user_query);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();
$stmt_user->close();

// Fetch all approved study groups for the user to join.
$groups_query = "SELECT group_id, group_name, description FROM study_group WHERE approved = 1 ORDER BY group_name ASC";
$groups_result = $conn->query($groups_query);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Athena! - Setup</title>
    <!-- Link to the new stylesheet for this page. -->
    <link rel="stylesheet" href="setup_style.css">
    <!-- Font Awesome for icons. -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="setup-container">
        <div class="setup-box">
            <div class="setup-header">
                <h1>Welcome to Athena!</h1>
                <p>Let's set up your profile.</p>
            </div>
            <!-- The form will be submitted to save_setup.php to process the data. -->
            <form id="setup-form" action="save_setup.php" method="POST">
                <!-- Section for choosing a profile picture. -->
                <div class="form-section">
                    <label>Choose a Profile Picture</label>
                    <div class="profile-pic-selector">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <img src="profile_pics/pic<?php echo $i; ?>.jpg" alt="Profile Picture <?php echo $i; ?>" class="profile-pic-option" data-pic="users/first_time/profile_pics/pic<?php echo $i; ?>.jpg">
                        <?php endfor; ?>
                    </div>
                    <!-- A hidden input to store the path of the selected profile picture. -->
                    <input type="hidden" name="profile_pic_path" id="profile-pic-path-input" required>
                </div>

                <!-- Section for entering the user's full name. -->
                <div class="form-section">
                    <label for="full-name">Your Full Name</label>
                    <input type="text" id="full-name" name="full_name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" placeholder="Enter your full name" required>
                </div>

                <!-- Section for joining study groups. -->
                <div class="form-section">
                    <label for="group-search">Join Your First Study Groups</label>
                    <input type="text" id="group-search" placeholder="Search groups...">
                    <div class="study-groups-list">
                        <?php if ($groups_result && $groups_result->num_rows > 0): ?>
                            <?php while($group = $groups_result->fetch_assoc()): ?>
                                <div class="group-item" data-group-name="<?php echo htmlspecialchars(strtolower($group['group_name'])); ?>">
                                    <div class="group-info">
                                        <h4><?php echo htmlspecialchars($group['group_name']); ?></h4>
                                        <p><?php echo htmlspecialchars($group['description']); ?></p>
                                    </div>
                                    <!-- The join button will be handled by JavaScript. -->
                                    <button type="button" class="join-btn" data-group-id="<?php echo $group['group_id']; ?>">Join</button>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No study groups are available to join at the moment.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- The main submit button for the form. -->
                <button type="submit" class="launch-btn">
                    <i class="fas fa-rocket"></i> Launch into Athena
                </button>
            </form>
        </div>
    </div>
    <!-- Link to the new JavaScript file for this page. -->
    <script src="setup_script.js"></script>
</body>
</html>
