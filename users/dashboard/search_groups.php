<?php
session_start();
require '../connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$search_term = $_GET['search'] ?? '';

if (empty($search_term)) {
    echo json_encode([]);
    exit();
}

$search_term_like = "%" . $search_term . "%";

// REVISED QUERY: Using LEFT JOIN for a more robust membership check
$query = "
    SELECT 
        sg.group_id, 
        sg.group_name, 
        sg.description,
        (SELECT COUNT(*) FROM group_members WHERE group_id = sg.group_id) as member_count,
        COUNT(gm_user.user_id) as is_member
    FROM 
        study_group sg
    LEFT JOIN 
        group_members gm_user ON sg.group_id = gm_user.group_id AND gm_user.user_id = ?
    WHERE 
        (sg.group_name LIKE ? OR sg.description LIKE ?) AND sg.approved = 1
    GROUP BY
        sg.group_id, sg.group_name, sg.description
";

$stmt = $conn->prepare($query);
// The parameter binding remains the same
$stmt->bind_param("iss", $user_id, $search_term_like, $search_term_like);
$stmt->execute();
$result = $stmt->get_result();

$groups = [];
while ($row = $result->fetch_assoc()) {
    $groups[] = $row;
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($groups);
