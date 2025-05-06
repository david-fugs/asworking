<?php
include("../../conexion.php");

// Inicializamos las condiciones de búsqueda
$where = [];

// Verifica si se ha enviado el valor de sell_order
if (isset($_GET['sell_order']) && $_GET['sell_order'] !== '') {
    $where[] = "d.sell_order = '" . $mysqli->real_escape_string($_GET['sell_order']) . "'";
}

// Verifica si se ha enviado el valor de date_devolution
if (isset($_GET['date_devolution']) && $_GET['date_devolution'] !== '') {
    $where[] = "d.date = '" . $mysqli->real_escape_string($_GET['date_devolution']) . "'";
}

// Verifica si se ha enviado el valor de upc_item
if (isset($_GET['upc_item']) && $_GET['upc_item'] !== '') {
    $where[] = "d.upc_item = '" . $mysqli->real_escape_string($_GET['upc_item']) . "'";
}

// Construye la consulta base
$query = "
    SELECT DISTINCT d.*, s.store_name, su.code_sucursal
    FROM devolutions AS d
    JOIN store AS s ON d.id_store = s.id_store
    JOIN sucursal AS su ON d.id_sucursal = su.id_sucursal
    JOIN items AS i ON d.upc_item = i.upc_item
";

// Si existen filtros, agregamos la cláusula WHERE
if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

// Ejecutamos la consulta
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
