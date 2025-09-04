<?php
include("../../conexion.php");

echo "<h2>Checking specific UPC and SKU in items table</h2>";

$upc_to_check = "733004811005"; // El UPC del error
echo "<p>Checking UPC: <strong>$upc_to_check</strong></p>";

// First, check what items exist with this UPC
$result = $mysqli->query("SELECT * FROM items WHERE upc_item = '$upc_to_check'");
if ($result && $result->num_rows > 0) {
    echo "<h3>Items found with this UPC:</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>UPC</th><th>SKU</th><th>Brand</th><th>Item</th><th>Location</th><th>Folder</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id_item']}</td>";
        echo "<td>{$row['upc_item']}</td>";
        echo "<td>{$row['sku_item']}</td>";
        echo "<td>{$row['brand_item']}</td>";
        echo "<td>{$row['item_item']}</td>";
        echo "<td>{$row['inventory_item']}</td>";
        echo "<td>{$row['folder_item']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>No items found with UPC: $upc_to_check</p>";
}

// Now check what reports exist with this UPC
echo "<h3>Reports with this UPC:</h3>";
$result = $mysqli->query("SELECT * FROM daily_report WHERE upc_final_report = '$upc_to_check' AND estado_reporte = 0");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>UPC</th><th>SKU</th><th>Quantity</th><th>Brand</th><th>Item</th><th>Estado</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id_report']}</td>";
        echo "<td>{$row['upc_final_report']}</td>";
        echo "<td>{$row['sku_report']}</td>";
        echo "<td>{$row['quantity_report']}</td>";
        echo "<td>{$row['brand_report']}</td>";
        echo "<td>{$row['item_report']}</td>";
        echo "<td>{$row['estado_reporte']}</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No active reports found with this UPC</p>";
}
?>
