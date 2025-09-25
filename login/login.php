<?php
session_start();

// If already logged in, redirect
if (isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Capture error messages from login_process.php
$error = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Memories</title>
  <link rel="stylesheet" href="../css/login.css">
  <link rel="icon" type="image/x-icon" href="../albums/placeholder/logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="login-container">
    <div class="login-box">
      <h2>Hello Love 🤍</h2>
      <p class="subtitle">Log in to access your gallery favorites ✨</p>

      <?php if ($error): ?>
        <p class="error" style="color:red;"><?= $error ?></p>
      <?php endif; ?>

      <form action="login_process.php" method="POST">
        <div class="input-group">
          <label for="username">Username or Email</label>
          <input type="text" id="username" name="username" placeholder="Enter username or email" required>
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter password" required>
        </div>

        <button type="submit" class="login-btn">Login</button>
      </form>

      <p class="signup-text">
        Don’t have an account? <a href="signup.php">Sign Up</a>
      </p>
    </div>
  </div>
</body>
</html>
