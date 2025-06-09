<?php
header('Content-Type: application/json');
include("../../conexion.php");

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data from $_POST
$data = $_POST;

// Validate required fields
$required_fields = ['sell_order', 'id_sell'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

try {
    // Extract and sanitize data
    $id_sell = intval($data['id_sell']);
    $sell_order = $mysqli->real_escape_string($data['sell_order']);
    $refund_amount = isset($data['refund_amount']) ? floatval($data['refund_amount']) : 0.00;
    $shipping_refund = isset($data['shipping_refund']) ? floatval($data['shipping_refund']) : 0.00;
    $tax_refund = isset($data['tax_refund']) ? floatval($data['tax_refund']) : 0.00;
    $final_fee_refund = isset($data['final_fee_refund']) ? floatval($data['final_fee_refund']) : 0.00;
    $fixed_charge_refund = isset($data['fixed_charge_refund']) ? floatval($data['fixed_charge_refund']) : 0.00;
    $other_fee_refund = isset($data['other_fee_refund']) ? floatval($data['other_fee_refund']) : 0.00;

    // Check if cancellation record already exists for this sell
    $check_sql = "SELECT id FROM cancellations WHERE order_id = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    $check_stmt->bind_param("s", $sell_order);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Update existing record
        $update_sql = "UPDATE cancellations SET 
                       refund_amount = ?, 
                       shipping_refund = ?, 
                       tax_refund = ?, 
                       final_fee_refund = ?, 
                       fixed_charge_refund = ?, 
                       other_fee_refund = ?, 
                       updated_at = NOW() 
                       WHERE order_id = ?";
        
        $update_stmt = $mysqli->prepare($update_sql);
        $update_stmt->bind_param("dddddds", $refund_amount, $shipping_refund, $tax_refund, $final_fee_refund, $fixed_charge_refund, $other_fee_refund, $sell_order);
        
        if ($update_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cancellation information updated successfully', 'action' => 'updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update cancellation information: ' . $mysqli->error]);
        }
        $update_stmt->close();
    } else {
        // Insert new record
        $insert_sql = "INSERT INTO cancellations (order_id, refund_amount, shipping_refund, tax_refund, final_fee_refund, fixed_charge_refund, other_fee_refund, created_at, updated_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $insert_stmt = $mysqli->prepare($insert_sql);
        $insert_stmt->bind_param("sdddddd", $sell_order, $refund_amount, $shipping_refund, $tax_refund, $final_fee_refund, $fixed_charge_refund, $other_fee_refund);
        
        if ($insert_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cancellation information saved successfully', 'action' => 'created', 'id' => $mysqli->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save cancellation information: ' . $mysqli->error]);
        }
        $insert_stmt->close();
    }

    $check_stmt->close();

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
} finally {
    $mysqli->close();
}
?>
