<?php
include("../../conexion.php");
header('Content-Type: application/json');

function generateUniqueSKU($mysqli) {
    $max_attempts = 100; // Máximo número de intentos para evitar bucle infinito
    $attempts = 0;
    
    do {
        // Generar SKU de 10 dígitos aleatorio
        $sku = '';
        for ($i = 0; $i < 10; $i++) {
            $sku .= rand(0, 9);
        }
        
        // Verificar que no exista en la tabla items
        $check_items = "SELECT COUNT(*) as count FROM items WHERE sku_item = '$sku'";
        $result_items = $mysqli->query($check_items);
        $exists_items = $result_items->fetch_assoc()['count'] > 0;
        
        // Verificar que no exista en la tabla inventory (si existe esta tabla)
        $exists_inventory = false;
        $check_inventory = "SELECT COUNT(*) as count FROM inventory WHERE sku_inventory = '$sku'";
        $result_inventory = $mysqli->query($check_inventory);
        if ($result_inventory) {
            $exists_inventory = $result_inventory->fetch_assoc()['count'] > 0;
        }
        
        $attempts++;
        
    } while (($exists_items || $exists_inventory) && $attempts < $max_attempts);
    
    if ($attempts >= $max_attempts) {
        return false; // No se pudo generar un SKU único
    }
    
    return $sku;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sku = generateUniqueSKU($mysqli);
    
    if ($sku) {
        echo json_encode([
            'success' => true,
            'sku' => $sku
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Could not generate unique SKU'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}

$mysqli->close();
?>
