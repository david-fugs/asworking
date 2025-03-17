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

    $upc_sku_item       = mb_strtoupper($_POST['upc_sku_item']);
    $date_item          = $_POST['date_item'];
    $brand_item         = mb_strtoupper($_POST['brand_item']);
    $item_item          = mb_strtoupper($_POST['item_item']);
    $ref_item           = mb_strtoupper($_POST['ref_item']);
    $color_item         = mb_strtoupper($_POST['color_item']);
    $size_item          = mb_strtoupper($_POST['size_item']);
    $category_item      = mb_strtoupper($_POST['category_item']);
    $cost_item          = $_POST['cost_item'];
    $weight_item        = mb_strtoupper($_POST['weight_item']);
    $inventory_item     = mb_strtoupper($_POST['inventory_item']);
    $estado_item        = 1;
    $fecha_alta_item    = date('Y-m-d h:i:s');
    $fecha_edit_item    = '0000-00-00 00:00:00';
    $id_usu             = $_SESSION['id'];

    // Validar si la clave ya existe
    $check_duplicate_sql = "SELECT * FROM items WHERE upc_sku_item = '$upc_sku_item'";
    $check_duplicate_result = $mysqli->query($check_duplicate_sql);

    if ($check_duplicate_result->num_rows > 0) 
    {
        // La clave ya existe, mostrar mensaje de error o redirigir a una página de error
        echo "<strong>CHECK THE UPC/SKU!</strong> THERE IS ALREADY THE SAME ONE.";
    } else 
    {
        // La clave no existe, realizar la inserción
        $sql = "INSERT INTO items (upc_sku_item, date_item, brand_item, item_item, ref_item, color_item, size_item, category_item, cost_item, weight_item, inventory_item, estado_item, fecha_alta_item, fecha_edit_item, id_usu) VALUES ('$upc_sku_item', '$date_item', '$brand_item', '$item_item', '$ref_item','$color_item','$size_item', '$category_item', '$cost_item', '$weight_item','$inventory_item','$estado_item', '$fecha_alta_item', '$fecha_edit_item', '$id_usu')";
        $resultado = $mysqli->query($sql);

        echo "
                <!DOCTYPE html>
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
                                <h3><b><i class='fas fa-users'></i> THE PROCESS WAS SUCCESSFULLY REGISTERED</b></h3><br />
                                <p align='center'><a href='../../access.php'><img src='../../img/atras.png' width=96 height=96></a></p>
                            </div>
                            </center>
                        </body>
                    </html>
        ";
    }

?>
