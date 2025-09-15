<?php
// Database connection
$host = "localhost"; 
$user = "root"; 
$pass = ""; 
$dbname = "our_memories"; 

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];

    // Define target directories based on category
    switch ($category) {
        case "solo":
            $prefix = "picture ni chin-";
            $targetDir = "albums/picture/"; 
            break;
        case "couple":
            $prefix = "Ourpicture-";
            $targetDir = "albums/ourpicture/"; 
            break;
        case "cute":
            $prefix = "Chin-";
            $targetDir = "albums/Funnypic/"; 
            break;
        case "food":
            $prefix = "food-";
            $targetDir = "albums/foods/"; 
            break;    
        default:
            $prefix = "Image-";
            $targetDir = "albums/";
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $existingFiles = glob($targetDir . $prefix . "*.*");
    $count = count($existingFiles) + 1;

    $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $newFileName = $prefix . str_pad($count, 3, '0', STR_PAD_LEFT) . "." . $extension;
    $targetFilePath = $targetDir . $newFileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        $sql = "INSERT INTO pictures (category, filename) VALUES ('$category', '$newFileName')";
        if ($conn->query($sql) === TRUE) {
            $message = "✨ Image uploaded successfully as <b>$newFileName</b>!";
        } else {
            $message = "⚠️ Error saving to database: " . $conn->error;
        }
    } else {
        $message = "⚠️ Error uploading file.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Image</title>
  <link rel="stylesheet" href="css/upload.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="upload-container">
      <a href="index.html#pictures" class="back-btn"><i class="fas fa-arrow-left"></i> Back</a>
      <h2 class="title"><i class="fas fa-cloud-upload-alt"></i> Upload Your Memory</h2>

      <?php if (!empty($message)) { ?>
          <p class="message"><?php echo $message; ?></p>
      <?php } ?>

      <form action="upload.php" method="POST" enctype="multipart/form-data" class="upload-form">
          <div class="form-group">
              <label for="category"><i class="fas fa-folder-open"></i> Choose category</label>
              <select name="category" id="category" required>
                  <option value="solo">Solo</option>
                  <option value="couple">Couple</option>
                  <option value="cute">Cute</option>
                  <option value="food">Foods</option>
              </select>
          </div>

          <div class="form-group">
              <label for="image"><i class="fas fa-image"></i> Select Image</label>
              <input type="file" name="image" id="image" required>
          </div>

          <button type="submit"><i class="fas fa-paper-plane"></i> Upload</button>
      </form>
  </div>
</body>
</html>
