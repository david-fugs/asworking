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

    if ($stmt->execute()) {
        echo "<script>alert('Item deleted correctly');
        window.location = 'showitems.php';</script>";
    } else {
        echo "<script>alert('Error deleting the item');
        window.location = 'showitems.php';</script>";
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
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
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
        color: #5d337a; /* Morado oscuro */
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
                <div class="col-md-4">
                    <input type="text" name="upc_item" class="form-control" placeholder="UPC" value="<?= htmlspecialchars($upc_item) ?>">
                </div>
                <div class="col-md-4">
                    <input type="text" name="item" class="form-control" placeholder="Item" value="<?= htmlspecialchars($item) ?>">
                </div>
                <div class="col-md-3">
                    <input type="text" name="ref" class="form-control" placeholder="Reference" value="<?= htmlspecialchars($reference) ?>">
                </div>
                <div class="col-md-1">
                    <input type="submit" value="Search" class="btn btn-primary w-100">
                </div>
            </form>
        </div>

        <?php 
        date_default_timezone_set("America/Bogota");
        require_once("../../zebra.php");

        // Inicializa la consulta base
        $queryBase = "SELECT items.*, inventory.quantity_inventory 
        FROM items 
        LEFT JOIN inventory ON items.upc_item = inventory.upc_inventory 
        WHERE 1=1";

        // Agrega filtros si existen
        if (!empty($_GET['upc_item'])) {
            $upc_item = $mysqli->real_escape_string($_GET['upc_item']);
            $queryBase .= " AND upc_item LIKE '%$upc_item%'";
        }
        if (!empty($_GET['item'])) {
            $item = $mysqli->real_escape_string($_GET['item']);
            $queryBase .= " AND item_item LIKE '%$item%'";
        }
        if (!empty($_GET['ref'])) {
            $reference = $mysqli->real_escape_string($_GET['ref']);
            $queryBase .= " AND ref_item = '$reference'";
        }

        // Ordenar por cantidad en inventory
        $queryBase .= " ORDER BY inventory.quantity_inventory DESC";

        // Consulta para conteo (sin duplicados)
        $countQuery = "SELECT COUNT(DISTINCT items.id_item) as total 
                       FROM items 
                       LEFT JOIN inventory ON items.upc_item = inventory.upc_inventory 
                       WHERE 1=1";

        // Aplicar mismos filtros a countQuery
        if (!empty($_GET['upc_item'])) {
            $countQuery .= " AND items.upc_item LIKE '%$upc_item%'";
        }
        if (!empty($_GET['item'])) {
            $countQuery .= " AND items.item_item LIKE '%$item%'";
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
        ?>
        
        <div class="table-container">
            <div class="">
                <table class="">
                <thead style="background-color: #632b8b; color: white;">
                       
                        <tr>
                            <th>No.</th>
                            <th>UPC</th>
                            <th>SKU</th>
                            <th>DATE</th>
                            <th>BRAND</th>
                            <th>ITEM</th>
                            <th>REF</th>
                            <th>COLOR</th>
                            <th>SIZE</th>
                            <th>CATEGORY</th>
                            <th>COST</th>
                            <th>WEIGHT</th>
                            <th>STOCK</th>
                            <th>BATCH</th>
                            <th>EDIT</th>
                            <th>DELETE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        while ($row = mysqli_fetch_array($result)) {
                            echo '<tr>
                                <td>' . $i . '</td>
                                <td>' . $row['upc_item'] . '</td>
                                <td>' . $row['sku_item'] . '</td>
                                <td>' . $row['date_item'] . '</td>
                                <td style="text-transform:uppercase;">' . $row['brand_item'] . '</td>
                                <td>' . $row['item_item'] . '</td>
                                <td>' . $row['ref_item'] . '</td>
                                <td>' . $row['color_item'] . '</td>
                                <td>' . $row['size_item'] . '</td>
                                <td>' . $row['category_item'] . '</td>
                                <td>' . $row['cost_item'] . '</td>
                                <td>' . $row['weight_item'] . '</td>
                                <td>' . $row['quantity_inventory'] . '</td>
                                <td>' . $row['inventory_item'] . '</td>
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
                                        data-batch="' . $row['inventory_item'] . '">
                                        <i class="fas fa-edit"></i>
                                    </button>     
                                </td>
                                <td ">
                                    <a href="?delete=' . $row['id_item'] . '" onclick="return confirm(\'¿Are you sure to Delete this item?\');" class="btn-action btn-delete">
                                        <i class="fas fa-trash-alt"></i>
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
            <a href="../access.php" class="back-btn">
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
                        </div>

                        <!-- Fecha -->
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="edit-date" name="date">
                            <label for="edit-date">Date</label>
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
            });
        });
    </script>
</body>
</html>