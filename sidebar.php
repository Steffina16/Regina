<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Modern Sidebar</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../css/sidebar.css">
</head>
<body>

  <!-- Menu button -->
  <div class="menu-btn" id="menuBtn">
    <span></span>
    <span></span>
    <span></span>
  </div>

  <!-- Sidebar -->
  <div class="sidebar" id="sidebar">
    <a href="../index.php">
      <div class="left"><i class="fas fa-home"></i> Home</div>
    </a>
    <a href="#" id="toggle-dark">
      <div class="left"><i id="darkIcon" class="fas fa-sun"></i> Dark/Light Mode</div>
      <span class="toggle" id="darkStatus">OFF</span>
    </a>
    <a href="#" id="theme-trigger">
      <div class="left"><i class="fas fa-palette"></i> Theme Color</div>
    </a>
    <a href="#" id="toggle-music">
    <div class="left">
      <i id="musicIcon" class="fas fa-music"></i> Soundtrack
    </div>
    <span class="toggle" id="musicStatus">OFF</span>
  </a>

    <!-- Hidden audio element -->
    <audio id="bg-music" loop>
      <source src="../sounds/song.mp3" type="audio/mpeg">
    </audio>


    <a href="#" id="toggle-sound">
      <div class="left">
        <i id="soundIcon" class="fas fa-volume-up"></i> Click Sound
      </div>
      <span class="toggle" id="soundStatus">ON</span>
    </a>

    <!-- Hidden audio element -->
    <audio id="click-sound" src="../sounds/click.ogg" preload="auto"></audio>
    </div>

  <!-- Theme color modal -->
  <div id="themeModal" class="theme-modal">
    <div class="modal-content">
      <h3>Pick a Theme Color</h3>
      <div class="color-options">
        <div class="color-circle" data-color="#ffb6c1" style="background:#ffb6c1;"></div>
        <div class="color-circle" data-color="#f8c8dc" style="background:#f8c8dc;"></div>
        <div class="color-circle" data-color="#ffe4e1" style="background:#ffe4e1;"></div>
        <div class="color-circle" data-color="#d8bfd8" style="background:#d8bfd8;"></div>
        <div class="color-circle" data-color="#e6e6fa" style="background:#e6e6fa;"></div>
        <div class="color-circle" data-color="#c1f0f6" style="background:#c1f0f6;"></div>
      </div>
    </div>
  </div>

  <script src="../js/sidebar.js"></script>
</body>
</html>
