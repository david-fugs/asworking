<?php
session_start();
include("../../conexion.php");

if (isset($_POST['id_sell']) && is_array($_POST['id_sell'])) {
    $ids = $_POST['id_sell'];

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids)); // i = integer

    // Preparar SELECT para obtener los registros completos
    $stmt = $mysqli->prepare("SELECT * FROM sell WHERE id_sell IN ($placeholders)");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();

    // Procesar cada fila: insertar en devolutions y actualizar inventory
    $successCount = 0;
    while ($row = $result->fetch_assoc()) {
        // 1. Insertar en devolutions
        $insert = $mysqli->prepare("INSERT INTO devolutions (
            sell_order, date, upc_item, quantity, received_shipping, payed_shipping,
            id_store, id_sucursal, comision_item, item_price, total_item
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $insert->bind_param(
            "issidiiiiid",
            $row['sell_order'],
            $row['date'],
            $row['upc_item'],
            $row['quantity'],
            $row['received_shipping'],
            $row['payed_shipping'],
            $row['id_store'],
            $row['id_sucursal'],
            $row['comision_item'],
            $row['item_price'],
            $row['total_item']
        );

        if (!$insert->execute()) {
            continue;
        }

        // 2. Actualizar inventory: sumar quantity al quantity_inventory
        $update = $mysqli->prepare("UPDATE inventory SET quantity_inventory = quantity_inventory + ? WHERE upc_inventory = ?");
        $update->bind_param("is", $row['quantity'], $row['upc_item']);
        $update->execute();

        $successCount++;
    }

    // 3. Marcar como devueltos
    $updateSell = $mysqli->prepare("UPDATE sell SET estado_sell = 0 WHERE id_sell IN ($placeholders)");
    $updateSell->bind_param($types, ...$ids);
    $updateSell->execute();

    echo ($successCount > 0) ? "success" : "error";

    $stmt->close();
    $mysqli->close();
} else {
    echo "no_id";
}
?>
