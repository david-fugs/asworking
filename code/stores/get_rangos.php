<?php
require_once '../../conexion.php';

header('Content-Type: application/json');

if (isset($_GET['id_sucursal'])) {
    $id = intval($_GET['id_sucursal']);
    $query = "SELECT id, sales_less_than, comision, cargo_fijo FROM fee_config_sucursal WHERE id_sucursal = $id ORDER BY sales_less_than ASC";
    $result = $mysqli->query($query);

    $rangos = [];

    while ($row = $result->fetch_assoc()) {
        $rangos[] = $row;
    }

    echo json_encode($rangos);
} else {
    echo json_encode([]);
}
?>
