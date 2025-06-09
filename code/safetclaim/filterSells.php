<?php
include("../../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $upc = isset($_POST['upc']) ? trim($_POST['upc']) : '';
    $sell_order = isset($_POST['sell_order']) ? trim($_POST['sell_order']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';

    // Construir la consulta con filtros dinámicos
    $query = "SELECT 
                sell.id_sell,
                sell.sell_order,
                sell.date,
                sell.upc_item,
                store_name,
                sell.id_store,
                sell.id_sucursal,
                code_sucursal,
                items.brand_item,
                items.item_item,
                items.color_item,
                items.ref_item
              FROM sell 
              LEFT JOIN store ON store.id_store = sell.id_store
              LEFT JOIN sucursal ON sucursal.id_sucursal = sell.id_sucursal
              LEFT JOIN items ON items.sku_item = sell.sku_item 
                              AND (items.upc_item = sell.upc_item OR items.upc_item IS NULL)
              WHERE sell.estado_sell = 1";

    $params = [];
    $types = "";

    if (!empty($upc)) {
        $query .= " AND sell.upc_item LIKE ?";
        $params[] = "%$upc%";
        $types .= "s";
    }

    if (!empty($sell_order)) {
        $query .= " AND sell.sell_order LIKE ?";
        $params[] = "%$sell_order%";
        $types .= "s";
    }

    if (!empty($date)) {
        $query .= " AND sell.date = ?";
        $params[] = $date;
        $types .= "s";
    }

    $query .= " ORDER BY sell.date DESC LIMIT 50";

    try {
        $stmt = $mysqli->prepare($query);
        
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $ventas = [];
        while ($row = $result->fetch_assoc()) {
            $ventas[] = $row;
        }
        
        echo json_encode(['success' => true, 'ventas' => $ventas]);
        
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$mysqli->close();
?>
