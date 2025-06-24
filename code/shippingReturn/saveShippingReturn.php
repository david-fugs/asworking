<?php
include("../../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {    $sell_order = isset($_POST['sell_order']) ? trim($_POST['sell_order']) : '';
    $billing_return = isset($_POST['billing_return']) ? floatval($_POST['billing_return']) : 0;
    
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
        SET billing_return = ?
        WHERE sell_order = ?
        ";
        
        $updateStmt = $mysqli->prepare($updateQuery);
        $updateStmt->bind_param("ds", $billing_return, $sell_order);
        
        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Shipping return information updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating shipping return information: ' . $mysqli->error]);
        }
        
        $updateStmt->close();
    } else {        // Insertar nuevo registro
        $insertQuery = "
        INSERT INTO shipping_return (sell_order, billing_return) 
        VALUES (?, ?)
        ";
        
        $insertStmt = $mysqli->prepare($insertQuery);
        $insertStmt->bind_param("sd", $sell_order, $billing_return);
        
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
        $stmtInsert->bind_param("sd", $sell_order, $billing_return);
        if ($stmtInsert->execute()) {
            //redirigir a la página de shipping.php
            header("Location: shippingReturn.php?message=Envío guardado correctamente.");
            
        } else {
            echo "Error al guardar el envío: " . $mysqli->error;
        }
    }

    // Cerrar las declaraciones
    if (isset($stmtCheck)) {
        $stmtCheck->close();
    }
    if (isset($stmtUpdate)) {
        $stmtUpdate->close();
    }
    if (isset($stmtInsert)) {
        $stmtInsert->close();
    }
} else {
    echo "Método no permitido.";

}