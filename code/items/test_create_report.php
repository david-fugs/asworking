<?php
// Test version without authentication for debugging
include("../../conexion.php");

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log all POST data for debugging
error_log("Test POST data received: " . print_r($_POST, true));

// Verificar que sea una petición POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

// Test database connection
if ($mysqli->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $mysqli->connect_error]);
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

// Debug log
error_log("Parsed data: UPC=$upc_item, SKU=$sku_item, NewQty=$new_quantity, AddedQty=$added_quantity");

// Validar datos requeridos
if (empty($upc_item) || empty($sku_item)) {
    echo json_encode(['status' => 'error', 'message' => 'UPC and SKU are required', 'debug' => ['upc' => $upc_item, 'sku' => $sku_item]]);
    exit();
}

if ($new_quantity <= 0 || $added_quantity <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid quantity values', 'debug' => ['new_qty' => $new_quantity, 'added_qty' => $added_quantity]]);
    exit();
}

try {
    // Test: just return success for now to check if the call is working
    echo json_encode([
        'status' => 'success', 
        'message' => 'Test successful - data received correctly',
        'test_data' => [
            'upc' => $upc_item,
            'sku' => $sku_item,
            'new_quantity' => $new_quantity,
            'added_quantity' => $added_quantity
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error', 
        'message' => $e->getMessage()
    ]);
}
?>
