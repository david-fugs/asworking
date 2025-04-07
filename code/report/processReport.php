<?php
include("../../conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $upc_asignado = $mysqli->real_escape_string($_POST['upc_asignado_report']);
    $upc_final = $mysqli->real_escape_string($_POST['upc_final_report']);
    $cons = $mysqli->real_escape_string($_POST['cons_report']);
    $folder = $mysqli->real_escape_string($_POST['folder_report']);
    $loc = $mysqli->real_escape_string($_POST['loc_report']);
    $quantity = $mysqli->real_escape_string($_POST['quantity_report']);
    $sku = $mysqli->real_escape_string($_POST['sku_report']);
    $brand = $mysqli->real_escape_string($_POST['brand_report']);
    $item = $mysqli->real_escape_string($_POST['item_report']);
    $vendor = $mysqli->real_escape_string($_POST['vendor_report']);
    $color = $mysqli->real_escape_string($_POST['color_report']);
    $size = $mysqli->real_escape_string($_POST['size_report']);
    $category = $mysqli->real_escape_string($_POST['category_report']);
    $weight = $mysqli->real_escape_string($_POST['weight_report']);
    $inventory = $mysqli->real_escape_string($_POST['inventory_report']);
    $sucursal = $mysqli->real_escape_string($_POST['sucursal_report']);
    $observacion = $mysqli->real_escape_string($_POST['observacion_report']);
    // Actualizar la tabla items (sin cambiar quantity_inventory)
    $query = "INSERT INTO daily_report (
        upc_asignado_report, upc_final_report, cons_report, folder_report, 
        loc_report, quantity_report, sku_report, brand_report, item_report, 
        vendor_report, color_report, size_report, category_report, 
        weight_report, inventory_report, sucursal_report, observacion_report
    ) VALUES (
        '$upc_asignado', '$upc_final', '$cons', '$folder',
        '$loc', '$quantity', '$sku', '$brand', '$item',
        '$vendor', '$color', '$size', '$category',
        '$weight', '$inventory', '$sucursal', '$observacion'
    )";    // Ejecutar consulta
    if ($mysqli->query($query)) {
        echo "<script>
            alert('Insert successful');
            window.location.href = 'addReport.php';
          </script>";
    } else {
        echo "<script>
            alert('Error  " . $mysqli->error . "');
            window.location.href = 'addReport.php';
          </script>";
    }
} else {
    echo "<script>
            alert('Method not valid');
            window.location.href = 'addReport.php';
          </script>";
}
