<?php
session_start();
require_once '../login/db.php'; // $pdo connection

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$username = $_SESSION['username'];

// Fetch user ID from database
$stmt = $pdo->prepare("SELECT id FROM users WHERE username=?");
$stmt->execute([$username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$user_id = $user['id'];

// The rest stays the same
if ($_POST['action'] == 'toggle_favorite') {
    $image_path = $_POST['image_path'];
    $album_name = $_POST['album_name'];

    try {
        $check_sql = "SELECT id FROM user_favorites WHERE user_id=? AND image_path=?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$user_id, $image_path]);

        if ($check_stmt->rowCount() > 0) {
            // Remove
            $delete_sql = "DELETE FROM user_favorites WHERE user_id=? AND image_path=?";
            $delete_stmt = $pdo->prepare($delete_sql);
            $delete_stmt->execute([$user_id, $image_path]);
            echo json_encode(['success'=>true,'is_favorite'=>false]);
        } else {
            // Add
            $insert_sql = "INSERT INTO user_favorites (user_id, image_path, album_name) VALUES (?,?,?)";
            $insert_stmt = $pdo->prepare($insert_sql);
            $insert_stmt->execute([$user_id, $image_path, $album_name]);
            echo json_encode(['success'=>true,'is_favorite'=>true]);
        }
    } catch (Exception $e) {
        echo json_encode(['success'=>false,'message'=>'Database error: '.$e->getMessage()]);
    }
}

if ($_POST['action']=='check_favorite') {
    $image_path = $_POST['image_path'];

    try {
        $check_sql = "SELECT id FROM user_favorites WHERE user_id=? AND image_path=?";
        $check_stmt = $pdo->prepare($check_sql);
        $check_stmt->execute([$user_id,$image_path]);
        echo json_encode(['is_favorite'=>$check_stmt->rowCount()>0]);
    } catch (Exception $e) {
        echo json_encode(['is_favorite'=>false,'error'=>$e->getMessage()]);
    }
}
?>