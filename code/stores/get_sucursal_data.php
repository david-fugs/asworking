<?php
include("../../conexion.php");

header('Content-Type: application/json');

if (isset($_GET['id_sucursal'])) {
    $id_sucursal = (int) $_GET['id_sucursal'];
    
    $query = "SELECT items_price, shipping_received, tax, incentives_offered 
              FROM sucursal 
              WHERE id_sucursal = ?";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_sucursal);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo json_encode($row);
    } else {
        echo json_encode([
            'items_price' => 0,
            'shipping_received' => 0,
            'tax' => 0,
            'incentives_offered' => 0
        ]);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'No id_sucursal provided']);
}
?>
