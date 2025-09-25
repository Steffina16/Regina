<?php
$host = "localhost";     // since you’re working on local server (XAMPP/MAMP)
$dbname = "our_memories"; // your database name
$username = "root";      // default for local XAMPP/MAMP
$password = "";          // default is empty for local XAMPP/MAMP

try {
    // Create a new PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
