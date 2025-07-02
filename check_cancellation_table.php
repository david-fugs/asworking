<?php
include 'conexion.php';

echo "Checking cancellations table structure:\n";
$result = $mysqli->query('SHOW TABLES LIKE "cancellations"');
if($result->num_rows > 0) {
    $result = $mysqli->query('DESCRIBE cancellations');
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
    }
    
    echo "\nSample data from cancellations table:\n";
    $result = $mysqli->query('SELECT * FROM cancellations LIMIT 3');
    while($row = $result->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "cancellations table does not exist\n";
}

$mysqli->close();
?>
