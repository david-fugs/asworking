<?php
include("../../conexion.php");

echo "<h2>Checking daily_report table structure</h2>";

// Check if observacion_report column exists
$result = $mysqli->query("SHOW COLUMNS FROM daily_report LIKE 'observacion_report'");
if ($result->num_rows > 0) {
    echo "<p style='color: green;'>✓ Column 'observacion_report' already exists</p>";
} else {
    echo "<p style='color: orange;'>⚠ Column 'observacion_report' does not exist</p>";
    echo "<p>Attempting to add the column...</p>";
    
    // Try to add the column
    $add_column_sql = "ALTER TABLE daily_report ADD COLUMN observacion_report TEXT NULL";
    if ($mysqli->query($add_column_sql)) {
        echo "<p style='color: green;'>✓ Successfully added 'observacion_report' column</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to add column: " . $mysqli->error . "</p>";
        echo "<p>The system will fallback to using existing columns.</p>";
    }
}

// Show current table structure
echo "<h3>Current daily_report table structure:</h3>";
$result = $mysqli->query("DESCRIBE daily_report");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?>
