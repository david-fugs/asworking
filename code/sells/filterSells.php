<?php
include("../../conexion.php");

header('Content-Type: application/json');

// Recuperar los parámetros de los filtros
$upc = $_POST["upc"] ?? '';
$sell_order = $_POST["sell_order"] ?? '';
$date = $_POST["date"] ?? '';

// Construir la consulta SQL dinámica
$query = "SELECT sell.*, store.store_name, sucursal.code_sucursal 
          FROM sell
          LEFT JOIN store ON sell.id_store = store.id_store
          LEFT JOIN sucursal ON sell.id_sucursal = sucursal.id_sucursal
          WHERE 1";

// Si se proporciona un UPC, agregar filtro
if ($upc) {
  $query .= " AND upc_item = ?";
}

// Si se proporciona un número de orden, agregar filtro
if ($sell_order) {
  $query .= " AND sell_order = ?";
}

// Si se proporciona una fecha, agregar filtro
if ($date) {
  $query .= " AND DATE(date) = ?";
}

$stmt = $mysqli->prepare($query);

if (!$stmt) {
  die("Error al preparar la consulta: " . $mysqli->error);
}

// Vincular los parámetros según los filtros proporcionados
$types = '';
$params = [];

if ($upc) {
  $types .= 's';  // 's' es para string
  $params[] = $upc;
}

if ($sell_order) {
  $types .= 's';  // 's' es para string
  $params[] = $sell_order;
}

if ($date) {
  $types .= 's';  // 's' es para string (se espera un formato de fecha 'YYYY-MM-DD')
  $params[] = $date;
}

if ($types) {
  $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$ventas = [];
while ($row = $result->fetch_assoc()) {
  $ventas[] = $row;
}

echo json_encode([
  "success" => true,
  "ventas" => $ventas
]);

?>