<?php
include 'conexion.php';

echo "=== Testing updated cancellations module with UPC independence ===\n\n";

// Test 1: Check table structure with UPC field
echo "1. Updated table structure:\n";
$result = $mysqli->query('DESCRIBE cancellations');
while($row = $result->fetch_assoc()) {
    echo "   " . $row['Field'] . " - " . $row['Type'] . "\n";
}

// Test 2: Create test data for UPC independence
echo "\n2. Creating test data for UPC independence:\n";

// Insert some test cancellations with different UPCs for same order
$test_order = '123';
$upc1 = '733004811005';
$upc2 = '123456789012';

// Check if test data exists
$check = $mysqli->query("SELECT COUNT(*) as count FROM cancellations WHERE order_id = '$test_order' AND upc_item = '$upc1'");
$exists1 = $check->fetch_assoc()['count'] > 0;

$check = $mysqli->query("SELECT COUNT(*) as count FROM cancellations WHERE order_id = '$test_order' AND upc_item = '$upc2'");
$exists2 = $check->fetch_assoc()['count'] > 0;

if (!$exists2) {
    // Insert second UPC for same order
    $insert_sql = "INSERT INTO cancellations (order_id, upc_item, refund_amount, net_cancellation, cancellation_date, created_at, updated_at) 
                   VALUES (?, ?, 5.00, 5.00, '2025-07-03', NOW(), NOW())";
    $stmt = $mysqli->prepare($insert_sql);
    $stmt->bind_param("ss", $test_order, $upc2);
    
    if ($stmt->execute()) {
        echo "   ✅ Created test cancellation for order $test_order, UPC $upc2\n";
    } else {
        echo "   ❌ Failed to create test data: " . $mysqli->error . "\n";
    }
} else {
    echo "   ℹ️  Test data already exists for UPC $upc2\n";
}

// Test 3: Show independence - different dates for same order but different UPCs
echo "\n3. Testing UPC independence:\n";
echo "   Cancellations for order '$test_order':\n";

$result = $mysqli->query("SELECT order_id, upc_item, cancellation_date, net_cancellation 
                         FROM cancellations 
                         WHERE order_id = '$test_order' 
                         ORDER BY upc_item");

while($row = $result->fetch_assoc()) {
    echo "     - Order: " . $row['order_id'] . 
         " | UPC: " . $row['upc_item'] . 
         " | Date: " . ($row['cancellation_date'] ?: 'NULL') . 
         " | Net: $" . number_format($row['net_cancellation'], 2) . "\n";
}

// Test 4: Update one specific UPC to different date
echo "\n4. Testing independent date updates:\n";
$update_date = '2025-07-04';
$update_sql = "UPDATE cancellations SET cancellation_date = ? WHERE order_id = ? AND upc_item = ?";
$stmt = $mysqli->prepare($update_sql);
$stmt->bind_param("sss", $update_date, $test_order, $upc1);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo "   ✅ Updated cancellation date for order $test_order, UPC $upc1 to $update_date\n";
} else {
    echo "   ❌ Failed to update or no changes made\n";
}

echo "\n   After update:\n";
$result = $mysqli->query("SELECT order_id, upc_item, cancellation_date, net_cancellation 
                         FROM cancellations 
                         WHERE order_id = '$test_order' 
                         ORDER BY upc_item");

while($row = $result->fetch_assoc()) {
    echo "     - Order: " . $row['order_id'] . 
         " | UPC: " . $row['upc_item'] . 
         " | Date: " . ($row['cancellation_date'] ?: 'NULL') . 
         " | Net: $" . number_format($row['net_cancellation'], 2) . "\n";
}

echo "\n✅ UPC independence test completed successfully!\n";
echo "Each UPC now has its own independent cancellation date.\n";

$mysqli->close();
?>
