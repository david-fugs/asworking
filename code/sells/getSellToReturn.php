<?php

include("../../conexion.php");

if (isset($_GET['id_sell'])) {
    $id_sell = $_GET['id_sell'];
    $response = [];
    // Consulta principal
    $sql = "SELECT s.*
            FROM sell AS s
            WHERE s.id_sell = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $id_sell);
    $stmt->execute();
    $result = $stmt->get_result();
    $response['items'] = $result->fetch_all(MYSQLI_ASSOC);
    

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'No id_sell provided']);
}