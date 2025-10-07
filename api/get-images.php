<?php
header('Content-Type: application/json');

// List all your album folders
$folders = ['concert', 'foods', 'Funnypic', 'ourpicture', 'picture', 'Scrapbook'];

$result = [];

foreach ($folders as $folder) {
    $path = "albums/$folder/";
    if (is_dir($path)) {
        // Get all jpg, jpeg, png, gif files
        $images = glob($path . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
        sort($images); // optional, alphabetical
        $result[$folder] = $images;
    } else {
        $result[$folder] = [];
    }
}

echo json_encode($result);
?>
