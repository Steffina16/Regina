<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Concert Scrapbook</title>
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@600&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/concert.css">
  <link rel="icon" type="image/x-icon" href="../albums/placeholder/logo.png">
  <?php include '../sidebar.php'; ?>
</head>
<body>
  <div class="scrapbook">
    <!-- Back button -->
    <a href="../index.html#pictures" class="back-btn">← Back to Main</a>

    <h1 class="title">Our Concert Scrapbook</h1>

    <!-- Top row of pictures -->
    <div class="row">
      <div class="photo">
        <img src="../albums/concert/concerttopleft.jpg" alt="Top Photo 1">
        <p>Couples together 💕</p>
      </div>
      <div class="photo">
        <img src="../albums/concert/concerttop.jpg" alt="Top Photo 2">
        <p> Wristbands ✨</p>
      </div>
    <div class="photo">
    <video controls muted playsinline class="photo-video">
     <source src="../albums/concert/Concert.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    <p> Concert 🎶 </p>
      </div>
    </div>

    <!-- Slideshow in the middle (JS will inject images here) -->
    <div class="slideshow">
      <div id="slideshow" class="slides"></div>
      <button id="prev" class="arrow left">&#10094;</button>
      <button id="next" class="arrow right">&#10095;</button>
    </div>
    
    <!-- Bottom row of pictures -->
    <div class="row">
      <div class="photo">
        <img src="../albums/concert/concertbotleft.jpg" alt="Bottom Photo 1">
        <p> Foodie 🍽️❤️ </p>
      </div>
      <div class="photo">
        <img src="../albums/concert/concertbot.jpg" alt="Bottom Photo 2">
        <p>Glowing Wristbands everywhere 🌈</p>
      </div>
      <div class="photo">
        <img src="../albums/concert/concertbotright.jpg" alt="Bottom Photo 3">
        <p> Dinner Date 💖</p>
      </div>
    </div>

        <!-- Lightbox overlay -->
    <div id="concert-lightbox">
      <img id="concert-lightbox-img" src="" alt="Enlarged Image">
    </div>

    <p class="caption">A scrapbook of music & memories 💖</p>
  </div>

  <script src="../js/concert.js"></script>
</body>
</html>
