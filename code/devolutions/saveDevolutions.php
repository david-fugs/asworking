<?php
header('Content-Type: application/json');
include("../../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);    $id_return = $input['id'] ?? '';
    $sell_order = $input['sell_order'] ?? '';
    $quantity = $input['quantity'] ?? '';
    $product_charge = $input['product_charge'] ?? '';
    $shipping_paid = $input['shipping_paid'] ?? '';
    $tax_return = $input['tax_return'] ?? '';
    $selling_fee_refund = $input['selling_fee_refund'] ?? '';
    $refund_administration_fee = $input['refund_administration_fee'] ?? '';
    $other_refund_fee = $input['other_refund_fee'] ?? '';
    $return_cost = $input['return_cost'] ?? '';
    $buyer_comments = $input['buyer_comments'] ?? '';
    
    if (empty($sell_order)) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }    try {
        // Si hay un id_return, actualizar el registro existente
        if (!empty($id_return)) {
            $query = "UPDATE returns SET 
                        quantity = ?,
                        product_charge = ?,
                        shipping_paid = ?,
                        tax_return = ?,
                        selling_fee_refund = ?,
                        refund_administration_fee = ?,
                        other_refund_fee = ?,
                        return_cost = ?,
                        buyer_comments = ?
                      WHERE id_return = ?";
            
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("sssssssssi", 
                $quantity,
                $product_charge,
                $shipping_paid,
                $tax_return,
                $selling_fee_refund,
                $refund_administration_fee,
                $other_refund_fee,
                $return_cost,
                $buyer_comments,
                $id_return
            );
        } else {
            // Si no hay id_return, crear un nuevo registro de devolución
            // Primero obtener los datos de la venta
            $sellQuery = "SELECT upc_item, sku_item FROM sell WHERE sell_order = ?";
            $sellStmt = $mysqli->prepare($sellQuery);
            $sellStmt->bind_param("s", $sell_order);
            $sellStmt->execute();
            $sellResult = $sellStmt->get_result();
            
            if ($sellResult->num_rows === 0) {
                echo json_encode(['success' => false, 'message' => 'Sell order not found']);
                exit;
            }
            
            $sellData = $sellResult->fetch_assoc();
            $sellStmt->close();
            
            $query = "INSERT INTO returns (
                        sell_order,
                        upc_item,
                        sku_item,
                        quantity,
                        product_charge,
                        shipping_paid,
                        tax_return,
                        selling_fee_refund,
                        refund_administration_fee,
                        other_refund_fee,
                        return_cost,
                        buyer_comments,
                        creation_date
                      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ssssssssssss", 
                $sell_order,
                $sellData['upc_item'],
                $sellData['sku_item'],
                $quantity,
                $product_charge,
                $shipping_paid,
                $tax_return,
                $selling_fee_refund,
                $refund_administration_fee,
                $other_refund_fee,
                $return_cost,
                $buyer_comments
            );
        }
        
        if ($stmt->execute()) {
            $message = !empty($id_return) ? 'Return updated successfully' : 'Return created successfully';
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error saving return']);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$mysqli->close();
?>
