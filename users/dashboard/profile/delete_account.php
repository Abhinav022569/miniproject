<?php
session_start();
require '../../connect.php';

// Security check: Ensure user is logged in and this is a POST request.
if (!isset($_SESSION['user_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../login_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Begin a transaction to ensure all or nothing is deleted.
$conn->begin_transaction();

try {
    // --- Step 1: Handle groups created by the user ---
    // Find all groups created by this user.
    $created_groups_query = "SELECT group_id FROM study_group WHERE user_id = ?";
    $stmt_created_groups = $conn->prepare($created_groups_query);
    $stmt_created_groups->bind_param("i", $user_id);
    $stmt_created_groups->execute();
    $created_groups_result = $stmt_created_groups->get_result();
    
    while ($group = $created_groups_result->fetch_assoc()) {
        $group_id = $group['group_id'];
        // For each group, delete all related data.
        $conn->query("DELETE FROM downloaded_notes WHERE note_id IN (SELECT note_id FROM notes WHERE group_id = $group_id)");
        $conn->query("DELETE FROM notes WHERE group_id = $group_id");
        $conn->query("DELETE FROM group_messages WHERE group_id = $group_id");
        $conn->query("DELETE FROM group_members WHERE group_id = $group_id");
        $conn->query("DELETE FROM reports WHERE group_id = $group_id");
        $conn->query("DELETE FROM study_group WHERE group_id = $group_id");
    }
    $stmt_created_groups->close();

    // --- Step 2: Delete user's personal data from various tables ---
    // The order is important to avoid foreign key constraint violations.
    
    // Delete their downloaded notes records.
    $conn->query("DELETE FROM downloaded_notes WHERE user_id = $user_id");
    // Delete reports they made or were targeted in.
    $conn->query("DELETE FROM reports WHERE user_id = $user_id OR target_id = $user_id");
    // Delete their messages.
    $conn->query("DELETE FROM group_messages WHERE user_id = $user_id");
    // Delete notes they uploaded.
    $conn->query("DELETE FROM notes WHERE user_id = $user_id");
    // Delete their to-do items.
    $conn->query("DELETE FROM to_do WHERE user_id = $user_id");
    // Delete their memberships in any groups.
    $conn->query("DELETE FROM group_members WHERE user_id = $user_id");

    // --- Step 3: Finally, delete the user from the users table ---
    $stmt_delete_user = $conn->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt_delete_user->bind_param("i", $user_id);
    $stmt_delete_user->execute();
    $stmt_delete_user->close();

    // If all queries were successful, commit the transaction.
    $conn->commit();

    // --- Step 4: Log the user out and destroy the session ---
    session_destroy();
    header("Location: ../../login_page.php?message=account_deleted");
    exit();

} catch (Exception $e) {
    // If any query fails, roll back the transaction.
    $conn->rollback();
    // Redirect back to the profile page with an error message.
    $_SESSION['error_message'] = "Could not delete account. An error occurred: " . $e->getMessage();
    header("Location: profile_page.php");
    exit();
}

$conn->close();
?>
