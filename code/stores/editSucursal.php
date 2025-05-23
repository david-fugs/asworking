<?php 
include("../../conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id_sucursal = (int) $_POST["id_sucursal"];
    $store_id = (int) $_POST["store"];
    $code_sucursal = $mysqli->real_escape_string($_POST["code_sucursal"]);

    // Actualizar la tabla sucursal
    $sql_update_sucursal = "UPDATE sucursal SET 
                                id_store = $store_id,
                                code_sucursal = '$code_sucursal'
                            WHERE id_sucursal = $id_sucursal";

    if (!$mysqli->query($sql_update_sucursal)) {
        echo "<script>
                alert('Error updating sucursal: " . $mysqli->error . "');
                window.location.href = 'seeSucursal.php';
              </script>";
        exit;
    }

    // Actualizar rangos
    $rango_ids = $_POST['rango_id'];            // Array con IDs de rangos
    $sales_less_than_arr = $_POST['sales_less_than'];  // Array con ventas límite
    $fee_arr = $_POST['fee'];                    // Array con fee
    $fixed_charge_arr = $_POST['fixed_charge']; // Array con cargo fijo

    for ($i = 0; $i < count($rango_ids); $i++) {
        $rango_id = (int) $rango_ids[$i];
        $sales_less_than = isset($sales_less_than_arr[$i]) ? (float) $sales_less_than_arr[$i] : 0;
        $fee = isset($fee_arr[$i]) ? $mysqli->real_escape_string($fee_arr[$i]) : '';
        $fixed_charge = isset($fixed_charge_arr[$i]) ? $mysqli->real_escape_string($fixed_charge_arr[$i]) : '';

        // Solo actualizar si rango_id existe (no vacío o 0)
        if ($rango_id > 0) {
            $sql_update_rango = "UPDATE fee_config_sucursal  SET 
                                    sales_less_than = $sales_less_than,
                                    comision = '$fee',
                                    cargo_fijo = '$fixed_charge'
                                WHERE id = $rango_id";

            if (!$mysqli->query($sql_update_rango)) {
                echo "<script>
                        alert('Error updating rango ID $rango_id: " . $mysqli->error . "');
                        window.location.href = 'seeSucursal.php';
                      </script>";
                exit;
            }
        }
    }

    // Si todo OK
    echo "<script>
            alert('Update successful');
            window.location.href = 'seeSucursal.php';
          </script>";
} else {
    echo "<script>
            alert('Invalid request method');
            window.location.href = 'seeSucursal.php';
          </script>";
}
?>
