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
function deleteMember($id_sucursal)
{
    global $mysqli;
    $query = "DELETE FROM sucursal WHERE id_sucursal = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $id_sucursal);

    if ($stmt->execute()) {
        echo "<script>alert('sucursal deleted correctly');
        window.location = 'seeSucursal.php';</script>";
    } else {
        echo "<script>alert('Error deleting the sucursal');
        window.location = 'seeSucursal.php';</script>";
    }
    $stmt->close();
}

// Obtener los filtros desde el formulario
$store_name = isset($_GET['store_name']) ? trim($_GET['store_name']) : '';
$code_sucursal = isset($_GET['code_sucursal']) ? trim($_GET['code_sucursal']) : '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ASWWORKING | SUCURSALES</title>
    <!-- Carga Font Awesome desde CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        thead {
            background: var(--primary);
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
            padding: 12px 10px;
            border-bottom: 1px solid rgba(153, 124, 171, 0.3);
            color: var(--text-dark);
            font-size: 0.9rem;
            transition: all 0.2s ease;
            text-align: center;
        }

        /* Action buttons */
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            transition: all 0.3s ease;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-edit {
            color: var(--primary);
        }

        .btn-edit:hover {
            background-color: rgba(99, 43, 139, 0.1);
            transform: scale(1.1);
        }

        .btn-delete {
            color: #dc3545;
        }

        .btn-delete:hover {
            background-color: rgba(220, 53, 69, 0.1);
            transform: scale(1.1);
        }

        /* Back button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: transparent;
            border: none;
            color: var(--primary-light);
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
            color: var(--primary);
            transform: translateX(-3px);
        }

        .back-btn i {
            transition: transform 0.3s ease;
        }

        .back-btn:hover i {
            transform: scale(1.1);
        }

        /* Add button */
        .btn-add-sucursal {
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-sucursal:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background: linear-gradient(to bottom, var(--primary-light), var(--primary));
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

            .search-form .row {
                flex-direction: column;
            }

            .search-form input[type="submit"] {
                width: 100%;
            }
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
        <h1 class="page-title text-center"><i class="fa-solid fa-store"></i> STORES</h1>

        <!-- Search Form -->
        <div class="search-form">
            <form action="seeSucursal.php" method="get" class="row g-3">
                <div class="col-md-5">
                    <input type="text" name="store_name" class="form-control" placeholder="Store Name" value="<?= htmlspecialchars($store_name) ?>">
                </div>
                <div class="col-md-5">
                    <input type="text" name="code_sucursal" class="form-control" placeholder="Sucursal Code" value="<?= htmlspecialchars($code_sucursal) ?>">
                </div>
                <div class="col-md-2">
                    <input type="submit" value="Search" class="btn btn-primary w-100">
                </div>
            </form>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="mx-auto">
                <button type="button" class="back-btn" onclick="window.location.href='../../access.php'">
                    <i class="fas fa-arrow-left"></i>
                </button>
            </div>
            <div class="me-3">
                <button type="button" class="btn-add-sucursal" data-bs-toggle="modal" data-bs-target="#modalAddSucursal">
                    <i class="fas fa-plus"></i> Add Sucursal
                </button>
            </div>
        </div>

        <?php
        date_default_timezone_set("America/Bogota");
        require_once("../../zebra.php");

        //traigo las tiendas para el select del modal
        $sql_store = "SELECT * FROM store ORDER BY store_name ASC";
        $result_store = $mysqli->query($sql_store);
        if (!$result_store) {
            die("Error en la consulta: " . $mysqli->error);
        }
        $stores = $result_store->fetch_all(MYSQLI_ASSOC);

        $queryBase = "
    SELECT 
        store.id_store,
        store.store_name,
        sucursal.id_sucursal,
        sucursal.code_sucursal,
        sucursal.items_price,
        sucursal.shipping_received,
        sucursal.tax,
        sucursal.incentives_offered,
        GROUP_CONCAT(CONCAT('Sales less than $', f.sales_less_than , ': ', f.comision, ' fee / ', f.cargo_fijo, ' Fixed Charge') SEPARATOR '<br>') AS detalles_rangos
    FROM store
    JOIN sucursal ON store.id_store = sucursal.id_store
    LEFT JOIN fee_config_sucursal f ON sucursal.id_sucursal = f.id_sucursal
    WHERE 1=1
";

        // Filtro por nombre de tienda
        if (!empty($_GET['store_name'])) {
            $nombre_store = $mysqli->real_escape_string($_GET['store_name']);
            $queryBase .= " AND store.store_name LIKE '%$nombre_store%'";
        }

        // Filtro por código de sucursal
        if (!empty($_GET['code_sucursal'])) {
            $code_sucursal = $mysqli->real_escape_string($_GET['code_sucursal']);
            $queryBase .= " AND sucursal.code_sucursal LIKE '%$code_sucursal%'";
        }

        // Agrupar por sucursal para que funcione el GROUP_CONCAT
        $queryBase .= "
            GROUP BY sucursal.id_sucursal
            ORDER BY store.store_name ASC
        ";
        // Conteo de registros
        $countQuery = "SELECT COUNT(DISTINCT sucursal.id_sucursal) as total 
        FROM store 
        JOIN sucursal ON store.id_store = sucursal.id_store
        WHERE 1=1";

        if (!empty($_GET['store_name'])) {
            $countQuery .= " AND store.store_name LIKE '%$nombre_store%'";
        }

        if (!empty($_GET['code_sucursal'])) {
            $countQuery .= " AND sucursal.code_sucursal LIKE '%$code_sucursal%'";
        }

        // Ejecutar conteo
        $countResult = $mysqli->query($countQuery);
        if (!$countResult) die("Error en conteo: " . $mysqli->error);

        $countRow = $countResult->fetch_assoc();
        $num_registros = $countRow['total'];

        // Paginación y ejecución final
        $resul_x_pagina = 50;
        if ($num_registros > 0) {
            $paginacion = new Zebra_Pagination();
            $paginacion->records($num_registros);
            $paginacion->records_per_page($resul_x_pagina);

            $page = $paginacion->get_page();
            $offset = ($page - 1) * $resul_x_pagina;

            $queryFinal = $queryBase . " LIMIT $offset, $resul_x_pagina";
            $result = $mysqli->query($queryFinal);

            if (!$result) {
                die("Error en la consulta: " . $mysqli->error);
            }

            echo '<div class="">';
            echo '<table class="">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>No.</th>';
            echo '<th>STORE NAME</th>';
            echo '<th>REFERENCE CODE</th>';
            echo '<th>OPTIONS</th>';
            echo '<th>RANGES</th>';
            echo '<th>ACTIONS</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            $i = 1;
            while ($row = mysqli_fetch_array($result)) {
                // Crear una cadena de opciones seleccionadas
                $options = [];
                if ($row['items_price'] == 1) $options[] = 'Items price';
                if ($row['shipping_received'] == 1) $options[] = 'Shipping received';
                if ($row['tax'] == 1) $options[] = 'Tax';
                if ($row['incentives_offered'] == 1) $options[] = 'Incentives Offered';
                $options_text = !empty($options) ? implode('<br>', $options) : '<em>None</em>';
                
                echo '<tr>';
                echo '<td>' . $i++ . '</td>';
                echo '<td>' . htmlspecialchars($row['store_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['code_sucursal']) . '</td>';
                echo '<td>' . $options_text . '</td>';
                echo '<td>' . ($row['detalles_rangos'] ? $row['detalles_rangos'] : '<em>No config</em>') . '</td>';
                echo '<td class="action-buttons">';
                echo '<button type="button" class="btn-action btn-edit" 
                    data-bs-toggle="modal" data-bs-target="#modalEdicionUnico"
                    data-id_sucursal="' . $row['id_sucursal'] . '"
                    data-id_store="' . htmlspecialchars($row['id_store']) . '"
                    data-code_sucursal="' . htmlspecialchars($row['code_sucursal']) . '">
                    <i class="fas fa-edit"></i>
                </button>';
                echo '<a href="?delete=' . $row['id_sucursal'] . '" onclick="return confirm(\'¿Are you sure to Delete this sucursal?\');" class="btn-action btn-delete">
            <i class="fas fa-trash-alt"></i>
          </a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
            echo '</div>';

            // Mostrar paginación
            $paginacion->render();
        } else {
            echo '<div class="alert alert-info">No se encontraron resultados.</div>';
        }
        ?>
    </div>

    <!-- Modal Edicion -->
    <div class="modal fade" id="modalEdicionUnico" tabindex="-1" aria-labelledby="modalEdicionUnicoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Header (lo dejamos igual como quieres) -->
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEdicionUnicoLabel">Edit Sucursal and Ranges</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <form id="formEdicionUnico" action="editSucursal.php" method="POST" class="p-4">
                    <input type="hidden" id="id_sucursal" name="id_sucursal">

                    <!-- Datos sucursal -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label for="edit-name" class="form-label fw-semibold">Store</label>
                            <select name="store" id="store_id_edit" class="form-control">
                                <option value=""></option>
                                <?php foreach ($stores as $store) { ?>
                                    <option value="<?= $store['id_store'] ?>"><?= htmlspecialchars($store['store_name']) ?></option>
                                <?php } ?>
                            </select>

                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_code_sucursal" class="form-label fw-semibold">Reference Code</label>
                            <input type="text" class="form-control" id="edit_code_sucursal" name="code_sucursal" placeholder="Código de la sucursal">
                        </div>
                    </div>

                    <!-- CHECKBOXES -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Additional Options</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_items_price" name="items_price" value="1">
                                    <label class="form-check-label" for="edit_items_price">Items price</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_shipping_received" name="shipping_received" value="1">
                                    <label class="form-check-label" for="edit_shipping_received">Shipping received</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_tax" name="tax" value="1">
                                    <label class="form-check-label" for="edit_tax">Tax</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="edit_incentives_offered" name="incentives_offered" value="1">
                                    <label class="form-check-label" for="edit_incentives_offered">Incentives Offered</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <!-- Rangos -->
                    <div id="rangos-container">
                        <!-- Rango 1 -->
                        <div class="row rango-item align-items-end mb-4">
                            <input type="hidden" id="rango_id_1" name="rango_id[]">
                            <div class="col-md-4">
                                <label for="sales_less_than_1" class="form-label fw-semibold">Sales Less Than:</label>
                                <input type="number" id="sales_less_than_1" name="sales_less_than[]" class="form-control" >
                            </div>
                            <div class="col-md-4">
                                <label for="fee_1" class="form-label fw-semibold">Fee:</label>
                                <input type="text" id="fee_1" name="fee[]" class="form-control" >
                            </div>
                            <div class="col-md-4">
                                <label for="fixed_charge_1" class="form-label fw-semibold">Fixed Charge:</label>
                                <input type="text" id="fixed_charge_1" name="fixed_charge[]" class="form-control" >
                            </div>
                        </div>

                        <!-- Rango 2 -->
                        <div class="row rango-item align-items-end mb-4">
                            <input type="hidden" id="rango_id_2" name="rango_id[]">
                            <div class="col-md-4">
                                <label for="sales_less_than_2" class="form-label fw-semibold">Sales Less Than:</label>
                                <input type="number" id="sales_less_than_2" name="sales_less_than[]" class="form-control" >
                            </div>
                            <div class="col-md-4">
                                <label for="fee_2" class="form-label fw-semibold">Fee:</label>
                                <input type="text" id="fee_2" name="fee[]" class="form-control" >
                            </div>
                            <div class="col-md-4">
                                <label for="fixed_charge_2" class="form-label fw-semibold">Fixed Charge:</label>
                                <input type="text" id="fixed_charge_2" name="fixed_charge[]" class="form-control" >
                            </div>
                        </div>

                        <!-- Rango 3 -->
                        <div class="row rango-item align-items-end mb-4">
                            <input type="hidden" id="rango_id_3" name="rango_id[]">
                            <div class="col-md-4">
                                <label for="sales_less_than_3" class="form-label fw-semibold">Sales Less Than:</label>
                                <input type="number" id="sales_less_than_3" name="sales_less_than[]" class="form-control" >
                            </div>
                            <div class="col-md-4">
                                <label for="fee_3" class="form-label fw-semibold">Fee:</label>
                                <input type="text" id="fee_3" name="fee[]" class="form-control" >
                            </div>
                            <div class="col-md-4">
                                <label for="fixed_charge_3" class="form-label fw-semibold">Fixed Charge:</label>
                                <input type="text" id="fixed_charge_3" name="fixed_charge[]" class="form-control" >
                            </div>
                        </div>
                    </div>

                    <!-- Footer botones -->
                    <div class="modal-footer px-0 pt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>



    <!-- Modal Add Sucursal -->
    <div class="modal fade" id="modalAddSucursal" tabindex="-1" aria-labelledby="modalAddSucursalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="addSucursal.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Sucursal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- STORE -->
                        <div class="mb-3">
                            <label for="id_store" class="form-label">Select Store</label>
                            <select name="id_store" class="form-control" id="id_store" required>
                                <?php foreach ($stores as $store) { ?>
                                    <option value="<?= $store['id_store'] ?>"><?= htmlspecialchars($store['store_name']) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- CODE -->
                        <div class="mb-3">
                            <label for="code_sucursal" class="form-label">Sucursal Code</label>
                            <input type="text" class="form-control" id="code_sucursal" name="code_sucursal" required>
                        </div>

                        <!-- CHECKBOXES -->
                        <div class="mb-3">
                            <label class="form-label">Additional Options</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="items_price" name="items_price" value="1">
                                        <label class="form-check-label" for="items_price">Items price</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="shipping_received" name="shipping_received" value="1">
                                        <label class="form-check-label" for="shipping_received">Shipping received</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="tax" name="tax" value="1">
                                        <label class="form-check-label" for="tax">Tax</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="incentives_offered" name="incentives_offered" value="1">
                                        <label class="form-check-label" for="incentives_offered">Incentives Offered</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RANGOS -->
                        <label class="form-label">Fee and Fixed Charge by Sales Range</label>
                        <div class="row g-2 border p-2 mb-2">
                            <div class="col-3"><input type="number" name="rango_1" class="form-control" placeholder="Sales less than" required></div>
                            <div class="col-3"><input type="number" step="0.01" name="comision_1" class="form-control" placeholder="Fee" required></div>
                            <div class="col-3"><input type="number" step="0.01" name="cargo_fijo_1" class="form-control" placeholder="Fixed charge" required></div>
                        </div>
                        <div class="row g-2 border p-2 mb-2">
                            <div class="col-3"><input type="number" name="rango_2" class="form-control" placeholder="Sales less than"></div>
                            <div class="col-3"><input type="number" step="0.01" name="comision_2" class="form-control" placeholder="Fee"></div>
                            <div class="col-3"><input type="number" step="0.01" name="cargo_fijo_2" class="form-control" placeholder="Fixed charge"></div>
                        </div>
                        <div class="row g-2 border p-2 mb-2">
                            <div class="col-3"><input type="number" name="rango_3" class="form-control" placeholder="Sales less than"></div>
                            <div class="col-3"><input type="number" step="0.01" name="comision_3" class="form-control" placeholder="Fee"></div>
                            <div class="col-3"><input type="number" step="0.01" name="cargo_fijo_3" class="form-control" placeholder="Fixed charge"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Sucursal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let modal = document.getElementById("modalEdicionUnico");

            modal.addEventListener("show.bs.modal", function(event) {
                let button = event.relatedTarget;

                // Campos fijos
                document.getElementById("id_sucursal").value = button.getAttribute("data-id_sucursal");
                document.getElementById("edit_code_sucursal").value = button.getAttribute("data-code_sucursal");

                // NUEVO: Seleccionar store en el select según data-id_store
                let id_store = button.getAttribute("data-id_store");
                let selectStore = document.getElementById("store_id_edit");
                if (selectStore && id_store) {
                    selectStore.value = id_store.toString().trim();
                    selectStore.dispatchEvent(new Event('change')); // opcional
                }

                // Obtener los datos actuales de la sucursal (incluyendo checkboxes)
                let id_sucursal = button.getAttribute("data-id_sucursal");

                // Obtener datos de la sucursal para los checkboxes
                fetch("get_sucursal_data.php?id_sucursal=" + id_sucursal)
                    .then(res => res.json())
                    .then(data => {
                        // Actualizar checkboxes
                        document.getElementById("edit_items_price").checked = data.items_price == 1;
                        document.getElementById("edit_shipping_received").checked = data.shipping_received == 1;
                        document.getElementById("edit_tax").checked = data.tax == 1;
                        document.getElementById("edit_incentives_offered").checked = data.incentives_offered == 1;
                    })
                    .catch(err => {
                        console.error("Error loading sucursal data:", err);
                    });

                // Limpiar inputs de rangos
                for (let i = 1; i <= 3; i++) {
                    document.getElementById(`rango_id_${i}`).value = "";
                    document.getElementById(`sales_less_than_${i}`).value = "";
                    document.getElementById(`fee_${i}`).value = "";
                    document.getElementById(`fixed_charge_${i}`).value = "";
                }

                fetch("get_rangos.php?id_sucursal=" + id_sucursal)
                    .then(res => res.json())
                    .then(rangos => {
                        console.log("rangos JSON:", JSON.stringify(rangos, null, 2));

                        rangos.forEach((rango, index) => {
                            if (index < 3) { // para no sobrepasar los inputs
                                document.getElementById(`rango_id_${index + 1}`).value = rango.id;
                                document.getElementById(`sales_less_than_${index + 1}`).value = rango.sales_less_than;
                                document.getElementById(`fee_${index + 1}`).value = rango.comision;
                                document.getElementById(`fixed_charge_${index + 1}`).value = rango.cargo_fijo;
                            }
                        });
                    })
                    .catch(() => {
                        console.error("Error cargando rangos");
                    });
            });
        });
    </script>
</body>

</html>