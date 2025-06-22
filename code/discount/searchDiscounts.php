<?php
include("../../conexion.php");

// Verificar que se recibieron los parámetros
$upc_item = isset($_POST['upc_item']) ? trim($_POST['upc_item']) : '';
$sell_order = isset($_POST['sell_order']) ? trim($_POST['sell_order']) : '';

if (empty($upc_item)) {
    echo '<div class="alert alert-warning text-center">
            <i class="fas fa-exclamation-triangle"></i>
            <h5>No UPC provided</h5>
            <p>Please enter a UPC code to search</p>
          </div>';
    exit;
}

// Construir la consulta SQL con filtros
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
            discounts.id_discount,            discounts.price_discount,
            discounts.shipping_discount,
            discounts.fee_credit,
            discounts.tax_return,
            discounts.net_markdown
          FROM sell 
          LEFT JOIN store ON store.id_store = sell.id_store
          LEFT JOIN sucursal ON sucursal.id_sucursal = sell.id_sucursal
          LEFT JOIN items ON items.sku_item = sell.sku_item 
                          AND (items.upc_item = sell.upc_item OR items.upc_item IS NULL)
          LEFT JOIN discounts ON BINARY discounts.sell_order = BINARY sell.sell_order
          WHERE sell.estado_sell = 1 AND sell.upc_item = ?";

$params = [$upc_item];
$types = "s";

// Agregar filtro por orden de venta si se proporciona
if (!empty($sell_order)) {
    $query .= " AND sell.sell_order = ?";
    $params[] = $sell_order;
    $types .= "s";
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

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {    echo '<table class="table table-striped" id="salesTable">
            <thead>
              <tr>
                <th>Sell Number</th>
                <th>Date</th>
                <th>UPC</th>
                <th>Brand</th>
                <th>Item</th>
                <th>Color</th>
                <th>Reference</th>
                <th>Store</th>
                <th>Sucursal</th>
                <th>Price Discount</th>
                <th>Shipping Discount</th>
                <th>Fee Credit</th>
                <th>Tax Return</th>
                <th>Net Markdown</th>
              </tr>
            </thead>
            <tbody>';
    
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
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['code_sucursal']) . "</td>";        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . ($row['price_discount'] ? '$' . number_format($row['price_discount'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . ($row['shipping_discount'] ? '$' . number_format($row['shipping_discount'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . ($row['fee_credit'] ? '$' . number_format($row['fee_credit'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . ($row['tax_return'] ? '$' . number_format($row['tax_return'], 2) : '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . ($row['net_markdown'] ? '$' . number_format($row['net_markdown'], 2) : '-') . "</td>";
        echo "</tr>";
    }
    
    echo '</tbody></table>';
    
    // Mostrar resumen de resultados
    echo '<div class="alert alert-success mt-3">
            <i class="fas fa-check-circle"></i>
            <strong>Search completed:</strong> Found ' . $result->num_rows . ' record(s) for UPC: ' . htmlspecialchars($upc_item) . '
          </div>';
    
} else {
    echo '<div class="alert alert-warning text-center">
            <i class="fas fa-search"></i>
            <h5>No Results Found</h5>
            <p>No discount records found for UPC: <strong>' . htmlspecialchars($upc_item) . '</strong></p>
            <small class="text-muted">Try searching with a different UPC code</small>
          </div>';
}

$stmt->close();
$mysqli->close();
?>
