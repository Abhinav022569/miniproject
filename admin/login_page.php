<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login - Athena</title>
  <link rel="stylesheet" href="adminstyle.css" />
</head>
<body>
  <!-- Header section, similar to user login page -->
  <header class="navbar">
    <div class="logo">ATHENA</div>
    <nav>
      <ul class="nav-links">
        <li><a href="../index.php">Home</a></li>
        <li><a href="../index.php#about">About</a></li>
        <li><a href="../index.php#services">Services</a></li>
        <li><a href="../index.php#contact">Contact</a></li>
      </ul>
    </nav>
    <!-- Admin login doesn't need a register button, so it's omitted -->
    <div class="nav-btn">
      <a href="../users/login_page.php" class="login-btn">User Login</a>
    </div>
  </header>

  <!-- Login container to center the content -->
  <div class="login-container">
    <!-- Login box with glass effect -->
    <div class="login-box glass">
      <!-- Left side for illustration/image -->
      <div class="login-left">
        <!-- Using bgimage1.jpeg as it's a generic image and available in the jpeg_files directory -->
        <img src="../jpeg_files/bgimage3.gif" alt="Admin Illustration"/>
      </div>
      <!-- Right side for the login form -->
      <div class="login-right">
        <h2>Admin Login</h2>
        <!-- Form action points to admin_login.php for processing -->
        <form action="admin_login.php" method="POST">
          <!-- Input group for username -->
          <div class="input-group">
            <input type="text" name="username" placeholder="Username" required />
          </div>
          <!-- Input group for password -->
          <div class="input-group">
            <input type="password" name="password" placeholder="Password" required />
          </div>
          <!-- Submit button -->
          <button type="submit" class="login-btn">LOGIN</button>
          <!-- Extras section (e.g., Forgot Password) -->
          <div class="extras">
            <a href="#">Forgot Password?</a>
          </div>
          <!-- No registration link for admin login -->
        </form>
      </div>
    </div>
  </div>
</body>
</html>
