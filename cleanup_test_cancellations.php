<?php
include 'conexion.php';

echo "Cleaning up test data...\n";

$test_order = 'TEST_DUP_UPC_123';

// Eliminar datos de prueba
$deleted_cancellations = $mysqli->query("DELETE FROM cancellations WHERE order_id = '$test_order'");
$deleted_sells = $mysqli->query("DELETE FROM sell WHERE sell_order = '$test_order'");

if ($deleted_cancellations && $deleted_sells) {
    echo "Test data cleaned up successfully.";
} else {
    echo "Error cleaning up: " . $mysqli->error;
}

$mysqli->close();
?>
