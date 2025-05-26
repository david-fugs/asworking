<?php

include("../../conexion.php");
require_once '../../zebra.php';
// Incluir la librería de Zebra_Pagination
$totalQuery = "SELECT COUNT(*) as total FROM sell WHERE estado_sell = 1";
$totalResult = $mysqli->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalRegistros = $totalRow['total'];

$pagination = new Zebra_Pagination();
$pagination->records($totalRegistros);
$pagination->records_per_page(15); // Cambia 10 por los registros por página que desees
$inicio = ($pagination->get_page() - 1) * 1;

// Consulta SQL para obtener los datos
$query = "SELECT 
            id_sell,
            sell_order,
            date,
            upc_item,
            received_shipping,
            payed_shipping,
            store_name,
            sell.id_store,
            sell.id_sucursal,
            code_sucursal,
            comision_item,
            quantity,
            total_item,
            item_price
          FROM sell 
          LEFT JOIN store  ON store.id_store = sell.id_store
          LEFT JOIN sucursal  ON sucursal.id_sucursal = sell.id_sucursal
          WHERE sell.estado_sell = 1
          limit $inicio, 15";

$result = $mysqli->query($query);

$data = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr class='clickable-row' data-id_sell='" . $row['id_sell'] . "'>";
    echo "<td><input type='checkbox' class='select-sell' value='" . $row['id_sell'] . "'></td>";
    echo "<td>" . $row['sell_order'] . "</td>";
    echo "<td>" . $row['date'] . "</td>";
    echo "<td>" . $row['upc_item'] . "</td>";
    echo "<td>" . $row['received_shipping'] . "</td>";
    echo "<td>" . $row['payed_shipping'] . "</td>";
    echo "<td>" . $row['store_name'] . "</td>";
    echo "<td>" . $row['code_sucursal'] . "</td>";
    echo "<td>" . $row['comision_item'] . "</td>";
    echo "<td>" . $row['quantity'] . "</td>";
    echo "<td>" . $row['item_price'] . "</td>";
    echo "<td>" . $row['total_item'] . "</td>";
    echo "<td>
      <button class='btn-action-icon btn-edit'
        data-id='" . $row['id_sell'] . "'
        data-sell_order='" . $row['sell_order'] . "'
        data-date='" . $row['date'] . "'
        data-upc='" . $row['upc_item'] . "'
        data-received_shipping='" . $row['received_shipping'] . "'
        data-payed_shipping='" . $row['payed_shipping'] . "'
        data-store-name='" . $row['store_name'] . "'
        data-store-id='" . $row['id_store'] . "'     
        data-sucursal-code='" . $row['code_sucursal'] . "'
        data-sucursal-id='" . $row['id_sucursal'] . "' 
        data-comision='" . $row['comision_item'] . "'
        data-quantity='" . $row['quantity'] . "'
        data-item_price='" . $row['item_price'] . "'
        data-total='" . $row['total_item'] . "'>
         <i class='fas fa-edit'></i>
      </button>
    </td>";
    echo "<td>
    <button class='btn-action-icon btn-delete' data-id='" . $row['id_sell'] . "'><i class='fas fa-trash-alt'></i></button> </td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='9'>No se encontraron registros.</td></tr>";
}
$pagination->render();


$mysqli->close();
