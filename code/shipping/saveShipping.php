<?php
require '../../conexion.php';

// Procesar POST (guardar datos)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sell_order = $_POST['sell_order'];
    $shipping_paid = $_POST['shipping_paid'];
    $shipping_other_carrier = $_POST['shipping_other_carrier'];
    $shipping_adjust = $_POST['shipping_adjust'];

    // Verificar si ya existe un registro de envío para esta orden
    $checkQuery = "SELECT * FROM shipping WHERE sell_order = ?";
    $stmtCheck = $mysqli->prepare($checkQuery);
    $stmtCheck->bind_param("s", $sell_order);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Actualizar el registro existente
        $updateQuery = "UPDATE shipping SET shipping_paid = ?, shipping_other_carrier = ?, shipping_adjust = ? WHERE sell_order = ?";
        $stmtUpdate = $mysqli->prepare($updateQuery);
        $stmtUpdate->bind_param("ddds", $shipping_paid, $shipping_other_carrier, $shipping_adjust, $sell_order);
        if ($stmtUpdate->execute()) {
             header("Location: shipping.php?message=Envío guardado correctamente.");
        } else {
            echo "Error al actualizar el envío: " . $mysqli->error;
        }
    } else {
        // Insertar un nuevo registro
        $insertQuery = "INSERT INTO shipping (sell_order, shipping_paid, shipping_other_carrier, shipping_adjust) VALUES (?, ?, ?, ?)";
        $stmtInsert = $mysqli->prepare($insertQuery);
        $stmtInsert->bind_param("sdds", $sell_order, $shipping_paid, $shipping_other_carrier, $shipping_adjust);
        if ($stmtInsert->execute()) {
            //redirigir a la página de shipping.php
            header("Location: shipping.php?message=Envío guardado correctamente.");
            
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