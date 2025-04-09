<?php
// Include database connection
include_once '../../conexion.php';
if (isset($_POST['upc_item'])) {
    $upc_item = strtoupper(trim($_POST['upc_item']));

    $stmt = $mysqli->prepare("SELECT upc_item FROM items WHERE upc_item = ?");
    $stmt->bind_param("s", $upc_item);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo 'existe';
    } else {
        echo 'no_existe';
    }

    $stmt->close();
}
?>