<?php
// start session before any HTML output
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Memories</title>

  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Playfair+Display:ital@1&display=swap">
  <link rel="stylesheet" href="css/styles.css">
  <link rel="stylesheet" href="css/sidebar.css">
  <link rel="icon" type="image/x-icon" href="albums/placeholder/logo.png">
</head>
<body>

  <!-- ✅ include sidebar -->
  <?php include __DIR__ . '/indexsbar.php'; ?>

<nav class="navbar">
  <a href="#" data-target="videos">Videos</a>

  <div class="dropdown">
      <button type="button" class="dropbtn">Message Menu</button>
      <div class="dropdown-content">
          <a href="html/message.html">Birthday Message</a>
      </div>
  </div>

  <a href="#" data-target="pictures">Pictures</a>
  <a href="#" data-target="quiz">Quiz</a>

  <div class="auth-links" style="margin-left:auto;">
    <?php if (isset($_SESSION['username'])): ?>
        <!-- When logged in, Login becomes Logout -->
        <a href="login/logout.php" class="login-btn">Logout</a>
    <?php else: ?>
        <a href="login/login.php" class="login-btn">Login</a>
    <?php endif; ?>
  </div>
</nav>


  <!-- ✅ Sections -->
  <section id="home" class="content-section active-section">
      <div class="message-container">
          <div id="slideshow-text" style="
              text-align: center;
              font-family: 'Playfair Display', serif;
              font-size: 1.5rem;
              color: #d23c67;
              margin-bottom: 20px;
          ">
              This is where we can see the memories we've made throughout the years 💖
          </div>
          <div id="slideshow-wrapper" style="
              display: flex;
              gap: 10px;
              width: 100%;
              height: 350px;
              overflow: hidden;
          ">
        <!-- Images injected here by JS -->
      </div>
    </div>
  </section>

  <!-- Videos Section -->
  <section id="videos" class="content-section">
    <div class="video-container">
      <h1>Special Videos</h1>
    </div>
  </section>

  <!-- Pictures Section -->
  <section id="pictures" class="content-section">
    <div class="gallery-container">
      <div class="gallery-header">
        <div class="gallery-header-wrapper">
          <div class="gallery-title">Picture Gallery</div>
          <a href="upload.php" class="upload-btn">Upload</a>
        </div>

        <div class="category-previews">
          <a href="html/favorites.php?category=favorites" class="category-preview">
            <img src="albums/placeholder/picture ni chin-004.jpg" alt="Your Favorites">
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
            <img src="albums/placeholder/inc.jpeg" alt="Food">
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

  <!-- Quiz Section -->
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

  <!-- Scripts -->
  <script src="js/script.js"></script>
  <script src="js/sidebar.js"></script>
</body>
</html>
