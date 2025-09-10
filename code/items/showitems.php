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
function deleteMember($id_item)
{
    global $mysqli; // Asegurar acceso a la conexión global

    $query = "DELETE FROM items WHERE id_item  = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $id_item);

    // Construir la URL de redirección con los filtros
    $redirect_url = "showitems.php";
    $filters = [];
    if (!empty($_GET['upc_item'])) $filters[] = "upc_item=" . urlencode($_GET['upc_item']);
    if (!empty($_GET['brand'])) $filters[] = "brand=" . urlencode($_GET['brand']);
    if (!empty($_GET['size'])) $filters[] = "size=" . urlencode($_GET['size']);
    if (!empty($_GET['ref'])) $filters[] = "ref=" . urlencode($_GET['ref']);
    
    if (!empty($filters)) {
        $redirect_url .= "?" . implode("&", $filters);
    }

    if ($stmt->execute()) {
        echo "<script>alert('Item deleted correctly');
        window.location = '$redirect_url';</script>";
    } else {
        echo "<script>alert('Error deleting the item');
        window.location = '$redirect_url';</script>";
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

// Obtener los filtros desde el formulario
$upc_item = isset($_GET['upc_item']) ? trim($_GET['upc_item']) : '';
$item = isset($_GET['item']) ? trim($_GET['item']) : '';
$reference = isset($_GET['ref']) ? trim($_GET['ref']) : '';
$plan = isset($_GET['plan_cta']) ? trim($_GET['plan_cta']) : '';
$estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';
// Nuevos filtros solicitados
$brand_filter = isset($_GET['brand']) ? trim($_GET['brand']) : '';
$size_filter = isset($_GET['size']) ? trim($_GET['size']) : '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ASWWORKING | ITEMS</title>
    <link href="../../../fontawesome/css/all.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <style>
        :root {
            --primary-dark: #4a2568;
            --primary: #632b8b;
            --primary-light: #5d337a;
            --secondary: #997cab;
            --secondary-light: #dac7e5;
            --text-dark: #2d2d2d;
            --text-light: #f8f9fa;
            --bg-light: #f5f3f7;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Header styles */
        .header-container {
            width: 100%;
            background-color: var(--secondary-light);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .logo {
            height: 100px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .page-title {
            color: var(--primary);
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 10px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(to right, var(--primary), var(--secondary-light));
            border-radius: 3px;
        }

        /* Search form */
        .search-form {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
        }

        .search-form input[type="text"] {
            border: 1px solid var(--secondary);
            border-radius: 6px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .search-form input[type="text"]:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
            outline: none;
        }

        .search-form input[type="submit"] {
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .search-form input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Table styles */
        .table-container {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            background: white;
            margin-bottom: 30px;
            position: relative;
        }

        /* Scroll hint indicator */
        .scroll-hint {
            position: absolute;
            top: 10px;
            right: 15px;
            background: rgba(99, 43, 139, 0.1);
            color: var(--primary);
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.7rem;
            font-weight: 600;
            z-index: 15;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                opacity: 1;
            }
        }

        @media (min-width: 1400px) {
            .scroll-hint {
                display: none;
            }
        }

        /* Top scroll bar */
        .top-scroll-container {
            overflow-x: auto;
            overflow-y: hidden;
            height: 35px;
            border: 2px solid var(--primary);
            background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
            border-radius: 8px;
            position: relative;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .top-scroll-content {
            height: 30px;
            width: 1100px;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Force scrollbar to always show */
        .top-scroll-container {
            scrollbar-width: auto !important;
        }

        /* Custom scrollbar for top scroll */
        .top-scroll-container::-webkit-scrollbar {
            height: 18px !important;
            background: #e9ecef;
            border-radius: 0 0 6px 6px;
        }

        .top-scroll-container::-webkit-scrollbar-track {
            background: linear-gradient(to right, #e9ecef, #f8f9fa, #e9ecef);
            border-radius: 0 0 6px 6px;
        }

        .top-scroll-container::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, var(--primary), var(--primary-dark));
            border-radius: 6px;
            min-width: 60px;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .top-scroll-container::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, var(--primary-dark), var(--primary));
            transform: scale(1.05);
        }

        /* Visual indicators for the scroll bar */
        .top-scroll-container::before {
            position: absolute;
            top: 2px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            font-weight: bold;
            color: var(--primary);
            letter-spacing: 1px;
            z-index: 10;
            text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.9);
            padding: 1px 8px;
            border-radius: 3px;
        }

        .top-scroll-container::after {
            content: '';
            position: absolute;
            bottom: 3px;
            left: 8px;
            right: 8px;
            height: 2px;
            background: linear-gradient(to right, var(--primary), transparent, var(--primary));
            border-radius: 1px;
            opacity: 0.6;
        }

        .table-responsive {
            border-radius: 10px;
            overflow-x: auto;
            position: relative;
            box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05);
            scrollbar-width: thin;
            scrollbar-color: var(--primary) #f1f1f1;
            border: 1px solid #dee2e6;
        }

        /* Custom scrollbar for webkit browsers */
        .table-responsive::-webkit-scrollbar {
            height: 12px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
            border: 2px solid #f1f1f1;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* Scroll indicator */
        .table-container::after {
            content: '← Desliza para ver más columnas →';
            position: absolute;
            bottom: 5px;
            right: 15px;
            font-size: 0.75rem;
            color: var(--secondary);
            background: rgba(255, 255, 255, 0.9);
            padding: 2px 8px;
            border-radius: 4px;
            pointer-events: none;
            z-index: 5;
        }

        @media (min-width: 1400px) {
            .table-container::after {
                display: none;
            }
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-width: 1100px;
            /* Reduced from 1200px for better fit */
        }

        thead {
            background: var(--primary) !important;
            color: white !important;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        thead th {
            padding: 14px 8px !important;
            text-align: center !important;
            font-weight: 600 !important;
            text-transform: uppercase !important;
            font-size: 0.75rem !important;
            letter-spacing: 0.5px !important;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1) !important;
            position: relative !important;
            white-space: nowrap !important;
            background-color: var(--primary) !important;
            color: white !important;
        }

        tbody tr {
            background-color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        tbody tr:nth-child(even) {
            background-color: rgba(248, 240, 255, 0.8);
        }

        tbody tr:hover {
            background-color: white;
            box-shadow: 0 4px 12px rgba(99, 43, 139, 0.1);
        }

        tbody td {
            padding: 10px 8px !important;
            border-bottom: 1px solid rgba(153, 124, 171, 0.3) !important;
            color: var(--text-dark) !important;
            font-size: 0.85rem !important;
            transition: all 0.2s ease !important;
            vertical-align: middle !important;
            text-align: center !important;
        }

        /* Specific column styling */
        tbody td:nth-child(15) {
            /* STORES column */
            max-width: 150px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            text-align: left !important;
        }

        /* Override Bootstrap table styles */
        .table> :not(caption)>*>* {
            padding: 10px 8px;
            background-color: var(--bs-table-bg);
            border-bottom-width: 1px;
            box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
        }

        .table-striped>tbody>tr:nth-of-type(odd)>td {
            background-color: rgba(248, 240, 255, 0.8) !important;
        }

        .table-hover>tbody>tr:hover>td {
            background-color: white !important;
            box-shadow: 0 4px 12px rgba(99, 43, 139, 0.1) !important;
        }

        /* Action buttons */
        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 36px;
            padding: 6px 10px;
            border-radius: 8px;
            transition: all 0.18s ease;
            border: none;
            background: rgba(0, 0, 0, 0.04);
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
        }

        .btn-edit {
            color: #ffffff;
            background: linear-gradient(90deg, #28a745, #20c997);
            border: 1px solid rgba(40, 167, 69, 0.15);
        }

        .btn-edit svg {
            width: 18px;
            height: 18px;
        }

        .btn-edit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(40, 167, 69, 0.18);
        }

        .btn-delete {
            color: #ffffff;
            background: linear-gradient(90deg, #dc3545, #e55353);
            border: 1px solid rgba(220, 53, 69, 0.15);
        }

        .btn-delete svg {
            width: 18px;
            height: 18px;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(220, 53, 69, 0.18);
        }

        /* Back button */
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

        /* Modal styles */
        .modal-header {
            background-color: var(--secondary-light);
            border-bottom: 1px solid var(--secondary);
        }

        .modal-title {
            color: var(--primary);
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(to bottom, var(--primary-light), var(--primary));
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .logo {
                height: 80px;
            }

            .page-title {
                font-size: 1.8rem;
            }
        }

        /* Checkbox styles for stores in modal */
        .checkbox-group {
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background-color: #f8f9fa;
            margin-top: 5px;
        }

        .form-check-inline {
            margin-right: 20px;
            margin-bottom: 10px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-top: 0.125em;
            border: 2px solid #6c757d;
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 43, 139, 0.25);
        }

        .form-check-label {
            font-weight: 600;
            color: #495057;
            margin-left: 8px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="header-container">
        <div class="container text-center">
            <img src='../../img/logo.png' class="logo" alt="ASWWORKING Logo">
        </div>
    </div>

    <div class="container">
        <h1 class="page-title text-center"><i class="fa-solid fa-file-signature"></i> ITEMS</h1>

        <!-- Search Form -->
        <div class="search-form">
            <form action="showitems.php" method="get" class="row g-3">
                <div class="col-6 col-md-2">
                    <input type="text" name="upc_item" class="form-control form-control-sm" placeholder="UPC" value="<?= htmlspecialchars($upc_item) ?>">
                </div>
                <!-- <div class="col-6 col-md-3">
                    <input type="text" name="item" class="form-control form-control-sm" placeholder="Item" value="<?= htmlspecialchars($item) ?>">
                </div> -->
                <div class="col-6 col-md-2">
                    <input type="text" name="brand" class="form-control form-control-sm" placeholder="Brand" value="<?= htmlspecialchars($brand_filter) ?>">
                </div>
                <div class="col-4 col-md-1">
                    <input type="text" name="size" class="form-control form-control-sm" placeholder="Size" value="<?= htmlspecialchars($size_filter) ?>">
                </div>
                <div class="col-6 col-md-2">
                    <input type="text" name="ref" class="form-control form-control-sm" placeholder="Reference" value="<?= htmlspecialchars($reference) ?>">
                </div>
                <div class="col-6 col-md-2 d-grid">
                    <input type="submit" value="Search" class="btn btn-primary">
                </div>
            </form>
        </div>
        <div class="text-center mb-4">
            <a href="../../access.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
        <?php
        date_default_timezone_set("America/Bogota");
        require_once("../../zebra.php");

        // Inicializa la consulta base
        // Usamos un LEFT JOIN contra un subquery agrupado para evitar duplicados
        // cuando la tabla inventory tiene múltiples filas por UPC.
        // Aggregate inventory by UPC and SKU so stock is associated to the specific SKU_item
        $queryBase = "SELECT items.*, inv.quantity_inventory, inv.inv_inventory_item AS inv_inventory_item, inv.observation_inventory
        FROM items
        LEFT JOIN (
         SELECT upc_inventory, sku_inventory,
             SUM(quantity_inventory) AS quantity_inventory,
             GROUP_CONCAT(DISTINCT item_inventory SEPARATOR ', ') AS inv_inventory_item,
             MAX(observation_inventory) AS observation_inventory
         FROM inventory
            GROUP BY upc_inventory, sku_inventory
        ) AS inv ON items.upc_item = inv.upc_inventory AND items.sku_item = inv.sku_inventory
            WHERE 1=1 AND items.estado_item = 1";

        // Agrega filtros si existen
        if (!empty($_GET['upc_item'])) {
            $upc_item = $mysqli->real_escape_string($_GET['upc_item']);
            $queryBase .= " AND upc_item LIKE '%$upc_item%'";
        }
        if (!empty($_GET['item'])) {
            $item = $mysqli->real_escape_string($_GET['item']);
            $queryBase .= " AND item_item LIKE '%$item%'";
        }
        // Filtro por brand
        if (!empty($_GET['brand'])) {
            $brand = $mysqli->real_escape_string($_GET['brand']);
            $queryBase .= " AND brand_item LIKE '%$brand%'";
        }
        // Filtro por size
        if (!empty($_GET['size'])) {
            $size = $mysqli->real_escape_string($_GET['size']);
            $queryBase .= " AND size_item LIKE '%$size%'";
        }
        if (!empty($_GET['ref'])) {
            $reference = $mysqli->real_escape_string($_GET['ref']);
            $queryBase .= " AND ref_item = '$reference'";
        }

        // Ordenar por cantidad agregada en inventory (subquery alias inv)
        $queryBase .= " ORDER BY inv.quantity_inventory DESC";

        // Consulta para conteo (no necesita JOIN a inventory)
        $countQuery = "SELECT COUNT(*) as total FROM items WHERE 1=1 AND estado_item = 1";

        // Aplicar mismos filtros a countQuery
        if (!empty($_GET['upc_item'])) {
            $countQuery .= " AND items.upc_item LIKE '%$upc_item%'";
        }
        if (!empty($_GET['item'])) {
            $countQuery .= " AND items.item_item LIKE '%$item%'";
        }
        if (!empty($_GET['brand'])) {
            $brand = $mysqli->real_escape_string($_GET['brand']);
            $countQuery .= " AND items.brand_item LIKE '%$brand%'";
        }
        if (!empty($_GET['size'])) {
            $size = $mysqli->real_escape_string($_GET['size']);
            $countQuery .= " AND items.size_item LIKE '%$size%'";
        }
        if (!empty($_GET['ref'])) {
            $countQuery .= " AND items.ref_item = '$reference'";
        }

        $countResult = $mysqli->query($countQuery);
        if (!$countResult) die("Error en conteo: " . $mysqli->error);
        $countRow = $countResult->fetch_assoc();
        $num_registros = $countRow['total'];

        $resul_x_pagina = 50;

        if ($num_registros > 0) {
            // Configuración de Zebra_Pagination
            $paginacion = new Zebra_Pagination();
            $paginacion->records($num_registros);
            $paginacion->records_per_page($resul_x_pagina);

            $page = $paginacion->get_page(); // Obtiene la página actual
            $offset = ($page - 1) * $resul_x_pagina; // Calcula el desplazamiento
            $queryFinal = $queryBase . " LIMIT $offset, $resul_x_pagina";
            $result = $mysqli->query($queryFinal);

            if (!$result) {
                die("Error en la consulta: " . $mysqli->error);
            }
        ?> <div class="table-container">

                <br>
                <!-- Top scroll bar -->
                <div class="top-scroll-container" title="Arrastra la barra inferior para hacer scroll horizontal en la tabla">
                    <div class="top-scroll-content"></div>
                </div>

                <div class="table-responsive" id="main-table">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="min-width: 40px; text-align: center;">No.</th>
                                <th style="min-width: 110px; text-align: center;">UPC</th>
                                <th style="min-width: 90px; text-align: center;">SKU</th>
                                <th style="min-width: 90px; text-align: center;">DATE</th>
                                <th style="min-width: 90px; text-align: center;">BRAND</th>
                                <th style="min-width: 140px; text-align: center;">ITEM</th>
                                <th style="min-width: 70px; text-align: center;">STYLE</th>
                                <th style="min-width: 70px; text-align: center;">FOLDER</th>
                                <th style="min-width: 70px; text-align: center;">COLOR</th>
                                <th style="min-width: 70px; text-align: center;">SIZE</th>
                                <th style="min-width: 70px; text-align: center;">COST</th>
                                <th style="min-width: 70px; text-align: center;">WEIGHT</th>
                                <th style="min-width: 70px; text-align: center;">STOCK</th>
                                <th style="min-width: 70px; text-align: center;">BATCH</th>
                                <th style="min-width: 100px; text-align: center;">LOCATION</th>
                                <th style="min-width: 110px; text-align: center;">STORES</th>
                                <th style="min-width: 140px; text-align: center;">OBSERVATION</th>
                                <th style="min-width: 50px; text-align: center;">EDIT</th>
                                <th style="min-width: 50px; text-align: center;">DELETE</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            while ($row = mysqli_fetch_array($result)) {
                                // Procesar stores_item para mostrar de manera legible
                                $stores_display = '';
                                if (!empty($row['stores_item'])) {
                                    $stores_array = json_decode($row['stores_item'], true);
                                    if (is_array($stores_array)) {
                                        $stores_display = implode(', ', $stores_array);
                                    } else {
                                        $stores_display = $row['stores_item'];
                                    }
                                } else {
                                    $stores_display = '<span class="text-muted">Not assigned</span>';
                                }

                                echo '<tr>
                                <td>' . $i . '</td>
                                <td>' . $row['upc_item'] . '</td>
                                <td>' . $row['sku_item'] . '</td>
                                <td>' . $row['date_item'] . '</td>
                                <td style="text-transform:uppercase;">' . $row['brand_item'] . '</td>
                                <td>' . $row['item_item'] . '</td>
                                <td>' . $row['ref_item'] . '</td>
                                <td>' . $row['folder_item'] . '</td>
                                <td>' . $row['color_item'] . '</td>
                                <td>' . $row['size_item'] . '</td>
                                <td>' . $row['cost_item'] . '</td>
                                <td>' . $row['weight_item'] . '</td>
                                <td>' . $row['quantity_inventory'] . '</td>
                                <td>' . ($row['batch_item'] ?? '') . '</td>
                                <td>' . ($row['inventory_item'] ?? '') . '</td>
                                <td>' . $stores_display . '</td>
                                <td>' . (isset($row['observation_item']) && $row['observation_item'] !== '' ? htmlspecialchars($row['observation_item']) : '<span class="text-muted">-</span>') . '</td>
                                <td>
                                    <button type="button" class="btn-action btn-edit" 
                                        data-bs-toggle="modal" data-bs-target="#modalEdicion"
                                        data-upc="' . $row['upc_item'] . '"
                                        data-sku="' . $row['sku_item'] . '"
                                        data-date="' . $row['date_item'] . '"
                                        data-brand="' . $row['brand_item'] . '"
                                        data-item="' . $row['item_item'] . '"
                                        data-ref="' . $row['ref_item'] . '"
                                        data-color="' . $row['color_item'] . '"
                                        data-size="' . $row['size_item'] . '"
                                        data-category="' . $row['category_item'] . '"
                                        data-cost="' . $row['cost_item'] . '"
                                        data-weight="' . $row['weight_item'] . '"
                                        data-id="' . $row['id_item'] . '"
                                        data-estado="' . $row['estado_item'] . '"
                                        data-stock="' . $row['quantity_inventory'] . '"
                                        data-batch="' . $row['inventory_item'] . '"
                                        data-observation="' . htmlspecialchars($row['observation_inventory']) . '"
                                        data-stores=\'' . htmlspecialchars($row['stores_item']) . '\'>
                                        <!-- Inline edit SVG icon + label for visibility -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true" focusable="false">
                                            <path d="M12.146.854a.5.5 0 0 1 .708 0l2.292 2.292a.5.5 0 0 1 0 .708l-9.193 9.193a.5.5 0 0 1-.168.11l-4 1.5a.5.5 0 0 1-.65-.65l1.5-4a.5.5 0 0 1 .11-.168l9.193-9.193zM11.207 2L3 10.207V12h1.793L14 3.793 11.207 2z"/>
                                        </svg>
                                        <span class="visually-hidden">Edit</span>
                                    </button>     
                                </td>
                                <td>
                                    <a href="?delete=' . $row['id_item'] . '" onclick="return confirm(\'¿Are you sure to Delete this item?\');" class="btn-action btn-delete" title="Delete item" aria-label="Delete item">
                                        <!-- Inline delete SVG icon + visible label -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true" focusable="false">
                                            <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5H6a.5.5 0 0 1-.5-.5v-7zM4.118 4 4 4.059V5h8V4.059L11.882 4H4.118zM2.5 3a1 1 0 0 1 1-1H6l.5-.5A1 1 0 0 1 7.5 1h1a1 1 0 0 1 .866.5L9.5 2h2.999a1 1 0 0 1 1 1v1H2.5V3z"/>
                                        </svg>
                                        <span class="visually-hidden">Delete</span>
                                    </a>
                                </td>
                            </tr>';
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php
            // Mostrar paginación
            $paginacion->render();
        } else {
            echo '<div class="alert alert-info text-center">No items found matching your criteria.</div>';
        }
        ?>

        <div class="text-center mb-4">
            <a href="../../access.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <!-- Modal Edicion -->
    <div class="modal fade" id="modalEdicion" tabindex="-1" aria-labelledby="modalEdicionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEdicionLabel">Edit Item</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="editItems.php" method="POST">
                        <input type="hidden" id="edit-id" name="id">
                        <!-- Campos ocultos para mantener los filtros -->
                        <input type="hidden" name="filter_upc_item" value="<?= htmlspecialchars($upc_item) ?>">
                        <input type="hidden" name="filter_brand" value="<?= htmlspecialchars($brand_filter) ?>">
                        <input type="hidden" name="filter_size" value="<?= htmlspecialchars($size_filter) ?>">
                        <input type="hidden" name="filter_ref" value="<?= htmlspecialchars($reference) ?>">

                        <!-- Primera Sección: Datos Generales -->
                        <h5 class="mb-3"> General</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-upc" name="upc">
                                    <label for="edit-upc">UPC</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-sku" name="sku">
                                    <label for="edit-sku">SKU</label>
                                </div>
                            </div>
                        </div>

                        <!-- Segunda Sección: Información del Producto -->
                        <h5 class="mb-3">Product Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-brand" name="brand">
                                    <label for="edit-brand">Brand</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-item" name="item">
                                    <label for="edit-item">Item</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-ref" name="ref">
                                    <label for="edit-ref">Reference</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-category" name="category">
                                    <label for="edit-category">Category</label>
                                </div>
                            </div>
                        </div>

                        <!-- Tercera Sección: Atributos del Producto -->
                        <h5 class="mb-3">Product Attributes</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-color" name="color">
                                    <label for="edit-color">Color</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-size" name="size">
                                    <label for="edit-size">Size</label>
                                </div>
                            </div>
                        </div>

                        <!-- Cuarta Sección: Datos Financieros -->
                        <h5 class="mb-3">Data</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-cost" name="cost">
                                    <label for="edit-cost">Cost</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-weight" name="weight">
                                    <label for="edit-weight">Weight</label>
                                </div>
                            </div>
                        </div>

                        <!-- Última Sección: Stock y Lote -->
                        <h5 class="mb-3">Stock</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-stock" name="stock">
                                    <label for="edit-stock">Stock</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="edit-batch" name="batch">
                                    <label for="edit-batch">Batch</label>
                                </div>
                            </div>
                        </div> <!-- Fecha -->
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="edit-date" name="date">
                            <label for="edit-date">Date</label>
                        </div>

                        <!-- Sección de Tiendas -->
                        <h5 class="mb-3">Stores to Publish</h5>
                        <div class="checkbox-group mb-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="stores[]" value="AS001" id="edit_store_AS001">
                                <label class="form-check-label" for="edit_store_AS001">AS001</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="stores[]" value="EB001" id="edit_store_EB001">
                                <label class="form-check-label" for="edit_store_EB001">EB001</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="stores[]" value="EB002" id="edit_store_EB002">
                                <label class="form-check-label" for="edit_store_EB002">EB002</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="stores[]" value="AM002" id="edit_store_AM002">
                                <label class="form-check-label" for="edit_store_AM002">AM002</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="stores[]" value="WM001" id="edit_store_WM001">
                                <label class="form-check-label" for="edit_store_WM001">WM001</label>
                            </div>
                        </div>
                        <!-- Observation field -->
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="edit-observation" name="observation" rows="2" maxlength="255" placeholder="Observation"></textarea>
                            <label for="edit-observation">Observation</label>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="guardarCambios">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let modalEdicion = document.getElementById("modalEdicion");

            modalEdicion.addEventListener("show.bs.modal", function(event) {
                let button = event.relatedTarget; // Botón que abrió el modal

                document.getElementById("edit-upc").value = button.getAttribute("data-upc");
                document.getElementById("edit-sku").value = button.getAttribute("data-sku");
                document.getElementById("edit-date").value = button.getAttribute("data-date");
                document.getElementById("edit-brand").value = button.getAttribute("data-brand");
                document.getElementById("edit-item").value = button.getAttribute("data-item");
                document.getElementById("edit-ref").value = button.getAttribute("data-ref");
                document.getElementById("edit-color").value = button.getAttribute("data-color");
                document.getElementById("edit-size").value = button.getAttribute("data-size");
                document.getElementById("edit-category").value = button.getAttribute("data-category");
                document.getElementById("edit-cost").value = button.getAttribute("data-cost");
                document.getElementById("edit-weight").value = button.getAttribute("data-weight");
                document.getElementById("edit-stock").value = button.getAttribute("data-stock");
                document.getElementById("edit-batch").value = button.getAttribute("data-batch");
                document.getElementById("edit-id").value = button.getAttribute("data-id");
                document.getElementById("edit-observation").value = button.getAttribute("data-observation") || '';

                // Cargar las tiendas seleccionadas
                loadStores(button.getAttribute("data-stores"));
            });

            function loadStores(storesJson) {
                // Limpiar todos los checkboxes primero
                const storeCheckboxes = document.querySelectorAll('input[name="stores[]"]');
                storeCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });

                // Si hay datos de tiendas, procesarlos
                if (storesJson && storesJson !== 'null' && storesJson !== '') {
                    try {
                        const stores = JSON.parse(storesJson);
                        if (Array.isArray(stores)) {
                            stores.forEach(store => {
                                const checkbox = document.getElementById('edit_store_' + store);
                                if (checkbox) {
                                    checkbox.checked = true;
                                }
                            });
                        }
                    } catch (e) {
                        console.log('Error parsing stores JSON:', e);
                    }
                }
            } // Sincronizar scroll horizontal superior con el de la tabla
            const topScroll = document.querySelector('.top-scroll-container');
            const mainTable = document.querySelector('.table-responsive');

            if (topScroll && mainTable) {
                console.log('Scroll elements found'); // Debug

                // Forzar que el top scroll sea visible ajustando su contenido al ancho real de la tabla
                const table = mainTable.querySelector('table');
                if (table) {
                    const tableWidth = table.offsetWidth;
                    const topScrollContent = topScroll.querySelector('.top-scroll-content');
                    if (topScrollContent) {
                        topScrollContent.style.width = tableWidth + 'px';
                        console.log('Set top scroll width to:', tableWidth); // Debug
                    }
                }

                // Cuando se hace scroll en la barra superior
                topScroll.addEventListener('scroll', function() {
                    console.log('Top scroll moved:', topScroll.scrollLeft); // Debug
                    mainTable.scrollLeft = topScroll.scrollLeft;
                });

                // Cuando se hace scroll en la tabla principal
                mainTable.addEventListener('scroll', function() {
                    console.log('Main table scroll moved:', mainTable.scrollLeft); // Debug
                    topScroll.scrollLeft = mainTable.scrollLeft;
                });

                // Forzar actualización después de que la página esté completamente cargada
                setTimeout(function() {
                    const table = mainTable.querySelector('table');
                    if (table) {
                        const tableWidth = table.scrollWidth;
                        const topScrollContent = topScroll.querySelector('.top-scroll-content');
                        if (topScrollContent) {
                            topScrollContent.style.width = tableWidth + 'px';
                            console.log('Updated top scroll width to:', tableWidth); // Debug
                        }
                    }
                }, 500);
            } else {
                console.log('Scroll elements not found'); // Debug
            }
        });
    </script>
</body>

</html>