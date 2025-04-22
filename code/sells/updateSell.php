<?php
// Conectar a la base de datos
include("../../conexion.php");

// Obtener los datos enviados
$id_sell = $_POST['id_sell'];
$sell_order = $_POST['sell_order'];
$date = $_POST['date'];
$upc = $_POST['upc'];
$comision = $_POST['comision'];
$received_shipping = $_POST['received_shipping'];
$payed_shipping = $_POST['payed_shipping'];
$storeID = $_POST['storeID'];
$sucursalID = $_POST['sucursalID'];
$quantity = $_POST['quantity'];
$total_item = $_POST['total_item'];

// Realizar la actualización en la base de datos
$query = "UPDATE sell SET
            sell_order = '$sell_order',
            date = '$date',
            upc_item = '$upc',
            comision_item = '$comision',
            received_shipping = '$received_shipping',
            payed_shipping = '$payed_shipping',
            id_store = '$storeID',
            id_sucursal = '$sucursalID',
            quantity = '$quantity',
            total_item = '$total_item'
          WHERE id_sell = '$id_sell'";

if ($mysqli->query($query) === TRUE) {
    // Responder con éxito
    echo json_encode(['success' => true]);
} else {
    // Responder con error
    echo json_encode(['success' => false, 'message' => $mysqli->error]);
}

// Cerrar la conexión
$mysqli->close();
?>
