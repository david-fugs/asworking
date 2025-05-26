<?php
session_start();
include("../../conexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
  echo json_encode(["success" => false, "error" => "Sesión no válida"]);
  exit();
}

$upc = $_POST["upc"] ?? '';
$stmt = $mysqli->prepare("
  SELECT DISTINCT items.upc_item, items.item_item, items.brand_item, items.cost_item, inventory.quantity_inventory, items.sku_item 
  FROM items
  LEFT JOIN inventory ON items.upc_item = inventory.upc_inventory
  WHERE upc_item = ?
");
if (!$stmt) {
  die("Error al preparar la consulta: " . $mysqli->error);
}

$stmt->bind_param("s", $upc);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
  $items[] = [
    "item" => $row["item_item"],
    "cost" => number_format($row["cost_item"], 2),
    "brand" => $row["brand_item"],
    "quantity" => isset($row["quantity_inventory"]) ? (int)$row["quantity_inventory"] : 0,
    "upc" => $row["upc_item"],
    "sku" => $row["sku_item"]
  ];
}

if (count($items) > 0) {
  echo json_encode(["success" => true, "items" => $items]);
} else {
  echo json_encode(["success" => false]);
}
