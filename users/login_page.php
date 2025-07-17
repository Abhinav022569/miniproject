<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Login</title>
  <link rel="stylesheet" href="userstyle.css" />
</head>
<body>
  <header class="navbar">
    <div class="logo">Athena</div>
    <nav>
      <ul class="nav-links">
        <li><a href="../index.php">Home</a></li>
        <li><a href="#">About</a></li>
        <li><a href="#">Services</a></li>
        <li><a href="#">Contact</a></li>
      </ul>
    </nav>
    <div class="nav-btn">
      <a href="registration/registeration.php" class="login-btn">Register</a>
    </div>
  </header>
  <div class="login-container">
    <div class="login-box">
      <div class="login-left">
        <img src="../jpeg_files/bgimage.jpeg" alt="Illustration"/>
      </div>
      <div class="login-right">
        <h2>Login</h2>
        <form action="./user_login.php" method="POST">
          <div class="input-group">
            <input type="text" name="username" placeholder="Username" required />
          </div>
          <div class="input-group">
            <input type="password" name="password" placeholder="Password" required />
          </div>
          <button type="submit" class="login-btn">LOGIN</button>
          <div class="extras">
            <a href="#">Forgot Password?</a>
          </div>
          <div class="register-link">
            <p>Don't have an account? <a href="./registration/registeration.php">Create your Account →</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>