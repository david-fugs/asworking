<?php
include 'conexion.php';

echo "Checking discounts table structure:\n";
$result = $mysqli->query('SHOW TABLES LIKE "discounts"');
if($result->num_rows > 0) {
    $result = $mysqli->query('DESCRIBE discounts');
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
    }
    
    echo "\nSample data from discounts table:\n";
    $result = $mysqli->query('SELECT * FROM discounts LIMIT 3');
    while($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "discounts table does not exist\n";
}

$mysqli->close();
?>
