<?php
include("../../conexion.php");

if (isset($_GET['sell_order'])) {
    $sell_order = trim($_GET['sell_order']);
    
    if (empty($sell_order)) {
        echo json_encode(['error' => 'Sell order is required']);
        exit;
    }
    
    // Obtener los items de la sell order
    $queryItems = "
    SELECT 
        s.sell_order,
        s.upc_item,
        s.sku_item,
        s.quantity,
        s.comision_item,
        s.cargo_fijo,
        s.item_profit,
        s.total_item
    FROM sell s
    WHERE s.sell_order = ?
    ORDER BY s.upc_item
    ";
    
    $stmtItems = $mysqli->prepare($queryItems);
    $stmtItems->bind_param("s", $sell_order);
    $stmtItems->execute();
    $resultItems = $stmtItems->get_result();
    
    $items = [];
    while ($row = $resultItems->fetch_assoc()) {
        $items[] = $row;
    }
    
    if (empty($items)) {
        echo json_encode(['error' => 'No items found for this sell order']);
        exit;
    }
      // Obtener información de shipping si existe
    $queryShipping = "
    SELECT 
        shipping_paid,
        shipping_other_carrier,
        shipping_adjust,
        shipping_date
    FROM shipping
    WHERE sell_order = ?
    ";
    
    $stmtShipping = $mysqli->prepare($queryShipping);
    $stmtShipping->bind_param("s", $sell_order);
    $stmtShipping->execute();
    $resultShipping = $stmtShipping->get_result();
    
    $shipping = null;
    if ($resultShipping->num_rows > 0) {
        $shipping = $resultShipping->fetch_assoc();
    }
    
    $response = [
        'items' => $items,
        'shipping' => $shipping
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    
    $stmtItems->close();
    $stmtShipping->close();
} else {
    echo json_encode(['error' => 'Sell order parameter is missing']);
}

$mysqli->close();
?>
