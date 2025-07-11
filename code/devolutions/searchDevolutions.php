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
            sell.item_profit,
            store_name,
            sell.id_store,
            sell.id_sucursal,
            code_sucursal,
            returns.id_return,
            returns.quantity,
            returns.product_charge,
            returns.shipping_paid,
            returns.tax_return,
            returns.selling_fee_refund,
            returns.refund_administration_fee,
            returns.other_refund_fee,
            returns.return_cost,
            returns.buyer_comments,
            returns.devolution_date
          FROM sell
          LEFT JOIN store ON store.id_store = sell.id_store
          LEFT JOIN sucursal ON sucursal.id_sucursal = sell.id_sucursal
          LEFT JOIN returns ON BINARY returns.sell_order = BINARY sell.sell_order 
                             AND returns.id_sell = sell.id_sell
                             AND BINARY returns.upc_item = BINARY sell.upc_item
                             AND (BINARY returns.sku_item = BINARY sell.sku_item 
                                  OR (returns.sku_item IS NULL AND sell.sku_item IS NULL)
                                  OR (returns.sku_item = '' AND sell.sku_item IS NULL)
                                  OR (returns.sku_item IS NULL AND sell.sku_item = ''))
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

// Preparar y ejecutar la consulta
$stmt = $mysqli->prepare($query);
if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
      if ($result->num_rows > 0) {        echo '<div class="table-responsive">
                <table class="table table-striped table-hover" id="returnsTable" style="table-layout: auto !important; border-collapse: collapse !important;">
                <thead>                  <tr style="display: table-row !important;">
                    <th style="display: table-cell !important;"><i class="fas fa-hashtag me-1"></i>Sell Order</th>
                    <th style="display: table-cell !important;"><i class="fas fa-calendar me-1"></i>Date</th>
                    <th style="display: table-cell !important;"><i class="fas fa-barcode me-1"></i>UPC</th>
                    <th style="display: table-cell !important;"><i class="fas fa-tag me-1"></i>SKU</th>
                    <th style="display: table-cell !important;"><i class="fas fa-cube me-1"></i>Quantity</th>
                    <th style="display: table-cell !important;"><i class="fas fa-dollar-sign me-1"></i>Product Charge</th>
                    <th style="display: table-cell !important;"><i class="fas fa-shipping-fast me-1"></i>Shipping Paid</th>
                    <th style="display: table-cell !important;"><i class="fas fa-receipt me-1"></i>Tax Return</th>
                    <th style="display: table-cell !important;"><i class="fas fa-percent me-1"></i>Selling Fee Refund</th>
                    <th style="display: table-cell !important;"><i class="fas fa-cog me-1"></i>Refund Administration Fee</th>
                    <th style="display: table-cell !important;"><i class="fas fa-plus me-1"></i>Other Refund Fee</th>
                    <th style="display: table-cell !important;"><i class="fas fa-undo me-1"></i>Return Cost</th>
                    <th style="display: table-cell !important;"><i class="fas fa-comment me-1"></i>Buyer Comments</th>
                    <th style="display: table-cell !important;"><i class="fas fa-calendar-check me-1"></i>Devolution Date</th>
                    <th style="display: table-cell !important;"><i class="fas fa-store me-1"></i>Branch</th>
                  </tr>
                </thead>
                <tbody>';        while ($row = $result->fetch_assoc()) {
            // Calcular Return Cost usando la fórmula:
            // (Product Charge + Shipping Paid + Tax Return - Selling Fee Refund + Refund Administration Fee + Other Refund Fee + Item Profit)
            $product_charge = floatval($row['product_charge'] ?? 0);
            $shipping_paid = floatval($row['shipping_paid'] ?? 0);
            $tax_return = floatval($row['tax_return'] ?? 0);
            $selling_fee_refund = floatval($row['selling_fee_refund'] ?? 0);
            $refund_administration_fee = floatval($row['refund_administration_fee'] ?? 0);
            $other_refund_fee = floatval($row['other_refund_fee'] ?? 0);
            $item_profit = floatval($row['item_profit'] ?? 0);
            
            $calculated_return_cost = $product_charge + $shipping_paid + $tax_return - $selling_fee_refund + $refund_administration_fee + $other_refund_fee + $item_profit;
            
            echo "<tr class='clickable-row' style='cursor: pointer;' 
                    data-id='" . htmlspecialchars($row['id_return'] ?? '') . "'
                    data-id-sell='" . htmlspecialchars($row['id_sell']) . "'
                    data-sell-order='" . htmlspecialchars($row['sell_order']) . "'
                    data-date='" . htmlspecialchars($row['date']) . "'
                    data-upc='" . htmlspecialchars($row['upc_item']) . "'
                    data-sku='" . htmlspecialchars($row['sku_item'] ?? '') . "'
                    data-quantity='" . htmlspecialchars($row['quantity'] ?? '0') . "'
                    data-product-charge='" . htmlspecialchars($row['product_charge'] ?? '0.00') . "'
                    data-shipping-paid='" . htmlspecialchars($row['shipping_paid'] ?? '0.00') . "'
                    data-tax-return='" . htmlspecialchars($row['tax_return'] ?? '0.00') . "'
                    data-selling-fee-refund='" . htmlspecialchars($row['selling_fee_refund'] ?? '0.00') . "'
                    data-refund-administration-fee='" . htmlspecialchars($row['refund_administration_fee'] ?? '0.00') . "'
                    data-other-refund-fee='" . htmlspecialchars($row['other_refund_fee'] ?? '0.00') . "'
                    data-return-cost='" . htmlspecialchars(number_format($calculated_return_cost, 2)) . "'
                    data-buyer-comments='" . htmlspecialchars($row['buyer_comments'] ?? '') . "'
                    data-item-profit='" . htmlspecialchars($row['item_profit'] ?? '0.00') . "'
                    data-devolution-date='" . htmlspecialchars($row['devolution_date'] ?? '') . "'
                    data-code-sucursal='" . htmlspecialchars($row['code_sucursal']) . "'>";            echo "<td><strong style='color: #000000 !important;'>" . htmlspecialchars($row['sell_order']) . "</strong></td>";
            echo "<td><span class='badge bg-dark text-white'>" . date('M d, Y', strtotime($row['date'])) . "</span></td>";
            echo "<td><code class='bg-dark text-white'>" . htmlspecialchars($row['upc_item']) . "</code></td>";
            echo "<td>" . (empty($row['sku_item']) ? '<em style="color: #000000 !important;">N/A</em>' : '<strong style="color: #000000 !important;">' . htmlspecialchars($row['sku_item']) . '</strong>') . "</td>";
            echo "<td><span class='badge bg-dark text-white'>" . htmlspecialchars($row['quantity'] ?? '0') . "</span></td>";
            echo "<td class='text-end'><strong style='color: #000000 !important;'>$" . number_format($row['product_charge'] ?? 0, 2) . "</strong></td>";
            echo "<td class='text-end'><strong style='color: #000000 !important;'>$" . number_format($row['shipping_paid'] ?? 0, 2) . "</strong></td>";
            echo "<td class='text-end'><strong style='color: #000000 !important;'>$" . number_format($row['tax_return'] ?? 0, 2) . "</strong></td>";
            echo "<td class='text-end'><strong style='color: #000000 !important;'>$" . number_format($row['selling_fee_refund'] ?? 0, 2) . "</strong></td>";
            echo "<td class='text-end'><strong style='color: #000000 !important;'>$" . number_format($row['refund_administration_fee'] ?? 0, 2) . "</strong></td>";
            echo "<td class='text-end'><strong style='color: #000000 !important;'>$" . number_format($row['other_refund_fee'] ?? 0, 2) . "</strong></td>";
            echo "<td class='text-end'><strong style='color: #000000 !important;'>$" . number_format($calculated_return_cost, 2) . "</strong></td>";
            echo "<td>" . (empty($row['buyer_comments']) ? '<em style="color: #000000 !important;">No comments</em>' : '<span class="text-truncate d-inline-block" style="max-width: 150px; color: #000000 !important;" title="' . htmlspecialchars($row['buyer_comments']) . '">' . htmlspecialchars($row['buyer_comments']) . '</span>') . "</td>";
            echo "<td>" . (empty($row['devolution_date']) ? '<em style="color: #000000 !important;">Not set</em>' : '<span class="badge bg-primary text-white">' . date('M d, Y', strtotime($row['devolution_date'])) . '</span>') . "</td>";
            echo "<td><span class='badge bg-dark text-white'>" . htmlspecialchars($row['code_sucursal']) . "</span></td>";
            echo "</tr>";        }
        // Construir mensaje dinámico basado en los criterios de búsqueda
        $search_terms = [];
        if (!empty($upc_item)) {
            $search_terms[] = "UPC: " . htmlspecialchars($upc_item);
        }
        if (!empty($sell_order)) {
            $search_terms[] = "Sell Order: " . htmlspecialchars($sell_order);
        }
        $search_term = implode(" and ", $search_terms);
        echo '</tbody></table></div>
              <div class="alert alert-success mt-3 text-center">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Search completed:</strong> Found ' . $result->num_rows . ' record(s) for ' . $search_term . '
              </div>';
          } else {
        // Construir mensaje dinámico basado en los criterios de búsqueda
        $search_terms = [];
        if (!empty($upc_item)) {
            $search_terms[] = "UPC: " . htmlspecialchars($upc_item);
        }
        if (!empty($sell_order)) {
            $search_terms[] = "Sell Order: " . htmlspecialchars($sell_order);
        }
        $search_term = implode(" and ", $search_terms);
        echo '<div class="alert alert-warning text-center">
                <i class="fas fa-search"></i>
                <h5>No Returns Found</h5>
                <p>No return records found for ' . $search_term . '</p>
              </div>';
    }
    
    $stmt->close();
} else {
    echo '<div class="alert alert-danger text-center">
            <i class="fas fa-exclamation-triangle"></i>
            <h5>Database Error</h5>
            <p>Error preparing database query</p>
          </div>';
}

$mysqli->close();
?>
