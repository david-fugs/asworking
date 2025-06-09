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
  SELECT items.upc_item, items.item_item, items.brand_item, items.cost_item, 
         inventory.quantity_inventory, items.sku_item, inventory.id_inventory,
         items.id_item, items.ref_item
  FROM items
  LEFT JOIN inventory ON items.upc_item = inventory.upc_inventory
  WHERE items.upc_item = ?
  ORDER BY items.sku_item, items.id_item, inventory.id_inventory
");
if (!$stmt) {
  die("Error al preparar la consulta: " . $mysqli->error);
}

$stmt->bind_param("s", $upc);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$stock_by_sku = []; // Para acumular stock por SKU

while ($row = $result->fetch_assoc()) {
  $sku = $row["sku_item"];
  
  // Acumular stock por SKU
  if (!isset($stock_by_sku[$sku])) {
    $stock_by_sku[$sku] = 0;
  }
  if (isset($row["quantity_inventory"]) && $row["id_inventory"] !== null) {
    $stock_by_sku[$sku] += (int)$row["quantity_inventory"];
  }
  
  // Crear item solo una vez por SKU
  $item_key = $row["id_item"] . "_" . $sku;
  if (!isset($items[$item_key])) {
    $items[$item_key] = [
      "id" => $row["id_inventory"], // Mantener un ID de referencia
      "item_id" => $row["id_item"],
      "item" => $row["item_item"],
      "cost" => $row["cost_item"],
      "brand" => $row["brand_item"],
      "quantity" => 0, // Se asignará después
      "upc" => $row["upc_item"],
      "sku" => $row["sku_item"],
      "ref_item" => $row["ref_item"] ?? null 
    ];
  }
}

// Asignar el stock acumulado a cada item
$final_items = [];
foreach ($items as $item) {
  $item["quantity"] = $stock_by_sku[$item["sku"]] ?? 0;
  $final_items[] = $item;
}

$items = $final_items;

if (count($items) > 0) {
  echo json_encode(["success" => true, "items" => $items]);
} else {
  echo json_encode(["success" => false]);
}
