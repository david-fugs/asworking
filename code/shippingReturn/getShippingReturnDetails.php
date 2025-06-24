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
      // Obtener información de shipping return si existe
    $queryShippingReturn = "
    SELECT 
        billing_return
    FROM shipping_return
    WHERE sell_order = ?
    ";
    
    $stmtShippingReturn = $mysqli->prepare($queryShippingReturn);
    $stmtShippingReturn->bind_param("s", $sell_order);
    $stmtShippingReturn->execute();
    $resultShippingReturn = $stmtShippingReturn->get_result();
    
    $shipping_return = null;
    if ($resultShippingReturn->num_rows > 0) {
        $shipping_return = $resultShippingReturn->fetch_assoc();
    }
    
    $response = [
        'items' => $items,
        'shipping_return' => $shipping_return
    ];
    
    header('Content-Type: application/json');
    echo json_encode($response);
    
    $stmtItems->close();
    $stmtShippingReturn->close();
} else {
    echo json_encode(['error' => 'Sell order parameter is missing']);
}

$mysqli->close();
?>
