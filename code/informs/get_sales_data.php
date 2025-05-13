<?php
include("../../conexion.php");

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;
$store = $_GET['store'] ?? null;

$response = [];

if ($startDate && $endDate) {
    $query = "
        SELECT 
            DATE_FORMAT(date, '%Y-%m') AS month, 
            SUM(total_item) AS total_sales
        FROM sell
        WHERE date BETWEEN ? AND ?
    ";

    // Agregamos condición si $store tiene valor
    if (!empty($store)) {
        $query .= " AND id_store = ?";
    }

    $query .= " GROUP BY month ORDER BY month ASC";

    $stmt = $mysqli->prepare($query);

    // Asociamos los parámetros dinámicamente
    if (!empty($store)) {
        $stmt->bind_param("sss", $startDate, $endDate, $store);
    } else {
        $stmt->bind_param("ss", $startDate, $endDate);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }

    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($response);
