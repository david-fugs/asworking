<?php
// Conectar a la base de datos
include("../../conexion.php");

// Verificar si la solicitud es PATCH o PUT
if ($_SERVER['REQUEST_METHOD'] == 'PATCH' || $_SERVER['REQUEST_METHOD'] == 'PUT') {

    // Obtener los datos enviados en formato JSON
    $data = json_decode(file_get_contents('php://input'), true);

    // Verificar si los datos son válidos
    if ($data && isset($data['id_sell'])) {
        // Obtener los valores del array
        $id_sell = $data['id_sell'];
        $sell_order = $data['sell_order'];
        $date = $data['date'];
        $upc = $data['upc'];
        $comision = $data['comision'];
        $received_shipping = $data['received_shipping'];
        $payed_shipping = $data['payed_shipping'];
        $storeID = $data['storeID'];
        $sucursalID = $data['sucursalID'];
        $quantity = $data['quantity'];
        $item_price = $data['item_price'];
        $total_item = $data['total_item'];

        // Consulta SQL para actualizar los datos
        $query = "UPDATE sell SET
                    sell_order = ?,
                    date = ?,
                    upc_item = ?,
                    comision_item = ?,
                    received_shipping = ?,
                    payed_shipping = ?,
                    id_store = ?,
                    id_sucursal = ?,
                    quantity = ?,
                    item_price = ?,
                    total_item = ?
                  WHERE id_sell = ?";

        // Preparar la consulta
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param(
            'ssssssssdddi',
            $sell_order,
            $date,
            $upc,
            $comision,
            $received_shipping,
            $payed_shipping,
            $storeID,
            $sucursalID,
            $quantity,
            $item_price,
            $total_item,
            $id_sell
        );

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    }
}

$mysqli->close();

