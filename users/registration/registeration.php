<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
  <link rel="stylesheet" href="../userstyle.css">
</head>
<body>
  <div class="login-box">
    <div class="login-header">
      <h2>Register <span>as a new user</span></h2>
    </div>
    <form action="user_register.php" method="POST">
      <div class="input-box">
        <i class="icon user"></i>
        <input type="text" name="user_name" placeholder="Username" required>
      </div>

      <div class="input-box">
        <i class="icon mail"></i>
        <input type="email" name="email" placeholder="Email" required>
      </div>

      <div class="input-box">
        <i class="icon phone"></i>
        <input type="tel" name="phone_no" placeholder="Phone Number" required>
      </div>

      <div class="input-box">
        <i class="icon lock"></i>
        <input type="password" name="password" placeholder="Password" required>
      </div>

      <button type="submit" class="signin-btn">Register</button>
    </form>
  </div>
</body>
</html>
