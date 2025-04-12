<?php
session_start();
include("../../conexion.php");

// Consulta SQL para obtener los datos
$query = "SELECT 
            sell_order,
            date,
            upc_item,
            received_shipping,
            payed_shipping,
            store_name,
            code_sucursal,
            comision_item,
            total_item
          FROM sell 
          LEFT JOIN store  ON store.id_store = sell.id_store
          LEFT JOIN sucursal  ON sucursal.id_sucursal = sell.id_sucursal";

$result = $mysqli->query($query);

$data = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['sell_order'] . "</td>";
    echo "<td>" . $row['date'] . "</td>";
    echo "<td>" . $row['upc_item'] . "</td>";
    echo "<td>" . $row['received_shipping'] . "</td>";
    echo "<td>" . $row['payed_shipping'] . "</td>";
    echo "<td>" . $row['store_name'] . "</td>";
    echo "<td>" . $row['code_sucursal'] . "</td>";
    echo "<td>" . $row['comision_item'] . "</td>";
    echo "<td>" . $row['total_item'] . "</td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='9'>No se encontraron registros.</td></tr>";
}


$mysqli->close();
