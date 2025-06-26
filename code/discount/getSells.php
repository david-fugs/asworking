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
            discounts.tax_return,
            discounts.discount_date
          FROM sell 
          LEFT JOIN store  ON store.id_store = sell.id_store
          LEFT JOIN sucursal  ON sucursal.id_sucursal = sell.id_sucursal
          LEFT JOIN items ON items.sku_item = sell.sku_item 
                          AND (items.upc_item = sell.upc_item OR items.upc_item IS NULL)
          LEFT JOIN discounts ON BINARY discounts.upc_item = BINARY sell.upc_item AND discounts.id_sell = sell.id_sell
          WHERE sell.estado_sell = 1
          limit $inicio, 15";

$result = $mysqli->query($query);

$data = [];

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . $row['sell_order'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . $row['date'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . $row['upc_item'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . $row['brand_item'] . "</td>";    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . $row['item_item'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . $row['color_item'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . $row['ref_item'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . $row['store_name'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . $row['code_sucursal'] . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . ($row['price_discount'] ? '$' . number_format($row['price_discount'], 2) : '-') . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . ($row['shipping_discount'] ? '$' . number_format($row['shipping_discount'], 2) : '-') . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . ($row['fee_credit'] ? '$' . number_format($row['fee_credit'], 2) : '-') . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . ($row['tax_return'] ? '$' . number_format($row['tax_return'], 2) : '-') . "</td>";
    echo "<td  class='clickable-row' data-sell_order='" . $row['sell_order'] . "' data-upc_item='" . $row['upc_item'] . "' data-id_sell='" . $row['id_sell'] . "'>" . ($row['discount_date'] ? $row['discount_date'] : 'Not set') . "</td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='15'>No se encontraron registros.</td></tr>";
}
$pagination->render();


$mysqli->close();
