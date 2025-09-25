<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if user exists by username or email
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Save login session
        $_SESSION['username'] = $user['username'];

        // ✅ Always go to index.php after login
        header("Location: ../index.php");
        exit();
    } else {
        // Invalid login
        header("Location: login.php?error=1");
        exit();
    }
}
?>
