<?php
include('../../conexion.php');

header('Content-Type: text/plain');

$result = $mysqli->query("SHOW CREATE TABLE items");
if ($result && $row = $result->fetch_assoc()) {
    echo $row['Create Table'];
} else {
    echo "Could not fetch CREATE TABLE for items. Error: " . $mysqli->error;
}

echo "\n\nColumns:\n";
$cols = $mysqli->query("SHOW COLUMNS FROM items");
if ($cols) {
    while ($c = $cols->fetch_assoc()) {
        echo $c['Field'] . " => " . $c['Type'] . "\tNULL:" . $c['Null'] . "\tDefault:" . $c['Default'] . "\n";
    }
} else {
    echo "Error fetching columns: " . $mysqli->error;
}
?>
