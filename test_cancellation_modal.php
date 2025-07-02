<?php
// Test script para verificar que el modal funciona
include "conexion.php";

echo "<h2>Testing Cancellation Modal Functionality</h2>";

// Test 1: Verificar que la tabla de cancellations existe y tiene los campos necesarios
echo "<h3>Test 1: Verificar estructura de tabla cancellations</h3>";
$query = "DESCRIBE cancellations";
$result = $mysqli->query($query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p style='color: green;'>✅ Tabla cancellations verificada correctamente</p>";
} else {
    echo "<p style='color: red;'>❌ Error al verificar tabla: " . $mysqli->error . "</p>";
}

// Test 2: Verificar que hay datos de prueba
echo "<h3>Test 2: Verificar datos de ejemplo</h3>";
$query = "SELECT COUNT(*) as total FROM sell WHERE estado_sell = 1 LIMIT 5";
$result = $mysqli->query($query);
$row = $result->fetch_assoc();
echo "<p>Sell orders activos encontrados: " . $row['total'] . "</p>";

$query = "SELECT sell_order, upc_item FROM sell WHERE estado_sell = 1 LIMIT 3";
$result = $mysqli->query($query);
echo "<p>Ejemplos de sell orders para probar:</p>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li>Order: " . $row['sell_order'] . " | UPC: " . $row['upc_item'] . "</li>";
}
echo "</ul>";

// Test 3: Verificar que el archivo JavaScript existe
echo "<h3>Test 3: Verificar archivos del módulo</h3>";
$files_to_check = [
    'code/cancellations/seeCancellations.php',
    'code/cancellations/seeCancellations.js',
    'code/cancellations/searchCancellations.php',
    'code/cancellations/getSellToReturn.php',
    'code/cancellations/saveCancellations.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file - Existe</p>";
    } else {
        echo "<p style='color: red;'>❌ $file - No existe</p>";
    }
}

echo "<h3>Instrucciones para probar:</h3>";
echo "<ol>";
echo "<li>Abre <a href='code/cancellations/seeCancellations.php' target='_blank'>seeCancellations.php</a></li>";
echo "<li>Busca por uno de los UPC codes listados arriba</li>";
echo "<li>Haz click en cualquier celda de la fila resultado</li>";
echo "<li>En el modal que se abre, haz click en el botón 'Edit Cancellation'</li>";
echo "<li>Debería abrirse un segundo modal para editar la cancelación</li>";
echo "</ol>";

echo "<h3>Si el modal no se abre, revisa:</h3>";
echo "<ul>";
echo "<li>Consola del navegador (F12) para errores de JavaScript</li>";
echo "<li>Que Bootstrap 5.3.3 esté cargado correctamente</li>";
echo "<li>Que no haya conflictos entre los modales</li>";
echo "</ul>";

$mysqli->close();
?>
