<?php
// update_quantity.php
include_once '../../conexion.php';

if (isset($_POST['upc_item']) && isset($_POST['sku_item']) && isset($_POST['quantity_inventory'])) {
    $upc_item = strtoupper(trim($_POST['upc_item']));
    $sku_item = strtoupper(trim($_POST['sku_item']));
    $quantity_inventory = intval($_POST['quantity_inventory']);

    // Actualizar la cantidad en la tabla inventory usando consulta plana (escapando la entrada)
    $upc_esc = $mysqli->real_escape_string($upc_item);
    $sku_esc = $mysqli->real_escape_string($sku_item);
    $quantity_esc = (int)$quantity_inventory;
    $sql = "UPDATE inventory SET quantity_inventory = $quantity_esc WHERE upc_inventory = '$upc_esc' AND sku_inventory = '$sku_esc'";
    if ($mysqli->query($sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Quantity updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters.']);
}
?>
