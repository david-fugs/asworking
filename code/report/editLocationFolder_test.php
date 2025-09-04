<?php
session_start();
include("../../conexion.php");

// Simple test version
echo "<!DOCTYPE html>";
echo "<html><head><title>Edit Location Folder - Working!</title></head>";
echo "<body>";
echo "<h1>✅ Edit Location Folder Page Loaded Successfully!</h1>";
echo "<p>Session ID: " . (isset($_SESSION['id']) ? $_SESSION['id'] : 'NOT SET') . "</p>";
echo "<p>User: " . (isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'NOT SET') . "</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";

// Check for reports with estado_reporte = 0
$sql = "SELECT COUNT(*) as count FROM daily_report WHERE estado_reporte = 0";
$result = $mysqli->query($sql);
if ($result) {
    $count = $result->fetch_assoc()['count'];
    echo "<p>Reports with estado_reporte = 0: " . $count . "</p>";
} else {
    echo "<p>Error checking reports: " . $mysqli->error . "</p>";
}

echo "<p><a href='../../access.php'>Back to Main Menu</a></p>";
echo "</body></html>";
?>
