<?php
// Archivo de prueba para verificar la funcionalidad de edición de campos de producto

include("conexion.php");

echo "<h2>Test: Edit Product Details Functionality</h2>";

// 1. Verificar que existen reportes con estado_reporte = 0
echo "<h3>1. Checking for processed reports (estado_reporte = 0):</h3>";
$sql = "SELECT COUNT(*) as count FROM daily_report WHERE estado_reporte = 0";
$result = $mysqli->query($sql);
$row = $result->fetch_assoc();
echo "Found {$row['count']} processed reports.<br>";

if ($row['count'] > 0) {
    // Mostrar algunos ejemplos
    $sql = "SELECT id_report, fecha_alta_reporte, upc_final_report, sku_report, 
                   item_report, brand_report, vendor_report, color_report, size_report,
                   folder_report, loc_report 
            FROM daily_report 
            WHERE estado_reporte = 0 
            LIMIT 3";
    $result = $mysqli->query($sql);
    
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th>ID</th><th>Date</th><th>UPC</th><th>SKU</th><th>Item</th><th>Brand</th><th>Vendor</th><th>Color</th><th>Size</th><th>Folder</th><th>Location</th></tr>";
    
    while ($report = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$report['id_report']}</td>";
        echo "<td>{$report['fecha_alta_reporte']}</td>";
        echo "<td>{$report['upc_final_report']}</td>";
        echo "<td>{$report['sku_report']}</td>";
        echo "<td>" . substr($report['item_report'], 0, 20) . "...</td>";
        echo "<td>{$report['brand_report']}</td>";
        echo "<td>{$report['vendor_report']}</td>";
        echo "<td>{$report['color_report']}</td>";
        echo "<td>{$report['size_report']}</td>";
        echo "<td>{$report['folder_report']}</td>";
        echo "<td>{$report['loc_report']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 2. Verificar estructura de la tabla
echo "<h3>2. Checking table structure:</h3>";
$sql = "DESCRIBE daily_report";
$result = $mysqli->query($sql);
$required_fields = ['item_report', 'brand_report', 'vendor_report', 'color_report', 'size_report', 'folder_report', 'loc_report'];
$found_fields = [];

echo "<ul>";
while ($row = $result->fetch_assoc()) {
    if (in_array($row['Field'], $required_fields)) {
        $found_fields[] = $row['Field'];
        echo "<li>✓ {$row['Field']} - {$row['Type']}</li>";
    }
}
echo "</ul>";

// Verificar que todos los campos necesarios existen
$missing_fields = array_diff($required_fields, $found_fields);
if (empty($missing_fields)) {
    echo "<p style='color: green;'>✓ All required fields are present in the database.</p>";
} else {
    echo "<p style='color: red;'>✗ Missing fields: " . implode(', ', $missing_fields) . "</p>";
}

// 3. Simular una actualización de prueba (sin ejecutar)
echo "<h3>3. Testing update query structure:</h3>";
$test_sql = "UPDATE daily_report 
             SET folder_report = ?, 
                 loc_report = ?,
                 item_report = ?,
                 brand_report = ?,
                 vendor_report = ?,
                 color_report = ?,
                 size_report = ?,
                 fecha_modificacion = NOW()
             WHERE id_report = ? 
             AND estado_reporte = 0";

if ($stmt = $mysqli->prepare($test_sql)) {
    echo "<p style='color: green;'>✓ Update query prepared successfully.</p>";
    $stmt->close();
} else {
    echo "<p style='color: red;'>✗ Error preparing update query: " . $mysqli->error . "</p>";
}

// 4. Verificar rutas de archivos
echo "<h3>4. Checking file paths:</h3>";
$files_to_check = [
    'code/report/editLocationFolder.php',
    'code/report/updateLocationFolder.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✓ $file exists</p>";
    } else {
        echo "<p style='color: red;'>✗ $file not found</p>";
    }
}

echo "<h3>Test completed!</h3>";
echo "<p><a href='code/report/editLocationFolder.php'>→ Go to Edit Product Details page</a></p>";
?>
