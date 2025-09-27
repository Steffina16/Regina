<?php
session_start();

// DEBUG: Check current directory
error_log("Current directory: " . __DIR__);
error_log("Document root: " . $_SERVER['DOCUMENT_ROOT']);

// Your database file is at ../login/db.php (one level up, then login folder)
$db_path = '../login/db.php';

if (file_exists($db_path)) {
    require_once $db_path;
    error_log("Database loaded successfully from: " . $db_path);
} else {
    // Try alternative paths
    $alternative_paths = [
        '../login/db.php',
        __DIR__ . '/../login/db.php',
        realpath(__DIR__ . '/../login/db.php')
    ];
    
    error_log("Trying alternative paths:");
    foreach ($alternative_paths as $path) {
        error_log("Checking: " . $path . " - Exists: " . (file_exists($path) ? 'YES' : 'NO'));
    }
    
    die("Database configuration file not found! Looking for: " . $db_path);
}

// ✅ Check session - use username (since login sets $_SESSION['username'])
if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
    header('Location: ../login/login.php');
    exit;
}

$username = $_SESSION['username'];
error_log("Username from session: " . $username);

// ✅ Get user_id from username
try {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if (!$user) {
        // If user not found, log out
        session_destroy();
        header("Location: ../login/login.php");
        exit;
    }

    $user_id = $user['id'];
    error_log("User ID resolved from username: " . $user_id);
} catch (Exception $e) {
    error_log("Database error resolving user_id: " . $e->getMessage());
    session_destroy();
    header("Location: ../login/login.php");
    exit;
}

// Get user's favorites
try {
    $sql = "SELECT uf.*, u.username 
            FROM user_favorites uf 
            JOIN users u ON uf.user_id = u.id 
            WHERE uf.user_id = ? 
            ORDER BY uf.created_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id]);
    $favorites = $stmt->fetchAll();

    // Get favorite counts per album
    $album_counts_sql = "SELECT album_name, COUNT(*) as count 
                         FROM user_favorites 
                         WHERE user_id = ? 
                         GROUP BY album_name";
    $album_stmt = $pdo->prepare($album_counts_sql);
    $album_stmt->execute([$user_id]);
    $album_counts = $album_stmt->fetchAll();

    $total_favorites = count($favorites);
    
    error_log("Favorites found: " . $total_favorites);
    
} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    $favorites = [];
    $album_counts = [];
    $total_favorites = 0;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - Regina's Gallery</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Playfair+Display:ital,wght@0,400;1,700&display=swap">
    <link rel="stylesheet" href="../css/album.css"> <!-- Adjusted path -->
    <link rel="icon" type="image/x-icon" href="../albums/placeholder/logo.png"> <!-- Adjusted path -->
    
    <!-- Include sidebar - adjust path as needed -->
    <?php 
    $sidebar_path = '../sidebar.php'; // Adjust based on your structure
    if (file_exists($sidebar_path)) {
        include $sidebar_path;
    } else {
        // Try other possible locations
        $sidebar_paths = ['../sidebar.php', 'sidebar.php', '../../sidebar.php'];
        $sidebar_loaded = false;
        foreach ($sidebar_paths as $path) {
            if (file_exists($path)) {
                include $path;
                $sidebar_loaded = true;
                break;
            }
        }
        if (!$sidebar_loaded) {
            error_log("Sidebar not found in any location");
        }
    }
    ?>
    
    <style>
        .favorites-header {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #fce4ec, #f8bbd9);
            margin-bottom: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        
        .favorites-title {
            font-family: 'Dancing Script', cursive;
            font-size: 3.5rem;
            color: #d81b60;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }
        
        .favorites-subtitle {
            font-family: 'Playfair Display', serif;
            font-size: 1.2rem;
            color: #880e4f;
            margin-bottom: 20px;
        }
        
        .stats-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
            min-width: 150px;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #d81b60;
            display: block;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .album-filters {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .album-filter {
            background: #f8bbd9;
            border: none;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Playfair Display', serif;
        }
        
        .album-filter.active {
            background: #d81b60;
            color: white;
        }
        
        .album-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(216, 27, 96, 0.3);
        }
        
        .favorites-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            padding: 20px;
        }
        
        .favorite-item {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
            background: white;
        }
        
        .favorite-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .favorite-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
            display: block;
        }
        
        .favorite-info {
            padding: 15px;
            background: white;
        }
        
        .favorite-album {
            background: #d81b60;
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            text-transform: capitalize;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .favorite-date {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        
        .remove-favorite {
            background: #ff4757;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Playfair Display', serif;
            width: 100%;
        }
        
        .remove-favorite:hover {
            background: #ff3742;
            transform: scale(1.05);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-family: 'Dancing Script', cursive;
            font-size: 2.5rem;
            color: #d81b60;
            margin-bottom: 10px;
        }
        
        .back-to-gallery {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: #d81b60;
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        
        .back-to-gallery:hover {
            background: #c2185b;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .favorites-gallery {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 15px;
                padding: 10px;
            }
            
            .favorites-title {
                font-size: 2.5rem;
            }
            
            .stats-container {
                gap: 15px;
            }
            
            .stat-card {
                min-width: 120px;
                padding: 15px;
            }
        }
    </style>
</head>
<body>

<div class="gallery-container">
    <!-- Adjust back button path -->
    <a href="../index.php#pictures" class="back-btn">← Back to Main</a>
    
    <div class="favorites-header">
        <h1 class="favorites-title">My Favorite Pictures</h1>
        <p class="favorites-subtitle">A collection of your most loved moments ✨</p>
        
        <div class="stats-container">
            <div class="stat-card">
                <span class="stat-number"><?php echo $total_favorites; ?></span>
                <span class="stat-label">Total Favorites</span>
            </div>
            
            <div class="stat-card">
                <span class="stat-number"><?php echo count($album_counts); ?></span>
                <span class="stat-label">Albums</span>
            </div>
            
            <?php if ($total_favorites > 0): ?>
            <div class="stat-card">
                <span class="stat-number">
                    <?php echo date('M j, Y', strtotime($favorites[0]['created_at'])); ?>
                </span>
                <span class="stat-label">Last Added</span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($total_favorites > 0): ?>
        <!-- Album Filters -->
        <div class="album-filters">
            <button class="album-filter active" data-album="all">All Albums (<?php echo $total_favorites; ?>)</button>
            <?php foreach ($album_counts as $album): ?>
                <button class="album-filter" data-album="<?php echo $album['album_name']; ?>">
                    <?php echo ucfirst($album['album_name']); ?> (<?php echo $album['count']; ?>)
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Favorites Gallery -->
        <div class="favorites-gallery" id="favoritesGallery">
            <?php foreach ($favorites as $favorite): ?>
                <div class="favorite-item" data-album="<?php echo $favorite['album_name']; ?>">
                    <img src="<?php echo htmlspecialchars($favorite['image_path']); ?>" 
                         alt="Favorite image from <?php echo $favorite['album_name']; ?> album" 
                         class="favorite-image"
                         onclick="openLightbox('<?php echo htmlspecialchars($favorite['image_path']); ?>')">
                    
                    <div class="favorite-info">
                        <span class="favorite-album"><?php echo ucfirst($favorite['album_name']); ?></span>
                        <div class="favorite-date">
                            Added: <?php echo date('F j, Y g:i A', strtotime($favorite['created_at'])); ?>
                        </div>
                        <button class="remove-favorite" 
                                data-image-path="<?php echo htmlspecialchars($favorite['image_path']); ?>"
                                onclick="removeFavorite(this)">
                            ❌ Remove from Favorites
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-state-icon">💔</div>
            <h3>No Favorites Yet</h3>
            <p>You haven't added any pictures to your favorites collection.</p>
            <p>Start exploring the galleries and click the heart icons to save your favorite moments!</p>
            <!-- Adjust gallery paths -->
            <a href="solo.php" class="back-to-gallery">Explore Solo Gallery</a>
            <a href="couple.php" class="back-to-gallery" style="background: #2196F3;">Explore Couple Gallery</a>
            <a href="cute.php" class="back-to-gallery" style="background: #4CAF50;">Explore Cute Gallery</a>
            <a href="food.php" class="back-to-gallery" style="background: #FF9800;">Explore Food Gallery</a>
        </div>
    <?php endif; ?>
</div>

<!-- Lightbox -->
<div id="lightbox" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 1000; justify-content: center; align-items: center;">
    <img id="lightbox-img" src="" alt="Enlarged image" style="max-width: 90%; max-height: 90%; border-radius: 10px;">
    <button onclick="closeLightbox()" style="position: absolute; top: 20px; right: 30px; background: none; border: none; color: white; font-size: 2rem; cursor: pointer;">×</button>
</div>

<script>
// Album filtering
document.querySelectorAll('.album-filter').forEach(button => {
    button.addEventListener('click', function() {
        document.querySelectorAll('.album-filter').forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');
        
        const album = this.getAttribute('data-album');
        const items = document.querySelectorAll('.favorite-item');
        
        items.forEach(item => {
            if (album === 'all' || item.getAttribute('data-album') === album) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
});

// Lightbox functions
function openLightbox(imageSrc) {
    document.getElementById('lightbox-img').src = imageSrc;
    document.getElementById('lightbox').style.display = 'flex';
}

function closeLightbox() {
    document.getElementById('lightbox').style.display = 'none';
}

document.getElementById('lightbox').addEventListener('click', function(e) {
    if (e.target === this) {
        closeLightbox();
    }
});

// Remove favorite function - adjust API path
function removeFavorite(button) {
    const imagePath = button.getAttribute('data-image-path');
    const favoriteItem = button.closest('.favorite-item');
    
    if (confirm('Are you sure you want to remove this image from your favorites?')) {
        // Adjust API path based on your structure
        fetch('../api/favorite_handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=toggle_favorite&image_path=${encodeURIComponent(imagePath)}&album_name=remove`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && !data.is_favorite) {
                favoriteItem.style.animation = 'fadeOut 0.5s ease';
                setTimeout(() => {
                    favoriteItem.remove();
                    updateStats();
                    checkEmptyState();
                }, 500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error removing favorite. Please try again.');
        });
    }
}

function updateStats() {
    const remainingItems = document.querySelectorAll('.favorite-item').length;
    document.querySelector('.stat-number').textContent = remainingItems;
    
    const allButton = document.querySelector('[data-album="all"]');
    allButton.textContent = `All Albums (${remainingItems})`;
}

function checkEmptyState() {
    const gallery = document.getElementById('favoritesGallery');
    if (gallery.children.length === 0) {
        location.reload();
    }
}

// Add fadeOut animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.8); }
    }
`;
document.head.appendChild(style);
</script>

</body>
</html>