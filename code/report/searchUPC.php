<?php
session_start();
include("../../conexion.php");

// Verify user is logged in
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

// Verify UPC was received
if (!isset($_POST['upc']) || empty(trim($_POST['upc']))) {
    echo json_encode(['success' => false, 'message' => 'UPC not provided']);
    exit();
}

$upc = trim($_POST['upc']);

try {
    // Search in items table by UPC
    $sql = "SELECT * FROM items WHERE upc_item = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Error preparing query: " . $mysqli->error);
    }
    
    $stmt->bind_param("s", $upc);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();        // Response structure with item information
        $response = [
            'success' => true,
            'item' => [
                'id' => $item['id_item'] ?? '',
                'upc' => $item['upc_item'] ?? '',
                'sku' => $item['sku_item'] ?? '',
                'item' => $item['item_item'] ?? '',
                'brand' => $item['brand_item'] ?? '',
                'ref' => $item['ref_item'] ?? '',
                'color' => $item['color_item'] ?? '',
                'size' => $item['size_item'] ?? '',
                'category' => $item['category_item'] ?? '',
                'weight' => $item['weight_item'] ?? '',
                'inventory' => $item['inventory_item'] ?? 0,
                'status' => $item['estado_item'] ?? 0
            ]
        ];
    } else {
        $response = [
            'success' => false,
            'message' => 'No product found with the provided UPC: ' . htmlspecialchars($upc)
        ];
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Error in searchUPC.php: " . $e->getMessage());
    $response = [
        'success' => false,
        'message' => 'Internal server error. Please try again.'
    ];
}

// Send JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
