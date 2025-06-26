<?php
include 'conexion.php';

echo "Checking sell table for UPC 733004811005:\n\n";

$query = "SELECT id_sell, sell_order, date, upc_item, sku_item, estado_sell 
          FROM sell 
          WHERE upc_item = '733004811005' 
          ORDER BY id_sell";

$result = $mysqli->query($query);

echo "SELL_ID | SELL_ORDER | DATE | UPC | SKU | ESTADO\n";
echo str_repeat("-", 60) . "\n";

while($row = $result->fetch_assoc()) {
    printf("%7d | %10s | %10s | %12s | %8s | %6d\n", 
           $row['id_sell'], 
           $row['sell_order'], 
           $row['date'], 
           $row['upc_item'], 
           $row['sku_item'] ?: 'NULL', 
           $row['estado_sell']);
}

$mysqli->close();
?>
