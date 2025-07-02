<?php
include 'conexion.php';

echo "=== Adding upc_item field to cancellations table ===\n\n";

// Check if upc_item column already exists
$result = $mysqli->query("SHOW COLUMNS FROM cancellations LIKE 'upc_item'");
if($result->num_rows > 0) {
    echo "⚠️  upc_item column already exists in cancellations table\n";
} else {
    echo "Adding upc_item column to cancellations table...\n";
    
    $alter_sql = "ALTER TABLE cancellations ADD COLUMN upc_item VARCHAR(255) NULL AFTER order_id";
    
    if($mysqli->query($alter_sql)) {
        echo "✅ upc_item column added successfully!\n";
    } else {
        echo "❌ Error adding upc_item column: " . $mysqli->error . "\n";
        exit;
    }
}

echo "\n=== New table structure ===\n";
$result = $mysqli->query('DESCRIBE cancellations');
while($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' - ' . $row['Type'] . ' - ' . $row['Null'] . ' - ' . $row['Default'] . "\n";
}

echo "\n=== Updating existing records with UPC data ===\n";

// Get existing cancellations and update them with UPC from sell table
$existing_cancellations = $mysqli->query("SELECT id, order_id FROM cancellations WHERE upc_item IS NULL OR upc_item = ''");

$updated = 0;
while($cancellation = $existing_cancellations->fetch_assoc()) {
    $order_id = $cancellation['order_id'];
    $cancellation_id = $cancellation['id'];
    
    // Get UPC from sell table for this order
    $sell_query = "SELECT upc_item FROM sell WHERE sell_order = ? AND estado_sell = 1 LIMIT 1";
    $sell_stmt = $mysqli->prepare($sell_query);
    $sell_stmt->bind_param("s", $order_id);
    $sell_stmt->execute();
    $sell_result = $sell_stmt->get_result();
    
    if($sell_row = $sell_result->fetch_assoc()) {
        $upc_item = $sell_row['upc_item'];
        
        // Update cancellation record with UPC
        $update_stmt = $mysqli->prepare("UPDATE cancellations SET upc_item = ? WHERE id = ?");
        $update_stmt->bind_param("si", $upc_item, $cancellation_id);
        
        if($update_stmt->execute()) {
            echo "✅ Updated cancellation ID $cancellation_id (order: $order_id) with UPC: $upc_item\n";
            $updated++;
        } else {
            echo "❌ Error updating cancellation ID $cancellation_id: " . $mysqli->error . "\n";
        }
    } else {
        echo "⚠️  No UPC found for order: $order_id\n";
    }
}

echo "\n✅ Updated $updated cancellation records with UPC data\n";

echo "\n=== Sample data after update ===\n";
$result = $mysqli->query('SELECT * FROM cancellations LIMIT 3');
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . " | Order: " . $row['order_id'] . " | UPC: " . ($row['upc_item'] ?: 'NULL') . 
         " | Date: " . ($row['cancellation_date'] ?: 'NULL') . " | Net: $" . number_format($row['net_cancellation'], 2) . "\n";
}

echo "\n=== Migration completed ===\n";
$mysqli->close();
?>
