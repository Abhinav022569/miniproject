<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Athena</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="logo">Athena</div>
    <nav>
      <a href="#home">Home</a>
      <a href="#about">About</a>
      <a href="#services">Services</a>
      <a href="#contact">Contact</a>
    </nav>
    <div class="login-dropdown">
      <button class="login-btn">Login ▼</button>
      <div class="dropdown-content">
        <a href="./admin/login_page.php">Admin</a>
        <a href="./users/login_page.php">User</a>
      </div>
    </div>
  </header>

  <main>
    <section id="home" class="fade-in-section">
      <div class="section-content">
        <h2>Welcome to Athena</h2>
        <p>The futuristic platform for collaborative learning and seamless note sharing among students.</p>
      </div>
    </section>

    <section id="about" class="fade-in-section">
      <div class="section-content">
        <h2>About Athena</h2>
        <p>Athena helps students share notes, collaborate in study groups, and stay organized in an interactive learning environment.</p>
      </div>
    </section>

    <section id="services" class="fade-in-section">
      <div class="section-content">
        <h2>What You Can Do</h2>
        <div class="features-grid">
          <div class="feature-card">
            <h3>📚 Join/Create Study Groups</h3>
            <p>Collaborate with peers in focused study environments.</p>
          </div>
          <div class="feature-card">
            <h3>📤 Upload Notes</h3>
            <p>Contribute your knowledge by sharing notes and materials.</p>
          </div>
          <div class="feature-card">
            <h3>📥 Download Notes</h3>
            <p>Access and download useful notes shared by others.</p>
          </div>
          <div class="feature-card">
            <h3>💬 Group Messaging</h3>
            <p>Stay in touch with your group using built-in messaging.</p>
          </div>
          <div class="feature-card">
            <h3>📝 Task Lists</h3>
            <p>Track your academic tasks with personalized to-do lists.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="contact" class="fade-in-section">
      <div class="section-content">
        <h2>Contact</h2>
        <p>Email us at <a href="mailto:support@athena.com" style="color: #00ffd5;">support@athena.com</a></p>
      </div>
    </section>
  </main>

  <footer>
    <p>&copy; <?php echo date("Y"); ?> Athena. All rights reserved.</p>
  </footer>

  <script src="script.js"></script>
    <canvas id="bgCanvas"></canvas>

</body>
</html>
