<?php
session_start();
include "../../conexion.php";
header('Content-Type: application/json');

// Filtros
$anio = isset($_GET['anio']) ? intval($_GET['anio']) : null;
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : null;
$id_store = isset($_GET['id_store']) ? intval($_GET['id_store']) : null;
$id_sucursal = isset($_GET['id_sucursal']) ? intval($_GET['id_sucursal']) : null;

// Query principal

$sql = "
SELECT 
    COUNT(r.id_return) AS total_returns,
    YEAR(s.date) AS anio,
    MONTH(s.date) AS mes,
    st.store_name
FROM returns r
JOIN sell s ON s.id_sell = r.id_sell
JOIN store st ON st.id_store = s.id_store
WHERE 1
";
$params = [];
$types = '';
if ($anio) {
    $sql .= " AND YEAR(s.date) = ?";
    $params[] = $anio;
    $types .= 'i';
}
if ($mes) {
    $sql .= " AND MONTH(s.date) = ?";
    $params[] = $mes;
    $types .= 'i';
}
if ($id_store) {
    $sql .= " AND st.id_store = ?";
    $params[] = $id_store;
    $types .= 'i';
}
// No filtro por sucursal
$sql .= "
GROUP BY anio, mes, st.store_name
ORDER BY anio DESC, mes DESC, st.store_name
";

$debug = isset($_GET['debug']) ? true : false;
if ($debug) {
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";
    if (!empty($params)) {
        echo "<b>Params:</b> ";
        print_r($params);
        echo "<br><b>Types:</b> $types";
    }
    exit;
}
$stmt = $mysqli->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'anio' => $row['anio'],
        'mes' => $row['mes'],
        'store_name' => $row['store_name'],
        'total_returns' => $row['total_returns']
    ];
}
echo json_encode($data);
