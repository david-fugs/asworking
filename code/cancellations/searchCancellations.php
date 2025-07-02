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
            store_name,
            sell.id_store,
            sell.id_sucursal,
            code_sucursal,
            cancellations.id,
            cancellations.refund_amount,
            cancellations.shipping_refund,
            cancellations.tax_refund,
            cancellations.final_fee_refund,
            cancellations.fixed_charge_refund,
            cancellations.other_fee_refund,
            cancellations.net_cancellation,
            cancellations.cancellation_date,
            cancellations.sku_item as cancellation_sku
          FROM sell
          LEFT JOIN store ON store.id_store = sell.id_store
          LEFT JOIN sucursal ON sucursal.id_sucursal = sell.id_sucursal
          LEFT JOIN cancellations ON BINARY cancellations.order_id = BINARY sell.sell_order 
                                    AND BINARY cancellations.upc_item = BINARY sell.upc_item
                                    AND cancellations.id_sell = sell.id_sell
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

if ($result->num_rows > 0) {    echo '<table class="table table-striped" id="salesTable">
            <thead class="table-custom-header">
              <tr>
                <th>Sell Number</th>
                <th>Date</th>
                <th>UPC</th>
                <th>SKU</th>
                <th>Store</th>
                <th>Sucursal</th>
                <th>Refund Amount</th>
                <th>Shipping Refund</th>
                <th>Tax Refund</th>
                <th>Final Fee Refund</th>
                <th>Fixed Charge Refund</th>
                <th>Other Fee Refund</th>
                <th>Net Cancellation</th>
                <th>Cancellation Date</th>
              </tr>
            </thead>
            <tbody>';
    
    while ($row = $result->fetch_assoc()) {
        // Crear los atributos data para cada fila
        $dataAttributes = "data-sell_order='" . htmlspecialchars($row['sell_order']) . "' " .
                         "data-upc_item='" . htmlspecialchars($row['upc_item']) . "' " .
                         "data-id_sell='" . htmlspecialchars($row['id_sell']) . "' " .
                         "data-sku_item='" . htmlspecialchars($row['sku_item']) . "'";
        
        // Verificar si tiene datos de cancelación
        $hasCancellationData = !empty($row['refund_amount']) || !empty($row['shipping_refund']) || 
                              !empty($row['tax_refund']) || !empty($row['final_fee_refund']) || 
                              !empty($row['fixed_charge_refund']) || !empty($row['other_fee_refund']) ||
                              !empty($row['cancellation_date']);
        
        // Agregar clase CSS según tenga o no datos de cancelación
        $rowClass = $hasCancellationData ? 'table-success' : 'table-light';
        $statusIcon = $hasCancellationData ? '<i class="fas fa-check-circle text-success"></i>' : '<i class="fas fa-minus-circle text-muted"></i>';
        
        echo "<tr class='clickable-row " . $rowClass . "' " . $dataAttributes . ">";
        echo "<td>" . htmlspecialchars($row['sell_order']) . " " . $statusIcon . "</td>";
        echo "<td>" . htmlspecialchars($row['date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['upc_item']) . "</td>";
        echo "<td>" . htmlspecialchars($row['sku_item']) . "</td>";
        echo "<td>" . htmlspecialchars($row['store_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['code_sucursal']) . "</td>";
        echo "<td>" . ($row['refund_amount'] ? '$' . number_format($row['refund_amount'], 2) : '-') . "</td>";
        echo "<td>" . ($row['shipping_refund'] ? '$' . number_format($row['shipping_refund'], 2) : '-') . "</td>";
        echo "<td>" . ($row['tax_refund'] ? '$' . number_format($row['tax_refund'], 2) : '-') . "</td>";
        echo "<td>" . ($row['final_fee_refund'] ? '$' . number_format($row['final_fee_refund'], 2) : '-') . "</td>";
        echo "<td>" . ($row['fixed_charge_refund'] ? '$' . number_format($row['fixed_charge_refund'], 2) : '-') . "</td>";
        echo "<td>" . ($row['other_fee_refund'] ? '$' . number_format($row['other_fee_refund'], 2) : '-') . "</td>";
        echo "<td>" . ($row['net_cancellation'] ? '$' . number_format($row['net_cancellation'], 2) : '-') . "</td>";
        echo "<td>" . ($row['cancellation_date'] ? htmlspecialchars($row['cancellation_date']) : '-') . "</td>";
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
            <p>No cancellation records found for ' . $search_term . '</p>
            <small class="text-muted">Try searching with a different criteria</small>
          </div>';
}

$stmt->close();
$mysqli->close();
?>
