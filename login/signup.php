<?php
require 'db.php';

$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $error = "Password must be at least 8 characters, with 1 uppercase letter and 1 number.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        try {
            $stmt->execute([$username, $email, $hashed]);
            header("Location: login.php?signup=success");
            exit();
        } catch (PDOException $e) {
            $error = "Username or email already taken!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign Up | Memories</title>
  <link rel="stylesheet" href="../css/signup.css">
  <link rel="icon" type="image/x-icon" href="albums/placeholder/logo.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <div class="signup-container">
    <div class="signup-box">
      <h2>Welcome Love 🤍</h2>
      <p class="subtitle">Create your account to save memories ✨</p>

      <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form method="post" id="signupForm">
        <div class="input-group">
          <label>Username</label>
          <input type="text" name="username" placeholder="Choose a username" 
                 value="<?= htmlspecialchars($username) ?>" required>
          <small class="error-msg"></small>
        </div>

        <div class="input-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="Enter your email" 
                 value="<?= htmlspecialchars($email) ?>" required>
          <small class="error-msg"></small>
        </div>

        <div class="input-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="At least 8 chars, 1 uppercase, 1 number" required>
          <small class="error-msg"></small>
        </div>

        <div class="input-group">
          <label>Confirm Password</label>
          <input type="password" name="confirm_password" placeholder="Re-enter your password" required>
          <small class="error-msg"></small>
        </div>

        <button type="submit" class="signup-btn">Sign Up</button>
      </form>

      <div class="divider"><span>or</span></div>

      <div class="social-signup">
        <button class="google-btn"><i class="fab fa-google"></i> Google</button>
        <button class="facebook-btn"><i class="fab fa-facebook-f"></i> Facebook</button>
      </div>

      <p class="login-text">
        Already have an account? <a href="login.php">Log In</a>
      </p>
    </div>
  </div>

  <script src="../js/signup.js"></script>
</body>
</html>
