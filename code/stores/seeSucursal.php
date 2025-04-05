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
    global $mysqli; // Asegurar acceso a la conexión global

    $query = "DELETE FROM sucursal WHERE id_sucursal  = ?";
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
function getStatus($estado)
{
    if ($estado == 1) {
        return "<span class='badge bg-success'>ACTIVO</span>";
    } else {
        return "<span class='badge bg-danger'>INACTIVO</span>";
    }
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
    <title>ASWWORKING | SOFT</title>
    <script src="js/64d58efce2.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Orbitron" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../items/css/styles.css">
    <link rel="stylesheet" type="text/css" href="../items/css/estilos2024.css">
    <link href="../../../fontawesome/css/all.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <style>
        th {
            font-size: 15px;
        }

        td {
            font-size: 15px;
        }

        .responsive {
            max-width: 100%;
            height: auto;
        }

        .selector-for-some-widget {
            box-sizing: content-box;
        }

        .pending {
            background-color: orange;
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .ok {
            background-color: lightblue;
            color: black;
            font-weight: bold;
            text-align: center;
        }

        .disabled-link {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"></script>
    <center style="margin-top: 20px;">
        <img src='../../img/logo.png' width="300" height="212" class="responsive">
    </center>
    <h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em; font-size: 40px; text-align: center;"><b><i class="fa-solid fa-file-signature"></i> SUCURSAL</b></h1>
    <div class="flex">
        <div class="box">
            <form action="showitems.php" method="get" class="form">
                <input name="store_name" type="text" placeholder="Store Name " value="<?= htmlspecialchars($store_name) ?>">
                <input name="code_sucursal" type="text" placeholder="code" value="<?= htmlspecialchars($code_sucursal) ?>">
                <input name="ref" type="text" placeholder="Reference" value="<?= htmlspecialchars($reference) ?>">
                <input value="Search" type="submit">
            </form>
        </div>
    </div>
    <br /><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="back" /></a><br>
    <?php
    date_default_timezone_set("America/Bogota");
    include("../../conexion.php");
    require_once("../../zebra.php");

    // Inicializa la consulta base
    $queryBase = "SELECT store.*, sucursal.* 
    FROM store 
    JOIN sucursal ON store.id_store = sucursal.id_store
    WHERE 1=1";

    // Agrega filtros si existen
    if (!empty($_GET['nombre_store'])) {
        $nombre_store = $mysqli->real_escape_string($_GET['nombre_store']);
        $queryBase .= " AND store.nombre_store LIKE '%$nombre_store%'";
    }
    if (!empty($_GET['direccion'])) {
        $direccion = $mysqli->real_escape_string($_GET['direccion']);
        $queryBase .= " AND store.direccion LIKE '%$direccion%'";
    }

    // Ordenar por ejemplo por nombre de sucursal
    $queryBase .= " ORDER BY store.store_name ASC";
    $countQuery = "SELECT COUNT(DISTINCT store.id_store) as total 
               FROM store 
               JOIN sucursal ON store.id_store = sucursal.id_store
               WHERE 1=1";

    if (!empty($_GET['nombre_store'])) {
        $countQuery .= " AND store.store_name LIKE '%$nombre_store%'";
    }

    if (!empty($_GET['sucursal'])) {
        $countQuery .= " AND store.store_name LIKE '%$sucursal%'";
    }
    $countResult = $mysqli->query($countQuery);

    if (!$countResult) die("Error en conteo: " . $mysqli->error);
    $countRow = $countResult->fetch_assoc();
    $num_registros = $countRow['total'];

    $resul_x_pagina = 50;
    // Si no hay registros, evita errores en la paginación
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

        echo "<section class='content'>
    <div class='card-body'>
        <div class='table-responsive'>
            <table class='table table-striped table-hover' style='width:1300px;'>
                <thead class='table-dark'>
                    <tr>
                        <th>No.</th>
                        <th>STORE NAME</th>
                        <th>REFERENCE CODE</th>
                        <th>FEE</th>
                        <th>EDIT</th>
                        <th>DELETE</th>
                    </tr>
                </thead>
                <tbody>";

        $i = 1;
        while ($row = mysqli_fetch_array($result)) {
            print_r($row);
            echo '<tr>
            <td data-label="NO.">' . $i . '</td>
            <td data-label="name">' . $row['store_name'] . '</td>
            <td data-label="code">' . $row['code_sucursal'] . '</td>
            <td data-label="comision">' . $row['comision_sucursal'] . '</td>
            <td data-label="Editar">
                <button type="button" class="btn-edit" 
                    data-bs-toggle="modal" data-bs-target="#modalEdicion"
                    data-id="' . $row['id_store'] . '"
                    data-id_sucursal="' . $row['id_sucursal'] . '"
                    data-name="' . $row['store_name'] . '"
                    data-code_sucursal="' . $row['code_sucursal'] . '"
                    data-comision_sucursal="' . $row['comision_sucursal'] . '"
                    style="background-color:transparent; border:none;">
                    <img src="../../img/editar.png" width="28" height="28">
                </button>     
            </td>
            <td data-label="Eliminar">
                <a href="?delete=' . $row['id_sucursal'] . '" onclick="return confirm(\'¿Are you sure to Delete this item?\');">
                    <i class="fa-sharp-duotone fa-solid fa-trash" style="color:red; height:20px;"></i>
                </a>
            </td>   
        </tr>';
            $i++;
        }

        echo '</tbody></table></div>';

        // Mostrar paginación
        $paginacion->render();
        echo '</section>';
    } else {
        echo "<p>No se encontraron resultados.</p>";
    }

    ?>


    <!-- Modal Edicion -->
    <div class="modal fade" id="modalEdicion" tabindex="-1" aria-labelledby="modalEdicionLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- Modal más ancho -->
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEdicionLabel">Edit </h1>
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

    <center>
        <br /><a href="../access.php"><img src='../../img/atras.png' width="72" height="72" title="Regresar" /></a>
    </center>

    <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
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

            // Enviar datos al servidor con AJAX al hacer clic en "Guardar cambios"
            document.getElementById("guardarCambios").addEventListener("click", function() {
                let formData = new FormData(document.getElementById("formEditar"));

                fetch("editItems.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Registro actualizado correctamente");
                            window.location.reload(); // Recargar la página para ver los cambios
                        } else {
                            alert("Error al actualizar");
                        }
                    })
                    .catch(error => console.error("Error:", error));
            });
        });
    </script>
</body>

</html>