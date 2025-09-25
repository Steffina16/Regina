<?php
session_start();
require_once "db.php";

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login/login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user info from DB
$stmt = $pdo->prepare("SELECT username, email, avatar FROM users WHERE username = :username LIMIT 1");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Fallback if avatar missing
$avatarPath = !empty($user['avatar']) ? $user['avatar'] : "albums/avatar/default.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profile | Memories</title>
  <link rel="stylesheet" href="../css/styles.css"> <!-- global -->
  <link rel="stylesheet" href="../css/profile.css"> <!-- profile-specific -->
  <link rel="icon" type="image/x-icon" href="../albums/placeholder/logo.png">
</head>
<body>

<div class="profile-container">
  <img src="../<?= htmlspecialchars($avatarPath) ?>" alt="Avatar" class="profile-avatar">

  <div class="profile-name"><?= htmlspecialchars($user['username']) ?></div>
  <div class="profile-email"><?= htmlspecialchars($user['email']) ?></div>

  <div class="profile-actions">
    <a href="edit_profile.php" class="edit-btn">Edit Profile</a>
    <a href="logout.php" class="logout-btn">Logout</a>
  </div>
</div>

</body>
</html>
