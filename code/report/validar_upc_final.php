<?php
include("../../conexion.php");
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['upc_final'])) {
    $upc_final = $mysqli->real_escape_string(trim($_POST['upc_final']));
    
    // Verificar si el UPC final existe en la tabla items
    $query = "SELECT COUNT(*) as count FROM items WHERE upc_item = '$upc_final'";
    $result = $mysqli->query($query);
    
    if ($result) {
        $row = $result->fetch_assoc();
        $exists = $row['count'] > 0;
        
        echo json_encode([
            'exists' => $exists,
            'message' => $exists ? 'UPC Final already exists' : 'UPC Final is available'
        ]);
    } else {
        echo json_encode([
            'exists' => false,
            'message' => 'Error checking UPC Final',
            'error' => $mysqli->error
        ]);
    }
} else {
    echo json_encode([
        'exists' => false,
        'message' => 'Invalid request'
    ]);
}

$mysqli->close();
?>
