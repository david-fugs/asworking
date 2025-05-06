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
    global $mysqli;
    $query = "DELETE FROM store WHERE id_store = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $id_store);

    if ($stmt->execute()) {
        echo "<script>alert('Sucursal deleted correctly');
        window.location = 'seeSucursal.php';</script>";
    } else {
        echo "<script>alert('Error deleting the sucursal');
        window.location = 'seeSucursal.php';</script>";
    }
    $stmt->close();
}

// Obtener los filtros desde el formulario
$store_name = isset($_GET['store_name']) ? trim($_GET['store_name']) : '';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ASWWORKING | STORES</title>
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
            position: relative;
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
        .btn-add-store {
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add-store:hover {
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
            <form action="seeStore.php" method="get" class="row g-3">
                <div class="col-md-10">
                    <input type="text" name="store_name" class="form-control" placeholder="Store Name" value="<?= htmlspecialchars($store_name) ?>">
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
                <button type="button" class="btn-add-store" data-bs-toggle="modal" data-bs-target="#modalAddStore">
                    <i class="fas fa-plus"></i> Add Store
                </button>
            </div>
        </div>


        <?php
        date_default_timezone_set("America/Bogota");
        require_once("../../zebra.php");

        // Inicializa la consulta base
        $queryBase = "SELECT * FROM store WHERE 1=1";

        // Filtro por nombre de tienda
        if (!empty($_GET['store_name'])) {
            $nombre_store = ($_GET['store_name']);
            $queryBase .= " AND store_name LIKE '%$nombre_store%'";
        }

        // Ordenar por nombre de tienda
        $queryBase .= " ORDER BY store_name ASC";

        // Conteo de registros
        $countQuery = "SELECT COUNT(DISTINCT id_store) as total FROM store WHERE 1=1";
        if (!empty($_GET['store_name'])) {
            $countQuery .= " AND store_name LIKE '%$nombre_store%'";
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
            echo '<th>EDIT</th>';
            echo '<th>DELETE</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            $i = 1;
            while ($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                echo '<td>' . $i . '</td>';
                echo '<td>' . $row['store_name'] . '</td>';
                echo '<td>';
                echo '<button type="button" class="btn-action btn-edit" 
                        data-bs-toggle="modal" data-bs-target="#modalEdicion"
                        data-id_store="' . $row['id_store'] . '"
                        data-name="' . $row['store_name'] . '">
                        <i class="fas fa-edit"></i>
                    </button>';
                echo '</td>';
                echo '<td>';
                echo '<a href="?delete=' . $row['id_store'] . '" onclick="return confirm(\'¿Are you sure to delete this store?\');" class="btn-action btn-delete">
                        <i class="fas fa-trash-alt"></i>
                    </a>';
                echo '</td>';
                echo '</tr>';
                $i++;
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';

            // Mostrar paginación
            $paginacion->render();
        } else {
            echo '<div class="alert alert-info">No stores found.</div>';
        }
        ?>
    </div>

    <!-- Modal Edicion -->
    <div class="modal fade" id="modalEdicion" tabindex="-1" aria-labelledby="modalEdicionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Store Info</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="editStore.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="id_store" name="id_store">

                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit-name" name="name" required>
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

    <!-- Modal Add Store -->
    <div class="modal fade" id="modalAddStore" tabindex="-1" aria-labelledby="modalAddStoreLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="addStore.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Store</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="store_name" class="form-label">Store Name</label>
                            <input type="text" class="form-control" id="store_name" name="store_name" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Store</button>
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
                document.getElementById("id_store").value = button.getAttribute("data-id_store");
                document.getElementById("edit-name").value = button.getAttribute("data-name");
            });
        });
    </script>
</body>

</html>