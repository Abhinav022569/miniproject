<?php
session_start();
require '../../connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login_page.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch all tasks for the user
$tasks_query = "SELECT to_do_id, task, due_date, status FROM to_do WHERE user_id = ? ORDER BY due_date ASC";
$stmt = $conn->prepare($tasks_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Categorize tasks
$due_today = [];
$upcoming = [];
$completed = [];
$current_date = date('Y-m-d');

while ($row = $result->fetch_assoc()) {
    if ($row['status'] == 'done') {
        $completed[] = $row;
    } elseif ($row['due_date'] == $current_date) {
        $due_today[] = $row;
    } elseif ($row['due_date'] > $current_date) {
        $upcoming[] = $row;
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>To-Do List - Athena</title>
    <link rel="stylesheet" href="../panelstyle.css" />
    <link rel="stylesheet" href="to_do_style.css" />
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
                    <li><a href="../user_panel.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
                    <li><a href="../study_groups/study_group.php"><i class="fas fa-users"></i> Study Groups</a></li>
                    <li><a href="../notes/notes.php"><i class="fas fa-book"></i> Notes</a></li>
                    <li><a href="../chat_box/group_chat.php"><i class="fas fa-comments"></i> Group Chat</a></li>
                    <li class="active"><a href="to-do.php"><i class="fas fa-clipboard-list"></i> To-Do List</a></li>
                    <li><a href="../announcements/u_announcements.php"><i class="fas fa-bullhorn"></i> Announcements</a></li>
                    <li><a href="../profile/profile_page.php"><i class="fas fa-user-circle"></i> Profile</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="../../user_logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header class="main-header">
                <div class="welcome-section">
                    <h2>To-Do List</h2>
                </div>
            </header>

            <section class="todo-container">
                <!-- Add Task Form -->
                <div class="add-task-card">
                    <form id="add-task-form">
                        <input type="text" name="task" id="task-input" placeholder="Add a new task..." required>
                        <div class="date-picker">
                            <select name="day" required>
                                <?php for ($i = 1; $i <= 31; $i++): ?>
                                    <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                            <select name="month" required>
                                <?php for ($i = 1; $i <= 12; $i++): ?>
                                    <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>"><?= date('F', mktime(0, 0, 0, $i, 10)) ?></option>
                                <?php endfor; ?>
                            </select>
                            <select name="year" required>
                                <?php $current_year = date('Y'); ?>
                                <?php for ($i = $current_year; $i <= $current_year + 5; $i++): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <button type="submit" class="add-task-btn">Add Task</button>
                    </form>
                </div>

                <!-- Task Lists -->
                <div class="task-list">
                    <div id="due-today-section">
                        <h3>Due Today</h3>
                        <div id="due-today-list">
                            <?php foreach ($due_today as $task): ?>
                                <div class="task-item" data-task-id="<?= $task['to_do_id'] ?>">
                                    <div class="task-checkbox"><i class="far fa-circle"></i></div>
                                    <p class="task-text"><?= htmlspecialchars($task['task']) ?></p>
                                    <div class="task-due-date">Due: Today</div>
                                    <div class="task-delete"><i class="fas fa-trash-alt"></i></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div id="upcoming-section">
                        <h3>Upcoming Tasks</h3>
                        <div id="upcoming-list">
                             <?php foreach ($upcoming as $task): ?>
                                <div class="task-item" data-task-id="<?= $task['to_do_id'] ?>">
                                    <div class="task-checkbox"><i class="far fa-circle"></i></div>
                                    <p class="task-text"><?= htmlspecialchars($task['task']) ?></p>
                                    <div class="task-due-date">Due: <?= date('M d', strtotime($task['due_date'])) ?></div>
                                    <div class="task-delete"><i class="fas fa-trash-alt"></i></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div id="completed-section">
                        <h3>Completed Tasks</h3>
                        <div id="completed-list">
                            <?php foreach ($completed as $task): ?>
                                <div class="task-item completed" data-task-id="<?= $task['to_do_id'] ?>">
                                    <div class="task-checkbox"><i class="fas fa-check-circle"></i></div>
                                    <p class="task-text"><?= htmlspecialchars($task['task']) ?></p>
                                    <div class="task-due-date">Completed: <?= date('M d', strtotime($task['due_date'])) ?></div>
                                    <div class="task-delete"><i class="fas fa-trash-alt"></i></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <script src="to_do_script.js"></script>
</body>
</html>
