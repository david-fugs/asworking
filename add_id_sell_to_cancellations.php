<?php
include 'conexion.php';

echo "Adding id_sell column to cancellations table...\n";

// Primero verificar si la columna ya existe
$check = $mysqli->query("SHOW COLUMNS FROM cancellations LIKE 'id_sell'");
if ($check->num_rows > 0) {
    echo "Column id_sell already exists in cancellations table.\n";
} else {
    // Añadir la columna id_sell
    $sql = "ALTER TABLE cancellations ADD COLUMN id_sell INT(11) AFTER order_id";
    if ($mysqli->query($sql)) {
        echo "Column id_sell added successfully.\n";
    } else {
        echo "Error adding column: " . $mysqli->error . "\n";
    }
}

// Verificar la estructura actualizada
echo "\nUpdated table structure:\n";
$result = $mysqli->query('DESCRIBE cancellations');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

$mysqli->close();
?>
