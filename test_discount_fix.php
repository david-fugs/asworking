<?php
include 'conexion.php';

echo "Testing discount fix for UPC 733004811005:\n\n";

// Mostrar los registros actuales para ese UPC
$query = "SELECT 
            d.id_discount, 
            d.id_sell, 
            d.sell_order, 
            d.upc_item, 
            d.discount_date,
            s.sku_item,
            s.date
          FROM discounts d
          LEFT JOIN sell s ON d.id_sell = s.id_sell
          WHERE d.upc_item = '733004811005'
          ORDER BY d.id_sell";

$result = $mysqli->query($query);

echo "Current discount records for UPC 733004811005:\n";
echo "ID | ID_SELL | SELL_ORDER | UPC | SKU | DISCOUNT_DATE | SELL_DATE\n";
echo str_repeat("-", 80) . "\n";

while($row = $result->fetch_assoc()) {
    printf("%2d | %7d | %10s | %12s | %8s | %12s | %10s\n", 
           $row['id_discount'], 
           $row['id_sell'], 
           $row['sell_order'], 
           $row['upc_item'], 
           $row['sku_item'] ?: 'NULL', 
           $row['discount_date'] ?: 'NULL', 
           $row['date']);
}

echo "\n";

// Test the search query to see if it correctly differentiates records
echo "Testing search results for UPC 733004811005:\n";
$query2 = "SELECT 
            sell.id_sell,
            sell.sell_order,
            sell.date,
            sell.upc_item,
            sell.sku_item,
            discounts.id_discount,
            discounts.price_discount,
            discounts.discount_date
          FROM sell 
          LEFT JOIN discounts ON BINARY discounts.upc_item = BINARY sell.upc_item AND discounts.id_sell = sell.id_sell
          WHERE sell.upc_item = '733004811005' AND sell.estado_sell = 1";

$result2 = $mysqli->query($query2);

echo "SELL_ID | SELL_ORDER | UPC | SKU | DISCOUNT_ID | PRICE_DISC | DISC_DATE\n";
echo str_repeat("-", 75) . "\n";

while($row = $result2->fetch_assoc()) {
    printf("%7d | %10s | %12s | %8s | %11s | %10s | %10s\n", 
           $row['id_sell'], 
           $row['sell_order'], 
           $row['upc_item'], 
           $row['sku_item'] ?: 'NULL', 
           $row['id_discount'] ?: 'NULL', 
           $row['price_discount'] ?: 'NULL', 
           $row['discount_date'] ?: 'NULL');
}

$mysqli->close();
?>
