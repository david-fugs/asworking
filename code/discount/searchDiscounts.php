<?php
include("../../conexion.php");

// Verificar que se recibieron los parámetros
$upc_item = isset($_POST['upc_item']) ? trim($_POST['upc_item']) : '';
$sell_order = isset($_POST['sell_order']) ? trim($_POST['sell_order']) : '';

if (empty($upc_item) && empty($sell_order)) {
    echo '<div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle"></i>
            <h5>No search criteria provided</h5>
            <p>Please enter either a UPC code or Sell Order to search</p>
          </div>';
    exit;
}

// Construir la consulta SQL con filtros dinámicos
$query = "SELECT 
            sell.id_sell,
            sell.sell_order,
            sell.date,
            sell.upc_item,
            sell.sku_item,
            store.store_name,
            sell.id_store,
            sell.id_sucursal,
            sucursal.code_sucursal,
            discounts.id_discount,
            discounts.price_discount,
            discounts.shipping_discount,
            discounts.fee_credit,
            discounts.tax_return,
            discounts.net_markdown,
            discounts.discount_date
          FROM sell 
          LEFT JOIN store ON store.id_store = sell.id_store
          LEFT JOIN sucursal ON sucursal.id_sucursal = sell.id_sucursal
          LEFT JOIN discounts ON BINARY discounts.upc_item = BINARY sell.upc_item AND discounts.id_sell = sell.id_sell
          WHERE sell.estado_sell = 1";

$params = [];
$types = "";

// Agregar filtros dinámicamente
$conditions = [];

if (!empty($upc_item)) {
    $conditions[] = "sell.upc_item = ?";
    $params[] = $upc_item;
    $types .= "s";
}

if (!empty($sell_order)) {
    $conditions[] = "sell.sell_order = ?";
    $params[] = $sell_order;
    $types .= "s";
}

if (!empty($conditions)) {
    $query .= " AND (" . implode(" OR ", $conditions) . ")";
}

$query .= " ORDER BY sell.date DESC";

$stmt = $mysqli->prepare($query);
if (!$stmt) {
    echo '<div class="alert alert-danger text-center">
            <i class="fas fa-exclamation-triangle"></i>
            <h5>Database Error</h5>
            <p>Error preparing query: ' . htmlspecialchars($mysqli->error) . '</p>
          </div>';
    exit;
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<table class="table table-striped" id="salesTable">
            <thead class="table-custom-header">
              <tr>
                <th>Sell Number</th>
                <th>Date</th>
                <th>UPC</th>
                <th>SKU</th>
                <th>Store</th>
                <th>Sucursal</th>
                <th>Price Discount</th>
                <th>Shipping Discount</th>
                <th>Fee Credit</th>
                <th>Tax Return</th>
                <th>Net Markdown</th>
                <th>Discount Date</th>
              </tr>
            </thead>
            <tbody>';
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . htmlspecialchars($row['sell_order']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . htmlspecialchars($row['upc_item']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . htmlspecialchars($row['sku_item'] ?: '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . htmlspecialchars($row['store_name']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . htmlspecialchars($row['code_sucursal']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . ($row['price_discount'] ? '$' . number_format($row['price_discount'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . ($row['shipping_discount'] ? '$' . number_format($row['shipping_discount'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . ($row['fee_credit'] ? '$' . number_format($row['fee_credit'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . ($row['tax_return'] ? '$' . number_format($row['tax_return'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . ($row['net_markdown'] ? '$' . number_format($row['net_markdown'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "' data-upc_item='" . htmlspecialchars($row['upc_item']) . "' data-id_sell='" . htmlspecialchars($row['id_sell']) . "'>" . ($row['discount_date'] ? htmlspecialchars($row['discount_date']) : 'Not set') . "</td>";
        echo "</tr>";
    }
    
    echo '</tbody></table>';
    
    // Mostrar resumen de resultados
    $search_term = !empty($upc_item) ? "UPC: " . htmlspecialchars($upc_item) : "Sell Order: " . htmlspecialchars($sell_order);
    echo '<div class="alert alert-success mt-3">
            <i class="fas fa-check-circle"></i>
            <strong>Search completed:</strong> Found ' . $result->num_rows . ' record(s) for ' . $search_term . '
          </div>';
    
} else {
    $search_term = !empty($upc_item) ? "UPC: " . htmlspecialchars($upc_item) : "Sell Order: " . htmlspecialchars($sell_order);
    echo '<div class="alert alert-warning text-center">
            <i class="fas fa-search"></i>
            <h5>No Results Found</h5>
            <p>No discount records found for ' . $search_term . '</p>
            <small class="text-muted">Try searching with a different criteria</small>
          </div>';
}

$stmt->close();
$mysqli->close();
?>
