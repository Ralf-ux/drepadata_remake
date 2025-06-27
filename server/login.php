<?php
// server/login.php

// Start session
session_start();

// Set JSON header
header('Content-Type: application/json');

// Include database connection
try {
    require_once 'conn.php';
} catch (Exception $e) {
    error_log('Connection file error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo json_encode(['type' => 'error', 'message' => 'Server error: Failed to load database connection.']);
    exit;
}

// Ensure the request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['type' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

// Collect input data
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Check if fields are empty
if (empty($email) || empty($password)) {
    echo json_encode(['type' => 'error', 'message' => 'Email and password are required.']);
    exit;
}

try {
    // Verify PDO connection
    if (!$pdo) {
        error_log('PDO connection is null');
        echo json_encode(['type' => 'error', 'message' => 'Server error: No database connection.']);
        exit;
    }

    // Check if users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() === 0) {
        error_log('Table "users" does not exist');
        echo json_encode(['type' => 'error', 'message' => 'Server error: Users table not found.']);
        exit;
    }

    // Check required columns
    $required_columns = ['id', 'name', 'email', 'password'];
    $stmt = $pdo->query("SHOW COLUMNS FROM users");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($required_columns as $col) {
        if (!in_array($col, $columns)) {
            error_log("Column '$col' missing in users table");
            echo json_encode(['type' => 'error', 'message' => 'Server error: Database schema issue.']);
            exit;
        }
    }

    // Prepare and execute query to check if email exists
    $stmt = $pdo->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Check if user exists
    if (!$user) {
        echo json_encode(['type' => 'error', 'message' => 'No account found with that email.']);
        exit;
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        echo json_encode(['type' => 'error', 'message' => 'Incorrect password.']);
        exit;
    }

    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];

    // Return success response with redirection
    echo json_encode([
        'type' => 'success',
        'message' => 'Login successful. Welcome, ' . htmlspecialchars($user['name']) . '!',
        'redirect' => '../client/consultation.php'
    ]);
    exit;

} catch (PDOException $e) {
    // Log detailed error for debugging
    error_log('Database error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo json_encode(['type' => 'error', 'message' => 'Server error: Database issue. Please try again later.']);
    exit;
} catch (Exception $e) {
    // Catch any other unexpected errors
    error_log('Unexpected error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
    echo json_encode(['type' => 'error', 'message' => 'Server error: Unexpected issue. Please try again later.']);
    exit;
}
?>