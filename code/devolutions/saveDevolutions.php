<?php
ob_start(); // Iniciar output buffering para capturar cualquier salida no deseada
header('Content-Type: application/json');

// Debug log
// error_log("saveDevolutions.php called");

include("../../conexion.php");

// Función helper para enviar respuesta JSON limpia
function sendJsonResponse($data) {
    ob_end_clean(); // Limpiar cualquier salida previa
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $id_return = $input['id'] ?? '';
    $id_sell = $input['id_sell'] ?? '';  // Agregar id_sell
    $sell_order = $input['sell_order'] ?? '';
    $upc_item = $input['upc_item'] ?? '';
    $sku_item = $input['sku_item'] ?? '';
    $quantity = $input['quantity'] ?? '';
    $product_charge = $input['product_charge'] ?? '';
    $shipping_paid = $input['shipping_paid'] ?? '';
    $tax_return = $input['tax_return'] ?? '';
    $selling_fee_refund = $input['selling_fee_refund'] ?? '';
    $refund_administration_fee = $input['refund_administration_fee'] ?? '';
    $other_refund_fee = $input['other_refund_fee'] ?? '';
    $return_cost = $input['return_cost'] ?? '';
    $buyer_comments = $input['buyer_comments'] ?? '';
    $devolution_date = $input['devolution_date'] ?? null;
    
    // Debug log (después de definir las variables)
    // error_log("Input received: " . json_encode($input));
    // error_log("SKU item value: '" . $sku_item . "' (empty: " . (empty($sku_item) ? 'true' : 'false') . ")");
    
    // Convertir fecha vacía a null
    if (empty($devolution_date)) {
        $devolution_date = null;
    }
    
    // Convertir sku_item vacío a null
    if (empty($sku_item)) {
        $sku_item = null;
    }
    
    if (empty($sell_order) || empty($upc_item)) {
        sendJsonResponse(['success' => false, 'message' => 'Missing required fields (sell_order, upc_item)']);
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
                        buyer_comments = ?,
                        devolution_date = ?
                      WHERE id_return = ?";
            
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ssssssssssi", 
                $quantity,
                $product_charge,
                $shipping_paid,
                $tax_return,
                $selling_fee_refund,
                $refund_administration_fee,
                $other_refund_fee,
                $return_cost,
                $buyer_comments,
                $devolution_date,
                $id_return
            );
        } else {
            // Si no hay id_return, verificar si ya existe un registro con esta combinación única
            // La combinación única es: sell_order + upc_item + sku_item + id_sell
            // Manejar casos donde sku_item puede ser NULL o vacío
            
            if (empty($sku_item)) {
                $checkQuery = "SELECT id_return FROM returns 
                              WHERE sell_order = ? AND upc_item = ? AND (sku_item IS NULL OR sku_item = '') AND id_sell = ?";
                $checkStmt = $mysqli->prepare($checkQuery);
                $checkStmt->bind_param("sss", $sell_order, $upc_item, $id_sell);
            } else {
                $checkQuery = "SELECT id_return FROM returns 
                              WHERE sell_order = ? AND upc_item = ? AND sku_item = ? AND id_sell = ?";
                $checkStmt = $mysqli->prepare($checkQuery);
                $checkStmt->bind_param("ssss", $sell_order, $upc_item, $sku_item, $id_sell);
            }
            
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            
            if ($checkResult->num_rows > 0) {
                // Ya existe, actualizar el registro existente
                $existingRow = $checkResult->fetch_assoc();
                $existing_id_return = $existingRow['id_return'];
                $checkStmt->close();
                
                $query = "UPDATE returns SET 
                            quantity = ?,
                            product_charge = ?,
                            shipping_paid = ?,
                            tax_return = ?,
                            selling_fee_refund = ?,
                            refund_administration_fee = ?,
                            other_refund_fee = ?,
                            return_cost = ?,
                            buyer_comments = ?,
                            devolution_date = ?
                          WHERE id_return = ?";
                
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("ssssssssssi", 
                    $quantity,
                    $product_charge,
                    $shipping_paid,
                    $tax_return,
                    $selling_fee_refund,
                    $refund_administration_fee,
                    $other_refund_fee,
                    $return_cost,
                    $buyer_comments,
                    $devolution_date,
                    $existing_id_return
                );
            } else {
                // No existe, crear un nuevo registro
                $checkStmt->close();
                
                $query = "INSERT INTO returns (
                            id_sell,
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
                            devolution_date,
                            creation_date
                          ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                
                $stmt = $mysqli->prepare($query);
                $stmt->bind_param("ssssssssssssss", 
                    $id_sell,
                    $sell_order,
                    $upc_item,
                    $sku_item,
                    $quantity,
                    $product_charge,
                    $shipping_paid,
                    $tax_return,
                    $selling_fee_refund,
                    $refund_administration_fee,
                    $other_refund_fee,
                    $return_cost,
                    $buyer_comments,
                    $devolution_date
                );
            }
        }
        
        if ($stmt->execute()) {
            $message = !empty($id_return) ? 'Return updated successfully' : 'Return created successfully';
            sendJsonResponse(['success' => true, 'message' => $message]);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Error saving return']);
        }
        
        $stmt->close();
    } catch (Exception $e) {
        sendJsonResponse(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    sendJsonResponse(['success' => false, 'message' => 'Invalid request method']);
}

$mysqli->close();
?>
