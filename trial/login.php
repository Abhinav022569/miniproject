<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>User Login</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <div class="login-left">
        <img src="../jpeg_files/bgimage.jpeg" alt="Illustration"/>
      </div>
      <div class="login-right">
        <h2>User Login</h2>
        <form action="/users/user_login.php" method="POST">
          <div class="input-group">
            <input type="text" name="username" placeholder="Username" required />
          </div>
          <div class="input-group">
            <input type="password" name="password" placeholder="Password" required />
          </div>
          <button type="submit" class="login-btn">LOGIN</button>
          <div class="extras">
            <a href="#">Forgot Username / Password?</a>
          </div>
          <div class="register-link">
            <p>Don't have an account? <a href="/users/registration/registeration.php">Create your Account →</a></p>
          </div>
        </form>
      </div>
    </div>
  </div>
</body>
</html>