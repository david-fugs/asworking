<?php
include("../../conexion.php");

// Inicializamos las condiciones de búsqueda
$where = [];

// Verifica si se ha enviado el valor de sell_order
if (isset($_GET['sell_order']) && $_GET['sell_order'] !== '') {
    $where[] = "r.sell_order = '" . $mysqli->real_escape_string($_GET['sell_order']) . "'";
}

// Verifica si se ha enviado el valor de date_devolution
if (isset($_GET['date_devolution']) && $_GET['date_devolution'] !== '') {
    $where[] = "DATE(r.date) = '" . $mysqli->real_escape_string($_GET['date_devolution']) . "'";
}

// Verifica si se ha enviado el valor de upc_item
if (isset($_GET['upc_item']) && $_GET['upc_item'] !== '') {
    $where[] = "r.upc_item = '" . $mysqli->real_escape_string($_GET['upc_item']) . "'";
}

// Construye la consulta base
// $query = "
//     SELECT DISTINCT d.*, s.store_name, su.code_sucursal
//     FROM devolutions AS d
//     JOIN store AS s ON d.id_store = s.id_store
//     JOIN sucursal AS su ON d.id_sucursal = su.id_sucursal
//     JOIN items AS i ON d.upc_item = i.upc_item
// ";
$query = " SELECT r.* , st.store_name,su.code_sucursal FROM returns as r
    LEFT JOIN sell as s ON r.sell_order = s.sell_order
    LEFT JOIN store AS st ON s.id_store = st.id_store
    LEFT JOIN sucursal AS su ON s.id_sucursal = su.id_sucursal
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
    echo "<td>" .date('Y-m-d',strtotime($row['creation_date']))  . "</td>";
    echo "<td>" . $row['upc_item'] . "</td>";
    echo "<td>" . $row['sku_item'] . "</td>";
    echo "<td>" . $row['quantity'] . "</td>";
    echo "<td>" . $row['product_charge'] . "</td>";
    echo "<td>" . $row['shipping_paid'] . "</td>";
    echo "<td>" . $row['tax_return'] . "</td>";
    echo "<td>" . $row['selling_fee_refund'] . "</td>";
    echo "<td>" . $row['refund_administration_fee'] . "</td>";
    echo "<td>" . $row['other_refund_fee'] . "</td>";
    echo "<td>" . $row['return_cost'] . "</td>";
    echo "<td>" . $row['buyer_comments'] . "</td>";
    echo "<td>" . $row['code_sucursal'] . "</td>";

    echo '<td data-label="Eliminar">
            <a href="?delete=' . $row['id_return'] . '" onclick="return confirm(\'¿Are you sure to Delete this item?\');">
                 <i class="fa-sharp-duotone fa-solid fa-trash" style="color:red; height:20px;"></i>
            </a>
          </td>';
  }
} else {
  echo "<tr><td colspan='9'>There is no Returns Yei!.</td></tr>";
}


$mysqli->close();
