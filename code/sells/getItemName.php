<?php
session_start();
include("../../conexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
  echo json_encode(["success" => false, "error" => "Sesión no válida"]);
  exit();
}

$upc = $_POST["upc"] ?? '';

$stmt = $mysqli->prepare("SELECT * FROM items
  LEFT JOIN inventory ON items.upc_item = inventory.upc_inventory
 WHERE upc_item = ? LIMIT 1");
if (!$stmt) {
  die("Error al preparar la consulta: " . $mysqli->error);
}

$stmt->bind_param("s", $upc);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
  echo json_encode([
    "success" => true,
    "item" => $row["item_item"],
    "cost" => number_format($row["cost_item"], 2),
    "brand" => $row["brand_item"],
    "quantity" => $row["quantity_inventory"],
  ]);
} else {
  echo json_encode(["success" => false]);
}
