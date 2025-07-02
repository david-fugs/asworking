<?php
header('Content-Type: application/json');
include("../../conexion.php");

if (!isset($_GET['sell_order'])) {
    echo json_encode(['error' => 'Sell order is required']);
    exit;
}

$sell_order = $_GET['sell_order'];
$upc_item = isset($_GET['upc_item']) ? $_GET['upc_item'] : null;
$id_sell = isset($_GET['id_sell']) ? $_GET['id_sell'] : null;

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
    // Si se especifica un UPC e id_sell, buscar cancelación específica para ese combination
    $cancellations = [];
    
    if ($upc_item && $id_sell) {
        // Buscar cancelación específica para este order_id + id_sell + upc_item
        $cancellationQuery = "SELECT * FROM cancellations WHERE order_id = ? AND id_sell = ? AND upc_item = ?";
        $cancellationStmt = $mysqli->prepare($cancellationQuery);
        $cancellationStmt->bind_param("sis", $sell_order, $id_sell, $upc_item);
        $cancellationStmt->execute();
        $cancellationResult = $cancellationStmt->get_result();
        
        if ($cancellationResult->num_rows > 0) {
            $cancellation = $cancellationResult->fetch_assoc();
            // Usar id_sell + upc_item como clave para el array de cancelaciones
            $cancellations[$id_sell . '_' . $upc_item] = $cancellation;
        }
    } else {
        // Obtener todas las cancelaciones para este order_id
        $cancellationQuery = "SELECT * FROM cancellations WHERE order_id = ?";
        $cancellationStmt = $mysqli->prepare($cancellationQuery);
        $cancellationStmt->bind_param("s", $sell_order);
        $cancellationStmt->execute();
        $cancellationResult = $cancellationStmt->get_result();
        
        while ($cancellation = $cancellationResult->fetch_assoc()) {
            // Usar id_sell + upc_item como clave para el array de cancelaciones
            $key = $cancellation['id_sell'] . '_' . $cancellation['upc_item'];
            $cancellations[$key] = $cancellation;
        }
    }

    echo json_encode([
        'items' => $items,
        'cancellations' => $cancellations,
        // Para compatibilidad con código existente, retornar la primera cancelación
        'cancellation' => ($upc_item && $id_sell && isset($cancellations[$id_sell . '_' . $upc_item])) ? 
                          $cancellations[$id_sell . '_' . $upc_item] : 
                          (count($cancellations) > 0 ? reset($cancellations) : null)
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
} finally {
    $mysqli->close();
}
?>
