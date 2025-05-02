<?php
session_start();
include("../../conexion.php");

header("Content-Type: application/json");

// Obtener datos JSON desde fetch
$data = json_decode(file_get_contents("php://input"), true);


// Validación básica
if (!isset($data['ventas']) || !is_array($data['ventas']) || count($data['ventas']) === 0) {
  http_response_code(400);
  echo json_encode(["success" => false, "message" => "No hay datos de ventas válidos."]);
  exit();
}

// Obtener el siguiente valor de sell_order
$result = $mysqli->query("SELECT MAX(sell_order) AS max_order FROM sell");
$row = $result->fetch_assoc();
$sell_order = $row['max_order'] ? $row['max_order'] + 1 : 1;

// Fecha actual
$sell_date = date("Y-m-d");

// Preparar consulta
$sql = "INSERT INTO sell (
  sell_order, 
  upc_item, 
  quantity, 
  received_shipping, 
  payed_shipping, 
  id_store, 
  id_sucursal, 
  comision_item,
  item_price,  
  total_item, 
  date
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


$stmt = $mysqli->prepare($sql);

if (!$stmt) {
  http_response_code(500);
  echo json_encode(["success" => false, "message" => "Error al preparar statement: " . $mysqli->error]);
  exit();
}

// Insertar cada venta
foreach ($data['ventas'] as $venta) {

  $upc_item = $venta['upc_item'];
  $quantity = (int) $venta['quantity'];
  $received_shipping = isset($venta['received_shipping']) ? (float) $venta['received_shipping'] : 0;
  $payed_shipping = isset($venta['payed_shipping']) ? (int) $venta['payed_shipping'] : 0;
  $id_store = isset($venta['id_store']) ? (int) $venta['id_store'] : 0;
  $id_sucursal = isset($venta['id_sucursal']) ? (int) $venta['id_sucursal'] : 0;
  $comision_item = isset($venta['comision']) ? (float) $venta['comision'] : 0;
  $item_price = isset($venta['item_price']) ? (float) $venta['item_price'] : 0;
  $total_item = isset($venta['total_item']) ? (float) $venta['total_item'] : 0;


  // Ahora, vincula los parámetros y ejecuta la consulta
  $stmt->bind_param(
    "isidiiiddds",
    $sell_order,
    $upc_item,
    $quantity,
    $received_shipping,
    $payed_shipping,
    $id_store,
    $id_sucursal,
    $comision_item,
    $item_price,
    $total_item,
    $sell_date
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
