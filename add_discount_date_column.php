<?php
include 'conexion.php';

echo "Adding discount_date column to discounts table...\n";

// Add the discount_date column
$sql = "ALTER TABLE discounts ADD COLUMN discount_date DATE NULL AFTER total_discount";

if ($mysqli->query($sql) === TRUE) {
    echo "Column discount_date added successfully\n";
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
