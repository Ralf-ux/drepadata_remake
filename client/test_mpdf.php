<?php
// Minimal test script to verify mPDF installation and autoloader

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if autoload.php exists
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Autoloader not found at $autoloadPath. Please check the path.");
}

require_once $autoloadPath;

use Mpdf\Mpdf;

try {
    $mpdf = new Mpdf();
    echo "mPDF loaded successfully.";
} catch (Exception $e) {
    echo "Error loading mPDF: " . $e->getMessage();
}
?>
