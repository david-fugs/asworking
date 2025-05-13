<?php
include("../../conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Capturar datos del formulario
    $id_store = (int) $_POST["id_store"];
    $code_sucursal = $mysqli->real_escape_string($_POST["code_sucursal"]);
    $comision_sucursal = $mysqli->real_escape_string($_POST["comision_sucursal"]);
    $cargo_fijo = $mysqli->real_escape_string($_POST["cargo_fijo"]);

    $sql_insert_sucursal = "INSERT INTO sucursal (id_store,code_sucursal, comision_sucursal,cargo_fijo) VALUES ( '$id_store','$code_sucursal', '$comision_sucursal', '$cargo_fijo')";

    // Ejecutar consulta
    if ($mysqli->query( $sql_insert_sucursal)) {
        echo "<script>
            alert('Insert successful');
            window.location.href = 'seeSucursal.php';
          </script>";
    } else {
        echo "<script>
            alert('Error  " . $mysqli->error . "');
            window.location.href = 'seeSucursal.php';
          </script>";
    }
} else {
    echo "<script>
            alert('Method not valid');
            window.location.href = 'seeSucursal.php';
          </script>";
}
