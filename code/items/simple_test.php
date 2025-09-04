<?php
// Simple test to identify the issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Test 1: Basic PHP execution
    echo json_encode(['status' => 'test1', 'message' => 'PHP is working']);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
