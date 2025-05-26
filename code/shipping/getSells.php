<?php
require '../../conexion.php';

$sell_order = $_GET['sell_order'];
$sql = "SELECT * FROM sell WHERE sell_order = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $sell_order);
$stmt->execute();
$result = $stmt->get_result();

echo "<table class='table table-bordered table-sm'>";
echo "<thead><tr>";
echo "<th>UPC</th>
<th>SKU</th>
<th>Quantity</th>
<th>Final Fee</th>
<th>Fixed Charge</th>
<th>Comisión</th>
<th>Fecha</th>
";
// Agrega las otras columnas necesarias
echo "</tr></thead><tbody>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>{$row['upc_item']}</td>";
    echo "<td>{$row['sku_item']}</td>";
    echo "<td>{$row['quantity']}</td>";
    echo "<td>\${$row['item_price']}</td>";
    echo "<td>\${$row['total_item']}</td>";
    echo "<td>\${$row['comision']}</td>";
    // Agrega las otras columnas necesarias
    echo "</tr>";
}
echo "</tbody></table>";
