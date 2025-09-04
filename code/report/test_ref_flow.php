<?php
include("../../conexion.php");

echo "<h2>Testing REF/Style Flow</h2>";

// Check if a test UPC exists
$test_upc = "TEST123456";
$result = $mysqli->query("SELECT * FROM items WHERE upc_item = '$test_upc'");

if ($result && $result->num_rows > 0) {
    $item = $result->fetch_assoc();
    echo "<h3>Test Item Found:</h3>";
    echo "<p><strong>UPC:</strong> {$item['upc_item']}</p>";
    echo "<p><strong>SKU:</strong> {$item['sku_item']}</p>";
    echo "<p><strong>REF (Style):</strong> {$item['ref_item']}</p>";
    echo "<p><strong>Brand:</strong> {$item['brand_item']}</p>";
    echo "<p><strong>Item:</strong> {$item['item_item']}</p>";
    
    // Check daily_report entries
    echo "<h3>Daily Report Entries for this UPC:</h3>";
    $report_result = $mysqli->query("SELECT * FROM daily_report WHERE upc_final_report = '$test_upc' ORDER BY fecha_alta_reporte DESC");
    
    if ($report_result && $report_result->num_rows > 0) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>UPC</th><th>SKU</th><th>Vendor Report (Style)</th><th>Observacion Report</th><th>Estado</th><th>Fecha</th></tr>";
        while ($report = $report_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$report['id_report']}</td>";
            echo "<td>{$report['upc_final_report']}</td>";
            echo "<td>{$report['sku_report']}</td>";
            echo "<td>" . htmlspecialchars($report['vendor_report'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($report['observacion_report'] ?? '') . "</td>";
            echo "<td>{$report['estado_reporte']}</td>";
            echo "<td>{$report['fecha_alta_reporte']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No daily report entries found.</p>";
    }
    
} else {
    echo "<p>Test item not found. You can create one using additems.php or addReport.php</p>";
}

echo "<hr>";
echo "<h3>Test Links:</h3>";
echo "<p><a href='../items/additems.php' target='_blank'>Test additems.php</a></p>";
echo "<p><a href='addReport.php' target='_blank'>Test addReport.php</a></p>";
echo "<p><a href='editLocationFolder.php' target='_blank'>Test editLocationFolder.php</a></p>";
?>
