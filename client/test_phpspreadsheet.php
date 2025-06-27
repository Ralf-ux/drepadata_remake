<?php
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    if (class_exists('PhpOffice\\PhpSpreadsheet\\IOFactory')) {
        echo "PhpSpreadsheet is loaded successfully.\n";
    } else {
        echo "PhpSpreadsheet class not found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
