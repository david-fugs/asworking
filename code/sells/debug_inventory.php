<?php
session_start();
include("../../conexion.php");

header('Content-Type: application/json');

$upc = "733004811005";

echo json_encode([
    "debug" => true,
    "upc_searched" => $upc,
    "inventory_structure" => "checking...",
    "inventory_records" => "checking..."
]);

// Agregar debug temporal
error_log("=== DEBUG INVENTORY ===");

// Ver estructura de inventory
$result = $mysqli->query("DESCRIBE inventory");
$inventory_structure = [];
while ($row = $result->fetch_assoc()) {
    $inventory_structure[] = $row;
    error_log("Inventory column: " . $row['Field']);
}

// Ver registros de inventory para este UPC
$stmt = $mysqli->prepare("SELECT * FROM inventory WHERE upc_inventory = ?");
$stmt->bind_param("s", $upc);
$stmt->execute();
$result = $stmt->get_result();
$inventory_records = [];
while ($row = $result->fetch_assoc()) {
    $inventory_records[] = $row;
    error_log("Inventory record: " . json_encode($row));
}

echo "\n\n" . json_encode([
    "inventory_structure" => $inventory_structure,
    "inventory_records" => $inventory_records
], JSON_PRETTY_PRINT);
?>
