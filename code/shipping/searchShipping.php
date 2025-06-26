<?php
include("../../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sell_order = isset($_POST['sell_order']) ? trim($_POST['sell_order']) : '';
    
    if (empty($sell_order)) {
        echo '<div class="alert alert-warning text-center">
                <i class="fas fa-exclamation-triangle"></i>
                <h5>Invalid Search</h5>
                <p>Please enter a Sell Order to search.</p>
              </div>';
        exit;
    }
      // Buscar en la tabla sell con el sell_order específico
    $query = "
    SELECT DISTINCT 
        s.sell_order, 
        s.date, 
        COALESCE(sh.shipping_paid, 0) as shipping_paid, 
        COALESCE(sh.shipping_other_carrier, 0) as shipping_other_carrier, 
        COALESCE(sh.shipping_adjust, 0) as shipping_adjust,
        sh.shipping_date
    FROM sell AS s
    LEFT JOIN shipping AS sh ON s.sell_order = sh.sell_order
    WHERE s.sell_order = ?
    ";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $sell_order);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {        echo '<table class="table table-bordered">
                <thead class="table-custom-header">
                    <tr>
                        <th>Sell Order</th>
                        <th>Date</th>
                        <th>Shipping Paid</th>
                        <th>Shipping Other Carriers</th>
                        <th>Shipping Label Adjustment</th>
                        <th>Shipping Date</th>
                        <th>Add Shipping</th>
                    </tr>
                </thead>
                <tbody>';
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr class='clickable-row' data-sell_order='" . htmlspecialchars($row['sell_order']) . "'>";            echo "<td>" . htmlspecialchars($row['sell_order']) . "</td>";
            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
            echo "<td>$" . number_format($row['shipping_paid'], 2) . "</td>";
            echo "<td>$" . number_format($row['shipping_other_carrier'], 2) . "</td>";
            echo "<td>$" . number_format($row['shipping_adjust'], 2) . "</td>";
            echo "<td>" . ($row['shipping_date'] ? htmlspecialchars($row['shipping_date']) : 'Not set') . "</td>";
            echo "<td>";
            echo "<button class='btn btn-action-icon btn-edit btn-sm' onclick=\"event.stopPropagation(); document.querySelector('[data-sell_order=\\\"" . htmlspecialchars($row['sell_order']) . "\\\"]').click();\"><i class='fas fa-edit'></i></button>";
            echo "</td>";
            echo "</tr>";
        }
        
        echo '</tbody></table>';
    } else {
        echo '<div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i>
                <h5>No Results Found</h5>
                <p>No shipping records found for Sell Order: <strong>' . htmlspecialchars($sell_order) . '</strong></p>
              </div>';
    }
    
    $stmt->close();
}

$mysqli->close();
?>
