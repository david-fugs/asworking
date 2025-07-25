<?php
include "../../conexion.php";
header('Content-Type: application/json');

$anio = isset($_GET['anio']) ? $_GET['anio'] : '';
$mes = isset($_GET['mes']) ? $_GET['mes'] : '';
$id_store = isset($_GET['id_store']) ? $_GET['id_store'] : '';
$id_sucursal = isset($_GET['id_sucursal']) ? $_GET['id_sucursal'] : '';

$where = [];
$params = [];

if ($anio) {
    $where[] = "YEAR(sell.date) = ?";
    $params[] = $anio;
}
if ($mes) {
    $where[] = "MONTH(sell.date) = ?";
    $params[] = $mes;
}
if ($id_store) {
    $where[] = "store.id_store = ?";
    $params[] = $id_store;
}
if ($id_sucursal) {
    $where[] = "sucursal.id_sucursal = ?";
    $params[] = $id_sucursal;
}

$where_sql = $where ? ("WHERE " . implode(" AND ", $where)) : '';

$sql = "SELECT store.store_name, sucursal.code_sucursal, MONTH(sell.date) as mes, YEAR(sell.date) as anio, SUM(sell.total_item) as total_items
        FROM sell
        INNER JOIN sucursal ON sell.id_sucursal = sucursal.id_sucursal
        INNER JOIN store ON sucursal.id_store = store.id_store
        $where_sql
        GROUP BY store.store_name, sucursal.code_sucursal, anio, mes
        ORDER BY anio DESC, mes DESC, store.store_name ASC";

$stmt = $mysqli->prepare($sql);
$types = str_repeat('s', count($params));
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
