<?php
include("../../conexion.php");

$startDate = $_GET['start_date'] ?? null;
$endDate = $_GET['end_date'] ?? null;

$response = [];

if ($startDate && $endDate) {
    $query = "
        SELECT 
            DATE_FORMAT(date, '%Y-%m') AS month, 
            SUM(total_item) AS total_sales
        FROM sell
        WHERE date BETWEEN ? AND ?
        GROUP BY month
        ORDER BY month ASC
    ";

    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("ss", $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }

    $stmt->close();
}

header('Content-Type: application/json');
echo json_encode($response);
