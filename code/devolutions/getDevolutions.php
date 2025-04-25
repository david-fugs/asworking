<?php

include("../../conexion.php");

// Consulta SQL para obtener los datos
$query = "SELECT DISTINCT d.*, s.store_name, su.code_sucursal
FROM devolutions AS d
JOIN store AS s ON d.id_store = s.id_store
JOIN sucursal AS su ON d.id_sucursal = su.id_sucursal
JOIN items AS i ON d.upc_item = i.upc_item;

          ";

$result = $mysqli->query($query);

$data = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td><input type='checkbox' class='select-sell' value='" . $row['id_devolution'] . "'></td>";
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
    echo "<td>" . date('Y-m-d', strtotime($row['devolution_date'])) . "</td>";

    echo '<td data-label="Eliminar">
            <a href="?delete=' . $row['id_devolution'] . '" onclick="return confirm(\'¿Are you sure to Delete this item?\');">
                 <i class="fa-sharp-duotone fa-solid fa-trash" style="color:red; height:20px;"></i>
            </a>
          </td>';
  }
} else {
  echo "<tr><td colspan='9'>No se encontraron registros.</td></tr>";
}


$mysqli->close();
