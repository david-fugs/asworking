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
$item_item          = ucfirst(strtolower($_POST['item_item'])); // Primera mayúscula, resto minúscula
$ref_item           = mb_strtoupper($_POST['ref_item']);
$color_item         = mb_strtoupper($_POST['color_item']);
$size_item          = mb_strtoupper($_POST['size_item']);
$category_item      = mb_strtoupper($_POST['category_item']);
$cost_item          = $_POST['cost_item'];
$weight_item        = mb_strtoupper($_POST['weight_item']);
$inventory_item     = mb_strtoupper($_POST['inventory_item']);
$quantity_inventory = $_POST['quantity_inventory'] ?? 0;
$observation_inventory = isset($_POST['observation_inventory']) ? trim($_POST['observation_inventory']) : '';

// Procesar las tiendas seleccionadas
$stores_selected = [];
if (isset($_POST['stores']) && is_array($_POST['stores'])) {
    $valid_stores = ['AS001', 'EB001', 'EB002', 'AM002', 'WM001'];
    foreach ($_POST['stores'] as $store) {
        $store = strtoupper(trim($store));
        if (in_array($store, $valid_stores)) {
            $stores_selected[] = $store;
        }
    }
}

// Validar que se haya seleccionado al menos una tienda
if (empty($stores_selected)) {
    echo "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='utf-8'>
        <title>Error - ASWWORKING</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>body{background:#f5f3f7;font-family:Segoe UI, Tahoma, Geneva, Verdana, sans-serif;margin:0;padding:0}</style>
    </head>
    <body>
        <script>
            (function(){
                if (typeof Swal === 'undefined') {
                    var s = document.createElement('script');
                    s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                    s.onload = showError;
                    document.head.appendChild(s);
                } else { showError(); }

                function showError(){
                    Swal.fire({
                        title: 'Error',
                        text: 'You must select at least one store.',
                        icon: 'error',
                        confirmButtonText: 'Go back',
                        confirmButtonColor: '#632b8b'
                    }).then((result) => { window.history.back(); });
                }
            })();
        </script>
    </body>
    </html>";
    exit();
}

// Convertir las tiendas a formato JSON para almacenar en la base de datos
$stores_json = json_encode($stores_selected);

$estado_item        = 1;
$fecha_alta_item    = date('Y-m-d h:i:s');
$fecha_edit_item    = '0000-00-00 00:00:00';
$id_usu             = $_SESSION['id'];

// Validar si la clave ya existe
$check_duplicate_sql = "SELECT * FROM items WHERE upc_item = '$upc_item'";
$check_duplicate_result = $mysqli->query($check_duplicate_sql);

// La clave no existe, realizar la inserción
// Construir el SQL para items

// Escape values to prevent SQL syntax errors (e.g., quotes inside JSON)
$upc_item_esc = $mysqli->real_escape_string($upc_item);
$sku_item_esc = $mysqli->real_escape_string($sku_item);
$date_item_esc = $mysqli->real_escape_string($date_item);
$brand_item_esc = $mysqli->real_escape_string($brand_item);
$item_item_esc = $mysqli->real_escape_string($item_item);
$ref_item_esc = $mysqli->real_escape_string($ref_item);
$color_item_esc = $mysqli->real_escape_string($color_item);
$size_item_esc = $mysqli->real_escape_string($size_item);
$category_item_esc = $mysqli->real_escape_string($category_item);
$cost_item_esc = $mysqli->real_escape_string($cost_item);
$weight_item_esc = $mysqli->real_escape_string($weight_item);
$inventory_item_esc = $mysqli->real_escape_string($inventory_item);
$stores_json_escaped = $mysqli->real_escape_string($stores_json);
$observation_inventory_escaped = $mysqli->real_escape_string($observation_inventory);

// Use a prepared statement to avoid SQL injection and escaping issues (e.g. backslashes)
$stmt = $mysqli->prepare("INSERT INTO items (
    upc_item, sku_item, date_item, brand_item, item_item, ref_item,
    color_item, size_item, category_item, cost_item, weight_item,
    batch_item, stores_item, estado_item, fecha_alta_item, fecha_edit_item, id_usu, observation_item
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

if (!$stmt) {
    // Prepare error
    die('Prepare failed: ' . $mysqli->error);
}

// Bind parameters: 13 strings, 1 int (estado), 2 strings (fechas), 1 int (id_usu), 1 string (observation) => types
$estado_val = 0;
$id_usu_int = intval($id_usu);
$types = 'sssssssssssssissis';
$stmt->bind_param($types,
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
    $stores_json,
    $estado_val,
    $fecha_alta_item,
    $fecha_edit_item,
    $id_usu_int,
    $observation_inventory
);

if ($stmt->execute()) {
    // Close the prepared statement early so it always runs on successful execute
    $stmt->close();
    // Insertar en la tabla inventory
    $sql_inventory = "INSERT INTO inventory (upc_inventory, sku_inventory, quantity_inventory, observation_inventory) VALUES ('{$upc_item_esc}', '{$sku_item_esc}', $quantity_inventory, '{$observation_inventory_escaped}')";
    if ($mysqli->query($sql_inventory)) {
        // Insertar en la tabla daily_report para el flujo de reportes
        $estado_reporte = 0;
        $fecha_alta_reporte = $fecha_alta_item;
        // Limitar longitud del JSON para evitar error de constraint en stores_report
        $max_length = 200; // Ajusta este valor según el tamaño real del campo en tu base de datos
        $stores_json_valid = $stores_selected;
        while (strlen(json_encode($stores_json_valid)) > $max_length && count($stores_json_valid) > 0) {
            array_pop($stores_json_valid);
        }
        $stores_json_final = json_encode($stores_json_valid);
        $stores_json_escaped = $mysqli->real_escape_string($stores_json_final);
        // Definir valores para los campos vacíos
        $empty = '';
        $cons_report = $empty;
        $folder_report = $empty;
        $loc_report = $empty;
        $vendor_report = $empty;
        $observacion_report = $empty;
        $sql_report = "INSERT INTO daily_report (
            upc_asignado_report, upc_final_report, cons_report, folder_report, 
            loc_report, quantity_report, sku_report, brand_report, item_report, 
            vendor_report, color_report, size_report, category_report, 
            weight_report, inventory_report, observacion_report, stores_report, estado_reporte, fecha_alta_reporte
        ) VALUES (
            '{$upc_item_esc}', '{$upc_item_esc}', '$cons_report', '$folder_report', '$loc_report', $quantity_inventory, '{$sku_item_esc}', '{$brand_item_esc}', '{$item_item_esc}', '$vendor_report', '{$color_item_esc}', '{$size_item_esc}', '{$category_item_esc}', '{$weight_item_esc}', '{$inventory_item_esc}', '$observacion_report', '{$stores_json_escaped}', $estado_reporte, '$fecha_alta_reporte'
        )";
        if ($mysqli->query($sql_report)) {
            // Todo OK, mostrar SweetAlert y redirigir
            ?>
            <!DOCTYPE html>
            <html lang="es">
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Item registered</title>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <style>body{background:#f5f3f7;font-family:Segoe UI, Tahoma, Geneva, Verdana, sans-serif;margin:0;padding:0}</style>
            </head>
            <body>
            <script>
                // Ensure SweetAlert is available, then show the modal
                (function(){
                    if (typeof Swal === 'undefined') {
                        // If CDN failed to load synchronously, try to load it dynamically
                        var s = document.createElement('script');
                        s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                        s.onload = showAlert;
                        document.head.appendChild(s);
                    } else {
                        showAlert();
                    }

                    function showAlert(){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Item registered successfully!',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#632b8b',
                            allowOutsideClick: false
                        }).then((result) => {
                            // Open the additems.php view in the same folder instead of reloading
                            window.location.href = 'additems.php';
                        });
                    }
                })();
            </script>
            </body>
            </html>
            <?php
            exit();
        } else {
            // Error al insertar en daily_report
            $errorMsg = addslashes($mysqli->error);
            echo "<!DOCTYPE html><html lang='es'><head><meta charset='utf-8'><title>Error</title>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
                <style>body{background:#f5f3f7;font-family:Segoe UI, Tahoma, Geneva, Verdana, sans-serif;margin:0;padding:0}</style>
                </head><body>
                <script>
                (function(){
                    if (typeof Swal === 'undefined') {
                        var s = document.createElement('script'); s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11'; s.onload = showErr;
                        document.head.appendChild(s);
                    } else { showErr(); }
                    function showErr(){
                        Swal.fire({icon:'error',title:'Error',html: 'Error inserting report: $errorMsg',confirmButtonText:'Reload',confirmButtonColor:'#632b8b'}).then(()=>{ window.location.reload(); });
                    }
                })();
                </script>
                </body></html>";
            exit();
        }
    } else {
        // Error al insertar en inventory
        $errorMsg = addslashes($mysqli->error);
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='utf-8'><title>Error</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <style>body{background:#f5f3f7;font-family:Segoe UI, Tahoma, Geneva, Verdana, sans-serif;margin:0;padding:0}</style>
            </head><body>
            <script>
            (function(){
                if (typeof Swal === 'undefined') {
                    var s = document.createElement('script'); s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11'; s.onload = showErr;
                    document.head.appendChild(s);
                } else { showErr(); }
                function showErr(){
                    Swal.fire({icon:'error',title:'Error',html: 'Error inserting inventory: $errorMsg',confirmButtonText:'Reload',confirmButtonColor:'#632b8b'}).then(()=>{ window.location.reload(); });
                }
            })();
            </script>
            </body></html>";
        exit();
    }
} else {
    // Error al insertar en items
    $errorMsg = addslashes($mysqli->error);
    echo "<!DOCTYPE html><html lang='es'><head><meta charset='utf-8'><title>Error</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>body{background:#f5f3f7;font-family:Segoe UI, Tahoma, Geneva, Verdana, sans-serif;margin:0;padding:0}</style>
        </head><body>
        <script>
        (function(){
            if (typeof Swal === 'undefined') {
                var s = document.createElement('script'); s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11'; s.onload = showErr;
                document.head.appendChild(s);
            } else { showErr(); }
            function showErr(){
                Swal.fire({icon:'error',title:'Error',html: 'Error inserting item: $errorMsg',confirmButtonText:'Reload',confirmButtonColor:'#632b8b'}).then(()=>{ window.location.reload(); });
            }
        })();
        </script>
        </body></html>";
    exit();
}
