<?php
    session_start();

    if (!isset($_SESSION['id'])) {
        header("Location: index.php");
    }

    $usuario      = $_SESSION['usuario'];
    $nombre       = $_SESSION['nombre'];
    $tipo_usuario = $_SESSION['tipo_usuario'];

    include("../../conexion.php");
    header("Content-Type: text/html;charset=utf-8");
    date_default_timezone_set("America/Bogota");

    // Generar id_rec único de 9 caracteres
    $id_rec         = mb_strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 9));
    $date_rec       = $_POST['date_rec'];
    $upc_sku_item   = mb_strtoupper($_POST['upc_sku_item']);
    $cost_rec       = $_POST['cost_rec'];
    $inventory_rec   = mb_strtoupper($_POST['inventory_rec']);
    $estado_rec     = 1;
    $fecha_alta_rec = date('Y-m-d h:i:s');
    $fecha_edit_rec = '0000-00-00 00:00:00';
    $id_usu         = $_SESSION['id'];

    // Verificar si el upc_sku_item existe en la tabla items
    $check_item_sql = "SELECT * FROM items WHERE upc_sku_item = '$upc_sku_item'";
    $check_item_result = $mysqli->query($check_item_sql);

    if ($check_item_result->num_rows == 0) {
        // El upc_sku_item no existe en la tabla items
        echo " <!DOCTYPE html>
                        <html lang='es'>
                            <head>
                                <meta charset='utf-8' />
                                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                                <meta http-equiv='X-UA-Compatible' content='ie=edge'>
                                <link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet'>
                                <link href='https://fonts.googleapis.com/css?family=Orbitron' rel='stylesheet'>
                                <link rel='stylesheet' href='../../css/bootstrap.min.css'>
                                <link href='../../fontawesome/css/all.css' rel='stylesheet'>
                                <title>ASWWORKING</title>
                                <style>
                                    .responsive {
                                        max-width: 100%;
                                        height: auto;
                                    }
                                </style>
                            </head>
                            <body>
                                <center>
                                   <img src='../../img/logo.png' width=300 height=174 class='responsive'>
                                    <div class='container'>
                                    <br />
                                <h3><b><i class='fa-solid fa-triangle-exclamation'></i>ERROR:</b> UPC/SKU does not exist in items table.</h3><br />
                                    <p align='center'><a href='addrecord.php'><img src='../../img/atras.png' width=96 height=96></a></p>
                                </div>
                                </center>";
    } else {
        // El upc_sku_item existe en la tabla items, realizar la inserción en la tabla record
        // Validar si la clave ya existe
        $check_duplicate_sql = "SELECT * FROM record WHERE id_rec = '$id_rec'";
        $check_duplicate_result = $mysqli->query($check_duplicate_sql);

        if ($check_duplicate_result->num_rows > 0) 
        {
            // La clave ya existe, mostrar mensaje de error o redirigir a una página de error
            echo "<strong>CHECK THE UPC/SKU!</strong> THERE IS ALREADY THE SAME ONE.";
        } else 
        {
            // La clave no existe, realizar la inserción
            $sql = "INSERT INTO record (id_rec, date_rec, upc_sku_item, cost_rec, inventory_rec, estado_rec, fecha_alta_rec, fecha_edit_rec, id_usu) VALUES ('$id_rec', '$date_rec', '$upc_sku_item', '$cost_rec', '$inventory_rec', '$estado_rec', '$fecha_alta_rec', '$fecha_edit_rec', '$id_usu')";
            $resultado = $mysqli->query($sql);

            echo "                  <center>
                                   <img src='../../img/logo.png' width=300 height=174 class='responsive'>
                                    <div class='container'>
                                    <br />
                                    <h3><b><i class='fas fa-users'></i> THE PROCESS WAS SUCCESSFULLY REGISTERED</b></h3><br />
                                    <p align='center'><a href='../../access.php'><img src='../../img/atras.png' width=96 height=96></a></p>
                                </div>
                                </center>
                            </body>
                        </html>
            ";
        }
    }
?>
