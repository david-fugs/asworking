<?php
include 'conexion.php';

echo "Adding shipping_date column to shipping table...\n";

// Add the shipping_date column
$sql = "ALTER TABLE shipping ADD COLUMN shipping_date DATE NULL AFTER shipping_adjust";

if ($mysqli->query($sql) === TRUE) {
    echo "Column shipping_date added successfully\n";
} else {
    echo "Error adding column: " . $mysqli->error . "\n";
}

// Check the new structure
echo "\nUpdated shipping table structure:\n";
$result = $mysqli->query('DESCRIBE shipping');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
}

$mysqli->close();
?>
