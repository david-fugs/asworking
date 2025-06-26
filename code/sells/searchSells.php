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

// Construir la consulta SQL con filtros dinámicos usando LEFT JOINs
$query = "SELECT 
            sell.id_sell,
            sell.sell_order,
            sell.date,
            sell.upc_item,
            sell.sku_item,
            sell.received_shipping,
            sell.payed_shipping,
            store.store_name,
            sell.id_store,
            sell.id_sucursal,
            sucursal.code_sucursal,
            sell.comision_item,
            sell.quantity,
            sell.item_price,
            sell.total_item,
            -- Verificar si existe en cada tabla
            CASE WHEN shipping.sell_order IS NOT NULL THEN 1 ELSE 0 END as has_shipping,
            CASE WHEN shipping_return.sell_order IS NOT NULL THEN 1 ELSE 0 END as has_shipping_return,
            CASE WHEN discounts.sell_order IS NOT NULL THEN 1 ELSE 0 END as has_discounts,
            CASE WHEN safetclaim.sell_order IS NOT NULL THEN 1 ELSE 0 END as has_safetclaim,
            CASE WHEN cancellations.order_id IS NOT NULL THEN 1 ELSE 0 END as has_cancellations,
            CASE WHEN returns.upc_item IS NOT NULL THEN 1 ELSE 0 END as has_returns
          FROM sell 
          LEFT JOIN store ON store.id_store = sell.id_store
          LEFT JOIN sucursal ON sucursal.id_sucursal = sell.id_sucursal
          LEFT JOIN shipping ON shipping.sell_order COLLATE utf8mb4_unicode_ci = sell.sell_order COLLATE utf8mb4_unicode_ci
          LEFT JOIN shipping_return ON shipping_return.sell_order COLLATE utf8mb4_unicode_ci = sell.sell_order COLLATE utf8mb4_unicode_ci
          LEFT JOIN discounts ON discounts.sell_order COLLATE utf8mb4_unicode_ci = sell.sell_order COLLATE utf8mb4_unicode_ci
          LEFT JOIN safetclaim ON safetclaim.sell_order COLLATE utf8mb4_unicode_ci = sell.sell_order COLLATE utf8mb4_unicode_ci
          LEFT JOIN cancellations ON cancellations.order_id COLLATE utf8mb4_unicode_ci = sell.sell_order COLLATE utf8mb4_unicode_ci
          LEFT JOIN returns ON returns.upc_item COLLATE utf8mb4_unicode_ci = sell.upc_item COLLATE utf8mb4_unicode_ci
          WHERE sell.estado_sell = 1";

$params = [];
$types = "";

// Agregar filtros dinámicamente
$conditions = [];

if (!empty($upc_item)) {
    $conditions[] = "sell.upc_item COLLATE utf8mb4_unicode_ci = ?";
    $params[] = $upc_item;
    $types .= "s";
}

if (!empty($sell_order)) {
    $conditions[] = "sell.sell_order COLLATE utf8mb4_unicode_ci = ?";
    $params[] = $sell_order;
    $types .= "s";
}

if (!empty($conditions)) {
    $query .= " AND (" . implode(" OR ", $conditions) . ")";
}

$query .= " GROUP BY sell.id_sell ORDER BY sell.date DESC";

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
    // Función para mostrar check o x
    function getStatusIcon($count) {
        return $count > 0 ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>';
    }
    
    echo '<table class="table table-striped" id="salesTable">
            <thead class="table-custom-header">
              <tr>
                <th>Sell Number</th>
                <th>Date</th>
                <th>UPC</th>
                <th>SKU</th>
                <th>Store</th>
                <th>Sucursal</th>
                <th>Shipping</th>
                <th>Shipping Return</th>
                <th>Discounts</th>
                <th>Safe Claim</th>
                <th>Cancellations</th>
                <th>Returns</th>
              </tr>
            </thead>
            <tbody>';
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['sell_order']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['upc_item']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['sku_item'] ?: '-') . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['store_name']) . "</td>";
        echo "<td class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . htmlspecialchars($row['code_sucursal']) . "</td>";
        echo "<td class='clickable-row text-center' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . getStatusIcon($row['has_shipping']) . "</td>";
        echo "<td class='clickable-row text-center' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . getStatusIcon($row['has_shipping_return']) . "</td>";
        echo "<td class='clickable-row text-center' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . getStatusIcon($row['has_discounts']) . "</td>";
        echo "<td class='clickable-row text-center' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . getStatusIcon($row['has_safetclaim']) . "</td>";
        echo "<td class='clickable-row text-center' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . getStatusIcon($row['has_cancellations']) . "</td>";
        echo "<td class='clickable-row text-center' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>" . getStatusIcon($row['has_returns']) . "</td>";
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
            <p>No sales records found for ' . $search_term . '</p>
            <small class="text-muted">Try searching with a different criteria</small>
          </div>';
}

$stmt->close();
$mysqli->close();
?>
