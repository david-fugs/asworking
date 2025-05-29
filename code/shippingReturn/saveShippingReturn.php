<?php
require '../../conexion.php';

// Procesar POST (guardar datos)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    print_r($_POST);
    $sell_order = $_POST['sell_order'];
    $billing_return = $_POST['billing_return'];

    // Verificar si ya existe un registro de envío para esta orden
    $checkQuery = "SELECT * FROM shipping_return WHERE sell_order = ?";
    $stmtCheck = $mysqli->prepare($checkQuery);
    $stmtCheck->bind_param("s", $sell_order);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Actualizar el registro existente
        $updateQuery = "UPDATE shipping_return SET billing_return = ? WHERE sell_order = ?";
        $stmtUpdate = $mysqli->prepare($updateQuery);
        $stmtUpdate->bind_param("ds", $billing_return, $sell_order);
        if ($stmtUpdate->execute()) {
             header("Location: shippingReturn.php?message=Envío guardado correctamente.");
        } else {
            echo "Error al actualizar el envío: " . $mysqli->error;
        }
    } else {
        // Insertar un nuevo registro
        $insertQuery = "INSERT INTO shipping_return (sell_order, billing_return) VALUES (?, ?)";
        $stmtInsert = $mysqli->prepare($insertQuery);
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