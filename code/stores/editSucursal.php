<?php
include("../../conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Capturar datos del formulario
    $id_sucursal = (int) $_POST["id_sucursal"];
    $name = $mysqli->real_escape_string($_POST["name"]);
    $code_sucursal = $mysqli->real_escape_string($_POST["code_sucursal"]);
    $comision_sucursal = $mysqli->real_escape_string($_POST["comision_sucursal"]);

    // Actualizar la tabla items (sin cambiar quantity_inventory)
    $sql_update_items = "UPDATE sucursal SET 
                            code_sucursal = '$code_sucursal', 
                            comision_sucursal = '$comision_sucursal'
                        WHERE id_sucursal = $id_sucursal";


// Ejecutar consulta
if ($mysqli->query($sql_update_items)) {
    echo "<script>
            alert('Update successful');
            window.location.href = 'seeSucursal.php';
          </script>";
} else {
    echo "<script>
            alert('Error : " . $mysqli->error . "');
            window.location.href = 'seeSucursal.php';
          </script>";
}
} else {
    echo "<script>
            alert('Not valid method');
            window.location.href = 'seeSucursal.php';
          </script>";
}