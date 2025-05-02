<?php
// Include database connection
include_once '../../conexion.php';

if (isset($_POST['upc_item'])) {
    $upc_item = strtoupper(trim($_POST['upc_item']));

    // Consulta para obtener los items correspondientes al UPC
    $stmt = $mysqli->prepare("SELECT brand_item, item_item FROM items WHERE upc_item = ?");
    $stmt->bind_param("s", $upc_item);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Si existen coincidencias, devolver los resultados
        $stmt->bind_result($brand_item, $item_item);
        
        $items = [];
        while ($stmt->fetch()) {
            $items[] = ['brand_item' => $brand_item, 'item_item' => $item_item];
        }

        echo json_encode(['status' => 'existe', 'items' => $items]);
    } else {
        echo json_encode(['status' => 'no_existe']);
    }

    $stmt->close();
}
?>
