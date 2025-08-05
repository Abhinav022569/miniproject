<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Home - ATHENA</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<div id="vanta-bg"></div>

<header>
  <div class="logo"><a href="index.php">ATHENA</a></div>
  <nav>
    <a href="#home">Home</a>
    <a href="#about">About</a>
    <a href="#services">Features</a>
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
        <p>
        Discover a smarter way to learn and grow with <strong>Athena</strong> — a futuristic platform designed to empower students with collaborative tools, easy access to shared notes, and seamless group communication.
        </p>
    </div>
  </section>

  <section id="about" class="fade-in-section">
    <div class="section-content">
        <h2>About Athena</h2>
        <p>
        Athena is a next-generation platform built to enhance student collaboration and knowledge sharing. We believe learning should be accessible, organized, and empowering — not scattered or stressful.
        </p>
        <p>
        With features like group discussions, note sharing, and task management, Athena transforms how students connect and succeed together. Whether you're working solo or as part of a study group, Athena is your digital academic partner.
        </p>
    </div>
  </section>

  <section id="services" class="fade-in-section">
    <div class="section-content">
      <h2>Features for Users</h2>
      <div class="features-grid">
        <div class="feature-card">
          <h3>Join/Create Study Groups</h3>
          <p>Collaborate with your peers by forming groups for targeted learning.</p>
        </div>
        <div class="feature-card">
          <h3>Upload Notes</h3>
          <p>Share your insights and help others while building your own knowledge.</p>
        </div>
        <div class="feature-card">
          <h3>Download Notes</h3>
          <p>Instantly access notes from your peers and group members.</p>
        </div>
        <div class="feature-card">
          <h3>Group Messaging</h3>
          <p>Chat and share ideas within your study groups easily.</p>
        </div>
        <div class="feature-card">
          <h3>To-Do List</h3>
          <p>Manage your academic tasks efficiently using built-in reminders.</p>
        </div>
      </div>
    </div>
  </section>

  <section id="contact" class="fade-in-section">
    <div class="section-content">
      <h2>Contact Us</h2>
      <p>Have questions or feedback?</p><p> Email us at:
        <a href="mailto:support@athena.com" style="color: #00ffd5;">support@athena.com</a>
      </p>
    </div>
  </section>

        <footer>
            <p>&copy; <?php echo date("Y"); ?> ATHENA. All rights reserved.</p>
        </footer>

</main>
<!-- VANTA.NET -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.net.min.js"></script>
<script src="script.js"></script>
</body>
</html>
