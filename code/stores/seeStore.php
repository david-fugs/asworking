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
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .top-bar .center {
            flex-grow: 1;
            text-align: center;
            margin-left: 200px;
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
    <h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em; font-size: 40px; text-align: center;"><b><i class="fa-solid fa-file-signature"></i> STORE</b></h1>
    <div class="flex">
        <div class="box">
            <form action="seeStore.php" method="get" class="form">
                <input name="store_name" type="text" placeholder="Store Name " value="<?= htmlspecialchars($store_name) ?>">
                <input value="Search" type="submit">
            </form>
        </div>
    </div>

    <div class="top-bar">
        <div></div>
        <div class="center">
            <a href="../../access.php">
                <img src='../../img/atras.png' width="72" height="72" title="Back">
            </a>
        </div>
        <div style="display: flex; justify-content: flex-end; margin: 20px 0;">
            <button type="button" class="btn-add-store" data-bs-toggle="modal" data-bs-target="#modalAddStore">
                Add Store
            </button>
        </div>
    </div>
    <?php
    date_default_timezone_set("America/Bogota");
    include("../../conexion.php");
    require_once("../../zebra.php");

    // Inicializa la consulta base
    $queryBase = "SELECT *
    FROM store 
    WHERE 1=1";

    // Filtro por nombre de tienda (store_name)
    if (!empty($_GET['store_name'])) {
        $nombre_store = ($_GET['store_name']);
        $queryBase .= " AND store_name LIKE '%$nombre_store%'";
    }

    // Ordenar por nombre de tienda
    $queryBase .= " ORDER BY store_name ASC";

    // --------- Conteo ----------
    $countQuery = "SELECT COUNT(DISTINCT id_store) as total 
    FROM store 
    WHERE 1=1";

    // Aplica los mismos filtros al conteo
    if (!empty($_GET['store_name'])) {
        $countQuery .= " AND store_name LIKE '%$nombre_store%'";
    }

    // Ejecutar conteo
    $countResult = $mysqli->query($countQuery);
    if (!$countResult) die("Error en conteo: " . $mysqli->error);

    $countRow = $countResult->fetch_assoc();
    $num_registros = $countRow['total'];

    // ------------ Paginación y ejecución final -------------
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


        echo "<section class='content'>
    <div class='card-body'>
        <div class='table-responsive'>
            <table class='table table-striped table-hover' style='width:1300px;'>
                <thead class='table-dark'>
                    <tr>
                        <th>No.</th>
                        <th>STORE NAME</th>
                        <th>EDIT</th>
                        <th>DELETE</th>
                    </tr>
                </thead>
                <tbody>";

        $i = 1;
        while ($row = mysqli_fetch_array($result)) {
            echo '<tr>
            <td data-label="NO.">' . $i . '</td>
            <td data-label="name">' . $row['store_name'] . '</td>
            <td data-label="Editar">
                <button type="button" class="btn-edit" 
                    data-bs-toggle="modal" data-bs-target="#modalEdicion"
                    data-id_store="' . $row['id_store'] . '"
                    data-name="' . $row['store_name'] . '"
                    style="background-color:transparent; border:none;">
                    <img src="../../img/editar.png" width="28" height="28">
                </button>     
            </td>
            <td data-label="Eliminar">
                <a href="?delete=' . $row['id_store'] . '" onclick="return confirm(\'¿Are you sure to Delete this sucursal?\');">
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
        <div class="modal-dialog">
            <div class="modal-content rounded-4 shadow-sm">
                <div class="modal-header bg-dark text-white"> <!-- Negro con texto blanco -->
                    <h5 class="modal-title" id="modalEdicionLabel">Edit Store Info</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="editStore.php" method="POST">
                    <div class="modal-body px-4 py-3">
                        <input type="hidden" id="id_store" name="id_store">

                        <div class="mb-3">
                            <label for="edit-name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit-name" name="name">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="guardarCambios">Save</button>
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
                        <h5 class="modal-title" id="modalAddStoreLabel">Add Store</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Aquí tus campos del formulario -->
                        <div class="mb-3">
                            <label for="store_name" class="form-label">Store Name</label>
                            <input type="text" class="form-control" id="store_name" name="store_name" required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Save Store</button>
                    </div>
                </form>
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
                document.getElementById("id_store").value = button.getAttribute("data-id_store");
                document.getElementById("edit-name").value = button.getAttribute("data-name");

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