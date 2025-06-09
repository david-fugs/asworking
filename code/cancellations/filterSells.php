<?php
include("../../conexion.php");

// Get filter parameters
$upc_item = isset($_GET['upc_item']) ? trim($_GET['upc_item']) : '';
$sell_order = isset($_GET['sell_order']) ? trim($_GET['sell_order']) : '';
$sellDate = isset($_GET['sellDate']) ? trim($_GET['sellDate']) : '';

// Build WHERE clause based on filters
$whereConditions = ["sell.estado_sell = 1"];
$params = [];
$types = "";

if (!empty($upc_item)) {
    $whereConditions[] = "sell.upc_item LIKE ?";
    $params[] = "%$upc_item%";
    $types .= "s";
}

if (!empty($sell_order)) {
    $whereConditions[] = "sell.sell_order LIKE ?";
    $params[] = "%$sell_order%";
    $types .= "s";
}

if (!empty($sellDate)) {
    $whereConditions[] = "DATE(sell.date) = ?";
    $params[] = $sellDate;
    $types .= "s";
}

$whereClause = implode(" AND ", $whereConditions);

// Count total records for pagination
$countQuery = "SELECT COUNT(*) as total FROM sell WHERE $whereClause";
if (!empty($params)) {
    $countStmt = $mysqli->prepare($countQuery);
    $countStmt->bind_param($types, ...$params);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
} else {
    $countResult = $mysqli->query($countQuery);
}

$totalRow = $countResult->fetch_assoc();
$totalRegistros = $totalRow['total'];

// Pagination setup
require_once '../../zebra.php';
$pagination = new Zebra_Pagination();
$pagination->records($totalRegistros);
$pagination->records_per_page(15);
$inicio = ($pagination->get_page() - 1) * 15;

// Main query with filters
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
            cancellations.id,
            cancellations.refund_amount,
            cancellations.shipping_refund,
            cancellations.tax_refund,
            cancellations.final_fee_refund,
            cancellations.fixed_charge_refund,
            cancellations.other_fee_refund
          FROM sell 
          LEFT JOIN store ON store.id_store = sell.id_store
          LEFT JOIN sucursal ON sucursal.id_sucursal = sell.id_sucursal
          LEFT JOIN items ON items.sku_item = sell.sku_item 
                          AND (items.upc_item = sell.upc_item OR items.upc_item IS NULL)
          LEFT JOIN cancellations ON BINARY cancellations.order_id = BINARY sell.sell_order
          WHERE $whereClause
          ORDER BY sell.date DESC
          LIMIT $inicio, 15";

if (!empty($params)) {
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $mysqli->query($query);
}

$data = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['sell_order']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['upc_item']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['brand_item']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['item_item']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['color_item']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['ref_item']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['store_name']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['code_sucursal']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . ($row['refund_amount'] ? '$' . number_format($row['refund_amount'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . ($row['shipping_refund'] ? '$' . number_format($row['shipping_refund'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . ($row['tax_refund'] ? '$' . number_format($row['tax_refund'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . ($row['final_fee_refund'] ? '$' . number_format($row['final_fee_refund'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . ($row['fixed_charge_refund'] ? '$' . number_format($row['fixed_charge_refund'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . ($row['other_fee_refund'] ? '$' . number_format($row['other_fee_refund'], 2) : '-') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='15'>No se encontraron registros con los filtros aplicados.</td></tr>";
}

// Render pagination
$pagination->render();

$mysqli->close();
?>
