<?php
session_start();

// Clean any previous output
if (ob_get_level()) {
    ob_end_clean();
}

// Set JSON header first
header('Content-Type: application/json');

// Disable HTML error output
ini_set('display_errors', 0);
error_reporting(0);

try {
    include("../../conexion.php");

    // Check if user is authenticated
    if (!isset($_SESSION['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
        exit();
    }

    // Verify POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
        exit();
    }

    // Check database connection
    if ($mysqli->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
        exit();
    }

    // Get POST data
    $upc_item = isset($_POST['upc_item']) ? trim(strtoupper($_POST['upc_item'])) : '';
    $sku_item = isset($_POST['sku_item']) ? trim(strtoupper($_POST['sku_item'])) : '';
    $date_item = isset($_POST['date_item']) ? trim($_POST['date_item']) : date('Y-m-d');
    $brand_item = isset($_POST['brand_item']) ? trim(strtoupper($_POST['brand_item'])) : '';
    $item_item = isset($_POST['item_item']) ? trim($_POST['item_item']) : '';
    $ref_item = isset($_POST['ref_item']) ? trim(strtoupper($_POST['ref_item'])) : '';
    $color_item = isset($_POST['color_item']) ? trim(strtoupper($_POST['color_item'])) : '';
    $size_item = isset($_POST['size_item']) ? trim(strtoupper($_POST['size_item'])) : '';
    $category_item = isset($_POST['category_item']) ? trim(strtoupper($_POST['category_item'])) : '';
    $cost_item = isset($_POST['cost_item']) ? floatval($_POST['cost_item']) : 0;
    $weight_item = isset($_POST['weight_item']) ? trim(strtoupper($_POST['weight_item'])) : '';
    $batch_item = isset($_POST['batch_item']) ? trim(strtoupper($_POST['batch_item'])) : '';
    $inventory_item = isset($_POST['inventory_item']) ? trim(strtoupper($_POST['inventory_item'])) : '';
    $quantity_inventory = isset($_POST['quantity_inventory']) ? intval($_POST['quantity_inventory']) : 0;
    $observation_inventory = isset($_POST['observation_inventory']) ? trim($_POST['observation_inventory']) : '';
    $stores = isset($_POST['stores']) ? $_POST['stores'] : [];
    $user_id = $_SESSION['id'];

    // Validate required data
    if (empty($upc_item) || empty($sku_item) || empty($brand_item) || empty($item_item) || empty($batch_item)) {
        echo json_encode(['status' => 'error', 'message' => 'UPC, SKU, Brand, Item, and Batch are required']);
        exit();
    }

    if ($quantity_inventory <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Quantity must be greater than 0']);
        exit();
    }

    // Check if item with same UPC, SKU AND BATCH already exists
    $check_sql = "SELECT id_item FROM items WHERE upc_item = ? AND sku_item = ? AND batch_item = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    $check_stmt->bind_param("sss", $upc_item, $sku_item, $batch_item);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'An item with this UPC, SKU, and Batch already exists. Please use a different batch number.'
        ]);
        $check_stmt->close();
        exit();
    }
    $check_stmt->close();

    // Start transaction
    $mysqli->autocommit(FALSE);

    // Convert stores array to JSON
    $stores_json = json_encode($stores);

    // Insert into items table
    $insert_item_sql = "INSERT INTO items (
        upc_item, sku_item, date_item, brand_item, item_item, ref_item, 
        color_item, size_item, category_item, cost_item, weight_item, 
        batch_item, inventory_item, observation_item, stores_item, 
        quantity_item, estado_item, fecha_alta_item, id_usu
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), ?)";
    
    $insert_item_stmt = $mysqli->prepare($insert_item_sql);
    
    if (!$insert_item_stmt) {
        throw new Exception("Error preparing item insert statement: " . $mysqli->error);
    }
    
    $insert_item_stmt->bind_param(
        "sssssssssssssssii",
        $upc_item,
        $sku_item,
        $date_item,
        $brand_item,
        $item_item,
        $ref_item,
        $color_item,
        $size_item,
        $category_item,
        $cost_item,
        $weight_item,
        $batch_item,
        $inventory_item,
        $observation_inventory,
        $stores_json,
        $quantity_inventory,
        $user_id
    );
    
    if (!$insert_item_stmt->execute()) {
        throw new Exception("Error inserting item: " . $insert_item_stmt->error);
    }
    
    $new_item_id = $insert_item_stmt->insert_id;
    $insert_item_stmt->close();

    // Insert into inventory table
    $insert_inventory_sql = "INSERT INTO inventory (
        upc_inventory, sku_inventory, brand_inventory, item_inventory, 
        ref_inventory, color_inventory, size_inventory, cost_inventory, 
        quantity_inventory, observation_inventory, costo_inventario, 
        estado_inventory, fecha_alta_inventory
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW())";
    
    $insert_inventory_stmt = $mysqli->prepare($insert_inventory_sql);
    
    if (!$insert_inventory_stmt) {
        throw new Exception("Error preparing inventory insert statement: " . $mysqli->error);
    }
    
    $insert_inventory_stmt->bind_param(
        "ssssssssissd",
        $upc_item,
        $sku_item,
        $brand_item,
        $item_item,
        $ref_item,
        $color_item,
        $size_item,
        $cost_item,
        $quantity_inventory,
        $observation_inventory,
        $cost_item
    );
    
    if (!$insert_inventory_stmt->execute()) {
        throw new Exception("Error inserting inventory: " . $insert_inventory_stmt->error);
    }
    
    $insert_inventory_stmt->close();

    // Commit transaction
    $mysqli->commit();
    $mysqli->autocommit(TRUE);

    echo json_encode([
        'status' => 'success',
        'message' => 'New batch created successfully',
        'item_id' => $new_item_id,
        'upc' => $upc_item,
        'sku' => $sku_item,
        'batch' => $batch_item
    ]);

} catch (Exception $e) {
    // Rollback on error
    if (isset($mysqli)) {
        $mysqli->rollback();
        $mysqli->autocommit(TRUE);
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Error creating new batch: ' . $e->getMessage()
    ]);
}
?>
