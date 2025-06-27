<?php
// server/conn.php
try {
    $pdo = new PDO("mysql:host=localhost;dbname=drepadata", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("SET NAMES utf8mb4");
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    header('Content-Type: application/json', true, 500);
    echo json_encode(['type' => 'error', 'message' => 'Server error: Unable to connect to the database.']);
    exit;
}
?>