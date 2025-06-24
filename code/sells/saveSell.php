<?php
session_start();
include("../../conexion.php");

header("Content-Type: application/json");

// Obtener datos JSON desde fetch
$data = json_decode(file_get_contents("php://input"), true);
$sell_order = $data['sell_order'];

// Validación básica
if (!isset($data['ventas']) || !is_array($data['ventas']) || count($data['ventas']) === 0) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "No hay datos de ventas válidos."]);
  exit();
}

// Fecha actual
$sell_date = date("Y-m-d");

// Preparar consulta
$sql = "INSERT INTO sell (
  sell_order, 
  upc_item,
  sku_item, 
  quantity, 
  received_shipping, 
  tax,
  withheld_tax,
  id_store, 
  id_sucursal, 
  comision_item,
  cargo_fijo,
  item_price,  
  total_item, 
  incentives,
  international_fee,
  ad_fee,
  other_fee,
  date,
  item_profit,
  markup,
  profit_margin
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ? ,? ,?, ?)";


$stmt = $mysqli->prepare($sql);

if (!$stmt) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Error al preparar statement: " . $mysqli->error]);
  exit();
}

// Insertar cada venta
foreach ($data['ventas'] as $venta) {
  $upc_item = $venta['upc_item'];
  $sku_item = isset($venta['sku']) ? $venta['sku'] : '';
  $quantity = (int) $venta['quantity'];  $received_shipping = isset($venta['received_shipping']) ? (float) $venta['received_shipping'] : 0;
  $tax = isset($venta['tax']) ? (float) $venta['tax'] : 0;
  $withheld_tax = isset($venta['withheld_tax']) ? (float) $venta['withheld_tax'] : 0;
  $id_store = isset($venta['id_store']) ? (int) $venta['id_store'] : 0;
  $id_sucursal = isset($venta['id_sucursal']) ? (int) $venta['id_sucursal'] : 0;
  $comision_item = isset($venta['comision']) ? (float) $venta['comision'] : 0;
  $cargo_fijo = isset($venta['cargo_fijo']) ? (float) $venta['cargo_fijo'] : 0;
  $incentives_value = isset($venta['incentives_value']) ? (float) $venta['incentives_value'] : 0;
  $international_fee_value = isset($venta['international_fee_value']) ? (float) $venta['international_fee_value'] : 0;
  $ad_fee_value = isset($venta['ad_fee_value']) ? (float) $venta['ad_fee_value'] : 0;
  $other_fee_value = isset($venta['other_fee_value']) ? (float) $venta['other_fee_value'] : 0;
  $item_price = isset($venta['item_price']) ? (float) $venta['item_price'] : 0;
  $total_item = isset($venta['total_item']) ? (float) $venta['total_item'] : 0;
  $item_profit = isset($venta['item_profit']) ? (float) $venta['item_profit'] : 0;
  $markup = isset($venta['markup']) ? (float) $venta['markup'] : 0;
  $profit_margin = isset($venta['profit_margin']) ? (float) $venta['profit_margin'] : 0;  // Ahora, vincula los parámetros y ejecuta la consulta
  $stmt->bind_param(
    "sssiddiiiddddddddsddd",
    $sell_order,
    $upc_item,
    $sku_item,
    $quantity,
    $received_shipping,
    $tax,
    $withheld_tax,
    $id_store,
    $id_sucursal,
    $comision_item,
    $cargo_fijo,
    $item_price,
    $total_item,
    $incentives_value,
    $international_fee_value,
    $ad_fee_value,
    $other_fee_value,
    $sell_date,
    $item_profit,
    $markup,
    $profit_margin
  );


  if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al guardar venta: " . $stmt->error]);
    exit();
  }

  //  UPDATE de inventario restando la cantidad vendida
  $updateQuery = "UPDATE inventory SET quantity_inventory = quantity_inventory - $quantity WHERE upc_inventory = '$upc_item'";
  if (!$mysqli->query($updateQuery)) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Error al actualizar inventario: " . $mysqli->error]);
    exit();
  }
}

// Cierre
$stmt->close();
$mysqli->close();

// Respuesta final
echo json_encode(["success" => true, "message" => "Ventas guardadas exitosamente.", "sell_order" => $sell_order]);
