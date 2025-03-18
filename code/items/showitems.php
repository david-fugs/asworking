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
function deleteMember($upsi_item)
{
    global $mysqli; // Asegurar acceso a la conexión global

    $query = "DELETE FROM items WHERE upsi_item  = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $upsi_item);

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
$upc_sku = isset($_GET['upc_sku']) ? trim($_GET['upc_sku']) : '';
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
    <title>ASWWORKING | SOFT</title>
    <script src="js/64d58efce2.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Orbitron" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <link rel="stylesheet" type="text/css" href="css/estilos2024.css">
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
    <h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em; font-size: 40px; text-align: center;"><b><i class="fa-solid fa-file-signature"></i> ITEMS</b></h1>
    <div class="flex">
        <div class="box">
            <form action="showitems.php" method="get" class="form">
                <input name="upc_sku" type="text" placeholder="Upc sku" value="<?= htmlspecialchars($upc_sku) ?>">
                <input name="item" type="text" placeholder="Item" value="<?= htmlspecialchars($item) ?>">
                <input name="ref" type="text" placeholder="Reference" value="<?= htmlspecialchars($reference) ?>">
                <input name="plan_cta" type="text" placeholder="Plan Cliente" value="<?= htmlspecialchars($plan) ?>">
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
    $queryBase = "SELECT * FROM items WHERE 1=1";
    // Agrega filtros si existen
    if (!empty($_GET['upc_sku'])) {
        $upc_sku = $mysqli->real_escape_string($_GET['upc_sku']);
        $queryBase .= " AND upc_sku_item LIKE '%$upc_sku%'";
    }
    if (!empty($_GET['item'])) {
        $item = $mysqli->real_escape_string($_GET['item']);
        $queryBase .= " AND item_item  LIKE '%$item%'";
    }
    if (!empty($_GET['ref'])) {
        $reference = $mysqli->real_escape_string($_GET['ref']);
        $queryBase .= " AND ref_item = '$reference'";
    }

    if (!empty($_GET['nom_cta'])) {
        $nom_cta = $mysqli->real_escape_string($_GET['nom_cta']);
        $queryBase .= " AND nom_cta LIKE '%$nom_cta%'";
    }

    // Contar total de registros antes de aplicar el LIMIT
    $res = $mysqli->query($queryBase);
    if (!$res) {
        die("Error en la consulta: " . $mysqli->error);
    }
    $num_registros = mysqli_num_rows($res);
    $resul_x_pagina = 100;
    // Configuración de Zebra_Pagination
    $paginacion = new Zebra_Pagination();
    $paginacion->records($num_registros);
    $paginacion->records_per_page($resul_x_pagina);


    $page = $paginacion->get_page(); // Obtiene la página actual
    $offset = ($page - 1) * $resul_x_pagina; // Calcula el desplazamiento
    $queryFinal = $queryBase . "  LIMIT $offset, $resul_x_pagina";
    // Ejecutar consulta con paginación
    $result = $mysqli->query($queryFinal);
    if (!$result) {
        die("Error en la consulta: " . $mysqli->error);
    }
    // Inicia la tabla
    echo "<section class='content'>
    <div class='card-body'>
        <div class='table-responsive'>
            <table style='width:1300px;'>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>UPC </th>
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
                <tbody>";
    $i = 1;
    while ($row = mysqli_fetch_array($result)) {
        echo '<tr>
			<td data-label="NO.">' . $i . '</td>
            <td data-label="upsi">' . $row['upsi_item'] . '</td>
            <td data-label="sku">' . $row['sku_item'] . '</td>
            <td data-label="date">' . $row['date_item'] . '</td>
            <td style="text-transform:uppercase;" data-label="brand">' . $row['brand_item'] . '</td>
            <td data-label="item">' . $row['item_item'] . '</td>
            <td data-label="ref">' . $row['ref_item'] . '</td>
            <td data-label="color">' . $row['color_item'] . '</td>
            <td data-label="size">' . $row['size_item'] . '</td>
            <td data-label="category">' . $row['category_item'] . '</td>
            <td data-label="cost">' . $row['cost_item'] . '</td>
			<td data-label="weight">' . $row['weight_item'] . '</td>
			<td data-label="ESTADO">'  . '</td>
            <td data-label="ESTADO">'  . '</td>
            <td data-label="EDITAR">
                <button type="button" class="btn-edit" 
                    data-bs-toggle="modal" data-bs-target="#modalEdicion"
                    data-upsi="' . $row['upsi_item'] . '"
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
                    style="background-color:transparent; border:none;">
                    <img src="../../img/editar.png" width="28" height="28">
                </button>     
            </td>
            <td data-label="ELIMINAR">
                <a href="?delete=' . $row['upsi_item'] . '" onclick="return confirm(\'¿Are you sure to Delete this item?\');">
                    <i class="fa-sharp-duotone fa-solid fa-trash" style="color:red; height:20px;"></i>
                </a>
            </td>   
        </tr>';
        $i++;
    }

    // Cierra la tabla y muestra la paginación
    echo '</tbody></table></div>';

    // Mostrar la paginación después de la tabla
    $paginacion->render();
    echo '</section>';

    ?>
    <!-- Modal Edicion -->
    <div class="modal fade" id="modalEdicion" tabindex="-1" aria-labelledby="modalEdicionLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="modalEdicionLabel">Editar Registro</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditar">
                        <input type="hidden" id="edit-upsi" name="upsi">
                        <div class="mb-3">
                            <label for="edit-sku" class="form-label">SKU</label>
                            <input type="text" class="form-control" id="edit-sku" name="sku">
                        </div>
                        <div class="mb-3">
                            <label for="edit-date" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="edit-date" name="date">
                        </div>
                        <div class="mb-3">
                            <label for="edit-brand" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="edit-brand" name="brand">
                        </div>
                        <div class="mb-3">
                            <label for="edit-item" class="form-label">Item</label>
                            <input type="text" class="form-control" id="edit-item" name="item">
                        </div>
                        <div class="mb-3">
                            <label for="edit-ref" class="form-label">Referencia</label>
                            <input type="text" class="form-control" id="edit-ref" name="ref">
                        </div>
                        <div class="mb-3">
                            <label for="edit-color" class="form-label">Color</label>
                            <input type="text" class="form-control" id="edit-color" name="color">
                        </div>
                        <div class="mb-3">
                            <label for="edit-size" class="form-label">Tamaño</label>
                            <input type="text" class="form-control" id="edit-size" name="size">
                        </div>
                        <div class="mb-3">
                            <label for="edit-category" class="form-label">Categoría</label>
                            <input type="text" class="form-control" id="edit-category" name="category">
                        </div>
                        <div class="mb-3">
                            <label for="edit-cost" class="form-label">Costo</label>
                            <input type="text" class="form-control" id="edit-cost" name="cost">
                        </div>
                        <div class="mb-3">
                            <label for="edit-weight" class="form-label">Peso</label>
                            <input type="text" class="form-control" id="edit-weight" name="weight">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardarCambios">Guardar cambios</button>
                </div>
            </div>
        </div>
    </div>
    <center>
        <br /><a href="../access.php"><img src='img/atras.png' width="72" height="72" title="Regresar" /></a>
    </center>

    <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let modalEdicion = document.getElementById("modalEdicion");

            modalEdicion.addEventListener("show.bs.modal", function(event) {
                let button = event.relatedTarget; // Botón que abrió el modal

                document.getElementById("edit-upsi").value = button.getAttribute("data-upsi");
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
                            location.reload();
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