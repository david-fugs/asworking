<?php
include 'conexion.php';

echo "Adding upc_item column to discounts table...\n";

// Add the upc_item column to relate discounts to specific UPCs
$sql = "ALTER TABLE discounts ADD COLUMN upc_item VARCHAR(255) NULL AFTER sell_order";

if ($mysqli->query($sql) === TRUE) {
    echo "Column upc_item added successfully\n";
} else {
    echo "Error adding column: " . $mysqli->error . "\n";
}

// Check the new structure
echo "\nUpdated discounts table structure:\n";
$result = $mysqli->query('DESCRIBE discounts');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
}

$mysqli->close();
?>
