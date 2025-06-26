<?php
include 'conexion.php';

echo "Searching for discount-related tables:\n";
$result = $mysqli->query("SHOW TABLES LIKE '%discount%'");
while($row = $result->fetch_array()) {
    echo $row[0] . "\n";
}

echo "\nSearching for all tables:\n";
$result = $mysqli->query("SHOW TABLES");
while($row = $result->fetch_array()) {
    echo $row[0] . "\n";
}

$mysqli->close();
?>
