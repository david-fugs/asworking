<?php
include 'conexion.php';

echo "Current shipping table structure:\n";
$result = $mysqli->query('DESCRIBE shipping');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
}

echo "\nSample data from shipping table:\n";
$result = $mysqli->query('SELECT * FROM shipping LIMIT 3');
while($row = $result->fetch_assoc()) {
    print_r($row);
}

$mysqli->close();
?>
