<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Registration</title>
  <link rel="stylesheet" href="regstyle.css">
</head>
<body>
  <header class="navbar">
    <div class="logo">ATHENA</div>
    <nav>
      <ul class="nav-links">
        <li><a href="../../index.php">Home</a></li>
        <li><a href="#">About</a></li>
        <li><a href="#">Services</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </nav>
    <div class="nav-btn">
      <a href="../login_page.php" class="login-btn">Login</a>
    </div>
  </header>

  <div class="login-container">
    <div class="login-box">
      <div class="login-left">
        <img src="../../jpeg_files/bgimage.jpeg" alt="Register Illustration">
      </div>
      <div class="login-right">
        <h2>Create Account</h2>
        <form action="user_register.php" method="POST">
          <div class="input-group">
            <input type="text" name="user_name" placeholder="Username" required>
          </div>
          <div class="input-group">
            <input type="email" name="email" placeholder="Email" required>
          </div>
          <div class="input-group">
            <input type="tel" name="phone_no" placeholder="Phone Number" required>
          </div>
          <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
          </div>
          <button type="submit" class="login-btn">Register</button>
          <div class="register-link">
            <p>Already have an account? <a href="../login_page.php">Login here</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>
