<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login</title>
  <link rel="stylesheet" href="userstyle.css">
</head>
<body>
  <div class="login-box">
    <div class="login-header">
      <h2>Login <span>to your account</span></h2>
    </div>
    <form action="user_login.php" method="POST">
      <div class="input-box">
        <i class="icon user"></i>
        <input type="text" name="username" placeholder="Username or Email" required>
      </div>
      <div class="input-box">
        <i class="icon lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>
      <div class="extras">
        <label><input type="checkbox" name="remember"> Remember me</label>
        <a href="#">Forgot password?</a>
      </div>
      <button type="submit" class="signin-btn">Sign In</button>
    </form>
  </div>
</body>
</html>
