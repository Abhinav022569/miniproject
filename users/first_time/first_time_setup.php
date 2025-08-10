<?php
session_start();
require '../connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$user_query = "SELECT name FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($user_query);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();
$stmt_user->close();

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
    <link rel="stylesheet" href="setup_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="setup-container">
        <div class="setup-box">
            <div class="setup-header">
                <h1>Welcome to Athena!</h1>
                <p>Let's set up your profile.</p>
            </div>
            <form id="setup-form" action="save_setup.php" method="POST">
                <!-- NEW: Main content area with a two-column layout -->
                <div class="setup-main-content">
                    <!-- Left Column for Personal Info -->
                    <div class="setup-column-left">
                        <div class="form-section">
                            <label>Choose a Profile Picture</label>
                            <div class="profile-pic-selector">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <img src="profile_pics/pic<?php echo $i; ?>.jpg" alt="Profile Picture <?php echo $i; ?>" class="profile-pic-option" data-pic="users/first_time/profile_pics/pic<?php echo $i; ?>.jpg">
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" name="profile_pic_path" id="profile-pic-path-input" required>
                        </div>
                        <div class="form-section">
                            <label for="full-name">Your Full Name</label>
                            <input type="text" id="full-name" name="full_name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" placeholder="Enter your full name" required>
                        </div>
                        <div class="form-section">
                            <label for="phone-no">Your Phone Number</label>
                            <input type="tel" id="phone-no" name="phone_no" placeholder="Enter your 10-digit phone number" required>
                        </div>
                    </div>

                    <!-- Right Column for Study Groups -->
                    <div class="setup-column-right">
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
                                            <button type="button" class="join-btn" data-group-id="<?php echo $group['group_id']; ?>">Join</button>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <p>No study groups are available to join at the moment.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="launch-btn">
                    <i class="fas fa-rocket"></i> Launch into Athena
                </button>
            </form>
        </div>
    </div>
    <script src="setup_script.js"></script>
</body>
</html>
