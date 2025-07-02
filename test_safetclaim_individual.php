<?php
include 'conexion.php';

echo "Testing SafetClaim individual record management:\n\n";

// Verificar registros con el mismo UPC pero diferentes id_sell
$query = "SELECT 
            s.id_sell,
            s.sell_order,
            s.upc_item,
            s.sku_item,
            sc.id_safetclaim,
            sc.safetclaim_date
          FROM sell s
          LEFT JOIN safetclaim sc ON sc.id_sell = s.id_sell
          WHERE s.upc_item = '733004811005' AND s.estado_sell = 1
          ORDER BY s.id_sell";

$result = $mysqli->query($query);

echo "Current records for UPC 733004811005:\n";
echo "ID_SELL | SELL_ORDER | UPC | SKU | SAFETCLAIM_ID | SAFETCLAIM_DATE\n";
echo str_repeat("-", 80) . "\n";

while($row = $result->fetch_assoc()) {
    printf("%7d | %10s | %12s | %8s | %13s | %15s\n", 
           $row['id_sell'], 
           $row['sell_order'], 
           $row['upc_item'], 
           $row['sku_item'] ?: 'NULL', 
           $row['id_safetclaim'] ?: 'NULL', 
           $row['safetclaim_date'] ?: 'NULL');
}

echo "\nNow each record should be managed individually based on id_sell.\n";
echo "When you click on a specific row, it should load only that record's SafetClaim data.\n";

$mysqli->close();
?>
