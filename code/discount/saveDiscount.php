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

try {    // Extract and sanitize data
    $id_sell = intval($data['id_sell']);
    $sell_order = $mysqli->real_escape_string($data['sell_order']);
    $price_discount = isset($data['price_discount']) ? floatval($data['price_discount']) : 0.00;
    $shipping_discount = isset($data['shipping_discount']) ? floatval($data['shipping_discount']) : 0.00;
    $fee_credit = isset($data['fee_credit']) ? floatval($data['fee_credit']) : 0.00;
    $tax_return = isset($data['tax_return']) ? floatval($data['tax_return']) : 0.00;
    
    // Calculate net_markdown: Price Discount + Shipping Discount - Fee Credit
    $net_markdown = $price_discount + $shipping_discount - $fee_credit;
    
    // Calculate total discount: Fee Credit (Suma) minus all others (Resta)
    $total_discount = $fee_credit - ($price_discount + $shipping_discount + $tax_return);

    // Check if discount record already exists for this sell
    $check_sql = "SELECT id_discount FROM discounts WHERE id_sell = ? OR sell_order = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    $check_stmt->bind_param("is", $id_sell, $sell_order);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {        // Update existing record
        $update_sql = "UPDATE discounts SET 
                       price_discount = ?, 
                       shipping_discount = ?, 
                       fee_credit = ?, 
                       tax_return = ?, 
                       net_markdown = ?, 
                       total_discount = ?, 
                       updated_at = NOW() 
                       WHERE id_sell = ?";
        
        $update_stmt = $mysqli->prepare($update_sql);
        $update_stmt->bind_param("ddddddi", $price_discount, $shipping_discount, $fee_credit, $tax_return, $net_markdown, $total_discount, $id_sell);
        
        if ($update_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Discount information updated successfully', 'action' => 'updated']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update discount information: ' . $mysqli->error]);
        }
        $update_stmt->close();
    } else {        // Insert new record
        $insert_sql = "INSERT INTO discounts (id_sell, sell_order, price_discount, shipping_discount, fee_credit, tax_return, net_markdown, total_discount, created_at, updated_at) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $insert_stmt = $mysqli->prepare($insert_sql);
        $insert_stmt->bind_param("isdddddd", $id_sell, $sell_order, $price_discount, $shipping_discount, $fee_credit, $tax_return, $net_markdown, $total_discount);
        
        if ($insert_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Discount information saved successfully', 'action' => 'created', 'id' => $mysqli->insert_id]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save discount information: ' . $mysqli->error]);
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
