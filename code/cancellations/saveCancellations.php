<?php
header('Content-Type: application/json');
include("../../conexion.php");

// Limpiar cualquier output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data from $_POST
$data = $_POST;

// Validate required fields
$required_fields = ['sell_order', 'id_sell', 'upc_item'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

try {    // Extract and sanitize data
    $id_sell = intval($data['id_sell']);
    $sell_order = $mysqli->real_escape_string($data['sell_order']);
    $upc_item = $mysqli->real_escape_string($data['upc_item']);
    $refund_amount = isset($data['refund_amount']) ? floatval($data['refund_amount']) : 0.00;
    $shipping_refund = isset($data['shipping_refund']) ? floatval($data['shipping_refund']) : 0.00;
    $tax_refund = isset($data['tax_refund']) ? floatval($data['tax_refund']) : 0.00;
    $final_fee_refund = isset($data['final_fee_refund']) ? floatval($data['final_fee_refund']) : 0.00;
    $fixed_charge_refund = isset($data['fixed_charge_refund']) ? floatval($data['fixed_charge_refund']) : 0.00;
    $other_fee_refund = isset($data['other_fee_refund']) ? floatval($data['other_fee_refund']) : 0.00;
    $cancellation_date = isset($data['cancellation_date']) && !empty($data['cancellation_date']) ? $data['cancellation_date'] : null;
    
    // Get the sku_item from the sell table
    $sku_query = "SELECT sku_item FROM sell WHERE id_sell = ? AND sell_order = ? AND upc_item = ?";
    $sku_stmt = $mysqli->prepare($sku_query);
    
    if (!$sku_stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $mysqli->error]);
        exit;
    }
    
    $sku_stmt->bind_param("iss", $id_sell, $sell_order, $upc_item);
    $sku_stmt->execute();
    $sku_result = $sku_stmt->get_result();
    
    $sku_item = null;
    if ($sku_result->num_rows > 0) {
        $sku_row = $sku_result->fetch_assoc();
        $sku_item = $sku_row['sku_item'];
    }
    $sku_stmt->close();
    
    // Calculate Net Cancellation
    // Formula: Refund amount + Shipping Refund + Tax Refund - Final Fee Refund - Fixed Charge Refund - Other Fee Refund
    $net_cancellation = $refund_amount + $shipping_refund + $tax_refund - $final_fee_refund - $fixed_charge_refund - $other_fee_refund;

    // Check if cancellation record already exists for this specific sell item (order_id + id_sell + upc_item)
    $check_sql = "SELECT id FROM cancellations WHERE order_id = ? AND id_sell = ? AND upc_item = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    $check_stmt->bind_param("sis", $sell_order, $id_sell, $upc_item);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {        // Update existing record
        $update_sql = "UPDATE cancellations SET 
                       refund_amount = ?, 
                       shipping_refund = ?, 
                       tax_refund = ?, 
                       final_fee_refund = ?, 
                       fixed_charge_refund = ?, 
                       other_fee_refund = ?,
                       net_cancellation = ?,
                       cancellation_date = ?,
                       sku_item = ?, 
                       updated_at = NOW() 
                       WHERE order_id = ? AND id_sell = ? AND upc_item = ?";
        
        $update_stmt = $mysqli->prepare($update_sql);
        $update_stmt->bind_param("dddddddsssis", $refund_amount, $shipping_refund, $tax_refund, $final_fee_refund, $fixed_charge_refund, $other_fee_refund, $net_cancellation, $cancellation_date, $sku_item, $sell_order, $id_sell, $upc_item);
        
        if ($update_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cancellation information updated successfully', 'action' => 'updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update cancellation information: ' . $mysqli->error]);
        }
        $update_stmt->close();
    } else {        // Insert new record
        $insert_sql = "INSERT INTO cancellations (order_id, id_sell, upc_item, sku_item, refund_amount, shipping_refund, tax_refund, final_fee_refund, fixed_charge_refund, other_fee_refund, net_cancellation, cancellation_date, created_at, updated_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $insert_stmt = $mysqli->prepare($insert_sql);
        $insert_stmt->bind_param("sissddddddds", $sell_order, $id_sell, $upc_item, $sku_item, $refund_amount, $shipping_refund, $tax_refund, $final_fee_refund, $fixed_charge_refund, $other_fee_refund, $net_cancellation, $cancellation_date);
        
        if ($insert_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Cancellation information saved successfully', 'action' => 'created', 'id' => $mysqli->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save cancellation information: ' . $mysqli->error]);
        }
        $insert_stmt->close();
    }

    $check_stmt->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
} finally {
    if (isset($mysqli)) {
        $mysqli->close();
    }
}

// Asegurar que no hay output adicional después
exit;
?>
