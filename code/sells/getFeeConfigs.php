<?php
session_start();
include("../../conexion.php");

header("Content-Type: application/json");

if (!isset($_GET['id_sucursal'])) {
    echo json_encode(["success" => false, "message" => "ID de sucursal requerido"]);
    exit();
}

$id_sucursal = (int) $_GET['id_sucursal'];

try {
    $query = "SELECT id, sales_less_than, comision, cargo_fijo 
              FROM fee_config_sucursal 
              WHERE id_sucursal = ? 
              ORDER BY sales_less_than ASC";
    
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_sucursal);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $configs = [];
    while ($row = $result->fetch_assoc()) {
        $configs[] = [
            'id' => $row['id'],
            'sales_less_than' => (float) $row['sales_less_than'],
            'comision' => (float) $row['comision'],
            'cargo_fijo' => (float) $row['cargo_fijo']
        ];
    }
    
    echo json_encode(["success" => true, "configs" => $configs]);
    
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}

$mysqli->close();
?>
