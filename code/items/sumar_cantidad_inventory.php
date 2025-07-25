<?php
// sumar_cantidad_inventory.php
header('Content-Type: application/json');
include("../../conexion.php");

if (!isset($_POST['upc_item']) || !isset($_POST['cantidad'])) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan parámetros.']);
    exit;
}

$upc = strtoupper(trim($_POST['upc_item']));
$cantidad = intval($_POST['cantidad']);

if ($cantidad < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Cantidad inválida.']);
    exit;
}

// Buscar el registro en inventory por upc_item
$sql = "SELECT id_inventory, quantity_inventory FROM inventory WHERE upc_item = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('s', $upc);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $id_inventory = $row['id_inventory'];
    $cantidad_actual = intval($row['quantity_inventory']);
    $nueva_cantidad = $cantidad_actual + $cantidad;

    // Actualizar la cantidad
    $update = $conexion->prepare("UPDATE inventory SET quantity_inventory = ? WHERE id_inventory = ?");
    $update->bind_param('ii', $nueva_cantidad, $id_inventory);
    if ($update->execute()) {
        echo json_encode(['status' => 'success', 'nueva_cantidad' => $nueva_cantidad]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar la cantidad.']);
    }
    $update->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'No se encontró el UPC en inventory.']);
}
$stmt->close();
$conexion->close();
?>
