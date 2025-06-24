<?php
// Include database connection
include_once '../../conexion.php';

function generateRandomSKU() {
    // Generate a random 10-digit number
    return str_pad(mt_rand(1, 9999999999), 10, '0', STR_PAD_LEFT);
}

function isSkuUnique($sku, $mysqli) {
    $stmt = $mysqli->prepare("SELECT sku_item FROM items WHERE sku_item = ?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $stmt->store_result();
    
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    
    return !$exists;
}

function generateUniqueSKU($mysqli) {
    $maxAttempts = 100; // Prevent infinite loop
    $attempts = 0;
    
    do {
        $sku = generateRandomSKU();
        $attempts++;
        
        if ($attempts >= $maxAttempts) {
            // If we can't find a unique SKU after many attempts, 
            // use timestamp + random number as fallback
            $sku = date('YmdHis') . mt_rand(1000, 9999);
            break;
        }
        
    } while (!isSkuUnique($sku, $mysqli));
    
    return $sku;
}

// If called via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate_sku') {
    $uniqueSku = generateUniqueSKU($mysqli);
    echo json_encode(['status' => 'success', 'sku' => $uniqueSku]);
    exit;
}
?>
