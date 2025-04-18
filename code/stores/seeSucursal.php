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
    <h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em; font-size: 40px; text-align: center;"><b><i class="fa-solid fa-file-signature"></i> SUCURSAL</b></h1>
    <div class="flex">
        <div class="box">
            <form action="seeSucursal.php" method="get" class="form">
                <input name="store_name" type="text" placeholder="Store Name " value="<?= htmlspecialchars($store_name) ?>">
                <input name="code_sucursal" type="text" placeholder="code" value="<?= htmlspecialchars($code_sucursal) ?>">
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
            <button type="button" class="btn-add-store" data-bs-toggle="modal" data-bs-target="#modalAddSucursal">
                Add Sucursal
            </button>
        </div>
    </div>
    <?php
    date_default_timezone_set("America/Bogota");
    include("../../conexion.php");
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

    // --------- Conteo ----------
    $countQuery = "SELECT COUNT(DISTINCT store.id_store) as total 
    FROM store 
    JOIN sucursal ON store.id_store = sucursal.id_store
    WHERE 1=1";

    // Aplica los mismos filtros al conteo
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
                        <th>REFERENCE CODE</th>
                        <th>FEE</th>
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
                <a href="?delete=' . $row['id_sucursal'] . '" onclick="return confirm(\'¿Are you sure to Delete this sucursal?\');">
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

                <form action="editSucursal.php" method="POST">
                    <div class="modal-body px-4 py-3">
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

                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="guardarCambios">Save</button>
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
                        <h5 class="modal-title" id="modalAddSucursalLabel">Add Store</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Aquí tus campos del formulario -->
                        <div class="mb-3">
                            <label for="store_name" class="form-label">Select Store</label>
                            <select name="id_store" class="form-control" id="id_store" required>
                                <?php foreach ($stores as $store) { ?>
                                    <option value="<?= $store['id_store'] ?>"><?= $store['store_name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="code_sucursal" class="form-label">Sucursal Code</label>
                            <input type="text" class="form-control" id="code_sucursal" name="code_sucursal" required>
                        </div>
                        <div class="mb-3">
                            <label for="comision_sucursal" class="form-label">Fee</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="comision_sucursal" name="comision_sucursal" >
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
                document.getElementById("id_sucursal").value = button.getAttribute("data-id_sucursal");

                document.getElementById("edit-name").value = button.getAttribute("data-name");
                document.getElementById("edit_code_sucursal").value = button.getAttribute("data-code_sucursal");
                document.getElementById("edit_comision_sucursal").value = button.getAttribute("data-comision_sucursal");


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