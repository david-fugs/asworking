<?php
include("../../conexion.php");

echo "<h2>Available UPCs in Database</h2>";

$result = $mysqli->query("SELECT DISTINCT upc_item, brand_item, item_item, sku_item FROM items LIMIT 10");
if ($result && $result->num_rows > 0) {
    echo "<p>Found " . $result->num_rows . " UPCs (showing first 10):</p>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>UPC</th><th>Brand</th><th>Item</th><th>SKU</th><th>Test Link</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['upc_item']}</td>";
        echo "<td>{$row['brand_item']}</td>";
        echo "<td>{$row['item_item']}</td>";
        echo "<td>{$row['sku_item']}</td>";
        echo "<td><a href='test_upc_modal.php?upc={$row['upc_item']}' target='_blank'>Test</a></td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No UPCs found in database. The table might be empty.</p>";
    
    // Let's insert a test item
    echo "<h3>Creating test item...</h3>";
    $test_insert = "INSERT INTO items (upc_item, sku_item, brand_item, item_item, ref_item, color_item, size_item, category_item, cost_item, weight_item, inventory_item, quantity_inventory, batch_item) 
                   VALUES ('TEST123456', 'SKU001', 'TEST BRAND', 'Test Item Name', 'REF001', 'RED', 'M', 'CLOTHING', 25.99, '1LB', 'LOC-A1', 10, 'BATCH001')";
    
    if ($mysqli->query($test_insert)) {
        echo "<p style='color: green;'>Test item created successfully! UPC: TEST123456</p>";
        echo "<p><a href='test_upc_modal.php?upc=TEST123456' target='_blank'>Test with this UPC</a></p>";
    } else {
        echo "<p style='color: red;'>Error creating test item: " . $mysqli->error . "</p>";
    }
}
?>
