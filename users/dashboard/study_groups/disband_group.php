<?php
session_start();
require '../../connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

// Ensure the request is a POST request and group_id is set
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['group_id'])) {
    // Redirect with an error message if the request is invalid
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: study_group.php");
    exit();
}

$group_id = $_POST['group_id'];
$current_user_id = $_SESSION['user_id'];

// Use a transaction to ensure all deletions succeed or none do
$conn->begin_transaction();

try {
    // Security Check: Verify the current user is the creator of the group
    $sql_check_creator = "SELECT user_id FROM study_group WHERE group_id = ?";
    $stmt_check = $conn->prepare($sql_check_creator);
    $stmt_check->bind_param("i", $group_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows == 0) {
        throw new Exception("Group not found.");
    }
    
    $group_data = $result_check->fetch_assoc();
    
    if ($group_data['user_id'] != $current_user_id) {
        // If the user is not the creator, roll back and show an error
        throw new Exception("You do not have permission to disband this group.");
    }
    $stmt_check->close();

    // Proceed with deletion in the correct order to avoid foreign key errors

    // 1. Delete from downloaded_notes (which is linked to the notes table)
    $sql_dn = "DELETE dn FROM downloaded_notes dn JOIN notes n ON dn.note_id = n.note_id WHERE n.group_id = ?";
    $stmt_dn = $conn->prepare($sql_dn);
    $stmt_dn->bind_param("i", $group_id);
    $stmt_dn->execute();
    $stmt_dn->close();

    // 2. Delete from notes
    $sql_notes = "DELETE FROM notes WHERE group_id = ?";
    $stmt_notes = $conn->prepare($sql_notes);
    $stmt_notes->bind_param("i", $group_id);
    $stmt_notes->execute();
    $stmt_notes->close();

    // 3. Delete from group_messages
    $sql_msgs = "DELETE FROM group_messages WHERE group_id = ?";
    $stmt_msgs = $conn->prepare($sql_msgs);
    $stmt_msgs->bind_param("i", $group_id);
    $stmt_msgs->execute();
    $stmt_msgs->close();

    // 4. Delete from to_do
    $sql_todo = "DELETE FROM to_do WHERE group_id = ?";
    $stmt_todo = $conn->prepare($sql_todo);
    $stmt_todo->bind_param("i", $group_id);
    $stmt_todo->execute();
    $stmt_todo->close();
    
    // 5. Delete from group_members
    $sql_members = "DELETE FROM group_members WHERE group_id = ?";
    $stmt_members = $conn->prepare($sql_members);
    $stmt_members->bind_param("i", $group_id);
    $stmt_members->execute();
    $stmt_members->close();

    // 6. Finally, delete the group itself from the study_group table
    $sql_group = "DELETE FROM study_group WHERE group_id = ?";
    $stmt_group = $conn->prepare($sql_group);
    $stmt_group->bind_param("i", $group_id);
    $stmt_group->execute();
    $stmt_group->close();

    // If all queries were successful, commit the transaction
    $conn->commit();
    $_SESSION['success_message'] = "Group was successfully disbanded.";

} catch (Exception $e) {
    // If any step failed, roll back all changes
    $conn->rollback();
    $_SESSION['error_message'] = "An error occurred: " . $e->getMessage();
}

$conn->close();
// CORRECTED: Redirect back to the study_group.php in the *same* directory.
header("Location: study_group.php");
exit();
?>
