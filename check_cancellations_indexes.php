<?php
include 'conexion.php';

echo "Checking indexes on cancellations table:\n";
$result = $mysqli->query('SHOW INDEX FROM cancellations');
while($row = $result->fetch_assoc()) {
    print_r($row);
}

$mysqli->close();
?>
