<?php
include("../../conexion.php");

echo "<h2>Debug REF/Style Values</h2>";

// Check recent daily_report entries
$result = $mysqli->query("SELECT * FROM daily_report WHERE estado_reporte = 0 ORDER BY fecha_alta_reporte DESC LIMIT 10");

if ($result && $result->num_rows > 0) {
    echo "<h3>Recent Daily Report Entries (estado_reporte = 0):</h3>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>UPC</th><th>SKU</th><th>vendor_report (Style)</th><th>observacion_report</th><th>Fecha</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $style_value = $row['vendor_report'] ?? 'NULL';
        $obs_value = $row['observacion_report'] ?? 'NULL';
        
        echo "<tr>";
        echo "<td>{$row['id_report']}</td>";
        echo "<td>{$row['upc_final_report']}</td>";
        echo "<td>{$row['sku_report']}</td>";
        echo "<td style='background:" . (empty($style_value) || $style_value === 'NULL' ? '#ffcccc' : '#ccffcc') . "'>" . htmlspecialchars($style_value) . "</td>";
        echo "<td style='background:" . (strpos($obs_value, 'Added:') !== false ? '#ccffcc' : '#ffcccc') . "'>" . htmlspecialchars($obs_value) . "</td>";
        echo "<td>{$row['fecha_alta_reporte']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><strong>Legend:</strong></p>";
    echo "<p>🟢 Green = Good values | 🔴 Red = Empty/Missing values</p>";
    
} else {
    echo "<p>No daily report entries found with estado_reporte = 0</p>";
}

// Check the structure of daily_report table
echo "<h3>Daily Report Table Structure:</h3>";
$result = $mysqli->query("DESCRIBE daily_report");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $highlight = ($row['Field'] === 'vendor_report' || $row['Field'] === 'observacion_report') ? 'style="background: #ffffcc"' : '';
        echo "<tr $highlight>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
