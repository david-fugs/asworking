<?php
include("../../conexion.php");

echo "<h2>Database Tables Structure</h2>";

// Check if tables exist
$tables = ['items', 'inventory', 'daily_report'];

foreach ($tables as $table) {
    echo "<h3>Table: $table</h3>";
    
    // Check if table exists
    $result = $mysqli->query("SHOW TABLES LIKE '$table'");
    if ($result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Table exists</p>";
        
        // Show table structure
        $result = $mysqli->query("DESCRIBE $table");
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$row['Field']}</td>";
            echo "<td>{$row['Type']}</td>";
            echo "<td>{$row['Null']}</td>";
            echo "<td>{$row['Key']}</td>";
            echo "<td>{$row['Default']}</td>";
            echo "<td>{$row['Extra']}</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show row count
        $result = $mysqli->query("SELECT COUNT(*) as count FROM $table");
        $count = $result->fetch_assoc()['count'];
        echo "<p>Total rows: $count</p>";
        
    } else {
        echo "<p style='color: red;'>✗ Table does not exist</p>";
    }
    
    echo "<hr>";
}
?>
