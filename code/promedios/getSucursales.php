<?php
include "../../conexion.php";
header('Content-Type: application/json');
$id_store = isset($_GET['id_store']) ? $_GET['id_store'] : '';
$data = [];
if ($id_store) {
  $q = $mysqli->prepare("SELECT id_sucursal, code_sucursal FROM sucursal WHERE id_store = ? ORDER BY code_sucursal ASC");
  $q->bind_param('s', $id_store);
  $q->execute();
  $res = $q->get_result();
  while ($row = $res->fetch_assoc()) {
    $data[] = $row;
  }
}
echo json_encode($data);
