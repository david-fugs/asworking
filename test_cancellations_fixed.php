<?php
echo "=== Testing Fixed Cancellations Module ===\n\n";

echo "🧪 Test 1: Checking if UPC 733004811005 exists in sell table:\n";
include 'conexion.php';

$upc = '733004811005';
$result = $mysqli->query("SELECT sell_order, upc_item, sku_item, estado_sell FROM sell WHERE upc_item = '$upc' LIMIT 3");

if ($result->num_rows > 0) {
    echo "   ✅ Found sell records for UPC $upc:\n";
    while($row = $result->fetch_assoc()) {
        echo "     - Order: {$row['sell_order']} | UPC: {$row['upc_item']} | SKU: {$row['sku_item']} | Status: {$row['estado_sell']}\n";
    }
} else {
    echo "   ❌ No sell records found for UPC $upc\n";
}

echo "\n🧪 Test 2: Checking cancellations for UPC $upc:\n";
$result = $mysqli->query("SELECT order_id, upc_item, cancellation_date, net_cancellation FROM cancellations WHERE upc_item = '$upc'");

if ($result->num_rows > 0) {
    echo "   ✅ Found cancellation records for UPC $upc:\n";
    while($row = $result->fetch_assoc()) {
        echo "     - Order: {$row['order_id']} | UPC: {$row['upc_item']} | Date: " . ($row['cancellation_date'] ?: 'NULL') . " | Net: $" . number_format($row['net_cancellation'], 2) . "\n";
    }
} else {
    echo "   ℹ️  No cancellation records found for UPC $upc (this is normal if no cancellations have been created yet)\n";
}

echo "\n🧪 Test 3: Testing searchCancellations.php with UPC $upc:\n";

// Simulate POST request to searchCancellations.php
$_POST['upc_item'] = $upc;
$_POST['sell_order'] = '';

// Change to cancellations directory
$original_dir = getcwd();
chdir('code/cancellations');

// Capture output
ob_start();
include 'searchCancellations.php';
$output = ob_get_clean();

// Return to original directory
chdir($original_dir);

echo "   Search results for UPC $upc:\n";
if (strpos($output, 'No Results Found') !== false) {
    echo "   ℹ️  No results found (expected if no active sell records)\n";
} else if (strpos($output, 'table') !== false) {
    echo "   ✅ Search returned table results\n";
    // Count rows in the output
    $row_count = substr_count($output, '<tr>') - 1; // Subtract header row
    echo "   📊 Found $row_count record(s)\n";
} else {
    echo "   ⚠️  Unexpected output format\n";
}

echo "\n✅ Tests completed!\n";
echo "The cancellations module should now work correctly with UPC searches.\n";
echo "Try searching for UPC $upc in the web interface.\n";

$mysqli->close();
?>
