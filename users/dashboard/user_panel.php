<?php
require '../connect.php';
session_start();

// Dummy user_id (replace with session-based one)
$user_id = 1;

// Fetch Study Groups
$groups_query = "SELECT group_name, created_at FROM study_groups WHERE user_id = $user_id LIMIT 3";
$groups_result = $conn->query($groups_query);

// Fetch Upcoming Tasks
$tasks_query = "SELECT task FROM to_do WHERE user_id = $user_id LIMIT 3";
$tasks_result = $conn->query($tasks_query);

// Fetch Recently Downloaded Notes
$notes_query = "
  SELECT notes.title, downloaded_notes.download_date 
  FROM downloaded_notes 
  JOIN notes ON downloaded_notes.note_id = notes.note_id 
  WHERE downloaded_notes.user_id = $user_id 
  ORDER BY downloaded_notes.download_date DESC 
  LIMIT 3
";
$notes_result = $conn->query($notes_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Dashboard - Athena</title>
  <link rel="stylesheet" href="dashboard.css" />
</head>
<body>
  <header class="navbar">
    <div class="logo">Athena</div>
    <nav>
      <ul class="nav-links">
        <li><a href="../index.php">Home</a></li>
        <li><a href="#">Groups</a></li>
        <li><a href="#">Notes</a></li>
        <li><a href="#">Tasks</a></li>
      </ul>
    </nav>
    <div class="nav-btn">
      <a href="../logout.php" class="logout-btn">Logout</a>
    </div>
  </header>

  <main class="dashboard-container">
    <section class="dashboard-section">
      <h2>Study Groups</h2>
      <div class="card-grid">
        <?php while($row = $groups_result->fetch_assoc()): ?>
          <div class="card">
            <h3><?= htmlspecialchars($row['group_name']) ?></h3>
            <span><?= htmlspecialchars($row['created_at']) ?></span>
          </div>
        <?php endwhile; ?>
      </div>
    </section>

    <section class="dashboard-section">
      <h2>Upcoming Tasks</h2>
      <div class="card-grid">
        <?php while($row = $tasks_result->fetch_assoc()): ?>
          <div class="card">
            <h3><?= htmlspecialchars($row['title']) ?></h3>
          </div>
        <?php endwhile; ?>
      </div>
    </section>

    <section class="dashboard-section">
      <h2>Recent Notes</h2>
      <div class="card-grid">
        <?php while($row = $notes_result->fetch_assoc()): ?>
          <div class="card">
            <h3><?= htmlspecialchars($row['note_title']) ?></h3>
            <p>Downloaded: <?= htmlspecialchars($row['download_date']) ?></p>
          </div>
        <?php endwhile; ?>
      </div>
    </section>
  </main>
</body>
</html>