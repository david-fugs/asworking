<?php
include("../../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    $sell_order = isset($_POST['sell_order']) ? trim($_POST['sell_order']) : '';
    $billing_return = isset($_POST['billing_return']) ? floatval($_POST['billing_return']) : 0;
    $shipping_return_date = isset($_POST['shipping_return_date']) ? trim($_POST['shipping_return_date']) : null;
    
    if (empty($sell_order)) {
        echo json_encode(['success' => false, 'message' => 'Sell order is required']);
        exit;
    }
    
    // Verificar si ya existe un registro de shipping return para esta sell order
    $checkQuery = "SELECT sell_order FROM shipping_return WHERE sell_order = ?";
    $checkStmt = $mysqli->prepare($checkQuery);
    $checkStmt->bind_param("s", $sell_order);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {        // Actualizar registro existente
        $updateQuery = "
        UPDATE shipping_return 
        SET billing_return = ?,
            shipping_return_date = ?
        WHERE sell_order = ?
        ";
        
        $updateStmt = $mysqli->prepare($updateQuery);
        $updateStmt->bind_param("dss", $billing_return, $shipping_return_date, $sell_order);
        
        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Shipping return information updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating shipping return information: ' . $mysqli->error]);
        }
        
        $updateStmt->close();
    } else {        // Insertar nuevo registro
        $insertQuery = "
        INSERT INTO shipping_return (sell_order, billing_return, shipping_return_date) 
        VALUES (?, ?, ?)
        ";
        
        $insertStmt = $mysqli->prepare($insertQuery);
        $insertStmt->bind_param("sds", $sell_order, $billing_return, $shipping_return_date);
        
        if ($insertStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Shipping return information saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error saving shipping return information: ' . $mysqli->error]);
        }
        
        $insertStmt->close();
    }
    
    $checkStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$mysqli->close();
?>