<?php
header('Content-Type: application/json');
include("../../conexion.php");

if (!isset($_GET['sell_order']) || empty($_GET['sell_order'])) {
    echo json_encode(['error' => 'Sell order is required']);
    exit;
}

$sell_order = $_GET['sell_order'];

try {
    // Query to get summary data from sell_summary table
    $query = "SELECT final_fee, fixed_charge, final_total, total_items 
              FROM sell_summary 
              WHERE sell_order = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $sell_order);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $summary = $result->fetch_assoc();
        echo json_encode(['success' => true, 'summary' => $summary]);
    } else {
        // If no summary found, return null
        echo json_encode(['success' => true, 'summary' => null]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$mysqli->close();
?>
