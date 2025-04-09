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

$upc_item       = mb_strtoupper($_POST['upc_item']);
$sku_item       =  ($_POST['sku_item']) ?? '';
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
$quantity_inventory = $_POST['quantity_inventory'] ?? 0;
$estado_item        = 1;
$fecha_alta_item    = date('Y-m-d h:i:s');
$fecha_edit_item    = '0000-00-00 00:00:00';
$id_usu             = $_SESSION['id'];

// Validar si la clave ya existe
$check_duplicate_sql = "SELECT * FROM items WHERE upc_item = '$upc_item'";
$check_duplicate_result = $mysqli->query($check_duplicate_sql);

// La clave no existe, realizar la inserción
$sql = "INSERT INTO items (
            upc_item, sku_item, date_item, brand_item, item_item, ref_item, 
            color_item, size_item, category_item, cost_item, weight_item, 
            inventory_item, estado_item, fecha_alta_item, fecha_edit_item, id_usu
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $mysqli->prepare($sql);

if ($stmt) {
    $stmt->bind_param(
        "ssssssssssssssss",
        $upc_item,
        $sku_item,
        $date_item,
        $brand_item,
        $item_item,
        $ref_item,
        $color_item,
        $size_item,
        $category_item,
        $cost_item,
        $weight_item,
        $inventory_item,
        $estado_item,
        $fecha_alta_item,
        $fecha_edit_item,
        $id_usu
    );

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>✅ Registro insertado correctamente.</div>";

        //agregar en la tabla inventario en la columna upc_inventory y quantity_inventory
        $sql_inventory = "INSERT INTO inventory (upc_inventory, quantity_inventory) VALUES (?, ?)";
        $stmt_inventory = $mysqli->prepare($sql_inventory);
        $stmt_inventory->bind_param("si", $upc_item, $quantity_inventory);
        if ($stmt_inventory->execute()) {
            echo "<div class='alert alert-success'>✅ Registro de inventario insertado correctamente.</div>";
        } else {
            echo "<div class='alert alert-danger'>❌ Error al insertar en inventario: " . $stmt_inventory->error . "</div>";
        }
        $stmt_inventory->close();
    } else {
        echo "<div class='alert alert-danger'>❌ Error al insertar: " . $stmt->error . "</div>";
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>❌ Error en la preparación de la consulta: " . $mysqli->error . "</div>";
}
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
