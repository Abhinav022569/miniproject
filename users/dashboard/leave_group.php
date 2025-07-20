<?php
session_start();
require '../connect.php'; // Ensures the database connection is available

/**
 * leave_group.php
 *
 * This script handles the server-side logic for a user leaving a study group.
 * It performs the following actions:
 * 1. Starts a session and connects to the database.
 * 2. Verifies that a user is logged in; otherwise, it exits.
 * 3. Retrieves the user_id from the session and the group_id from the POST request.
 * 4. Validates the incoming group_id.
 * 5. IMPORTANT: Checks if the user is the creator of the group. Creators cannot "leave" their
 * own groups via this method; they would need a separate "delete group" functionality.
 * This prevents orphaned groups.
 * 6. If the user is a valid member (and not the creator), it executes a DELETE query
 * to remove the user's record from the `group_members` table.
 * 7. Returns a JSON response indicating the success or failure of the operation,
 * along with a descriptive message.
 */

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'User not logged in. Please log in to continue.']);
    exit();
}

$user_id = $_SESSION['user_id'];
// Get group_id from the POST request sent by the JavaScript fetch call
$group_id = $_POST['group_id'] ?? 0;

// Validate that the group_id is a valid number
if ($group_id == 0) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid group ID provided.']);
    exit();
}

// --- Business Logic: Prevent group creator from leaving their own group ---
// A user who creates a group is its owner and should not be able to leave it.
// They should have an option to delete the group instead.
$check_creator_query = "SELECT user_id FROM study_group WHERE group_id = ? AND user_id = ?";
$stmt_check_creator = $conn->prepare($check_creator_query);
$stmt_check_creator->bind_param("ii", $group_id, $user_id);
$stmt_check_creator->execute();
$result_creator = $stmt_check_creator->get_result();

if ($result_creator->num_rows > 0) {
    // If the user is the creator, send back an error message.
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Group creators cannot leave their own group. You can manage or delete it instead.']);
    $stmt_check_creator->close();
    $conn->close();
    exit();
}
$stmt_check_creator->close();

// --- Database Operation: Remove the user from the group ---
// Prepare the DELETE statement to remove the user's membership from the specified group.
$delete_query = "DELETE FROM group_members WHERE group_id = ? AND user_id = ?";
$stmt_delete = $conn->prepare($delete_query);
$stmt_delete->bind_param("ii", $group_id, $user_id);

// Set the content type to JSON for the response
header('Content-Type: application/json');

if ($stmt_delete->execute()) {
    // Check if a row was actually deleted to confirm they were a member
    if ($stmt_delete->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Successfully left the group!']);
    } else {
        // This case handles scenarios where the user wasn't a member to begin with.
        echo json_encode(['success' => false, 'message' => 'You were not a member of this group.']);
    }
} else {
    // Handle any SQL execution errors
    error_log("Failed to leave group: " . $stmt_delete->error); // Log error for debugging
    echo json_encode(['success' => false, 'message' => 'Failed to leave the group due to a server error.']);
}

// Close the statement and the database connection
$stmt_delete->close();
$conn->close();
?>
