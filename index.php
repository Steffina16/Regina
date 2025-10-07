<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/login/db.php";

// check if logged in and get user avatar
$userAvatar = null;
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $stmt = $pdo->prepare("SELECT avatar FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $userAvatar = !empty($user['avatar']) ? $user['avatar'] : "albums/avatar/default.png";
}

// 🌟 Count total pictures in all albums
$albumFolders = ['concert','foods','Funnypic','ourpicture','picture','Scrapbook'];
$totalPictures = 0;
foreach ($albumFolders as $folder) {
    $path = __DIR__ . "/albums/$folder";
    if (is_dir($path)) {
        $files = glob("$path/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
        $totalPictures += count($files);
    }
}

// 🌟 Calculate how many years, months, days you and her have been together
$startDate = new DateTime("2023-01-01"); // change to your anniversary date
$today = new DateTime();
$diff = $today->diff($startDate);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Memories</title>
  <link rel="icon" type="image/x-icon" href="albums/placeholder/logo.png">

  <!-- Google Fonts -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Playfair+Display:ital@1&display=swap">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <!-- Styles -->
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/sidebar.css">
</head>
<body>

<?php include __DIR__ . '/indexsbar.php'; ?>

<!-- 🌸 Top Navigation Bar -->
<nav class="navbar">
  <div class="nav-icons">
    <a href="#" data-target="slideshow" class="home-icon active"><i class="fas fa-heart"></i></a>
    <a href="#" data-target="home"><i class="fas fa-home"></i></a>
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

<!-- 🎞️ Slideshow Section (First to Show) -->
<section id="slideshow" class="content-section active-section">
  <div class="message-container">
    <div id="slideshow-text" style="text-align:center; font-family:'Playfair Display', serif; font-size:1.5rem; color:#d23c67; margin-bottom:20px;">
      This is where we can see the memories we've made throughout the years 💖
    </div>
    <div id="slideshow-wrapper" style="display:flex; gap:10px; width:100%; height:350px; overflow:hidden;"></div>

    <!-- 🌟 Stats Under Slideshow -->
    <div class="stats-bar" style="text-align:center; margin-top:20px; font-family:'Playfair Display', serif; font-size:1.2rem; color:#d23c67;">
      <span>Total Pictures: <?= $totalPictures ?></span> |
      <span>Total Videos: 0</span> |
      <span>Time Together: <?= $diff->y ?> years, <?= $diff->m ?> months, <?= $diff->d ?> days</span>
    </div>
  </div>
</section>

<!-- 🏠 Home Section (Post Box + Feed) -->
<section id="home" class="content-section">
  <?php if (isset($_SESSION['username'])): ?>
  <div class="post-container">
    <div class="post-top">
      <img src="<?= htmlspecialchars($userAvatar) ?>" alt="User Avatar" class="post-avatar">
      <input type="text" placeholder="What's on your mind, <?= htmlspecialchars($username) ?>?" class="post-input">
    </div>
    <div class="post-bottom">
      <button class="post-option"><i class="fas fa-image"></i> Photo</button>
      <button class="post-option"><i class="fas fa-smile"></i> Feeling</button>
      <button class="post-option"><i class="fas fa-video"></i> Video</button>
    </div>
  </div>
  <?php endif; ?>

  <div class="feed-placeholder">
    <p>💬 Your posts will appear here soon...</p>
  </div>
</section>

<!-- 🎥 Videos Section -->
<section id="videos" class="content-section">
  <div class="video-container"><h1>Special Videos</h1></div>
</section>

<!-- 🖼️ Pictures Section -->
<section id="pictures" class="content-section">
  <div class="gallery-container">
    <div class="gallery-header">
      <div class="gallery-header-wrapper">
        <div class="gallery-title">Picture Gallery</div>
        <a href="upload.php" class="upload-btn">Upload</a>
      </div>

      <div class="category-previews">
        <a href="html/favorites.php?category=favorites" class="category-preview">
          <img src="albums/placeholder/favorite.jpg" alt="Your Favorites">
          <div class="preview-label">Favorites</div>
        </a>
        <a href="html/solo.php?category=Solo" class="category-preview">
          <img src="albums/placeholder/ganda.jpg" alt="Solo Pics">
          <div class="preview-label">Solo Pictures</div>
        </a>
        <a href="html/couple.php?category=Ourpicture" class="category-preview">
          <img src="albums/placeholder/Ourpicture-004.jpg" alt="Our Pictures">
          <div class="preview-label">Us Together</div>
        </a>
        <a href="html/Scrapbook.php?category=HBDscrapbook" class="category-preview">
          <img src="albums/placeholder/123.jpg" alt="Scrap Book">
          <div class="preview-label">Birthday Scrapbook</div>
        </a>
        <a href="html/concert.php?category=INCconcert" class="category-preview">
          <img src="albums/placeholder/inc.jpeg" alt="Concert">
          <div class="preview-label">Concert Date</div>
        </a>
        <a href="html/food.php?category=Yummyfoods" class="category-preview">
          <img src="albums/placeholder/foodie.jpg" alt="Food">
          <div class="preview-label">Shared Bites</div>
        </a>
        <a href="html/cute.php?category=cute" class="category-preview">
          <img src="albums/placeholder/picture ni chin-004.jpg" alt="Her Funny Moments">
          <div class="preview-label">Her Joker Side</div>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- 🧩 Quiz Section -->
<section id="quiz" class="content-section">
  <div class="quiz-container">
    <h1>Our Relationship Quiz</h1>
    <div class="quiz-intro">
      <p>Test Kung Gaano Moko kamahal.</p>
      <button id="start-quiz" class="quiz-button">Start Quiz</button>
    </div>
    <div id="quiz-questions" class="hidden"></div>
    <div id="quiz-results" class="hidden"></div>
  </div>  
</section>

<script src="js/script.js"></script>
<script src="js/sidebar.js"></script>
</body>
</html>
