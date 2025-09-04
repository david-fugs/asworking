<?php
include("../../conexion.php");

echo "<h2>Sample Data</h2>";

// Check items table sample data
echo "<h3>Sample Items (first 5 rows)</h3>";
$result = $mysqli->query("SELECT * FROM items LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    $first_row = true;
    while ($row = $result->fetch_assoc()) {
        if ($first_row) {
            echo "<tr>";
            foreach (array_keys($row) as $column) {
                echo "<th>$column</th>";
            }
            echo "</tr>";
            $first_row = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data in items table</p>";
}

echo "<hr>";

// Check daily_report table sample data
echo "<h3>Sample Daily Reports (first 5 rows)</h3>";
$result = $mysqli->query("SELECT * FROM daily_report LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    $first_row = true;
    while ($row = $result->fetch_assoc()) {
        if ($first_row) {
            echo "<tr>";
            foreach (array_keys($row) as $column) {
                echo "<th>$column</th>";
            }
            echo "</tr>";
            $first_row = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data in daily_report table</p>";
}

echo "<hr>";

// Check inventory table sample data  
echo "<h3>Sample Inventory (first 5 rows)</h3>";
$result = $mysqli->query("SELECT * FROM inventory LIMIT 5");
if ($result && $result->num_rows > 0) {
    echo "<table border='1' cellpadding='5'>";
    $first_row = true;
    while ($row = $result->fetch_assoc()) {
        if ($first_row) {
            echo "<tr>";
            foreach (array_keys($row) as $column) {
                echo "<th>$column</th>";
            }
            echo "</tr>";
            $first_row = false;
        }
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No data in inventory table</p>";
}
?>
