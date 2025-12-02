  <?php
  if (session_status() === PHP_SESSION_NONE) {
      session_start();
  }
  require_once __DIR__ . "/login/db.php";

  // ensure $albums exists by scanning the albums/ folder
  $albums = [];
  $albumsDir = __DIR__ . '/albums';
  if (is_dir($albumsDir)) {
      foreach (scandir($albumsDir) as $folder) {
          if ($folder === '.' || $folder === '..') continue;
          $folderPath = $albumsDir . '/' . $folder;
          if (!is_dir($folderPath)) continue;
          $files = [];
          foreach (scandir($folderPath) as $f) {
              if ($f === '.' || $f === '..') continue;
              $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
              if (in_array($ext, ['jpg','jpeg','png','gif','mp4'])) {
                  // path relative to  so <img src="albums/..."> works
                  $files[] = 'albums/' . $folder . '/' . $f;
              }
          }
          if (!empty($files)) $albums[$folder] = $files;
      }
  }

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

  // Count total posts
  $totalPosts = $pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();

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
    <!-- Custom Styles -->
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/sidebar.css">
  </head>
  <body>

  <?php include __DIR__ . '/indexsbar.php'; ?>

  <!-- 🌸 Navbar -->
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

  <!-- 🎞️ Slideshow Section (lightweight) -->
  <section id="slideshow" class="content-section active-section">
    <div class="message-container">
      <div id="slideshow-text" style="text-align:center; font-family:'Playfair Display', serif; font-size:1.5rem; color:#d23c67; margin-bottom:20px;">
        This is where we can see the memories we've made 💖
      </div>

      <div id="slideshow-wrapper">
        <div class="album-spacer"></div>

        <?php
        // show only a small preview per album to avoid loading all images
        $thumbLimit = 6;
        foreach ($albums as $folder => $files): 
            $preview = array_slice($files, 0, $thumbLimit);
        ?>
          <div class="album-section" tabindex="0">
            <h3><?= htmlspecialchars($folder) ?></h3>
            <div class="album-media" style="display:flex; gap:6px; flex-wrap:wrap; justify-content:center;">
              <?php foreach ($preview as $file):
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg','jpeg','png','gif'])): ?>
                  <img src="<?= htmlspecialchars($file) ?>" loading="lazy" class="album-img" alt="<?= htmlspecialchars($folder) ?>" style="height:120px; width:auto; object-fit:cover;">
                <?php elseif ($ext === 'mp4'): ?>
                  <video controls preload="metadata" class="album-video" style="height:120px; width:auto;">
                    <source src="<?= htmlspecialchars($file) ?>" type="video/mp4">
                  </video>
                <?php endif; ?>
              <?php endforeach; ?>

              <?php if (count($files) > count($preview)): ?>
                <a class="view-more" href="html/album.php?name=<?= urlencode($folder) ?>" style="align-self:center; padding:6px 10px; border-radius:8px; background:#ffddee; color:#a33; text-decoration:none;">
                  +<?= count($files) - count($preview) ?> more
                </a>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>

        <div class="album-spacer"></div>
      </div>
      
      <?php
      // ✅ Auto-count total pictures from selected albums (no videos)
      $selectedAlbums = ['ourpicture', 'concert', 'foods', 'Funnypic', 'picture'];
      $totalPictures = 0;

      foreach ($selectedAlbums as $album) {
          $dirPath = __DIR__ . "/albums/$album";
          if (is_dir($dirPath)) {
              $images = glob($dirPath . "/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
              $totalPictures += count($images);
          }
      }
      ?>
      <div class="stats-bar" style="text-align:center; margin-top:20px;">
        <span>Total Pictures: <?= $totalPictures ?></span>  |
        <span>Total Posts: <?= $totalPosts ?></span> |
        <span>Total Videos: 0</span> |
        <span>Time Together: <?= $diff->y ?>y, <?= $diff->m ?>m, <?= $diff->d ?>d</span>
      </div>
    </div>
  </section>

  <!-- 🏠 Home Section (Post Box + Feed) -->
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
            <img src="albums/placeholder/inc.jpg" alt="Concert">
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

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" style="display:none;">
    <div class="modal-content">
      <p>Are you sure you want to delete this post?</p>
      <div style="display:flex; justify-content:center; gap:15px;">
        <button id="cancelDelete">Cancel</button>
        <button id="confirmDelete">Delete</button>
      </div>
    </div>
  </div>

  <!-- export PHP album list to JS for slideshow -->
  <script>
  window.slideshowImages = <?php
      $flat = [];

      // ✅ change this to the album(s) you want
      $allowedAlbums = ['ourpicture','concert','foods','Funnypic','picture']; 

      if (!empty($albums) && is_array($albums)) {
          foreach ($albums as $folder => $files) {
              if (!in_array($folder, $allowedAlbums)) continue; // skip others

              foreach ($files as $f) {
                  $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                  if (in_array($ext, ['jpg','jpeg','png','gif'])) {
                      $flat[] = $f;
                  }
              }
          }
      }

      // safe JSON export of file paths
      echo json_encode(array_values($flat), JSON_UNESCAPED_SLASHES);
  ?>;
  </script>


  <script src="js/script.js"></script>
  <script src="js/sidebar.js"></script>
  <script>
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
