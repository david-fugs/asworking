<?php
include("../../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    $sell_order = isset($_POST['sell_order']) ? trim($_POST['sell_order']) : '';
    $shipping_paid = isset($_POST['shipping_paid']) ? floatval($_POST['shipping_paid']) : 0;
    $shipping_other_carrier = isset($_POST['shipping_other_carrier']) ? floatval($_POST['shipping_other_carrier']) : 0;
    $shipping_adjust = isset($_POST['shipping_adjust']) ? floatval($_POST['shipping_adjust']) : 0;
    $shipping_date = isset($_POST['shipping_date']) ? trim($_POST['shipping_date']) : null;
    
    if (empty($sell_order)) {
        echo json_encode(['success' => false, 'message' => 'Sell order is required']);
        exit;
    }
    
    // Verificar si ya existe un registro de shipping para esta sell order
    $checkQuery = "SELECT sell_order FROM shipping WHERE sell_order = ?";
    $checkStmt = $mysqli->prepare($checkQuery);
    $checkStmt->bind_param("s", $sell_order);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {        // Actualizar registro existente
        $updateQuery = "
        UPDATE shipping 
        SET shipping_paid = ?, 
            shipping_other_carrier = ?, 
            shipping_adjust = ?,
            shipping_date = ?
        WHERE sell_order = ?
        ";
        
        $updateStmt = $mysqli->prepare($updateQuery);
        $updateStmt->bind_param("dddss", $shipping_paid, $shipping_other_carrier, $shipping_adjust, $shipping_date, $sell_order);
        
        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Shipping information updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating shipping information: ' . $mysqli->error]);
        }
        
        $updateStmt->close();
    } else {        // Insertar nuevo registro
        $insertQuery = "
        INSERT INTO shipping (sell_order, shipping_paid, shipping_other_carrier, shipping_adjust, shipping_date) 
        VALUES (?, ?, ?, ?, ?)
        ";
        
        $insertStmt = $mysqli->prepare($insertQuery);
        $insertStmt->bind_param("sddds", $sell_order, $shipping_paid, $shipping_other_carrier, $shipping_adjust, $shipping_date);
        
        if ($insertStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Shipping information saved successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error saving shipping information: ' . $mysqli->error]);
        }
        
        $insertStmt->close();
    }
    
    $checkStmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$mysqli->close();
?>