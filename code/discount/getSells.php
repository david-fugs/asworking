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
            sell.id_sell,
            sell.sell_order,
            sell.date,
            sell.upc_item,
            store_name,
            sell.id_store,
            sell.id_sucursal,
            code_sucursal,
            items.brand_item,
            items.item_item,
            items.color_item,
            items.ref_item,
            discounts.id_discount,
            discounts.price_discount,
            discounts.shipping_discount,
            discounts.fee_credit,
            discounts.tax_return
          FROM sell 
          LEFT JOIN store  ON store.id_store = sell.id_store
          LEFT JOIN sucursal  ON sucursal.id_sucursal = sell.id_sucursal
          LEFT JOIN items ON items.sku_item = sell.sku_item 
                          AND (items.upc_item = sell.upc_item OR items.upc_item IS NULL)
          LEFT JOIN discounts ON BINARY discounts.sell_order = BINARY sell.sell_order
          WHERE sell.estado_sell = 1
          limit $inicio, 15";

$result = $mysqli->query($query);

$data = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . $row['sell_order'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . $row['date'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . $row['upc_item'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . $row['brand_item'] . "</td>";    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . $row['item_item'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . $row['color_item'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . $row['ref_item'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . $row['store_name'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . $row['code_sucursal'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . ($row['price_discount'] ? '$' . number_format($row['price_discount'], 2) : '-') . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . ($row['shipping_discount'] ? '$' . number_format($row['shipping_discount'], 2) : '-') . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . ($row['fee_credit'] ? '$' . number_format($row['fee_credit'], 2) : '-') . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "'>" . ($row['tax_return'] ? '$' . number_format($row['tax_return'], 2) : '-') . "</td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='14'>No se encontraron registros.</td></tr>";
}
$pagination->render();


$mysqli->close();
