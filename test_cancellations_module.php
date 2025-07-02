<?php
include 'conexion.php';

echo "=== Testing Cancellations Module ===\n\n";

// Test 1: Check table structure
echo "1. Table structure:\n";
$result = $mysqli->query('DESCRIBE cancellations');
while($row = $result->fetch_assoc()) {
    echo "   " . $row['Field'] . " - " . $row['Type'] . "\n";
}

// Test 2: Check sample data
echo "\n2. Sample data with cancellation_date:\n";
$result = $mysqli->query('SELECT order_id, net_cancellation, cancellation_date FROM cancellations LIMIT 3');
while($row = $result->fetch_assoc()) {
    echo "   Order: " . $row['order_id'] . 
         " | Net: $" . number_format($row['net_cancellation'], 2) . 
         " | Date: " . ($row['cancellation_date'] ?: 'NULL') . "\n";
}

// Test 3: Check if there's at least one sell order that can be used for testing
echo "\n3. Available sell orders for testing:\n";
$result = $mysqli->query('SELECT DISTINCT sell_order FROM sell WHERE estado_sell = 1 LIMIT 5');
while($row = $result->fetch_assoc()) {
    echo "   Sell Order: " . $row['sell_order'] . "\n";
}

// Test 4: Test getSellToReturn.php simulation
echo "\n4. Testing getSellToReturn for order '123':\n";
$sell_order = '123';
$query = "SELECT 
            sell.id_sell,
            sell.sell_order,
            sell.date,
            sell.upc_item,
            sell.sku_item
          FROM sell
          WHERE sell.sell_order = ? AND sell.estado_sell = 1 LIMIT 1";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $sell_order);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "   Found sell: " . $row['sell_order'] . " | UPC: " . $row['upc_item'] . " | SKU: " . $row['sku_item'] . "\n";
    
    // Check if cancellation exists
    $cancellationQuery = "SELECT * FROM cancellations WHERE order_id = ?";
    $cancellationStmt = $mysqli->prepare($cancellationQuery);
    $cancellationStmt->bind_param("s", $sell_order);
    $cancellationStmt->execute();
    $cancellationResult = $cancellationStmt->get_result();
    
    if ($cancellation = $cancellationResult->fetch_assoc()) {
        echo "   Existing cancellation: Date = " . ($cancellation['cancellation_date'] ?: 'NULL') . 
             " | Net = $" . number_format($cancellation['net_cancellation'], 2) . "\n";
    } else {
        echo "   No existing cancellation found for this order\n";
    }
} else {
    echo "   No sell found for order: $sell_order\n";
}

echo "\n=== Test completed ===\n";
$mysqli->close();
?>
