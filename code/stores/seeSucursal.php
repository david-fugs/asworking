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
        <h1 class="page-title text-center"><i class="fa-solid fa-store"></i> SUCURSALES</h1>

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

        // Inicializa la consulta base
        $queryBase = "SELECT store.*, sucursal.* 
        FROM store 
        JOIN sucursal ON store.id_store = sucursal.id_store
        WHERE 1=1";

        // Filtro por nombre de tienda (store_name)
        if (!empty($_GET['store_name'])) {
            $nombre_store = ($_GET['store_name']);
            $queryBase .= " AND store.store_name LIKE '%$nombre_store%'";
        }

        // Filtro por código de sucursal (code_sucursal en tabla sucursal)
        if (!empty($_GET['code_sucursal'])) {
            $code_sucursal = $mysqli->real_escape_string($_GET['code_sucursal']);
            $queryBase .= " AND sucursal.code_sucursal LIKE '%$code_sucursal%'";
        }

        // Ordenar por nombre de tienda
        $queryBase .= " ORDER BY store.store_name ASC";

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
            echo '<th>FEE</th>';
            echo '<th>ACTIONS</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            $i = 1;
            while ($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                echo '<td>' . $i . '</td>';
                echo '<td>' . htmlspecialchars($row['store_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['code_sucursal']) . '</td>';
                echo '<td>' . htmlspecialchars($row['comision_sucursal']) . '</td>';
                echo '<td class="action-buttons">';
                echo '<button type="button" class="btn-action btn-edit" 
                        data-bs-toggle="modal" data-bs-target="#modalEdicion"
                        data-id="' . $row['id_store'] . '"
                        data-id_sucursal="' . $row['id_sucursal'] . '"
                        data-name="' . htmlspecialchars($row['store_name']) . '"
                        data-code_sucursal="' . htmlspecialchars($row['code_sucursal']) . '"
                        data-comision_sucursal="' . htmlspecialchars($row['comision_sucursal']) . '">
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
    <div class="modal fade" id="modalEdicion" tabindex="-1" aria-labelledby="modalEdicionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Sucursal Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="editSucursal.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="id_sucursal" name="id_sucursal">

                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit-name" name="name" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="edit_code_sucursal" class="form-label">Code Sucursal</label>
                            <input type="text" class="form-control" id="edit_code_sucursal" name="code_sucursal">
                        </div>

                        <div class="mb-3">
                            <label for="edit_comision_sucursal" class="form-label">Fee</label>
                            <input type="text" class="form-control" id="edit_comision_sucursal" name="comision_sucursal">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Add Sucursal -->
    <div class="modal fade" id="modalAddSucursal" tabindex="-1" aria-labelledby="modalAddSucursalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="addSucursal.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Sucursal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="id_store" class="form-label">Select Store</label>
                            <select name="id_store" class="form-control" id="id_store" required>
                                <?php foreach ($stores as $store) { ?>
                                    <option value="<?= $store['id_store'] ?>"><?= htmlspecialchars($store['store_name']) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="code_sucursal" class="form-label">Sucursal Code</label>
                            <input type="text" class="form-control" id="code_sucursal" name="code_sucursal" required>
                        </div>
                        <div class="mb-3">
                            <label for="comision_sucursal" class="form-label">Fee</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="comision_sucursal" name="comision_sucursal">
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
            let modalEdicion = document.getElementById("modalEdicion");

            modalEdicion.addEventListener("show.bs.modal", function(event) {
                let button = event.relatedTarget;
                document.getElementById("id_sucursal").value = button.getAttribute("data-id_sucursal");
                document.getElementById("edit-name").value = button.getAttribute("data-name");
                document.getElementById("edit_code_sucursal").value = button.getAttribute("data-code_sucursal");
                document.getElementById("edit_comision_sucursal").value = button.getAttribute("data-comision_sucursal");
            });
        });
    </script>
</body>

</html>