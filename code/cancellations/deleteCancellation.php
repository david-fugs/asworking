<?php
header('Content-Type: application/json');
include("../../conexion.php");

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get sell_order from POST data
if (!isset($_POST['sell_order']) || empty($_POST['sell_order'])) {
    echo json_encode(['success' => false, 'message' => 'Sell order is required']);
    exit;
}

$sell_order = $_POST['sell_order'];

try {
    // Delete cancellation record
    $delete_sql = "DELETE FROM cancellations WHERE order_id = ?";
    $delete_stmt = $mysqli->prepare($delete_sql);
    $delete_stmt->bind_param("s", $sell_order);
    
    if ($delete_stmt->execute()) {
        if ($delete_stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Cancellation data deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No cancellation data found for this order']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete cancellation data: ' . $mysqli->error]);
    }
    
    $delete_stmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
} finally {
    $mysqli->close();
}
?>
