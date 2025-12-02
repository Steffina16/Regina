<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/login/db.php";

// Check if logged in and get user avatar
$userAvatar = null;
$userId = null;
$username = null;
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $stmt = $pdo->prepare("SELECT id, avatar FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $userId = $user['id'];
    $userAvatar = !empty($user['avatar']) ? $user['avatar'] : "albums/avatar/default.png";
}

// Albums for display
$albumFolders = ['concert','foods','Funnypic','ourpicture','picture','Scrapbook'];
$albums = [];
foreach ($albumFolders as $folder) {
    $path = __DIR__ . "/albums/$folder";
    if (is_dir($path)) {
        $files = glob("$path/*.{jpg,jpeg,png,gif,mp4}", GLOB_BRACE);

        // Convert server paths to web paths
        $webFiles = [];
        foreach ($files as $file) {
            $webFiles[] = 'albums/' . $folder . '/' . basename($file);
        }

        $albums[$folder] = $webFiles;
    }
}

// Count stats from database (fast, no folder scan)
$totalPosts    = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
$totalPictures = $pdo->query("SELECT COUNT(*) FROM posts WHERE media_type='image'")->fetchColumn();
$totalVideos   = $pdo->query("SELECT COUNT(*) FROM posts WHERE media_type='video'")->fetchColumn();

// Calculate time together
$startDate = new DateTime("2023-01-01"); 
$today = new DateTime();
$diff = $today->diff($startDate);

// Handle new post submission
if (isset($_POST['submit_post']) && isset($userId)) {
    $content = $_POST['content'] ?? '';
    $mediaPath = null;
    $mediaType = $_POST['media_type'] ?? 'image';
    if (!empty($_FILES['media']['name'])) {
        $ext = pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . "." . $ext;
        $targetDir = "uploads/posts/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $targetFile = $targetDir . $filename;
        if (move_uploaded_file($_FILES['media']['tmp_name'], $targetFile)) {
            $mediaPath = $targetFile;
        }
    }
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, content, media, media_type) VALUES (:user_id, :content, :media, :media_type)");
    $stmt->execute([
        'user_id' => $userId,
        'content' => $content,
        'media' => $mediaPath,
        'media_type' => $mediaType
    ]);
    header("Location: index.php");
    exit;
}

// Slideshow images (randomized per refresh)
$slideshowFolders = ['ourpicture','picture','Funnypic'];
$slideshowImages = [];
foreach ($slideshowFolders as $folder) {
    $path = __DIR__ . "/albums/$folder";
    if (is_dir($path)) {
        $files = glob("$path/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
        foreach ($files as $file) {
            $slideshowImages[] = 'albums/' . $folder . '/' . basename($file);
        }
    }
}
shuffle($slideshowImages);
$slideshowImagesJSON = json_encode($slideshowImages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Memories</title>
  <link rel="icon" type="image/x-icon" href="albums/placeholder/logo.png">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Playfair+Display:ital@1&display=swap">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>

<?php include __DIR__ . '/indexsbar.php'; ?>

<nav class="navbar">
  <div class="nav-icons">
    <a href="#" data-target="slideshow" class="home-icon active"><i class="fas fa-heart"></i></a>
    <a href="#" data-target="home" id="homeLink"><i class="fas fa-home"></i></a>
    <a href="#" data-target="videos"><i class="fas fa-video"></i></a>
    <div class="dropdown">
      <button class="dropbtn"><i class="fas fa-envelope"></i></button>
      <div class="dropdown-content">
        <a href="html/message.html">Birthday Message</a>
      </div>
    </div>
    <a href="#" data-target="pictures"><i class="fas fa-image"></i></a>
    <a href="#" data-target="quiz"><i class="fas fa-question-circle"></i></a>
  </div>

  <div class="auth-links">
    <?php if (isset($_SESSION['username'])): ?>
      <div class="profile-dropdown">
        <img src="<?= htmlspecialchars($userAvatar) ?>" alt="Profile" class="profile-icon" id="profileBtn">
        <div class="profile-menu" id="profileMenu">
          <a href="login/profile.php"><i class="fas fa-user"></i> Profile</a>
          <a href="login/settings.php"><i class="fas fa-cog"></i> Settings</a>
          <a href="login/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
      </div>
    <?php else: ?>
      <a href="login/login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> Login</a>
    <?php endif; ?>
  </div>
</nav>

<section id="slideshow" class="content-section active-section">
  <div class="message-container">
    <div id="slideshow-text" style="text-align:center; font-family:'Playfair Display', serif; font-size:1.5rem; color:#d23c67; margin-bottom:20px;">
      This is where we can see the memories we've made 💖
    </div>
    <div id="slideshow-wrapper">
      <div class="album-spacer"></div>
      <?php foreach ($albums as $folder => $files): ?>
        <div class="album-section">
          <h3><?= htmlspecialchars($folder) ?></h3>
          <div style="display:flex; gap:5px; flex-wrap:wrap;">
            <?php foreach ($files as $file):
              $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
              if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
                <img src="<?= htmlspecialchars($file) ?>" class="album-img" style="height:150px;">
              <?php elseif ($ext === 'mp4'): ?>
                <video controls class="album-video" style="height:150px;">
                  <source src="<?= htmlspecialchars($file) ?>" type="video/mp4">
                </video>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
      <div class="album-spacer"></div>
    </div>

    <div class="stats-bar" style="text-align:center; margin-top:20px;">
      <span>Total Pictures: <?= $totalPictures ?></span> |
      <span>Total Posts: <?= $totalPosts ?></span> |
      <span>Total Videos: <?= $totalVideos ?></span> |
      <span>Time Together: <?= $diff->y ?>y, <?= $diff->m ?>m, <?= $diff->d ?>d</span>
    </div>
  </div>
</section>

<section id="home" class="content-section">
<?php if (isset($_SESSION['username'])): ?>
<form method="POST" enctype="multipart/form-data">
  <div class="post-container">
    <div class="post-top">
      <img src="<?= htmlspecialchars($userAvatar) ?>" alt="User Avatar" class="post-avatar">
      <input type="text" name="content" placeholder="What's on your mind, <?= htmlspecialchars($username) ?>?" class="post-input">
    </div>
    <div class="post-bottom">
      <label for="media-upload" class="file-icon-btn"><i class="fas fa-image"></i></label>
      <input type="file" id="media-upload" name="media" style="display:none;">
      <select name="media_type">
        <option value="image">Image</option>
        <option value="video">Video</option>
      </select>
      <button type="submit" name="submit_post"></button>
    </div>
  </div>
</form>

<div class="feed-container">
<?php
$stmt = $pdo->query("
  SELECT posts.*, users.username, users.avatar 
  FROM posts 
  JOIN users ON posts.user_id = users.id 
  ORDER BY posts.created_at DESC
");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($posts as $post):
?>
  <div class="feed-post" data-post-id="<?= $post['id'] ?>">
    <div class="post-header">
      <div class="post-header-left">
        <img src="<?= htmlspecialchars($post['avatar']) ?>" class="post-avatar">
        <span class="post-username"><?= htmlspecialchars($post['username']) ?></span>
      </div>
      <div style="position:relative;">
        <span class="post-time"><?= date("M d, Y H:i", strtotime($post['created_at'])) ?></span>
        <?php if (isset($userId) && $post['user_id'] == $userId): ?>
        <div class="options-container">
          <i class="fas fa-ellipsis-h options-icon"></i>
          <div class="options-menu">
            <button type="button" class="trigger-delete" data-post-id="<?= $post['id'] ?>">Delete</button>
          </div>
        </div>  
        <?php endif; ?>
      </div>
    </div>
    <div class="post-content">
      <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
      <?php if ($post['media']): ?>
        <?php if ($post['media_type'] === 'image'): ?>
          <img src="<?= htmlspecialchars($post['media']) ?>" class="post-media">
        <?php else: ?>
          <video controls class="post-media">
            <source src="<?= htmlspecialchars($post['media']) ?>" type="video/mp4">
          </video>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
<?php endforeach; ?>
</div>

<?php else: ?>
<div style="text-align:center; padding:50px; font-size:1.2rem; color:#d23c67;">
  Please <a href="login/login.php" style="color:#ff6393; text-decoration:underline;">login</a> to see posts.
</div>
<?php endif; ?>
</section>

<script>
window.slideshowImages = <?= $slideshowImagesJSON ?>;
</script>

<script src="js/script.js"></script>
<script src="js/sidebar.js"></script>
<script>
// Delete modal
const modal = document.getElementById('deleteModal');
let selectedPostId = null;
document.querySelectorAll('.trigger-delete').forEach(btn => {
  btn.addEventListener('click', () => {
    selectedPostId = btn.dataset.postId;
    modal.style.display = 'block';
  });
});
document.getElementById('cancelDelete').addEventListener('click', () => {
  selectedPostId = null;
  modal.style.display = 'none';
});
document.getElementById('confirmDelete').addEventListener('click', () => {
  if (!selectedPostId) return;
  fetch('api/delete_post.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'post_id=' + encodeURIComponent(selectedPostId)
  }).then(() => location.reload());
});

// Home link protection for guests
const homeLink = document.getElementById('homeLink');
homeLink.addEventListener('click', e => {
    e.preventDefault();
    const loggedIn = <?= isset($_SESSION['username']) ? 'true' : 'false'; ?>;
    if (!loggedIn) {
        alert("Please login to see posts.");
        return;
    }
    document.querySelectorAll('.content-section').forEach(sec => sec.classList.remove('active-section'));
    document.getElementById('home').classList.add('active-section');
});
</script>
</body>
</html>
