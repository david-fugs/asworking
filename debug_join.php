<?php
include 'conexion.php';

// Buscar un UPC específico que sabemos que funciona
$test_upc = '009269321123';

echo "=== PRUEBA CON UPC ESPECÍFICO: $test_upc ===\n";

echo "\n1. Datos en tabla SELL para este UPC:\n";
$result = $mysqli->query("SELECT sell_order, upc_item, sku_item FROM sell WHERE upc_item = '$test_upc'");
while($row = $result->fetch_assoc()) {
    echo "Sell Order: " . $row['sell_order'] . " | UPC: " . $row['upc_item'] . " | SKU: " . ($row['sku_item'] ?? 'NULL') . "\n";
}

echo "\n2. Datos en tabla ITEMS para este UPC:\n";
$result = $mysqli->query("SELECT upc_item, sku_item, item_item FROM items WHERE upc_item = '$test_upc'");
while($row = $result->fetch_assoc()) {
    echo "UPC: " . $row['upc_item'] . " | SKU: " . ($row['sku_item'] ?? 'NULL') . " | Item: " . $row['item_item'] . "\n";
}

echo "\n3. JOIN completo para este UPC:\n";
$result = $mysqli->query("
SELECT 
    sell.sell_order,
    sell.upc_item as sell_upc,
    items.upc_item as items_upc,
    items.item_item
FROM sell 
LEFT JOIN items ON items.upc_item = sell.upc_item
WHERE sell.upc_item = '$test_upc' AND sell.estado_sell = 1
");
while($row = $result->fetch_assoc()) {
    echo "Sell Order: " . $row['sell_order'] . " | Item: " . ($row['item_item'] ?? 'NULL') . "\n";
}

echo "\n4. Conteo de registros en cada tabla:\n";
$result = $mysqli->query("SELECT COUNT(*) as count FROM sell WHERE estado_sell = 1");
$sell_count = $result->fetch_assoc()['count'];
echo "Registros en SELL (activos): $sell_count\n";

$result = $mysqli->query("SELECT COUNT(*) as count FROM items");
$items_count = $result->fetch_assoc()['count'];
echo "Registros en ITEMS: $items_count\n";

$result = $mysqli->query("
SELECT COUNT(*) as count 
FROM sell 
LEFT JOIN items ON items.upc_item = sell.upc_item
WHERE sell.estado_sell = 1 AND items.item_item IS NOT NULL
");
$matched_count = $result->fetch_assoc()['count'];
echo "Registros con MATCH exitoso: $matched_count\n";

$mysqli->close();
?>
