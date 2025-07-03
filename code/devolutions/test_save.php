<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

echo json_encode([
    'success' => true,
    'message' => 'Test connection successful',
    'method' => $_SERVER['REQUEST_METHOD'],
    'data' => file_get_contents('php://input')
]);
?>
