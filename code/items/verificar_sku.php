<?php
// Include database connection
include_once '../../conexion.php';

if (isset($_POST['sku_item'])) {
    $sku_item = strtoupper(trim($_POST['sku_item']));

    // Consulta para verificar si el SKU ya existe
    $stmt = $mysqli->prepare("SELECT sku_item FROM items WHERE sku_item = ?");
    $stmt->bind_param("s", $sku_item);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(['status' => 'existe']);
    } else {
        echo json_encode(['status' => 'no_existe']);
    }

    $stmt->close();
}
?>
