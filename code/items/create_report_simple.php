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

    // Check if user is authenticated (only if needed)
    // Commented out for testing: if (!isset($_SESSION['id'])) {
    //     echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    //     exit();
    // }

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
    $upc_item = isset($_POST['upc_item']) ? trim($_POST['upc_item']) : '';
    $sku_item = isset($_POST['sku_item']) ? trim($_POST['sku_item']) : '';
    $new_quantity = isset($_POST['new_quantity']) ? intval($_POST['new_quantity']) : 0;
    $added_quantity = isset($_POST['added_quantity']) ? intval($_POST['added_quantity']) : 0;

    // Validate required data
    if (empty($upc_item) || empty($sku_item)) {
        echo json_encode(['status' => 'error', 'message' => 'UPC and SKU are required']);
        exit();
    }

    if ($new_quantity <= 0 || $added_quantity <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid quantity values']);
        exit();
    }

    // Start transaction
    $mysqli->autocommit(FALSE);

    // Update inventory table
    $check_sql = "SELECT id_inventory FROM inventory WHERE upc_inventory = ? AND sku_inventory = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    
    if (!$check_stmt) {
        throw new Exception("Error preparing check statement");
    }
    
    $check_stmt->bind_param("ss", $upc_item, $sku_item);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        // Update existing record
        // $update_sql = "UPDATE inventory SET quantity_inventory = ? WHERE upc_inventory = ? AND sku_inventory = ?";
        // $update_stmt = $mysqli->prepare($update_sql);
        // $update_stmt->bind_param("iss", $new_quantity, $upc_item, $sku_item);
        // $update_stmt->execute();
        // $update_stmt->close();
    } else {
        // Insert new record
        $insert_sql = "INSERT INTO inventory (upc_inventory, sku_inventory, quantity_inventory) VALUES (?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_sql);
        $insert_stmt->bind_param("ssi", $upc_item, $sku_item, $new_quantity);
        $insert_stmt->execute();
        $insert_stmt->close();
    }
    
    $check_stmt->close();

    // Get additional item details for daily_report
    $brand_item = isset($_POST['brand_item']) ? trim($_POST['brand_item']) : '';
    $item_item = isset($_POST['item_item']) ? trim($_POST['item_item']) : '';
    $color_item = isset($_POST['color_item']) ? trim($_POST['color_item']) : '';
    $size_item = isset($_POST['size_item']) ? trim($_POST['size_item']) : '';
    $ref_item = isset($_POST['ref_item']) ? trim($_POST['ref_item']) : '';
    $cost_item = isset($_POST['cost_item']) ? trim($_POST['cost_item']) : '';
    $batch_item = isset($_POST['batch_item']) ? trim($_POST['batch_item']) : '';
    $category_item = isset($_POST['category_item']) ? trim($_POST['category_item']) : '';
    $weight_item = isset($_POST['weight_item']) ? trim($_POST['weight_item']) : '';
    // current user id for FK on items.id_usu
    $user_id = isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;
    
    // Check if item exists in items table, if not create it
    $check_item_sql = "SELECT id_item FROM items WHERE upc_item = ? AND sku_item = ?";
    $check_item_stmt = $mysqli->prepare($check_item_sql);
    $check_item_stmt->bind_param("ss", $upc_item, $sku_item);
    $check_item_stmt->execute();
    $check_item_result = $check_item_stmt->get_result();
    
    if ($check_item_result->num_rows == 0) {
        // Item doesn't exist, create it
        // include id_usu to satisfy FK constraint (items.id_usu -> usuarios.id)
        $insert_item_sql = "INSERT INTO items (upc_item, sku_item, brand_item, item_item, ref_item, color_item, size_item, category_item, cost_item, weight_item, inventory_item, batch_item, id_usu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '', ?, ?)";
        $insert_item_stmt = $mysqli->prepare($insert_item_sql);

        if ($insert_item_stmt) {
            $insert_item_stmt->bind_param("sssssssssssi", $upc_item, $sku_item, $brand_item, $item_item, $ref_item, $color_item, $size_item, $category_item, $cost_item, $weight_item, $batch_item, $user_id);
            $insert_item_stmt->execute();
            if ($insert_item_stmt->error) {
                throw new Exception('Item insert error: ' . $insert_item_stmt->error);
            }
            $insert_item_stmt->close();
        }
    }
    $check_item_stmt->close();
    
    // Get current location/folder from items table
    $item_sql = "SELECT inventory_item, folder_item FROM items WHERE upc_item = ? AND sku_item = ? LIMIT 1";
    $item_stmt = $mysqli->prepare($item_sql);
    $item_stmt->bind_param("ss", $upc_item, $sku_item);
    $item_stmt->execute();
    $item_result = $item_stmt->get_result();
    
    $current_location = '';
    $current_folder = '';
    if ($item_row = $item_result->fetch_assoc()) {
        $current_location = $item_row['inventory_item'] ?? '';
        $current_folder = $item_row['folder_item'] ?? '';
    }
    $item_stmt->close();
    
    // Check if a record exists in daily_report with same UPC, SKU and estado_reporte = 0
    $check_report_sql = "SELECT id_report, quantity_report, observacion_report FROM daily_report 
                         WHERE upc_final_report = ? AND sku_report = ? AND estado_reporte = 0 
                         LIMIT 1";
    $check_report_stmt = $mysqli->prepare($check_report_sql);
    $check_report_stmt->bind_param("ss", $upc_item, $sku_item);
    $check_report_stmt->execute();
    $check_report_result = $check_report_stmt->get_result();
    
    if ($check_report_result->num_rows > 0) {
        // Record exists with estado_reporte = 0, UPDATE by adding the quantity
        $existing_report = $check_report_result->fetch_assoc();
        $existing_quantity = intval($existing_report['quantity_report']);
        $existing_observacion = $existing_report['observacion_report'] ?? '';
        $report_id = $existing_report['id_report'];
        
        // Add the new quantity to the existing one
        $updated_quantity = $existing_quantity + $added_quantity;
        
        // Extract the current quantity from observacion_report and add the new one
        // Format: "Added quantity: X"
        $current_added = 0;
        if (preg_match('/Added quantity:\s*(\d+)/', $existing_observacion, $matches)) {
            $current_added = intval($matches[1]);
        }
        $new_added_total = $current_added + $added_quantity;
        $updated_observacion = "Added quantity: " . $new_added_total;
        
        // Update the record with new quantity and observacion
        $update_report_sql = "UPDATE daily_report SET quantity_report = ?, observacion_report = ?, fecha_alta_reporte = ? 
                              WHERE id_report = ?";
        $update_report_stmt = $mysqli->prepare($update_report_sql);
        $fecha_alta = date('Y-m-d H:i:s');
        $update_report_stmt->bind_param("issi", $updated_quantity, $updated_observacion, $fecha_alta, $report_id);
        $update_report_stmt->execute();
        $update_report_stmt->close();
        
    } else {
        // No existing record found, INSERT a new one
        $fecha_alta = date('Y-m-d H:i:s');
        
        // Check what columns exist in daily_report table
        $columns_sql = "SHOW COLUMNS FROM daily_report";
        $columns_result = $mysqli->query($columns_sql);
        $available_columns = [];
        while ($col = $columns_result->fetch_assoc()) {
            $available_columns[] = $col['Field'];
        }
        
        // Build dynamic INSERT based on available columns
        $insert_columns = [];
        $insert_values = [];
        $bind_types = '';
        $bind_params = [];
        
        // Required columns
        if (in_array('fecha_alta_reporte', $available_columns)) {
            $insert_columns[] = 'fecha_alta_reporte';
            $insert_values[] = '?';
            $bind_types .= 's';
            $bind_params[] = $fecha_alta;
        }
        
        if (in_array('upc_final_report', $available_columns)) {
            $insert_columns[] = 'upc_final_report';
            $insert_values[] = '?';
            $bind_types .= 's';
            $bind_params[] = $upc_item;
        }
        
        if (in_array('quantity_report', $available_columns)) {
            $insert_columns[] = 'quantity_report';
            $insert_values[] = '?';
            $bind_types .= 'i';
            $bind_params[] = $added_quantity; // Use only the added quantity, not new_quantity
        }
        
        if (in_array('sku_report', $available_columns)) {
            $insert_columns[] = 'sku_report';
            $insert_values[] = '?';
            $bind_types .= 's';
            $bind_params[] = $sku_item;
        }
        
        if (in_array('item_report', $available_columns)) {
            $insert_columns[] = 'item_report';
            $insert_values[] = '?';
            $bind_types .= 's';
            $bind_params[] = $item_item;
        }
        
        if (in_array('brand_report', $available_columns)) {
            $insert_columns[] = 'brand_report';
            $insert_values[] = '?';
            $bind_types .= 's';
            $bind_params[] = $brand_item;
        }
        
        if (in_array('vendor_report', $available_columns)) {
            $insert_columns[] = 'vendor_report';
            $insert_values[] = '?';
            $bind_types .= 's';
            $bind_params[] = $ref_item; // Use ref_item for style/vendor
        }
        
        // Store quantity information in observacion_report for display in Observation column
        if (in_array('observacion_report', $available_columns)) {
            $observacion_text = "Added quantity: " . $added_quantity;
            $insert_columns[] = 'observacion_report';
            $insert_values[] = '?';
            $bind_types .= 's';
            $bind_params[] = $observacion_text;
        }
        
        if (in_array('color_report', $available_columns)) {
            $insert_columns[] = 'color_report';
            $insert_values[] = '?';
            $bind_types .= 's';
            $bind_params[] = $color_item;
        }
        
        if (in_array('size_report', $available_columns)) {
            $insert_columns[] = 'size_report';
            $insert_values[] = '?';
            $bind_types .= 's';
            $bind_params[] = $size_item;
        }
        
        if (in_array('folder_report', $available_columns)) {
            $insert_columns[] = 'folder_report';
            $insert_values[] = '?';
            $bind_types .= 's';
            $bind_params[] = $current_folder;
        }
        
        if (in_array('loc_report', $available_columns)) {
            $insert_columns[] = 'loc_report';
            $insert_values[] = '?';
            $bind_types .= 's';
            $bind_params[] = $current_location;
        }
        
        // Set estado_reporte = 0 for editing
        if (in_array('estado_reporte', $available_columns)) {
            $insert_columns[] = 'estado_reporte';
            $insert_values[] = '?';
            $bind_types .= 'i';
            $bind_params[] = 0;
        }
        
        // Create the INSERT statement
        $report_sql = "INSERT INTO daily_report (" . implode(', ', $insert_columns) . ") VALUES (" . implode(', ', $insert_values) . ")";
        $report_stmt = $mysqli->prepare($report_sql);
        
        if ($report_stmt && !empty($bind_params)) {
            $report_stmt->bind_param($bind_types, ...$bind_params);
            $report_stmt->execute();
            $report_stmt->close();
        }
    }
    
    $check_report_stmt->close();

    // For now, let's just return success without creating daily_report entry
    // We can add that later once this basic version works
    
    $mysqli->commit();
    $mysqli->autocommit(TRUE);

    echo json_encode([
        'status' => 'success',
        'message' => 'Quantity updated successfully',
        'new_quantity' => $new_quantity,
        'added_quantity' => $added_quantity
    ]);

} catch (Exception $e) {
    if (isset($mysqli)) {
        $mysqli->rollback();
        $mysqli->autocommit(TRUE);
    }
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
