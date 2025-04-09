<?php
include("../../conexion.php");
session_start();

// Verificamos si llegaron los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seleccionados'])) {

    $id_usu = $_SESSION['id_usu'];
    $seleccionados = $_POST['seleccionados'];

    foreach ($seleccionados as $index) {
        // Aseguramos que sea un número
        $i = intval($index);

        // Obtenemos los datos de ese índice específico
        $id_report = $_POST['id_report'][$i];
        $fecha = $_POST['fecha_alta_reporte'][$i];
        $upcAsignado = $_POST['upc_asignado_report'][$i];
        $upcFinal = $_POST['upc_final_report'][$i];
        $cons = $_POST['cons_report'][$i];
        $folder = $_POST['folder_report'][$i];
        $loc = $_POST['loc_report'][$i];
        $quantity = $_POST['quantity_report'][$i];
        $sku = $_POST['sku_report'][$i];
        $brand = $_POST['brand_report'][$i];
        $item = $_POST['item_report'][$i];
        $vendor = $_POST['vendor_report'][$i];
        $color = $_POST['color_report'][$i];
        $size = $_POST['size_report'][$i];
        $cost = $_POST['cost_report'][$i] ?? '';
        $category = $_POST['category_report'][$i];
        $weight = $_POST['weight_report'][$i];
        $inventory = $_POST['inventory_report'][$i];
        $sucursal = $_POST['sucursal_report'][$i];
        $observacion = $_POST['observacion_report'][$i];

        $sql_insert = "INSERT INTO items (
            sku_item,  upc_item, date_item , brand_item, item_item, ref_item, color_item, size_item, category_item, cost_item, weight_item, inventory_item,id_usu
        ) VALUES (
            '$sku', '$upcFinal', '$fecha', '$brand', '$item', '$cons', '$color', '$size', '$category', '$cost', '$weight', '$inventory','$id_usu'
        ) ON DUPLICATE KEY UPDATE
            sku_item = '$sku',
            upc_item = '$upcFinal',
            date_item = '$fecha',
            brand_item = '$brand',
            item_item = '$item',
            ref_item = '$cons',
            color_item = '$color',
            size_item = '$size',
            category_item = '$category',
            cost_item = '$cost',
            weight_item = '$weight',
            inventory_item = '$inventory'

            ";
        // Ejecutar consulta
        if ($mysqli->query($sql_insert)) {
            echo "<script>alert('Insert Item  successful');</script>";
        } else {
            echo "<script>alert('Error " . $mysqli->error . "');</script>";
        }

        // Insertar la tabla items
        $sql_insert_inventory = "INSERT INTO inventory (upc_inventory, sku_inventory, quantity_inventory, sucursal_inventory) VALUES ('$upcFinal','$sku', '$quantity', '$sucursal') ON DUPLICATE KEY UPDATE quantity_inventory = '$quantity'";

        // Ejecutar consulta
        if ($mysqli->query($sql_insert_inventory)) {
            echo "<script>alert('Insert Inventory successful');</script>";
        } else {
            echo "<script>alert('Error " . $mysqli->error . "');</script>";
        }
        // Actualizar la tabla daily_report
        //solamente cambiamos el estado_reporte a 0 del id_reporte
        $sql_update = "UPDATE daily_report SET estado_reporte = 0 WHERE id_report = $id_report";
        // Ejecutar consulta
        if ($mysqli->query($sql_update)) {
            echo "<script>alert('Update report successful');</script>";
        } else {
            echo "<script>alert('Error " . $mysqli->error . "');</script>";
        }
    }
}
