<?php
// Include database connection
include_once '../../conexion.php';

function generateRandomSKU($length = 8) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
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
        $sku = generateRandomSKU(8); // 8 caracteres alfanuméricos
        $attempts++;
        if ($attempts >= $maxAttempts) {
            // Fallback: timestamp + random
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
