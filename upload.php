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
            $prefix = "picture-ni-Chin-";
            $targetDir = "albums/picture/"; 
            break;
        case "couple":
            $prefix = "Ouricture-";
            $targetDir = "albums/ourpicture/"; 
            break;
        case "cute":
            $prefix = "Chin-";
            $targetDir = "albums/Funnypic/"; 
            break;
        default:
            $prefix = "Image-";
            $targetDir = "albums/";
    }

    // Make sure target folder exists
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    // Count existing files in the target directory
    $existingFiles = glob($targetDir . $prefix . "*.*");
    $count = count($existingFiles) + 1;

    // Generate new filename
    $extension = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
    $newFileName = $prefix . str_pad($count, 3, '0', STR_PAD_LEFT) . "." . $extension;
    $targetFilePath = $targetDir . $newFileName;

    // Move uploaded file
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
        // Save into DB (store category + filename)
        $sql = "INSERT INTO pictures (category, filename) VALUES ('$category', '$newFileName')";
        if ($conn->query($sql) === TRUE) {
            $message = "Image uploaded successfully as <b>$newFileName</b> in $targetDir!";
        } else {
            $message = "Error saving to database: " . $conn->error;
        }
    } else {
        $message = "Error uploading file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Image</title>
    <link rel="stylesheet" href="css/upload.css">
</head>
<body>
    <div class="upload-container">
        <a href="index.html#pictures" class="back-btn">← Back to Main</a>
        <h2 class="title">Upload Your Memory</h2>

        <?php if (!empty($message)) { ?>
            <p class="message"><?php echo $message; ?></p>
        <?php } ?>

        <form action="upload.php" method="POST" enctype="multipart/form-data" class="upload-form">
            <label for="category">Choose category:</label>
            <select name="category" id="category" required>
                <option value="solo">Solo</option>
                <option value="couple">Couple</option>
                <option value="cute">Cute</option>
            </select>

            <label for="image">Select Image:</label>
            <input type="file" name="image" id="image" required>

            <button type="submit">Upload</button>
        </form>
    </div>
</body>
</html>
            