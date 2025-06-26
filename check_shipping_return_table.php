<?php
include 'conexion.php';

echo "Checking shipping_return table structure:\n";
$result = $mysqli->query('SHOW TABLES LIKE "shipping_return"');
if($result->num_rows > 0) {
    $result = $mysqli->query('DESCRIBE shipping_return');
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
    }
    
    echo "\nSample data from shipping_return table:\n";
    $result = $mysqli->query('SELECT * FROM shipping_return LIMIT 3');
    while($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "shipping_return table does not exist\n";
}

$mysqli->close();
?>
