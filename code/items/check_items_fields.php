<?php
include("../../conexion.php");

echo "<h2>Items table structure - checking for style/vendor fields</h2>";

$result = $mysqli->query("DESCRIBE items");
if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    while ($row = $result->fetch_assoc()) {
        $field = $row['Field'];
        $highlight = '';
        if (stripos($field, 'vendor') !== false || stripos($field, 'style') !== false || stripos($field, 'ref') !== false) {
            $highlight = ' style="background-color: yellow;"';
        }
        echo "<tr$highlight>";
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
