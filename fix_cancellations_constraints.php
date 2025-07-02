<?php
include 'conexion.php';

echo "=== Removing unique constraint and updating for UPC independence ===\n\n";

// Check current indexes
echo "1. Current indexes on cancellations table:\n";
$result = $mysqli->query("SHOW INDEX FROM cancellations");
while($row = $result->fetch_assoc()) {
    echo "   " . $row['Key_name'] . " - " . $row['Column_name'] . " (Unique: " . ($row['Non_unique'] ? 'No' : 'Yes') . ")\n";
}

// Drop the unique constraint on order_id if it exists
echo "\n2. Removing unique constraint on order_id...\n";
$drop_constraint = "ALTER TABLE cancellations DROP INDEX unique_order_id";
if($mysqli->query($drop_constraint)) {
    echo "   ✅ Unique constraint removed successfully\n";
} else {
    echo "   ℹ️  Constraint may not exist or already removed: " . $mysqli->error . "\n";
}

// Add new composite unique key for order_id + upc_item
echo "\n3. Adding composite unique constraint for order_id + upc_item...\n";
$add_constraint = "ALTER TABLE cancellations ADD UNIQUE KEY unique_order_upc (order_id, upc_item)";
if($mysqli->query($add_constraint)) {
    echo "   ✅ Composite unique constraint added successfully\n";
} else {
    echo "   ⚠️  Failed to add constraint: " . $mysqli->error . "\n";
}

echo "\n4. Updated indexes:\n";
$result = $mysqli->query("SHOW INDEX FROM cancellations");
while($row = $result->fetch_assoc()) {
    echo "   " . $row['Key_name'] . " - " . $row['Column_name'] . " (Unique: " . ($row['Non_unique'] ? 'No' : 'Yes') . ")\n";
}

echo "\n=== Database structure updated for UPC independence ===\n";
$mysqli->close();
?>
