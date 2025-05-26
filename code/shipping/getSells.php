<?php
require '../../conexion.php';

$sell_order = $_GET['sell_order'];
$sql = "SELECT s.*, sh.shipping_paid, sh.shipping_other_carrier,sh.shipping_adjust FROM sell as s 
LEFT JOIN shipping as sh ON s.sell_order = sh.sell_order
WHERE s.sell_order = ?
";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $sell_order);
$stmt->execute();
$result = $stmt->get_result();

$sql_shipping = "SELECT * FROM shipping WHERE sell_order = ?";
$stmt_shipping = $mysqli->prepare($sql_shipping);
$stmt_shipping->bind_param("s", $sell_order);
$stmt_shipping->execute();
$result_shipping = $stmt_shipping->get_result();
$shipping = $result_shipping->fetch_assoc();
    echo "<h4>Sell Order $sell_order </h4>";
echo "<table class='table table-bordered table-sm mt-3'>";
echo "<thead>";
echo "<tr>
        <th>UPC</th>
        <th>SKU</th>
        <th>Quantity</th>
        <th>Final Fee</th>
        <th>Fixed Charge</th>
        <th>Item Profit</th>
        <th>Total Item</th>
      </tr>";
echo "</thead>";
echo "<tbody>";

$totalGeneral = 0;

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['upc_item']}</td>
            <td>{$row['sku_item']}</td>
            <td>{$row['quantity']}</td>
            <td>\${$row['comision_item']}</td>
            <td>\${$row['cargo_fijo']}</td>
            <td>\${$row['item_profit']}</td>
            <td>\${$row['total_item']}</td>
          </tr>";

    // Acumular el total general
    $totalGeneral += $row['total_item'];
}

// Fila final con el total general
echo "<tr>
        <td colspan='6' class='text-end'><strong>Total:</strong></td>
        <td><strong>\$" . number_format($totalGeneral, 2) . "</strong></td>
      </tr>";

echo "</tbody>";
echo "</table>";

echo "<form method='post' action='saveShipping.php' class='mt-4'>
    <div class='row mb-3'>
        <div class='col-md-4'>
            <label for='shipping_paid' class='form-label'>Shipping Paid</label>
            <input type='number' step='0.01' name='shipping_paid' value='" . (isset($shipping['shipping_paid']) ? htmlspecialchars($shipping['shipping_paid']) : '') . "' id='shipping_paid' class='form-control'>
        </div>
        <div class='col-md-4'>
            <label for='shipping_other' class='form-label'>Shipping Other Carriers</label>
            <input type='number' step='0.01' name='shipping_other_carrier' value='" . (isset($shipping['shipping_other_carrier']) ? htmlspecialchars($shipping['shipping_other_carrier']) : '') . "' id='shipping_other' class='form-control' required>
        </div>
        <div class='col-md-4'>
            <label for='shipping_adjust' class='form-label'>Shipping Label Adjustment</label>
            <input type='number' step='0.01' name='shipping_adjust' value='" . (isset($shipping['shipping_adjust']) ? htmlspecialchars($shipping['shipping_adjust']) : '') . "' id='shipping_adjust' class='form-control' required>
        </div>
    </div>
    <input type='hidden' name='sell_order' value='" . htmlspecialchars($sell_order) . "'>
    <div class='text-end'>
        <button type='submit' class='btn' style='background-color: #632b8b; color: #fff; border-color: #632b8b;'>
        Save
        </button>    
    </div>

</form>";
