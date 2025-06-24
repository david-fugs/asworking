<?php
include("conexion.php");

// Check if withheld_tax column exists in sell table
$query = "SHOW COLUMNS FROM sell LIKE 'withheld_tax'";
$result = $mysqli->query($query);

if ($result->num_rows > 0) {
    echo "withheld_tax column exists in sell table.\n";
} else {
    echo "withheld_tax column does NOT exist in sell table.\n";
    echo "Need to add the column.\n";
    
    // Add the column
    $addColumnQuery = "ALTER TABLE sell ADD COLUMN withheld_tax DECIMAL(10,2) DEFAULT 0.00 AFTER tax";
    if ($mysqli->query($addColumnQuery)) {
        echo "withheld_tax column added successfully.\n";
    } else {
        echo "Error adding withheld_tax column: " . $mysqli->error . "\n";
    }
}

$mysqli->close();
?>
