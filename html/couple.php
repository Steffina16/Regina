<?php
// solo.php
$folder = "../albums/ourpicture/"; // adjust if your folder path differs
$files = glob($folder . "*.{jpg,jpeg,png,gif,JPG,PNG,JPEG,GIF}", GLOB_BRACE);
$files = array_values($files); // reindex array
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Solo Glamour</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Playfair+Display:ital,wght@0,400;1,700&display=swap">
  <link rel="stylesheet" href="../css/album.css">
  <link rel="icon" type="image/x-icon" href="../albums/placeholder/logo.png">
</head>
<body>
  <!-- Sidebar include -->
  <?php include '../sidebar.php'; ?>

  <div class="gallery-container">
    <a href="../index.html#pictures" class="back-btn">← Back to Main</a>
    <div class="album-container">
      <h2 class="category-title">Our Picture ✨</h2>

      <!-- Gallery and pagination containers -->
      <div class="gallery" id="gallery"></div>
      <div class="pagination" id="pagination"></div>
    </div>
  </div>

  <!-- Floating hearts decoration -->
  <div class="floating-hearts">
    <div class="heart">❤️</div>
    <div class="heart">❤️</div>
    <div class="heart">❤️</div>
  </div>

  <!-- Single Lightbox -->
  <div id="lightbox">
      <img id="lightbox-img" src="" alt="Enlarged Image">
  </div>

  <!-- pass PHP list to JS -->
  <script>
    const images = <?php echo json_encode($files); ?>;
  </script>

  <!-- include your JS (use the same filename / path your project references) -->
  <script src="../js/Solo.js"></script>
</body>
</html>
