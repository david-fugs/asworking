<?php
include("../../conexion.php");
session_start();
// Verificamos si llegaron los datos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seleccionados'])) {

    $id_usu =  1;
    $seleccionados = $_POST['seleccionados'];

    foreach ($seleccionados as $index) {
        // Aseguramos que sea un número
        $i = intval($index);        // Obtenemos los datos de ese índice específico y los escapamos para prevenir inyección SQL
        $id_report = intval($_POST['id_report'][$i]);
        $fecha = $mysqli->real_escape_string($_POST['fecha_alta_reporte'][$i]);
        $upcAsignado = $mysqli->real_escape_string($_POST['upc_asignado_report'][$i]);
        $upcFinal = $mysqli->real_escape_string($_POST['upc_final_report'][$i]);
        $cons = $mysqli->real_escape_string($_POST['cons_report'][$i]);
        $folder = $mysqli->real_escape_string($_POST['folder_report'][$i]);
        $loc = $mysqli->real_escape_string($_POST['loc_report'][$i]);
        $quantity = intval($_POST['quantity_report'][$i]);
        $sku = $mysqli->real_escape_string($_POST['sku_report'][$i]);
        $brand = $mysqli->real_escape_string($_POST['brand_report'][$i]);
        $item = $mysqli->real_escape_string($_POST['item_report'][$i]);
        $vendor = $mysqli->real_escape_string($_POST['vendor_report'][$i]);
        $color = $mysqli->real_escape_string($_POST['color_report'][$i]);
        $size = $mysqli->real_escape_string($_POST['size_report'][$i]);
        $cost = floatval($_POST['cost_report'][$i] ?? 0.0);
    $category = $mysqli->real_escape_string($_POST['category_report'][$i]);
    $weight = $mysqli->real_escape_string($_POST['weight_report'][$i]);
    // The previous single inventory field in the UI is now split: batch_report holds the old value,
    // and inventory_report is a new (possibly empty) inventory field. We capture both.
    $batch = $mysqli->real_escape_string($_POST['batch_report'][$i] ?? '');
    $inventory = $mysqli->real_escape_string($_POST['inventory_report'][$i] ?? '');
    $observacion = $mysqli->real_escape_string($_POST['observacion_report'][$i]);
        
        // Procesar stores data
        $stores_json = isset($_POST['stores_report'][$i]) ? $_POST['stores_report'][$i] : '';
        $stores_json_escaped = $mysqli->real_escape_string($stores_json);
        // Insert into items, including batch_items. inventory_item keeps the value of $loc as before.
        $sql_insert = "INSERT INTO items (
            sku_item, upc_item, date_item, brand_item, item_item, folder_item, color_item, size_item, category_item, cost_item, weight_item, inventory_item, batch_item, stores_item, id_usu
        ) VALUES (
            '$sku', '$upcFinal', '$fecha', '$brand', '$item', '$cons', '$color', '$size', '$category', '$cost', '$weight', '$loc', '$batch', '$stores_json_escaped', '$id_usu'
        ) ON DUPLICATE KEY UPDATE
            sku_item = '$sku',
            upc_item = '$upcFinal',
            date_item = '$fecha',
            brand_item = '$brand',
            item_item = '$item',
            folder_item = '$cons',
            color_item = '$color',
            size_item = '$size',
            category_item = '$category',
            cost_item = '$cost',
            weight_item = '$weight',
            inventory_item = '$loc',
            batch_item = '$batch',
            stores_item = '$stores_json_escaped',
            id_usu = '$id_usu'
            ";
        // Ejecutar consulta
        if ($mysqli->query($sql_insert)) {
            echo "<script>alert('Insert Item  successful');</script>";
        } else {
            echo "<script>alert('Error " . $mysqli->error . "');             window.location.href = 'seeReport.php';
</script>";
        }        // Insertar/actualizar la tabla inventory (sin ubicación)
        $sql_insert_inventory = "INSERT INTO inventory (upc_inventory, sku_inventory, quantity_inventory) VALUES ('$upcFinal','$sku', '$quantity') ON DUPLICATE KEY UPDATE quantity_inventory = '$quantity'";

        // Ejecutar consulta
        if ($mysqli->query($sql_insert_inventory)) {
            echo "<script>alert('Insert/Update Inventory successful');</script>";
        } else {
            echo "<script>alert('Error " . $mysqli->error . "');            window.location.href = 'seeReport.php';
</script>";
        }
        // Actualizar la tabla daily_report
        //solamente cambiamos el estado_reporte a 0 del id_reporte
        $sql_update = "UPDATE daily_report SET estado_reporte = 0 WHERE id_report = $id_report";
        // Ejecutar consulta
        if ($mysqli->query($sql_update)) {
            echo "<script>alert('Update report successful');
            window.location.href = 'seeReport.php';
            </script>";
        } else {
            echo "<script>alert('Error " . $mysqli->error . "');
                window.location.href = 'seeReport.php';
            </script>";
        }
    }
}
