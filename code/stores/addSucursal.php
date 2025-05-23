<?php
include("../../conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Capturar datos del formulario
    $id_store = (int) $_POST["id_store"];
    $code_sucursal = $mysqli->real_escape_string($_POST["code_sucursal"]);

    // Insertar sucursal principal
    $sql_insert_sucursal = "INSERT INTO sucursal (id_store, code_sucursal) VALUES ('$id_store', '$code_sucursal')";

    if ($mysqli->query($sql_insert_sucursal)) {
        $id_sucursal = $mysqli->insert_id; // ID recién insertado

        // Procesar los 3 posibles rangos
        for ($i = 1; $i <= 3; $i++) {
            $rango = $_POST["rango_$i"] ?? null;
            $comision = $_POST["comision_$i"] ?? null;
            $cargo_fijo = $_POST["cargo_fijo_$i"] ?? null;

            // Solo insertar si se ha rellenado el rango y alguno de los valores
            if (!empty($rango) && (!empty($comision) || !empty($cargo_fijo))) {
                $rango = (float)$rango;
                $comision = is_numeric($comision) ? (float)$comision : 0;
                $cargo_fijo = is_numeric($cargo_fijo) ? (float)$cargo_fijo : 0;

                $sql_rango = "INSERT INTO fee_config_sucursal (id_sucursal, sales_less_than, comision, cargo_fijo)
                              VALUES ('$id_sucursal', '$rango', '$comision', '$cargo_fijo')";
                $mysqli->query($sql_rango);
            }
        }

        echo "<script>
                alert('Sucursal y rangos guardados correctamente');
                window.location.href = 'seeSucursal.php';
              </script>";
    } else {
        echo "<script>
                alert('Error al guardar sucursal: " . $mysqli->error . "');
                window.location.href = 'seeSucursal.php';
              </script>";
    }
} else {
    echo "<script>
            alert('Método no válido');
            window.location.href = 'seeSucursal.php';
          </script>";
}
