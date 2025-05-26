<?php
session_start();
include("../../conexion.php");

if (!isset($_SESSION['id'])) {
  header("Location: ../../index.php");
  exit();
}

$id_store = $_POST["id_store"];

$stmt = $mysqli->prepare("SELECT s.id_sucursal, s.code_sucursal, f.sales_less_than, f.comision, f.cargo_fijo
                          FROM sucursal as s 
                          JOIN fee_config_sucursal as f ON s.id_sucursal = f.id_sucursal
                          WHERE s.id_store = ?");
$stmt->bind_param("i", $id_store);
$stmt->execute();
$result = $stmt->get_result();

// Agrupar configuraciones por sucursal
$sucursales = [];

while ($row = $result->fetch_assoc()) {
  $id = $row['id_sucursal'];
  if (!isset($sucursales[$id])) {
    $sucursales[$id] = [
      'code_sucursal' => $row['code_sucursal'],
      'configs' => []
    ];
  }
  $sucursales[$id]['configs'][] = [
    'sales_less_than' => $row['sales_less_than'],
    'comision' => $row['comision'],
    'cargo_fijo' => $row['cargo_fijo']
  ];
}

echo '<option value="">-- Selecciona una sucursal --</option>';

foreach ($sucursales as $id_sucursal => $data) {
  $configs_json = htmlspecialchars(json_encode($data['configs']), ENT_QUOTES, 'UTF-8');
  echo "<option value='{$id_sucursal}' data-configs='{$configs_json}'>{$data['code_sucursal']}</option>";
}
