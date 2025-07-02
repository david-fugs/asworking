<?php
include 'conexion.php';

echo "Checking cancellations table structure:\n";
$result = $mysqli->query('DESCRIBE cancellations');
while($row = $result->fetch_assoc()) {
    print_r($row);
}

echo "\nChecking sample data:\n";
$result = $mysqli->query('SELECT * FROM cancellations LIMIT 3');
while($row = $result->fetch_assoc()) {
    print_r($row);
}
?>
