<?php
include 'conexion.php';

echo "=== ESTRUCTURA DE LA TABLA SELL ===\n";
$result = $mysqli->query('DESCRIBE sell');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\n=== ESTRUCTURA DE LA TABLA ITEMS ===\n";
$result = $mysqli->query('DESCRIBE items');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . "\n";
}

echo "\n=== EJEMPLO DE DATOS EN SELL (primeros 3 registros) ===\n";
$result = $mysqli->query('SELECT sell_order, upc_item, sku_item FROM sell LIMIT 3');
while($row = $result->fetch_assoc()) {
    echo "Sell Order: " . $row['sell_order'] . " | UPC: " . $row['upc_item'] . " | SKU: " . ($row['sku_item'] ?? 'NULL') . "\n";
}

echo "\n=== EJEMPLO DE DATOS EN ITEMS (primeros 3 registros) ===\n";
$result = $mysqli->query('SELECT upc_item, sku_item, item_item FROM items LIMIT 3');
while($row = $result->fetch_assoc()) {
    echo "UPC: " . ($row['upc_item'] ?? 'NULL') . " | SKU: " . ($row['sku_item'] ?? 'NULL') . " | Item: " . ($row['item_item'] ?? 'NULL') . "\n";
}

echo "\n=== PRUEBA DE JOIN ACTUAL ===\n";
$result = $mysqli->query('
SELECT 
    sell.sell_order,
    sell.upc_item as sell_upc,
    items.upc_item as items_upc,
    items.item_item
FROM sell 
LEFT JOIN items ON items.upc_item = sell.upc_item
WHERE sell.estado_sell = 1
LIMIT 5
');
while($row = $result->fetch_assoc()) {
    echo "Sell Order: " . $row['sell_order'] . " | Sell UPC: " . $row['sell_upc'] . " | Items UPC: " . ($row['items_upc'] ?? 'NULL') . " | Item Name: " . ($row['item_item'] ?? 'NULL') . "\n";
}

$mysqli->close();
?>
