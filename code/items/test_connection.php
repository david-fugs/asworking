<?php
// Test database connection step by step
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

try {
    // Test 1: Include connection
    include("../../conexion.php");
    
    // Test 2: Check connection
    if ($mysqli->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $mysqli->connect_error]);
        exit();
    }
    
    // Test 3: Simple query
    $result = $mysqli->query("SELECT 1");
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Query failed: ' . $mysqli->error]);
        exit();
    }
    
    echo json_encode(['status' => 'success', 'message' => 'Database connection working']);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Exception: ' . $e->getMessage()]);
} catch (Error $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
}
?>
