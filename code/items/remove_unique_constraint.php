<?php
/**
 * Script to remove UNIQUE constraint from items.sku_item
 * This allows multiple items with the same SKU but different batches
 */

include("../../conexion.php");

echo "<h2>Removing UNIQUE constraint from items.sku_item</h2>";

// Check if the UNIQUE constraint exists
$check_sql = "SHOW INDEXES FROM items WHERE Key_name = 'idx_sku_item' AND Non_unique = 0";
$result = $mysqli->query($check_sql);

if ($result && $result->num_rows > 0) {
    echo "<p>UNIQUE constraint 'idx_sku_item' found. Removing it...</p>";
    
    // Remove the UNIQUE constraint
    $drop_sql = "ALTER TABLE items DROP INDEX idx_sku_item";
    
    if ($mysqli->query($drop_sql)) {
        echo "<p style='color: green;'>✓ Successfully removed UNIQUE constraint from sku_item</p>";
        
        // Add back as a regular (non-unique) index for performance
        $add_index_sql = "ALTER TABLE items ADD INDEX idx_sku_item (sku_item)";
        if ($mysqli->query($add_index_sql)) {
            echo "<p style='color: green;'>✓ Added regular index on sku_item for query performance</p>";
        } else {
            echo "<p style='color: orange;'>⚠ Warning: Could not add regular index: " . $mysqli->error . "</p>";
        }
        
    } else {
        echo "<p style='color: red;'>✗ Error removing constraint: " . $mysqli->error . "</p>";
    }
} else {
    echo "<p style='color: blue;'>ℹ UNIQUE constraint 'idx_sku_item' not found or already removed.</p>";
    
    // Check if a regular index exists
    $check_index_sql = "SHOW INDEXES FROM items WHERE Key_name = 'idx_sku_item'";
    $index_result = $mysqli->query($check_index_sql);
    
    if ($index_result && $index_result->num_rows > 0) {
        echo "<p style='color: green;'>✓ Regular index exists on sku_item</p>";
    } else {
        echo "<p>Adding regular index on sku_item...</p>";
        $add_index_sql = "ALTER TABLE items ADD INDEX idx_sku_item (sku_item)";
        if ($mysqli->query($add_index_sql)) {
            echo "<p style='color: green;'>✓ Added regular index on sku_item</p>";
        } else {
            echo "<p style='color: red;'>✗ Error adding index: " . $mysqli->error . "</p>";
        }
    }
}

echo "<hr><p><a href='additems.php'>← Back to Add Items</a></p>";

$mysqli->close();
?>
