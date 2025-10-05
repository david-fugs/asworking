<?php
session_start();
include("../../conexion.php");
if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
}
$nombre = $_SESSION['nombre'];
$tipo_usu = $_SESSION['tipo_usuario'];

if (isset($_GET['delete'])) {
    $num_doc_cta = $_GET['delete'];
    deleteMember($num_doc_cta);
}
function deleteMember($id_store)
{
    global $mysqli; // Asegurar acceso a la conexión global

    $query = "DELETE FROM store WHERE id_store  = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $id_store);

    if ($stmt->execute()) {
        echo "<script>alert('sucursal deleted correctly');
        window.location = 'seeSucursal.php';</script>";
    } else {
        echo "<script>alert('Error deleting the sucursal');
        window.location = 'seeSucursal.php';</script>";
    }

    $stmt->close();
}

function getStatus($estado)
{
    if ($estado == 1) {
        return "<span class='badge bg-success'>ACTIVO</span>";
    } else {
        return "<span class='badge bg-danger'>INACTIVO</span>";
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ASWWORKING | SOFT</title>
    <script src="js/64d58efce2.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Orbitron" rel="stylesheet">

    <link href="../../../fontawesome/css/all.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .top-bar .center {
            flex-grow: 1;
            text-align: center;
            margin-left: 300px;

        }

        .btn-add-store {
            padding: 10px 20px;
            background-color: #198754;
            /* verde tipo Bootstrap */
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 100px;
        }

        .btn-add-store:hover {
            background-color: #157347;
        }

        .table-responsive {
            overflow-x: auto;
            max-height: 600px;
            cursor: grab;
            position: relative;
        }

        .table-responsive:active {
            cursor: grabbing;
        }

        /* Improve dragging experience */
        .table-responsive.dragging,
        .top-scroll-container.dragging {
            cursor: grabbing !important;
            user-select: none;
        }

        .table-responsive.dragging *,
        .top-scroll-container.dragging * {
            pointer-events: none;
        }

        /* Top scroll bar styles (mirror) */
        .top-scroll-container {
            overflow-x: auto;
            overflow-y: hidden;
            height: 34px;
            border: none;
            background: transparent; /* removed purple background */
            border-radius: 6px;
            margin-bottom: 12px;
            box-shadow: none;
            cursor: grab;
            position: relative;
        }

        .top-scroll-container:active {
            cursor: grabbing;
        }

        .top-scroll-content {
            height: 30px;
            width: 1000px; /* will be adjusted by JS */
            background: transparent;
            display: block;
            pointer-events: none; /* Allow clicks to go through to container */
        }

        /* Custom scrollbar for the top scroll container (WebKit) */
        .top-scroll-container::-webkit-scrollbar {
            height: 12px;
        }

        .top-scroll-container::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 10px;
        }

        .top-scroll-container::-webkit-scrollbar-thumb {
            background: rgba(113,63,148,0.95);
            border-radius: 10px;
            border: 2px solid rgba(255,255,255,0.12);
            background-clip: padding-box;
        }

        .top-scroll-container::-webkit-scrollbar-thumb:hover {
            background: rgba(90,44,118,0.98);
        }

        /* Firefox scrollbar color */
        .top-scroll-container {
            scrollbar-color: rgba(113,63,148,0.95) rgba(245,241,248,0.6);
            scrollbar-width: thin;
        }

        .fixed-save-button {
            position: fixed;
            bottom: 20px;
            right: 30px;
            z-index: 99;
        }

        .header-container {
            width: 100%;
            background-color: #dac7e5;
            /* Color de fondo */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* ENCABEZADO ORIGINAL */
        .header {
            background-color: #dac7e5;
            /* Nuevo color de fondo */
            display: flex;
            align-items: center;
            padding: 30px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
            justify-content: center;
            /* Centramos el contenido */
        }

        .logo-container {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            height: 100%;
            display: flex;
            align-items: center;
        }

        .logo {
            height: 100px;
            /* Tamaño ligeramente reducido */
            width: auto;
            max-height: 100%;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
            /* Efecto hover sutil */
        }

        .title {
            margin: 0 auto;
            font-size: 40px;
            /* Tamaño ajustado */
            font-weight: 700;
            color: #632b8b;
            /* Color morado oscuro para contraste */
            text-transform: uppercase;
            letter-spacing: 2px;
            /* Mayor espaciado */
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .top-bar .center {
            flex-grow: 1;
            text-align: center;
            margin-left: 300px;
        }

        .btn-add-store {
            padding: 10px 20px;
            background-color: #632b8b;
            /* Cambiado a morado principal */
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 100px;
            transition: background-color 0.3s;
        }

        .btn-add-store:hover {
            background-color: #5d337a;
            /* Cambiado a morado oscuro */
            color: white;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: transparent;
            border: none;
            color: #5d337a;
            /* Morado oscuro */
            font-size: 1.8rem;
            cursor: pointer;
            padding: 15px;
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 50px;
            height: 50px;
        }

        .back-btn:hover {
            background-color: rgba(93, 51, 122, 0.1);
            color: #632b8b;
            transform: translateX(-3px);
        }

        .back-btn i {
            transition: transform 0.3s ease;
        }

        .back-btn:hover i {
            transform: scale(1.1);
        }

        /* Estilos generales para la tabla */
        .table-container {
            border-radius: 100px;
            overflow: hidden; /* Prevent any overflow from this container */
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            background: white;
            padding: 2px;
            max-width: 100vw; /* Ensure it never exceeds viewport width */
            width: 100%;
        }

        .table-content {
            width: 100%;
            position: relative;
        }
        
        /* Ensure table takes full width and scrolls properly */
        .table-responsive table {
            min-width: 2500px; /* Fixed minimum width to ensure all columns fit */
            width: max-content;
            table-layout: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(to right, #f9f5ff, #f0e6ff);
        }

        /* Bordes mejorados para toda la tabla */
        table {
            border: 2px solidrgb(216, 194, 234);
            box-shadow: 0 0 0 1px #4a2568;
            /* Borde interior para efecto de doble línea */
        }

        /* Encabezado con bordes */
        thead th {
            border-right: 1px solid rgba(238, 192, 246, 0.2);
            border-bottom: 2px solidrgb(231, 212, 246);
            position: relative;
        }

        thead th::before {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 100%;
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Celdas del cuerpo con bordes */
        tbody td {
            border-right: 1px solid rgba(74, 37, 104, 0.5);
            border-bottom: 1px solid rgba(74, 37, 104, 0.5);
            position: relative;
        }

        /* Eliminar bordes duplicados */
        thead th:last-child,
        tbody td:last-child {
            border-right: none;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        /* Efecto hover con bordes destacados */
        tbody tr:hover td {
            border-color: #632b8b;
            border-width: 1px;
            box-shadow:
                inset 0 0 0 1px #632b8b,
                0 0 0 1px #632b8b;
        }

        /* Esquinas redondeadas */
        thead tr:first-child th:first-child {
            border-top-left-radius: 10px;
        }

        thead tr:first-child th:last-child {
            border-top-right-radius: 10px;
        }

        tbody tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }

        tbody tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }

        /* Estilos para el encabezado - Efecto vidrio (glassmorphism) */
        thead {
            background: rgb(113, 63, 148);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        thead th {
            padding: 14px 10px;
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.82rem;
            letter-spacing: 0.5px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        thead th:hover {
            background: rgba(109, 53, 149, 0.9);
        }

        thead th::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: white;
            transition: width 0.3s ease;
        }

        thead th:hover::after {
            width: 70%;
        }

        /* Estilos para las filas - Efecto hover mejorado */
        tbody tr {
            background-color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
        }

        tbody tr:nth-child(even) {
            background-color: rgba(248, 240, 255, 0.8);
        }

        tbody tr:hover::before {
            opacity: 1;
        }

        /* Estilos para las celdas */
        tbody td {
            padding: 12px 10px;
            border-bottom: 1px solid rgba(153, 124, 171, 0.3);
            color: #444;
            font-size: 0.9rem;
            position: relative;
            transition: all 0.2s ease;
        }

        tbody tr:hover td {
            color: #333;
        }

        /* Efecto hover para celdas individuales */
        tbody td:hover {
            background: rgba(218, 199, 229, 0.4);
        }

        /* Estilos para los inputs - Diseño moderno */
        .form-control-sm {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(153, 124, 171, 0.5);
            border-radius: 6px;
            padding: 6px 10px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .form-control-sm:focus {
            outline: none;
            border-color: #632b8b;
            box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
            background-color: white;
        }

        /* Estilos para el checkbox */
        input[type="checkbox"] {
            -webkit-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #997cab;
            border-radius: 4px;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
        }

        input[type="checkbox"]:checked {
            background-color: #632b8b;
            border-color: #632b8b;
        }

        input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 12px;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        /* Estilos para el botón - Efecto 3D */
        .btn-add-store {
            background: linear-gradient(to bottom, #632b8b, #5d337a);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(99, 43, 139, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .btn-add-store:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(99, 43, 139, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .btn-add-store:active {
            transform: translateY(1px);
            box-shadow: 0 2px 4px rgba(99, 43, 139, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .btn-add-store::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(to bottom right,
                    rgba(255, 255, 255, 0) 0%,
                    rgba(255, 255, 255, 0) 45%,
                    rgba(255, 255, 255, 0.3) 48%,
                    rgba(255, 255, 255, 0) 52%,
                    rgba(255, 255, 255, 0) 100%);
            transform: rotate(30deg);
            transition: all 0.5s ease;
        }

        .btn-add-store:hover::after {
            left: 100%;
        }

        /* Bordes redondeados para la tabla */
        table {
            border-radius: 10px;
        }

        thead tr:first-child th:first-child {
            border-top-left-radius: 10px;
        }

        thead tr:first-child th:last-child {
            border-top-right-radius: 10px;
        }

        tbody tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }

        tbody tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }

        /* Efecto de título flotante */
        h5 {
            color: #632b8b;
            font-weight: 700;
            margin-bottom: 1.8rem !important;
            text-align: center;
            position: relative;
            display: inline-block;
            left: 50%;
            transform: translateX(-50%);
            padding: 0 20px;
        }

        /* Stores column styling */
        .stores-cell {
            background-color: rgba(99, 43, 139, 0.1) !important;
            border-radius: 4px;
            padding: 8px !important;
            text-align: center;
            min-width: 120px;
        }

        .stores-text {
            font-size: 0.85rem;
            color: #632b8b;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stores-not-assigned {
            color: #6c757d;
            font-style: italic;
            font-weight: 400;
        }

        h5::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 100%;
            height: 3px;
            background: linear-gradient(to right, transparent, #632b8b, transparent);
            border-radius: 3px;
        }

        /* Prevent browser horizontal scrollbar */
        html, body {
            overflow-x: hidden;
            max-width: 100vw;
        }

        .container-fluid {
            overflow-x: hidden;
            max-width: 100vw;
            padding-left: 15px;
            padding-right: 15px;
        }

        .row {
            margin-left: 0;
            margin-right: 0;
        }

        .col-md-12 {
            padding-left: 0;
            padding-right: 0;
        }
    </style>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"></script>
    <div class="header-container">
        <div class="header">
            <div class="logo-container">
                <img src='../../img/logo.png' class="logo" alt="Logo">
            </div>
            <h1 class="title"><i class="fa-solid fa-file-signature"></i> DAILY REPORT</h1>
        </div>
    </div>
    <div class="top-bar">
        <div></div>
        <div class="center">
            <a href="../../access.php" class="back-btn" title="Go Back">
                <i class="fas fa-arrow-circle-left fa-xl"></i>
            </a>
        </div>
        <div style="display: flex; justify-content: flex-end; margin: 20px 0;">
            <a href="addReport.php" class="btn-add-store">
                <i class="fas fa-file-alt"></i> Go to Report
            </a>
        </div>
    </div>
    <?php
    date_default_timezone_set("America/Bogota");
    include("../../conexion.php");
    require_once("../../zebra.php");

    //traer todo de daily_report ordenado por fecha en la fecha_alta_report
    $sql = "SELECT * FROM daily_report
    WHERE estado_reporte = 1
     ORDER BY fecha_alta_reporte DESC";
    $result = $mysqli->query($sql);
    if (!$result) {
        die("Error en la consulta: " . $mysqli->error);
    }
    $reports = $result->fetch_all(MYSQLI_ASSOC);



    ?>
    <div class="container-fluid mt-5" style="overflow-x: hidden; max-width: 100vw;">
        <div class="row justify-content-center" style="margin: 0;">
            <div class="col-md-12" style="padding: 0; max-width: 100%;"">
                <form action="procesar_articulos.php" method="POST">
                    <div class="table-container">
                        <div class="table-content">
                            <h5 class="mb-4 text-center">Daily Reports</h5>

                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-info-circle"></i> <strong>Instrucciones (Español):</strong></h6>
                                        <ul class="mb-0">
                                            <li>Selecciona los reportes que deseas procesar marcando el checkbox</li>
                                            <li>Modifica los campos necesarios directamente en la tabla</li>
                                            <li>Las tiendas asignadas se muestran en la columna "Stores"</li>
                                            <li>Haz clic en "Save Selected" para guardar los cambios</li>
                                            <li>Los reportes procesados se moverán al estado "Procesado"</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-info-circle"></i> <strong>Instructions (English):</strong></h6>
                                        <ul class="mb-0">
                                            <li>Select the reports you want to process by checking the checkbox</li>
                                            <li>Modify the necessary fields directly in the table</li>
                                            <li>Assigned stores are displayed in the "Stores" column</li>
                                            <li>Click "Save Selected" to save the changes</li>
                                            <li>Processed reports will move to "Processed" status</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Top synchronized scrollbar (mirrors horizontal scroll of the table) -->
                            <div class="top-scroll-container" style="margin-bottom:10px;">
                                <div class="top-scroll-content" aria-hidden="true"></div>
                            </div>

                            <div class="table-responsive">
                            <table class="">
                                <thead class="">
                                    <tr>
                                        <th></th>
                                        <th class="">Date</th>
                                        <th colspan="2">UPC</th>
                                        <th colspan="4">Info</th>

                                        <th colspan="5">Product Info</th>
                                        <th colspan="3">Specs</th>

                                        <th colspan="1">Batch </th>
                                        <th colspan="1">Stores</th>
                                        <th>Observation</th>
                                        <th>Actions</th>
                                    </tr>
                                        <tr>
                                        <th class=""></th>
                                        <th class=""></th>
                                        <th class="d-none">Assigned</th>
                                        <th>Final</th>
                                        <th>Cons</th>
                                        <th>Folder</th>
                                        <th>Location</th>
                                        <th>Quantity</th>
                                        <th class="">SKU</th>
                                        <th>Brand</th>
                                        <th class="">Item</th>
                                        <th>Style</th>
                                        <th>Color</th>
                                        <th>Size</th>
                                        <th>Cost</th>
                                        <th>Category</th>
                                        <th>Weight</th>
                                        <th>Batch</th>
                                        <th>Stores</th>
                                        <th>Observation</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($reports as $index => $report): 
                                        // Format fields: uppercase for all except item (first-letter uppercase, rest lowercase)
                                        $fecha = mb_strtoupper($report['fecha_alta_reporte'] ?? '', 'UTF-8');
                                        $upc_asignado = mb_strtoupper($report['upc_asignado_report'] ?? '', 'UTF-8');
                                        $upc_final_raw = $report['upc_final_report'] ?? '';
                                        // If upc_final empty, try to prefill from items using sku
                                        if (trim($upc_final_raw) === '') {
                                            $found_upc = '';
                                            $sku_lookup = $report['sku_report'] ?? '';
                                            if (!empty($sku_lookup)) {
                                                $stmt_upc = $mysqli->prepare("SELECT upc_item FROM items WHERE sku_item = ? LIMIT 1");
                                                if ($stmt_upc) {
                                                    $stmt_upc->bind_param('s', $sku_lookup);
                                                    $stmt_upc->execute();
                                                    $stmt_upc->bind_result($upc_item_row);
                                                    if ($stmt_upc->fetch()) {
                                                        $found_upc = $upc_item_row;
                                                    }
                                                    $stmt_upc->close();
                                                }
                                            }
                                            $upc_final = mb_strtoupper((string)($found_upc ?? ''), 'UTF-8');
                                        } else {
                                            $upc_final = mb_strtoupper($upc_final_raw, 'UTF-8');
                                        }
                                        $cons = mb_strtoupper($report['cons_report'] ?? '', 'UTF-8');
                                        $folder = mb_strtoupper($report['folder_report'] ?? '', 'UTF-8');
                                        // Prefill location from report, or from items.inventory_item if empty/'0'
                                        $loc_raw = $report['loc_report'] ?? '';
                                        if (trim($loc_raw) === '' || $loc_raw === '0') {
                                            $inv_val = '';
                                            $upc_for_loc = $report['upc_final_report'] ?? '';
                                            // prefer upc_final if present, otherwise try upc we've looked up above
                                            if (empty($upc_for_loc)) $upc_for_loc = $upc_final_raw;
                                            if (!empty($upc_for_loc)) {
                                                $stmt_loc = $mysqli->prepare("SELECT inventory_item FROM items WHERE upc_item = ? LIMIT 1");
                                                if ($stmt_loc) {
                                                    $stmt_loc->bind_param('s', $upc_for_loc);
                                                    $stmt_loc->execute();
                                                    $stmt_loc->bind_result($inv_item_row);
                                                    if ($stmt_loc->fetch()) {
                                                        $inv_val = $inv_item_row;
                                                    }
                                                    $stmt_loc->close();
                                                }
                                            }
                                            $loc = mb_strtoupper((string)($inv_val ?? ''), 'UTF-8');
                                        } else {
                                            $loc = mb_strtoupper($loc_raw, 'UTF-8');
                                        }
                                        $quantity = mb_strtoupper($report['quantity_report'] ?? '', 'UTF-8');
                                        $sku = mb_strtoupper($report['sku_report'] ?? '', 'UTF-8');
                                        $brand = mb_strtoupper($report['brand_report'] ?? '', 'UTF-8');
                                        // item: first letter uppercase, rest lowercase
                                        $item_raw = $report['item_report'] ?? '';
                                        $item_lower = mb_strtolower($item_raw, 'UTF-8');
                                        $item = $item_lower !== '' ? mb_strtoupper(mb_substr($item_lower, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($item_lower, 1, null, 'UTF-8') : $item_lower;
                                        $vendor = mb_strtoupper($report['vendor_report'] ?? '', 'UTF-8');
                                        $color = mb_strtoupper($report['color_report'] ?? '', 'UTF-8');
                                        $size = mb_strtoupper($report['size_report'] ?? '', 'UTF-8');
                                        // If cost_report is empty, try to prefill from items.cost_item using upc_final_report
                                        $cost_raw = $report['cost_report'] ?? '';
                                        if (trim($cost_raw) === '' || $cost_raw === '0') {
                                            $cost_item_val = '';
                                            $upc_lookup_cost = $report['upc_final_report'] ?? '';
                                            if (!empty($upc_lookup_cost)) {
                                                $stmt_cost = $mysqli->prepare("SELECT cost_item FROM items WHERE upc_item = ? LIMIT 1");
                                                if ($stmt_cost) {
                                                    $stmt_cost->bind_param('s', $upc_lookup_cost);
                                                    $stmt_cost->execute();
                                                    $stmt_cost->bind_result($cost_item_row);
                                                    if ($stmt_cost->fetch()) {
                                                        $cost_item_val = $cost_item_row;
                                                    }
                                                    $stmt_cost->close();
                                                }
                                            }
                                            $cost = mb_strtoupper((string)($cost_item_val ?? ''), 'UTF-8');
                                        } else {
                                            $cost = mb_strtoupper($cost_raw, 'UTF-8');
                                        }
                                        $category = mb_strtoupper($report['category_report'] ?? '', 'UTF-8');
                                        $weight = mb_strtoupper($report['weight_report'] ?? '', 'UTF-8');
                                        // The old 'inventory_report' now becomes the batch value in the UI
                                        $batch = mb_strtoupper($report['inventory_report'] ?? '', 'UTF-8');
                                        // New inventory input is left empty by default
                                        $inventory = '';
                                        $stores_json = $report['stores_report'] ?? '';
                                        // Prepare stores display in uppercase
                                        if (!empty($stores_json)) {
                                            $stores_array = json_decode($stores_json, true);
                                            if (is_array($stores_array)) {
                                                $stores_array = array_map(function($s){ return mb_strtoupper($s, 'UTF-8'); }, $stores_array);
                                                $stores_display = implode(', ', $stores_array);
                                            } else {
                                                $stores_display = mb_strtoupper($stores_json, 'UTF-8');
                                            }
                                        } else {
                                            $stores_display = 'NOT ASSIGNED';
                                        }
                                        $observacion = $report['observacion_report'] ?? '';
                                    ?>
                                        <tr>
                                            <input type="hidden" name="id_report[]" value="<?= htmlspecialchars($report['id_report']) ?>">
                                            <td>
                                                <input type="checkbox" name="seleccionados[]" value="<?= $index ?>">
                                            </td>
                                            <td><input style="width: 155px;"  type="text" name="fecha_alta_reporte[]" class="form-control form-control-sm" value="<?= htmlspecialchars($fecha) ?>"></td>
                                            <td class="d-none"><input type="hidden" name="upc_asignado_report[]" value="<?= htmlspecialchars($upc_asignado) ?>"></td>
                                            <td><input style="width: 124px;" type="text" name="upc_final_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($upc_final) ?>"></td>
                                            <td><input style="width: 155px;"  type="text" name="cons_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($cons) ?>"></td>
                                            <td><input style="width: 145px;"  type="text" name="folder_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($folder) ?>"></td>
                                            <td><input type="text" style="width: 90px;" name="loc_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($loc) ?>"></td>
                                            <td><input style="width: 42px;" type="text" name="quantity_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($quantity) ?>"></td>
                                            <td><input style="width: 112px;" type="text" name="sku_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($sku) ?>"></td>
                                            <td><input style="width: 160px;" type="text" name="brand_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($brand) ?>"></td>
                                            <td><input type="text" style="width: 240px;" name="item_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($item) ?>"></td>
                                            <td><input style="width: 80px;" type="text" name="vendor_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($vendor) ?>"></td>
                                            <td><input type="text" style="width: 110px;" name="color_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($color) ?>"></td>
                                            <td><input style="width: 110px;" type="text" name="size_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($size) ?>"></td>
                                            <td><input style="width: 55px;" type="text" name="cost_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($cost) ?>"></td>
                                            <td><input type="text" style="width: 100px;" name="category_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($category) ?>"></td>
                                            <td><input type="text" style="width: 140px;" name="weight_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($weight) ?>"></td>
                                            <td><input style="width: 140px;" type="text" name="batch_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($batch) ?>"></td>
                                            <td class="stores-cell">
                                                <span class="stores-text <?= empty($stores_json) ? 'stores-not-assigned' : '' ?>">
                                                    <?= htmlspecialchars($stores_display) ?>
                                                </span>
                                                <input type="hidden" name="stores_report[]" value="<?= htmlspecialchars($stores_json) ?>">
                                            </td>
                                            <td><input style="width: 180px;" type="text" name="observacion_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($observacion) ?>"></td>
                                            <td>
                                                <button type="button" class="btn btn-danger btn-sm delete-report-btn" data-id="<?= $report['id_report'] ?>">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                            <div class="text-center mt-3">
                                <button type="submit" class="btn-add-store" id="saveSelectedBtn">Save Selected</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <center>
        <a href="../../access.php" class="back-btn" title="Go Back">
            <i class="fas fa-arrow-circle-left fa-xl"></i>
        </a>
    </center>

    <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
    <!-- SweetAlert2 for nicer alerts when validating UPCs -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // --- Make Cost required for selected rows and validate on submit ---
        (function(){
            const form = document.querySelector('form[action="procesar_articulos.php"]');
            if (!form) return;

            // When a checkbox is toggled, mark corresponding cost input required
            form.querySelectorAll('input[type="checkbox"][name="seleccionados[]"]').forEach(cb => {
                cb.addEventListener('change', function() {
                    const row = cb.closest('tr');
                    if (!row) return;
                    const idx = Array.from(row.parentNode.children).indexOf(row);
                    // Find cost input within the row
                    const costInput = row.querySelector('input[name="cost_report[]"]');
                    if (costInput) {
                        if (cb.checked) {
                            costInput.setAttribute('required','required');
                            costInput.classList.add('required-highlight');
                        } else {
                            costInput.removeAttribute('required');
                            costInput.classList.remove('required-highlight');
                        }
                    }
                });
            });

            // On submit, ensure selected rows have cost filled
            form.addEventListener('submit', function(e) {
                const selected = form.querySelectorAll('input[type="checkbox"][name="seleccionados[]"]:checked');
                const missing = [];
                selected.forEach(cb => {
                    const row = cb.closest('tr');
                    if (!row) return;
                    const costInput = row.querySelector('input[name="cost_report[]"]');
                    if (costInput && costInput.value.trim() === '') {
                        missing.push(row);
                        costInput.classList.add('is-invalid');
                    }
                });
                if (missing.length > 0) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Missing cost', text: 'Please fill the Cost for every selected row before saving.' });
                    return false;
                }
            });
        })();

        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="seleccionados[]"]');
            const form = document.querySelector('form[action="procesar_articulos.php"]');
            const saveBtn = document.getElementById('saveSelectedBtn');

            // Add row highlighting when checkbox is checked
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const row = this.closest('tr');
                    if (this.checked) {
                        row.classList.add('table-success');
                    } else {
                        row.classList.remove('table-success');
                    }
                });
            });

            // Form validation - prevent submission if no checkboxes are selected
            form.addEventListener('submit', function(e) {
                const selectedCheckboxes = document.querySelectorAll('input[type="checkbox"][name="seleccionados[]"]:checked');
                
                if (selectedCheckboxes.length === 0) {
                    e.preventDefault(); // Prevent form submission
                    alert('Please select at least one report before submitting.');
                    return false;
                }
                
                // If validation passes, allow submission
                return true;
            });

            // Debounced validation for Final UPC inputs on this page
            function debounce(fn, wait) {
                let t;
                return function(...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), wait);
                };
            }

            const upcFinalInputs = document.querySelectorAll('input[name="upc_final_report[]"]');
            upcFinalInputs.forEach((input) => {
                // keep last good value to restore if duplicate is detected
                let originalValue = input.value;

                input.addEventListener('focus', function() {
                    originalValue = input.value;
                });

                const runValidate = debounce(function() {
                    const val = input.value.trim();
                    if (!val) return;
                    console.log('[seeReport] validating upc_final:', val);

                    // Use verificar_upc.php from items to retrieve matching items and show the additems modal/table
                    $.ajax({
                        url: '../items/verificar_upc.php',
                        type: 'POST',
                        dataType: 'json',
                        data: { upc_item: val },
                        success: function(data) {
                            console.log('[seeReport] verificar_upc response:', data);
                            if (data && data.status === 'existe' && Array.isArray(data.items) && data.items.length > 0) {
                                var tableHtml = '<div style="overflow:auto;max-width:100%;"><table class="table table-bordered"><thead><tr><th>Select</th><th>Brand</th><th>Item</th><th>SKU</th><th>REF</th><th>COST</th><th>Batch</th><th>Quantity</th></tr></thead><tbody>';
                                data.items.forEach(function(item, idx) {
                                    var qty = item.quantity_inventory || 0;
                                    var costDisplay = (typeof item.cost_item !== 'undefined' && item.cost_item !== null && item.cost_item !== '') ? '$' + parseFloat(item.cost_item).toFixed(2) : '';
                                    var refDisplay = item.ref_item || '';
                                    var batchDisplay = (typeof item.batch_item !== 'undefined' && item.batch_item !== null && item.batch_item !== '') ? item.batch_item : '';
                                    tableHtml += '<tr>' +
                                        '<td><input type="radio" name="selected_item_temp" value="' + idx + '" ' + (idx === 0 ? 'checked' : '') + '></td>' +
                                        '<td>' + (item.brand_item || '') + '</td>' +
                                        '<td>' + (item.item_item || '') + '</td>' +
                                        '<td>' + (item.sku_item || '') + '</td>' +
                                        '<td>' + refDisplay + '</td>' +
                                        '<td>' + costDisplay + '</td>' +
                                        '<td>' + batchDisplay + '</td>' +
                                        '<td>' + qty + '</td>' +
                                        '</tr>';
                                });
                                tableHtml += '</tbody></table></div>';

                                // Input para agregar cantidad (similar a additems.php)
                                var addQtyHtml = '<div class="form-group text-left">' +
                                    '<label for="add-qty-input">Add Quantity (will redirect to edit location):</label>' +
                                    '<input type="number" min="1" id="add-qty-input" class="form-control" style="width:120px;display:inline-block;" />' +
                                    '</div>';

                                Swal.fire({
                                    title: 'UPC already exists!',
                                    html: '<div style="text-align:left">' + tableHtml + addQtyHtml + '</div>',
                                    icon: 'warning',
                                    width: '90%',
                                    showCancelButton: true,
                                    confirmButtonText: 'Add Quantity & Edit Location',
                                    cancelButtonText: 'Use selected item',
                                    confirmButtonColor: '#632b8b',
                                    showDenyButton: true,
                                    denyButtonText: 'Cancel',
                                    denyButtonColor: '#6c757d',
                                    preConfirm: () => {
                                        const addQty = parseInt(document.getElementById('add-qty-input').value);
                                        if (isNaN(addQty) || addQty <= 0) {
                                            Swal.showValidationMessage('Please enter a valid quantity to add.');
                                            return false;
                                        }
                                        const selectedRadio = document.querySelector('input[name="selected_item_temp"]:checked');
                                        if (!selectedRadio) {
                                            Swal.showValidationMessage('Please select an item to update.');
                                            return false;
                                        }
                                        return {
                                            addQty: addQty,
                                            selectedIdx: parseInt(selectedRadio.value)
                                        };
                                    }
                                }).then((result) => {
                                    if (result.isConfirmed && result.value) {
                                        // Add quantity flow - create report and redirect to editLocationFolder
                                        var addQty = result.value.addQty;
                                        var selectedIdx = result.value.selectedIdx;
                                        var selectedItem = data.items[selectedIdx];
                                        var currentQty = parseInt(selectedItem.quantity_inventory) || 0;
                                        var newQty = currentQty + addQty;
                                        
                                        // Create a daily_report entry and redirect to editLocationFolder
                                        $.ajax({
                                            url: '../items/create_report_simple.php',
                                            type: 'POST',
                                            dataType: 'json',
                                            data: {
                                                upc_item: val.toUpperCase(),
                                                sku_item: (selectedItem.sku_item || '').toUpperCase(),
                                                brand_item: selectedItem.brand_item,
                                                item_item: selectedItem.item_item,
                                                ref_item: selectedItem.ref_item || '',
                                                color_item: selectedItem.color_item || '',
                                                size_item: selectedItem.size_item || '',
                                                category_item: selectedItem.category_item || '',
                                                weight_item: selectedItem.weight_item || '',
                                                cost_item: selectedItem.cost_item || '',
                                                batch_item: selectedItem.batch_item || '',
                                                current_quantity: currentQty,
                                                new_quantity: newQty,
                                                added_quantity: addQty
                                            },
                                            success: function(resp) {
                                                console.log('Create report response:', resp);
                                                if (resp.status === 'success') {
                                                    Swal.fire({
                                                        title: 'Success!',
                                                        text: 'Item quantity updated. You will now be redirected to edit the location.',
                                                        icon: 'success',
                                                        confirmButtonColor: '#632b8b',
                                                        confirmButtonText: 'Go to Edit Location'
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            // Redirect to editLocationFolder.php
                                                            window.location.href = 'editLocationFolder.php';
                                                        }
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        title: 'Error',
                                                        text: resp.message || 'Failed to create report entry.',
                                                        icon: 'error',
                                                        confirmButtonColor: '#632b8b'
                                                    });
                                                }
                                            },
                                            error: function(xhr, status, error) {
                                                console.log('AJAX Error:', xhr.responseText);
                                                Swal.fire({
                                                    title: 'Error',
                                                    text: 'Could not connect to server: ' + error,
                                                    icon: 'error',
                                                    confirmButtonColor: '#632b8b'
                                                });
                                            }
                                        });
                                    } else if (result.isDismissed && result.dismiss === 'cancel') {
                                        // Use selected item flow (original functionality)
                                        const selectedRadio = document.querySelector('input[name="selected_item_temp"]:checked');
                                        if (selectedRadio) {
                                            var selIdx = parseInt(selectedRadio.value);
                                            var selectedItem = data.items[selIdx];
                                            var row = input.closest('tr');
                                            if (row) {
                                                input.value = val.toUpperCase();
                                                var skuInput = row.querySelector('input[name="sku_report[]"]');
                                                if (skuInput) skuInput.value = (selectedItem.sku_item || '').toUpperCase();
                                                var costInput = row.querySelector('input[name="cost_report[]"]');
                                                if (costInput) {
                                                    if (typeof selectedItem.cost_item !== 'undefined' && selectedItem.cost_item !== null && selectedItem.cost_item !== '') {
                                                        costInput.value = parseFloat(selectedItem.cost_item).toFixed(2);
                                                    } else {
                                                        costInput.value = '';
                                                    }
                                                }
                                                var batchInput = row.querySelector('input[name="batch_report[]"]');
                                                if (batchInput) batchInput.value = selectedItem.batch_item || '';
                                                var locInput = row.querySelector('input[name="loc_report[]"]');
                                                if (locInput) locInput.value = selectedItem.batch_item || '';
                                            }
                                        }
                                    } else {
                                        // Cancel - restore original value
                                        input.value = originalValue;
                                    }
                                });
                            }
                        },
                        error: function(xhr, status, err) {
                            console.error('[seeReport] verificar_upc error', err);
                        }
                    });
                }, 350);

                // Run on blur and on input (debounced)
                input.addEventListener('blur', runValidate);
                input.addEventListener('input', runValidate);
            });

            // --- Text normalization rules requested by user ---
            // All text inputs -> UPPERCASE, except:
            // - item_report[] -> Capitalize first letter (rest lowercase)
            // - observacion_report[] -> leave as-is (free format)

            function toUpperTrim(s) {
                return s == null ? s : s.trim().toUpperCase();
            }

            // Uppercase without trimming (used while typing so trailing spaces are preserved)
            function toUpperNoTrim(s) {
                return s == null ? s : s.toUpperCase();
            }

            function capitalizeFirst(s) {
                if (s == null) return s;
                s = s.trim().toLowerCase();
                if (s.length === 0) return s;
                return s.charAt(0).toUpperCase() + s.slice(1);
            }

            // Select all text inputs inside the reports table body rows (scoped to the table beginning at line ~682)
            const reportForm = document.querySelector('form[action="procesar_articulos.php"]');
            const reportTable = reportForm ? reportForm.querySelector('table') : document.querySelector('table');
            console.log('[seeReport] reportTable found:', !!reportTable);
            const textInputs = reportTable ? reportTable.querySelectorAll('tbody input[type="text"]') : document.querySelectorAll('input[type="text"]');
            textInputs.forEach(inp => {
                const name = inp.getAttribute('name') || '';
                // Skip observation field
                if (name === 'observacion_report[]') return;

                console.log('[seeReport] attaching normalization to', name);

                // For item_report[] apply capitalize first on blur
                if (name === 'item_report[]') {
                    inp.addEventListener('blur', function() {
                        this.value = capitalizeFirst(this.value);
                    });
                } else {
                    // apply uppercase live on input and final trim+uppercase on blur
                    inp.addEventListener('input', function() {
                        // transform live to uppercase while preserving cursor roughly
                        // Use non-trimming uppercase here so the user can type a space
                        // at the end while composing the next word.
                        const start = this.selectionStart;
                        const end = this.selectionEnd;
                        this.value = toUpperNoTrim(this.value);
                        try {
                            this.setSelectionRange(start, end);
                        } catch (e) {
                            // ignore if not supported
                        }
                    });
                    inp.addEventListener('blur', function() {
                        this.value = toUpperTrim(this.value);
                    });
                }
            });

            // --- Enhanced horizontal scroll synchronization and mouse drag functionality ---
            (function() {
                const topScroll = document.querySelector('.top-scroll-container');
                const mainTable = document.querySelector('.table-container .table-responsive');
                if (!topScroll || !mainTable) return;

                const topScrollContent = topScroll.querySelector('.top-scroll-content');
                const table = mainTable.querySelector('table');

                // Enhanced synchronization function
                function syncTopWidth() {
                    if (!table || !topScrollContent) return;
                    
                    // Force table to recalculate its dimensions
                    table.style.minWidth = '2500px';
                    
                    // Wait a moment for layout to settle
                    setTimeout(() => {
                        // Get the actual scroll width of the table
                        const scrollWidth = table.scrollWidth;
                        const clientWidth = mainTable.clientWidth;
                        
                        // Add a much larger buffer to ensure we can reach the delete button column completely
                        // Adding approximately 350px to reach the Actions column and have extra space
                        const adjustedScrollWidth = scrollWidth + 350;
                        
                        // Set the same scroll width for perfect synchronization
                        topScrollContent.style.width = adjustedScrollWidth + 'px';
                        
                        // Ensure containers have same dimensions for perfect sync
                        topScroll.style.width = clientWidth + 'px';
                        
                        // Ensure the table container stays within bounds
                        mainTable.style.maxWidth = '100%';
                        mainTable.style.width = '100%';
                        
                        console.log('Sync - ScrollWidth:', scrollWidth, 'AdjustedWidth:', adjustedScrollWidth, 'ClientWidth:', clientWidth);
                    }, 10);
                }

                // Improved scroll synchronization with better precision
                let isTopScrolling = false;
                let isMainScrolling = false;

                topScroll.addEventListener('scroll', function() {
                    if (isMainScrolling) return;
                    isTopScrolling = true;
                    
                    // Direct synchronization - use the same scroll position
                    mainTable.scrollLeft = topScroll.scrollLeft;
                    
                    // Debug info to verify scroll range
                    console.log('Top scroll at:', topScroll.scrollLeft, 'Max:', topScroll.scrollWidth - topScroll.clientWidth);
                    
                    setTimeout(() => { isTopScrolling = false; }, 5);
                });

                mainTable.addEventListener('scroll', function() {
                    if (isTopScrolling) return;
                    isMainScrolling = true;
                    
                    // Direct synchronization - use the same scroll position
                    topScroll.scrollLeft = mainTable.scrollLeft;
                    
                    setTimeout(() => { isMainScrolling = false; }, 5);
                });

                // Mouse drag functionality for horizontal scrolling
                let isDragging = false;
                let startX = 0;
                let scrollStartLeft = 0;
                let dragTarget = null;

                // Add drag functionality to both scroll containers
                function addDragFunctionality(element, isTopScroll = false) {
                    element.style.cursor = 'grab';
                    
                    element.addEventListener('mousedown', function(e) {
                        // Only start drag on left mouse button and not on input elements
                        if (e.button !== 0 || e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
                        
                        isDragging = true;
                        startX = e.pageX;
                        scrollStartLeft = isTopScroll ? topScroll.scrollLeft : mainTable.scrollLeft;
                        dragTarget = isTopScroll ? 'top' : 'main';
                        element.style.cursor = 'grabbing';
                        
                        // Prevent text selection while dragging
                        e.preventDefault();
                        document.body.style.userSelect = 'none';
                    });
                }

                // Global mouse events for drag functionality
                document.addEventListener('mousemove', function(e) {
                    if (!isDragging) return;
                    
                    e.preventDefault();
                    const deltaX = startX - e.pageX;
                    const newScrollLeft = Math.max(0, scrollStartLeft + deltaX);
                    
                    // Add dragging class for visual feedback
                    if (dragTarget === 'top') {
                        topScroll.classList.add('dragging');
                        mainTable.classList.add('dragging');
                        topScroll.scrollLeft = newScrollLeft;
                        // This will trigger the scroll event and sync with main table
                    } else {
                        mainTable.classList.add('dragging');
                        topScroll.classList.add('dragging');
                        mainTable.scrollLeft = newScrollLeft;
                        // This will trigger the scroll event and sync with top scroll
                    }
                });

                document.addEventListener('mouseup', function() {
                    if (isDragging) {
                        isDragging = false;
                        dragTarget = null;
                        
                        // Remove dragging classes and reset cursors
                        topScroll.classList.remove('dragging');
                        mainTable.classList.remove('dragging');
                        topScroll.style.cursor = 'grab';
                        mainTable.style.cursor = 'grab';
                        document.body.style.userSelect = '';
                    }
                });

                // Prevent context menu during drag
                topScroll.addEventListener('contextmenu', function(e) {
                    if (isDragging) e.preventDefault();
                });
                
                mainTable.addEventListener('contextmenu', function(e) {
                    if (isDragging) e.preventDefault();
                });

                // Enhanced mouse wheel support for horizontal scrolling
                function addWheelSupport(element, isTopScroll = false) {
                    element.addEventListener('wheel', function(e) {
                        // If scrolling horizontally with trackpad/mouse wheel
                        if (Math.abs(e.deltaX) > Math.abs(e.deltaY)) {
                            e.preventDefault();
                            const scrollAmount = e.deltaX;
                            
                            if (isTopScroll) {
                                topScroll.scrollLeft += scrollAmount;
                            } else {
                                mainTable.scrollLeft += scrollAmount;
                            }
                        }
                        // If scrolling vertically but at the edges horizontally, allow horizontal scroll
                        else if (e.shiftKey || (Math.abs(e.deltaY) > 0 && (
                            (e.deltaY > 0 && (isTopScroll ? topScroll.scrollLeft < topScroll.scrollWidth - topScroll.clientWidth : mainTable.scrollLeft < mainTable.scrollWidth - mainTable.clientWidth)) ||
                            (e.deltaY < 0 && (isTopScroll ? topScroll.scrollLeft > 0 : mainTable.scrollLeft > 0))
                        ))) {
                            e.preventDefault();
                            const scrollAmount = e.deltaY;
                            
                            if (isTopScroll) {
                                topScroll.scrollLeft += scrollAmount;
                            } else {
                                mainTable.scrollLeft += scrollAmount;
                            }
                        }
                    }, { passive: false });
                }

                addWheelSupport(topScroll, true);
                addWheelSupport(mainTable, false);

                // Apply drag functionality
                addDragFunctionality(topScroll, true);
                addDragFunctionality(mainTable, false);

                // Enhanced sync setup with more frequent updates
                syncTopWidth();
                
                // More frequent resize handling
                window.addEventListener('resize', function(){ 
                    syncTopWidth();
                    setTimeout(syncTopWidth, 50); 
                    setTimeout(syncTopWidth, 150); 
                });
                
                // Enhanced ResizeObserver
                if (window.ResizeObserver && table) {
                    const ro = new ResizeObserver(function(entries){ 
                        syncTopWidth();
                        setTimeout(syncTopWidth, 30); 
                    });
                    ro.observe(table);
                    ro.observe(mainTable);
                    ro.observe(topScroll);
                }
                
                // More sync attempts to ensure proper initialization
                setTimeout(syncTopWidth, 50);
                setTimeout(syncTopWidth, 150);
                setTimeout(syncTopWidth, 300);
                setTimeout(syncTopWidth, 600);
                setTimeout(syncTopWidth, 1000);
                
                // Sync on any layout changes
                document.addEventListener('DOMContentLoaded', syncTopWidth);
                window.addEventListener('load', function() {
                    setTimeout(syncTopWidth, 100);
                });
                
                // Sync when table content changes (like when inputs are filled)
                const observer = new MutationObserver(function(mutations) {
                    let shouldSync = false;
                    mutations.forEach(function(mutation) {
                        if (mutation.type === 'childList' || mutation.type === 'attributes') {
                            shouldSync = true;
                        }
                    });
                    if (shouldSync) {
                        setTimeout(syncTopWidth, 100);
                    }
                });
                
                if (table) {
                    observer.observe(table, {
                        childList: true,
                        subtree: true,
                        attributes: true,
                        attributeFilter: ['style', 'class']
                    });
                }
            })();

            // Delete report buttons handler (preserve filters, remove row on success)
            document.querySelectorAll('.delete-report-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const row = this.closest('tr');
                    if (!id) return;

                    if (typeof Swal !== 'undefined' && Swal.fire) {
                        Swal.fire({
                            title: 'Delete report?',
                            text: 'This will remove the report permanently from daily_report.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Delete',
                            confirmButtonColor: '#d33'
                        }).then((res) => {
                            if (res.isConfirmed) doDelete(id, row);
                        });
                    } else {
                        if (confirm('Delete this report?')) doDelete(id, row);
                    }
                });
            });

            function doDelete(id, row) {
                fetch('delete_report.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id_report=' + encodeURIComponent(id)
                }).then(r => r.json()).then(data => {
                    if (data && data.status === 'success') {
                        if (row) row.remove();
                        if (typeof Swal !== 'undefined' && Swal.fire) {
                            Swal.fire({ title: 'Deleted', icon: 'success', timer: 1200, showConfirmButton: false });
                        }
                    } else {
                        const msg = data && data.message ? data.message : 'Delete failed';
                        if (typeof Swal !== 'undefined' && Swal.fire) {
                            Swal.fire({ title: 'Error', text: msg, icon: 'error' });
                        } else {
                            alert(msg);
                        }
                    }
                }).catch(err => {
                    console.error('Delete error', err);
                    if (typeof Swal !== 'undefined' && Swal.fire) {
                        Swal.fire({ title: 'Error', text: 'Network error', icon: 'error' });
                    } else {
                        alert('Network error');
                    }
                });
            }
        });
    </script>
</body>

</html>