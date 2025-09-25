<?php
// start session if not already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!-- Sidebar -->
<div class="menu-btn" id="menuBtn">
  <span></span><span></span><span></span>
</div>

<div class="sidebar" id="sidebar">

  <!-- Main links -->
  <?php if (isset($_SESSION['username'])): ?>
    <a href="login/profile.php">
      <div class="left"><i class="fas fa-user"></i> Profile</div>
    </a>
  <?php endif; ?>

  <a href="#" id="toggle-dark">
    <div class="left"><i id="darkIcon" class="fas fa-sun"></i> Dark/Light Mode</div>
    <span class="toggle" id="darkStatus">OFF</span>
  </a>

  <a href="#" id="theme-trigger">
    <div class="left"><i class="fas fa-palette"></i> Theme Color</div>
  </a>

  <a href="#" id="toggle-music">
    <div class="left"><i id="musicIcon" class="fas fa-music"></i> Soundtrack</div>
    <span class="toggle" id="musicStatus">OFF</span>
  </a>
  <audio id="bg-music" loop>
    <source src="sounds/song.mp3" type="audio/mpeg">
  </audio>

  <a href="#" id="toggle-sound">
    <div class="left"><i id="soundIcon" class="fas fa-volume-up"></i> Click Sound</div>
    <span class="toggle" id="soundStatus">ON</span>
  </a>
  <audio id="click-sound" src="sounds/click.ogg" preload="auto"></audio>

  <!-- Bottom logout (only visible when logged in) -->
  <div class="sidebar-bottom">
    <?php if (isset($_SESSION['username'])): ?>
      <a href="login/logout.php" class="sidebar-logout">
        <div class="left"><i class="fas fa-sign-out-alt"></i> Logout</div>
      </a>
    <?php endif; ?>
  </div>
</div>

<!-- Theme Modal -->
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
