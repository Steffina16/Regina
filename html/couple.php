<?php
session_start();
$folder = "../albums/ourpicture/"; // folder path
$files = glob($folder . "*.{jpg,jpeg,png,gif,JPG,PNG,JPEG,GIF}", GLOB_BRACE);
$files = array_values($files);

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Our Picture ✨</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Playfair+Display:ital,wght@0,400;1,700&display=swap">
  <link rel="stylesheet" href="../css/album.css">
  <link rel="icon" type="image/x-icon" href="../albums/placeholder/logo.png">
  <?php include '../sidebar.php'; ?>
</head>
<body>
  <div class="gallery-container">
    <a href="../index.php#pictures" class="back-btn">← Back to Main</a>

    <?php if($is_logged_in): ?>
    <a href="favorites.php" class="favorites-btn" style="margin-left: 10px; background: #ff4757; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">❤️ My Favorites</a>
    <?php endif; ?>

    <div class="album-container">
      <h2 class="category-title">Our Picture ✨</h2>
      <div class="gallery" id="gallery"></div>
      <div class="pagination" id="pagination"></div>
    </div>
  </div>

  <div class="floating-hearts">
    <div class="heart">❤️</div>
    <div class="heart">❤️</div>
    <div class="heart">❤️</div>
  </div>

  <div id="lightbox">
    <img id="lightbox-img" src="" alt="Enlarged Image">
  </div>

  <div id="notification"></div>

  <script>
    const images = <?php echo json_encode($files); ?>;
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const currentAlbum = 'ourpicture';
  </script>

  <script src="../js/Solo.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            if (window.favoriteSystem) {
                window.favoriteSystem.attachEventListeners();
                window.favoriteSystem.checkExistingFavorites();
            }
        }, 100);
    });
  </script>
</body>
</html>
