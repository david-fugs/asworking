<?php
include("../../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_sell'])) {
    $id_sell = $_POST['id_sell'];
    
    try {
        $stmt = $mysqli->prepare("UPDATE sell SET estado_sell = 0 WHERE id_sell = ?");
        $stmt->bind_param("i", $id_sell);
        
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
        
        $stmt->close();
    } catch (Exception $e) {
        echo "error: " . $e->getMessage();
    }
} else {
    echo "invalid request";
}

$mysqli->close();
?>
