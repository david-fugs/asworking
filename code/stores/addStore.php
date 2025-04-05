<?php
include("../../conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Capturar datos del formulario
    $name_store = $mysqli->real_escape_string($_POST["store_name"]);
    $sql_insert_store = "INSERT INTO store (store_name) VALUES ('$name_store')";
    // Ejecutar consulta
    if ($mysqli->query( $sql_insert_store)) {
        echo "<script>
            alert('Update successful');
            window.location.href = 'seeStore.php';
          </script>";
    } else {
        echo "<script>
            alert('Error  " . $mysqli->error . "');
            window.location.href = 'seeStore.php';
          </script>";
    }
} else {
    echo "<script>
            alert('Method not valid');
            window.location.href = 'seeStore.php';
          </script>";
}
