<?php
include 'conexion.php';

echo "Testing cancellations with duplicate UPC but different id_sell...\n\n";

// Crear datos de prueba en sell table si no existen
$test_order = 'TEST_DUP_UPC_123';
$test_upc = '123456789012';

// Eliminar datos de prueba existentes
$mysqli->query("DELETE FROM cancellations WHERE order_id = '$test_order'");
$mysqli->query("DELETE FROM sell WHERE sell_order = '$test_order'");

// Insertar dos items con el mismo UPC pero diferentes id_sell
$insert1 = "INSERT INTO sell (sell_order, upc_item, sku_item, quantity, total_item, estado_sell) 
            VALUES ('$test_order', '$test_upc', 'SKU001', 1, 10.00, 1)";
$insert2 = "INSERT INTO sell (sell_order, upc_item, sku_item, quantity, total_item, estado_sell) 
            VALUES ('$test_order', '$test_upc', 'SKU002', 1, 15.00, 1)";

if ($mysqli->query($insert1) && $mysqli->query($insert2)) {
    echo "Test data created successfully.\n";
    
    // Obtener los id_sell generados
    $result = $mysqli->query("SELECT id_sell, sku_item FROM sell WHERE sell_order = '$test_order' ORDER BY id_sell");
    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    
    echo "Items created:\n";
    foreach ($items as $item) {
        echo "- ID Sell: {$item['id_sell']}, SKU: {$item['sku_item']}\n";
    }
    
    // Crear cancelaciones para cada item
    foreach ($items as $item) {
        $id_sell = $item['id_sell'];
        $sku = $item['sku_item'];
        $refund = ($sku == 'SKU001') ? 5.00 : 7.50;
        
        $insert_cancel = "INSERT INTO cancellations (order_id, id_sell, upc_item, refund_amount, net_cancellation, created_at, updated_at) 
                          VALUES ('$test_order', $id_sell, '$test_upc', $refund, $refund, NOW(), NOW())";
        
        if ($mysqli->query($insert_cancel)) {
            echo "Cancellation created for ID Sell: $id_sell, SKU: $sku, Refund: $refund\n";
        } else {
            echo "Error creating cancellation for ID Sell: $id_sell - " . $mysqli->error . "\n";
        }
    }
    
    echo "\nNow testing retrieval...\n";
    
    // Probar la obtención de cancelaciones
    $query = "SELECT c.*, s.sku_item 
              FROM cancellations c 
              JOIN sell s ON s.id_sell = c.id_sell 
              WHERE c.order_id = '$test_order'";
    $result = $mysqli->query($query);
    
    echo "Cancellations found:\n";
    while ($row = $result->fetch_assoc()) {
        echo "- ID Sell: {$row['id_sell']}, UPC: {$row['upc_item']}, SKU: {$row['sku_item']}, Refund: {$row['refund_amount']}\n";
    }
    
} else {
    echo "Error creating test data: " . $mysqli->error . "\n";
}

echo "\nTest completed. You can now test the form with sell_order: $test_order\n";
echo "Remember to clean up test data when done.\n";

$mysqli->close();
?>
