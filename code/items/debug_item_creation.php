<?php
// Debug script for checking item creation
include("../../conexion.php");

// Check specific UPC and SKU from error
$upc = "1231231";
$sku = "9L6CDU9RYQ";

echo "<h3>Debugging item creation for UPC: $upc, SKU: $sku</h3>";

// Check in items table
$check_sql = "SELECT * FROM items WHERE upc_item = ? AND sku_item = ?";
$check_stmt = $mysqli->prepare($check_sql);
$check_stmt->bind_param("ss", $upc, $sku);
$check_stmt->execute();
$result = $check_stmt->get_result();

echo "<h4>Items table check:</h4>";
if ($result->num_rows > 0) {
    echo "<p style='color: green'>FOUND in items table:</p>";
    $row = $result->fetch_assoc();
    echo "<pre>" . print_r($row, true) . "</pre>";
} else {
    echo "<p style='color: red'>NOT FOUND in items table</p>";
}

// Check in inventory table
$inv_sql = "SELECT * FROM inventory WHERE upc_inventory = ? AND sku_inventory = ?";
$inv_stmt = $mysqli->prepare($inv_sql);
$inv_stmt->bind_param("ss", $upc, $sku);
$inv_stmt->execute();
$inv_result = $inv_stmt->get_result();

echo "<h4>Inventory table check:</h4>";
if ($inv_result->num_rows > 0) {
    echo "<p style='color: green'>FOUND in inventory table:</p>";
    $row = $inv_result->fetch_assoc();
    echo "<pre>" . print_r($row, true) . "</pre>";
} else {
    echo "<p style='color: red'>NOT FOUND in inventory table</p>";
}

// Check in daily_report table
$report_sql = "SELECT * FROM daily_report WHERE upc_final_report = ? AND sku_report = ?";
$report_stmt = $mysqli->prepare($report_sql);
$report_stmt->bind_param("ss", $upc, $sku);
$report_stmt->execute();
$report_result = $report_stmt->get_result();

echo "<h4>Daily_report table check:</h4>";
if ($report_result->num_rows > 0) {
    echo "<p style='color: green'>FOUND in daily_report table:</p>";
    while ($row = $report_result->fetch_assoc()) {
        echo "<pre>" . print_r($row, true) . "</pre>";
    }
} else {
    echo "<p style='color: red'>NOT FOUND in daily_report table</p>";
}

echo "<h4>Testing item creation manually:</h4>";

// Try to create the item manually
$test_upc = $upc;
$test_sku = $sku;
$test_brand = "TEST BRAND";
$test_item = "TEST ITEM";
$test_ref = "TEST REF";
$test_color = "TEST COLOR";
$test_size = "TEST SIZE";
$test_category = "TEST CATEGORY";
$test_cost = "10.00";
$test_weight = "1.00";
$test_batch = "TEST BATCH";

$insert_sql = "INSERT INTO items (upc_item, sku_item, brand_item, item_item, ref_item, color_item, size_item, category_item, cost_item, weight_item, inventory_item, batch_item, quantity_inventory) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '', ?, 0)";
$insert_stmt = $mysqli->prepare($insert_sql);

if (!$insert_stmt) {
    echo "<p style='color: red'>Error preparing statement: " . $mysqli->error . "</p>";
} else {
    $insert_stmt->bind_param("sssssssssss", $test_upc, $test_sku, $test_brand, $test_item, $test_ref, $test_color, $test_size, $test_category, $test_cost, $test_weight, $test_batch);
    
    if ($insert_stmt->execute()) {
        echo "<p style='color: green'>Successfully created test item!</p>";
        
        // Now check if it was really created
        $verify_stmt = $mysqli->prepare($check_sql);
        $verify_stmt->bind_param("ss", $test_upc, $test_sku);
        $verify_stmt->execute();
        $verify_result = $verify_stmt->get_result();
        
        if ($verify_result->num_rows > 0) {
            echo "<p style='color: green'>Verified: Item now exists in items table</p>";
            $row = $verify_result->fetch_assoc();
            echo "<pre>" . print_r($row, true) . "</pre>";
        } else {
            echo "<p style='color: red'>ERROR: Item was not created despite successful insert</p>";
        }
    } else {
        echo "<p style='color: red'>Error creating item: " . $insert_stmt->error . "</p>";
    }
}

?>
