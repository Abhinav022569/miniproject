<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Registration</title>
  <link rel="stylesheet" href="regstyle.css">
</head>
<body>
    <header class="navbar">
    <div class="logo">Athena</div>
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
    <div class="login-left">
      <img src="../../jpeg_files/bgimagereg.jpeg" alt="Register Illustration">
    </div>
    <div class="login-right">
      <div class="login-card">
        <h2>Register</h2>
        <form action="user_register.php" method="POST">
          <div class="input-group">
            <label for="email">Username</label>
            <input type="text" name="user_name" required>
          </div>
          <div class="input-group">
            <label for="email">Email</label>
            <input type="email" name="email" required>
          </div>
          <div class="input-group">
            <label for="phone_no">Phone Number</label>
            <input type="tel" name="phone_no" required>
          </div>
          <div class="input-group">
            <label for="password">Password</label>
            <input type="password" name="password" required>
          </div>
          <button type="submit" class="btn">Create Account</button>
        </form>
        <div class="login-footer">
          Already have an account? <a href="../login_page.php">Login</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
