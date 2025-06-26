<?php
include 'conexion.php';

echo "Adding shipping_return_date column to shipping_return table...\n";

// Add the shipping_return_date column
$sql = "ALTER TABLE shipping_return ADD COLUMN shipping_return_date DATE NULL AFTER billing_return";

if ($mysqli->query($sql) === TRUE) {
    echo "Column shipping_return_date added successfully\n";
} else {
    echo "Error adding column: " . $mysqli->error . "\n";
}

// Check the new structure
echo "\nUpdated shipping_return table structure:\n";
$result = $mysqli->query('DESCRIBE shipping_return');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
}

$mysqli->close();
?>
