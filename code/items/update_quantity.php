<?php
// update_quantity.php
include_once '../../conexion.php';

if (isset($_POST['upc_item']) && isset($_POST['sku_item']) && isset($_POST['quantity_inventory'])) {
    $upc_item = strtoupper(trim($_POST['upc_item']));
    $sku_item = strtoupper(trim($_POST['sku_item']));
    $quantity_inventory = intval($_POST['quantity_inventory']);

    // Actualizar la cantidad en la tabla inventory
    $stmt = $mysqli->prepare("UPDATE inventory SET quantity_inventory = ? WHERE upc_inventory = ? AND sku_inventory = ?");
    $stmt->bind_param("iss", $quantity_inventory, $upc_item, $sku_item);
    $success = $stmt->execute();
    $stmt->close();

    if ($success) {
        echo json_encode(['status' => 'success', 'message' => 'Quantity updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters.']);
}
?>
