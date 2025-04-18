<?php

include("../../conexion.php");

// Consulta SQL para obtener los datos
$query = "SELECT 
            id_sell,
            sell_order,
            date,
            upc_item,
            received_shipping,
            payed_shipping,
            store_name,
            code_sucursal,
            comision_item,
            quantity,
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
    echo "<td>" . $row['quantity'] . "</td>";
    echo "<td>" . $row['total_item'] . "</td>";
    echo "<td>
    <button class='edit-btn'
      data-id='" . $row['id_sell'] . "'
      data-sell_order='" . $row['sell_order'] . "'
      data-date='" . $row['date'] . "'
      data-upc='" . $row['upc_item'] . "'
      data-received_shipping='" . $row['received_shipping'] . "'
      data-payed_shipping='" . $row['payed_shipping'] . "'
      data-store='" . $row['store_name'] . "'
      data-sucursal='" . $row['code_sucursal'] . "'
      data-comision='" . $row['comision_item'] . "'
      data-quantity='" . $row['quantity'] . "'
      data-total='" . $row['total_item'] . "'>
      ✏️
    </button>
    <button class='delete-btn' data-id='" . $row['id_sell'] . "'>🗑️</button>
  </td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='9'>No se encontraron registros.</td></tr>";
}


$mysqli->close();
