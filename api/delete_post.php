<?php
session_start();
require_once __DIR__ . '/../login/db.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    $username = $_SESSION['username'];
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $userId = $user['id'];

    $postId = $_POST['post_id'];
    $stmt = $pdo->prepare("DELETE FROM posts WHERE id = :id AND user_id = :user_id");
    $stmt->execute(['id' => $postId, 'user_id' => $userId]);
}

// Redirect back to index.php to prevent resubmission
header("Location: ../index.php");
exit;
