<?php
session_start();
include("../../conexion.php");

header('Content-Type: application/json');

try {
    // Test database connection first
    if ($mysqli->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $mysqli->connect_error]);
        exit();
    }

    // Verificar que sea una petición POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        exit();
    }

    // Get the exact structure of daily_report table
    $result = $mysqli->query("DESCRIBE daily_report");
    $columns = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row['Field'];
        }
    }

    // Get the exact structure of inventory table
    $result = $mysqli->query("DESCRIBE inventory");
    $inv_columns = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $inv_columns[] = $row['Field'];
        }
    }

    echo json_encode([
        'status' => 'debug',
        'message' => 'Debug info',
        'daily_report_columns' => $columns,
        'inventory_columns' => $inv_columns,
        'post_data' => $_POST,
        'database_connected' => true
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Exception: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
