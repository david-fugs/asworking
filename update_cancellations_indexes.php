<?php
include 'conexion.php';

echo "Updating cancellations table indexes...\n";

// Eliminar el índice único existente
echo "Dropping old unique index...\n";
$drop_index = "ALTER TABLE cancellations DROP INDEX unique_order_upc";
if ($mysqli->query($drop_index)) {
    echo "Old unique index dropped successfully.\n";
} else {
    echo "Error dropping index: " . $mysqli->error . "\n";
}

// Crear nuevo índice único que incluya id_sell
echo "Creating new unique index with id_sell...\n";
$create_index = "ALTER TABLE cancellations ADD UNIQUE INDEX unique_order_sell_upc (order_id, id_sell, upc_item)";
if ($mysqli->query($create_index)) {
    echo "New unique index created successfully.\n";
} else {
    echo "Error creating new index: " . $mysqli->error . "\n";
}

// Verificar los índices actuales
echo "\nCurrent indexes:\n";
$result = $mysqli->query('SHOW INDEX FROM cancellations');
while($row = $result->fetch_assoc()) {
    if ($row['Key_name'] != 'PRIMARY') {
        echo "Index: {$row['Key_name']}, Column: {$row['Column_name']}, Unique: " . ($row['Non_unique'] ? 'No' : 'Yes') . "\n";
    }
}

$mysqli->close();
?>
