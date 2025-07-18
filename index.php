<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Home - Athena</title>
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

  <section id="home" class="fade-in-section">
    <h2>Welcome to Athena</h2>
    <p>The futuristic platform for collaborative learning and seamless note sharing among students.</p>
  </section>

  <section id="about" class="fade-in-section">
    <h2>About Us</h2>
    <p>NoteShare is built with the vision to empower students to share, access, and collaborate over academic materials efficiently. Say goodbye to traditional and slow channels — we’re bringing speed, accessibility, and community.</p>
  </section>

  <section id="services" class="fade-in-section">
    <h2>Features for Users</h2>
    <div class="features-grid">
      <div class="feature-card">
        <h3>Join/Create Study Groups</h3>
        <p>Collaborate with peers by creating or joining groups that match your study goals and topics.</p>
      </div>
      <div class="feature-card">
        <h3>Upload Notes</h3>
        <p>Share your own notes to contribute to the learning community and get feedback.</p>
      </div>
      <div class="feature-card">
        <h3>Download Notes</h3>
        <p>Access notes from other users or group members quickly and conveniently.</p>
      </div>
      <div class="feature-card">
        <h3>Group Messaging</h3>
        <p>Communicate with fellow group members using a built-in messaging system.</p>
      </div>
      <div class="feature-card">
        <h3>Task Lists</h3>
        <p>Stay organized with a personalized to-do list to manage your academic tasks.</p>
      </div>
    </div>
  </section>

  <section id="contact"  class="fade-in-section">
    <h2>Contact Us</h2>
    <p>Have questions or feedback? Reach out via email: <a href="mailto:support@noteshare.com" style="color: #00ffd5; text-decoration: underline;">support@noteshare.com</a></p>
  </section>

  <footer>
    <p>&copy; 2025 Athena. All rights reserved.</p>
  </footer>
<script src="script.js"></script>
</body>
</html>