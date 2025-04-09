<?php
session_start();
include("../../conexion.php");

if (!isset($_SESSION['id'])) {
  header("Location: ../../index.php");
  exit();
}

$id_store = $_POST["id_store"];

$stmt = $mysqli->prepare("SELECT id_sucursal, code_sucursal, comision_sucursal FROM sucursal WHERE id_store = ?");
$stmt->bind_param("i", $id_store);
$stmt->execute();
$result = $stmt->get_result();

echo '<option value="">-- Selecciona una sucursal --</option>';
while ($row = $result->fetch_assoc()) {
  echo "<option value='{$row['id_sucursal']}' data-comision='{$row['comision_sucursal']}'>{$row['code_sucursal']}</option>";

}
