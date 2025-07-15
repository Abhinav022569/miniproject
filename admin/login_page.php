<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="adminstyle.css">
</head>
<body>
  <div class="login-box">
    <div class="login-header">
      <h2>login <span>to administration</span></h2>
    </div>
    <form action="admin_login.php" method="POST">
      <div class="input-box">
        <i class="icon user"></i>
        <input type="text" name="username" placeholder="username" required>
      </div>
      <div class="input-box">
        <i class="icon lock"></i>
        <input type="password" name="password" placeholder="password" required>
      </div>
      <div class="extras">
        <label><input type="checkbox" name="remember"> remember me</label>
      </div>
      <button type="submit" class="signin-btn">sign in</button>
    </form>
  </div>
</body>
</html>
