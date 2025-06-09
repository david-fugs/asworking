<?php
header('Content-Type: application/json');
include("../../conexion.php");

if (!isset($_GET['sell_order'])) {
    echo json_encode(['error' => 'Sell order is required']);
    exit;
}

$sell_order = $_GET['sell_order'];

try {
    // Obtener información de la venta
    $query = "SELECT 
                sell.id_sell,
                sell.sell_order,
                sell.date,
                sell.upc_item,
                sell.sku_item,
                sell.quantity,
                sell.comision_item,
                sell.cargo_fijo,
                sell.item_profit,
                sell.total_item,
                store.store_name,
                sucursal.code_sucursal,
                items.brand_item,
                items.item_item,
                items.color_item,
                items.ref_item
              FROM sell
              LEFT JOIN store ON store.id_store = sell.id_store
              LEFT JOIN sucursal ON sucursal.id_sucursal = sell.id_sucursal
              LEFT JOIN items ON items.sku_item = sell.sku_item 
                              AND (items.upc_item = sell.upc_item OR items.upc_item IS NULL)
              WHERE sell.sell_order = ? AND sell.estado_sell = 1";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $sell_order);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    if (empty($items)) {
        echo json_encode(['error' => 'No items found for this sell order']);
        exit;
    }

    // Obtener información de cancellation existente (si existe)
    $cancellationQuery = "SELECT * FROM cancellations WHERE order_id = ?";
    $cancellationStmt = $mysqli->prepare($cancellationQuery);
    $cancellationStmt->bind_param("s", $sell_order);
    $cancellationStmt->execute();
    $cancellationResult = $cancellationStmt->get_result();
    
    $cancellation = null;
    if ($cancellationResult->num_rows > 0) {
        $cancellation = $cancellationResult->fetch_assoc();
    }

    echo json_encode([
        'items' => $items,
        'cancellation' => $cancellation
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} finally {
    $mysqli->close();
}
?>
