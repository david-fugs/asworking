<?php
session_start();
include("../../conexion.php");

// Limpiar cualquier output previo
ob_clean();

header('Content-Type: application/json');

// Deshabilitar la salida de errores HTML
ini_set('display_errors', 0);
error_reporting(0);

// Log all POST data for debugging
error_log("POST data received: " . print_r($_POST, true));

// For testing purposes, allow without authentication but use default values
$usuario_reporte = 'system';
if (isset($_SESSION['usuario'])) {
    $usuario_reporte = $_SESSION['usuario'];
}

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Obtener los datos del POST
$upc_item = isset($_POST['upc_item']) ? trim($_POST['upc_item']) : '';
$sku_item = isset($_POST['sku_item']) ? trim($_POST['sku_item']) : '';
$brand_item = isset($_POST['brand_item']) ? trim($_POST['brand_item']) : '';
$item_item = isset($_POST['item_item']) ? trim($_POST['item_item']) : '';
$ref_item = isset($_POST['ref_item']) ? trim($_POST['ref_item']) : '';
$color_item = isset($_POST['color_item']) ? trim($_POST['color_item']) : '';
$size_item = isset($_POST['size_item']) ? trim($_POST['size_item']) : '';
$category_item = isset($_POST['category_item']) ? trim($_POST['category_item']) : '';
$weight_item = isset($_POST['weight_item']) ? trim($_POST['weight_item']) : '';
$cost_item = isset($_POST['cost_item']) ? trim($_POST['cost_item']) : '';
$batch_item = isset($_POST['batch_item']) ? trim($_POST['batch_item']) : '';
$current_quantity = isset($_POST['current_quantity']) ? intval($_POST['current_quantity']) : 0;
$new_quantity = isset($_POST['new_quantity']) ? intval($_POST['new_quantity']) : 0;
$added_quantity = isset($_POST['added_quantity']) ? intval($_POST['added_quantity']) : 0;

// Validar datos requeridos
if (empty($upc_item) || empty($sku_item)) {
    echo json_encode(['status' => 'error', 'message' => 'UPC and SKU are required']);
    exit();
}

if ($new_quantity <= 0 || $added_quantity <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid quantity values']);
    exit();
}

try {
    // Iniciar transacción
    $mysqli->autocommit(FALSE);

    // 1. Primero, actualizar la cantidad en la tabla inventory
    // Verificar si existe el registro en inventory
    $stmt = $mysqli->prepare("SELECT id_inventory FROM inventory WHERE upc_inventory = ? AND sku_inventory = ?");
    if (!$stmt) {
        throw new Exception("Error preparing inventory check statement: " . $mysqli->error);
    }
    
    $stmt->bind_param("ss", $upc_item, $sku_item);
    if (!$stmt->execute()) {
        throw new Exception("Error checking inventory: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // El registro existe, actualizar
        $stmt->close();
        $stmt = $mysqli->prepare("UPDATE inventory SET quantity_inventory = ? WHERE upc_inventory = ? AND sku_inventory = ?");
        if (!$stmt) {
            throw new Exception("Error preparing inventory update statement: " . $mysqli->error);
        }
        
        $stmt->bind_param("iss", $new_quantity, $upc_item, $sku_item);
        if (!$stmt->execute()) {
            throw new Exception("Error updating inventory table: " . $stmt->error);
        }
    } else {
        // El registro no existe, crear uno nuevo
        $stmt->close();
        $stmt = $mysqli->prepare("INSERT INTO inventory (upc_inventory, sku_inventory, quantity_inventory) VALUES (?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Error preparing inventory insert statement: " . $mysqli->error);
        }
        
        $stmt->bind_param("ssi", $upc_item, $sku_item, $new_quantity);
        if (!$stmt->execute()) {
            throw new Exception("Error inserting into inventory table: " . $stmt->error);
        }
    }
    $stmt->close();

    // 2. Obtener información adicional del item (folder actual, location actual)
    $stmt = $mysqli->prepare("SELECT inventory_item, folder_item FROM items WHERE upc_item = ? AND sku_item = ? LIMIT 1");
    if (!$stmt) {
        throw new Exception("Error preparing select statement: " . $mysqli->error);
    }
    
    $stmt->bind_param("ss", $upc_item, $sku_item);
    if (!$stmt->execute()) {
        throw new Exception("Error getting item details: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $item_details = $result->fetch_assoc();
    $stmt->close();

    $current_location = isset($item_details['inventory_item']) ? $item_details['inventory_item'] : '';
    $current_folder = isset($item_details['folder_item']) ? $item_details['folder_item'] : '';

    // 3. Crear entrada en daily_report para edición de location/folder
    $fecha_alta_reporte = date('Y-m-d H:i:s');
    
    // Usar vendor_report para almacenar información sobre la cantidad agregada
    $vendor_info = "Added: {$added_quantity} units (Total: {$new_quantity})";
    
    $stmt = $mysqli->prepare("
        INSERT INTO daily_report (
            fecha_alta_reporte, 
            upc_final_report, 
            quantity_report, 
            sku_report, 
            item_report, 
            brand_report, 
            vendor_report, 
            color_report, 
            size_report, 
            folder_report, 
            loc_report, 
            estado_reporte,
            fecha_modificacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, NOW())
    ");
    
    if (!$stmt) {
        throw new Exception("Error preparing daily_report insert statement: " . $mysqli->error);
    }
    
    $stmt->bind_param("sissssssss", 
        $fecha_alta_reporte,
        $upc_item, 
        $new_quantity, 
        $sku_item, 
        $item_item, 
        $brand_item, 
        $vendor_info, 
        $color_item, 
        $size_item, 
        $current_folder, 
        $current_location
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error inserting into daily_report: " . $stmt->error);
    }
    
    $report_id = $mysqli->insert_id;
    $stmt->close();

    // Confirmar transacción
    $mysqli->commit();
    $mysqli->autocommit(TRUE);

    echo json_encode([
        'status' => 'success', 
        'message' => 'Report created successfully for location editing',
        'report_id' => $report_id,
        'new_quantity' => $new_quantity,
        'added_quantity' => $added_quantity
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    $mysqli->rollback();
    $mysqli->autocommit(TRUE);
    
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}
?>
