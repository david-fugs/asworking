<?php

include("../../conexion.php");

if (isset($_GET['id_sell'])) {
    $id_sell = $_GET['id_sell'];
    $response = [];
    // Consulta principal
    $sql = "SELECT s.*, r.product_charge, r.shipping_paid, r.tax_return, r.selling_fee_refund,r.refund_administration_fee, r.other_refund_fee, r.return_cost, r.buyer_comments, r.quantity AS return_quantity
            FROM sell AS s
            LEFT JOIN returns as r ON s.id_sell = r.id_sell
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