<?php
session_start();
require_once "db.php";

// Redirect if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login/login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch current info
$stmt = $pdo->prepare("SELECT username, email, avatar FROM users WHERE username = :username LIMIT 1");
$stmt->execute(['username' => $username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newEmail = $_POST['email'] ?? $user['email'];
    $avatarPath = $user['avatar']; // keep current avatar if none uploaded

    // Handle avatar upload
    if (!empty($_FILES['avatar']['name'])) {
        $targetDir = __DIR__ . "/../albums/avatar/"; // absolute path
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = uniqid() . "_" . basename($_FILES["avatar"]["name"]);
        $targetFile = $targetDir . $fileName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES["avatar"]["type"], $allowedTypes)) {
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $targetFile)) {
                // Save relative path for DB
                $avatarPath = "albums/avatar/" . $fileName;
            }
        }
    }

    // Update DB (email + avatar)
    $stmt = $pdo->prepare("UPDATE users SET email = :email, avatar = :avatar WHERE username = :username");
    $stmt->execute([
        'email' => $newEmail,
        'avatar' => $avatarPath,
        'username' => $username
    ]);

    // Redirect back to profile
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Profile | Memories</title>
  <link rel="stylesheet" href="../css/styles.css"> <!-- global -->
  <link rel="stylesheet" href="../css/profile.css"> <!-- profile-specific -->
</head>
<body>

<div class="profile-container">
  <h2>Edit Profile</h2>
  <form action="" method="POST" enctype="multipart/form-data">
    <div class="form-group">
      <label>Username:</label>
      <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled>
    </div>

    <div class="form-group">
      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
    </div>

    <div class="form-group">
      <label>Avatar:</label><br>
      <img src="../<?= htmlspecialchars($user['avatar']) ?>" alt="Current Avatar" class="profile-avatar">
      <input type="file" name="avatar" accept="image/*">
    </div>

    <button type="submit" class="save-btn">Save Changes</button>
  </form>
</div>

</body>
</html>
