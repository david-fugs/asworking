<?php
include("../../conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Capturar datos del formulario
    $id_store = (int) $_POST["id_store"];
    $name = $mysqli->real_escape_string($_POST["name"]);

    // Actualizar la tabla items (sin cambiar quantity_inventory)
    $sql_update_items = "UPDATE store SET 
                            store_name = '$name'
                        WHERE id_store = $id_store";
    // Ejecutar consulta
    if ($mysqli->query($sql_update_items)) {
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
