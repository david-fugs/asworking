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
    <h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em; font-size: 40px; text-align: center;"><b><i class="fa-solid fa-file-signature"></i> REPORT </b></h1>

    <div class="top-bar">
        <div></div>
        <div class="center">
            <a href="../../access.php">
                <img src='../../img/atras.png' width="72" height="72" title="Back">
            </a>
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

    ?>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <form action="processReport.php" method="POST" class="bg-white p-4 rounded shadow-sm border">
                    <h5 class="mb-4 text-center">New Report</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="upc_asignado_report" class="form-label"> Assigned UPC</label>
                            <input type="text" class="form-control form-control-sm" id="upc_asignado_report" onblur="buscarUPC() " name="upc_asignado_report">
                        </div>

                        <div class="col-md-6">
                            <label for="upc_final_report" class="form-label"> Final UPC</label>
                            <input type="text" class="form-control form-control-sm" id="upc_final_report" name="upc_final_report">
                        </div>

                        <div class="col-md-6">
                            <label for="cons_report" class="form-label">Cons</label>
                            <input type="text" class="form-control form-control-sm" id="cons_report" name="cons_report" required>
                        </div>

                        <div class="col-md-6">
                            <label for="folder_report" class="form-label">Folder</label>
                            <input type="text" class="form-control form-control-sm" id="folder_report" name="folder_report" required>
                        </div>

                        <div class="col-md-6">
                            <label for="loc_report" class="form-label">Location</label>
                            <input type="text" class="form-control form-control-sm" id="loc_report" name="loc_report">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="quantity_report" class="form-label">Quantity</label>
                            <input type="number" class="form-control form-control-sm" id="quantity_report" name="quantity_report" required>
                        </div>

                        <div class="col-md-6">
                            <label for="sku_report" class="form-label">SKU</label>
                            <input type="text" class="form-control form-control-sm" id="sku_report" name="sku_report">
                        </div>

                        <div class="col-md-6">
                            <label for="brand_report" class="form-label">Brand</label>
                            <input type="text" class="form-control form-control-sm" id="brand_report" name="brand_report">
                        </div>

                        <div class="col-md-6">
                            <label for="item_report" class="form-label">Item</label>
                            <input type="text" class="form-control form-control-sm" id="item_report" name="item_report">
                        </div>

                        <div class="col-md-6">
                            <label for="vendor_report" class="form-label">Vendor </label>
                            <input type="text" class="form-control form-control-sm" id="vendor_report" name="vendor_report">
                        </div>

                        <div class="col-md-6">
                            <label for="color_report" class="form-label">Color</label>
                            <input type="text" class="form-control form-control-sm" id="color_report" name="color_report">
                        </div>

                        <div class="col-md-6">
                            <label for="size_report" class="form-label">Size</label>
                            <input type="text" class="form-control form-control-sm" id="size_report" name="size_report">
                        </div>

                        <div class="col-md-6">
                            <label for="category_report" class="form-label">Category</label>
                            <input type="text" class="form-control form-control-sm" id="category_report" name="category_report">
                        </div>

                        <div class="col-md-6">
                            <label for="weight_report" class="form-label">Weight</label>
                            <input type="text" step="0.01" class="form-control form-control-sm" id="weight_report" name="weight_report">
                        </div>

                        <div class="col-md-6">
                            <label for="inventory_report" class="form-label">Inventory</label>
                            <input type="text" class="form-control form-control-sm" id="inventory_report" name="inventory_report">
                        </div>

                        <div class="col-md-6 mt-4">
                            <label for="sucursal_report" class="form-label">Sucursal</label>
                            <select class="form-select form-select-sm" id="sucursal_report" name="sucursal_report" required>
                                <option value="">Select Sucursal</option>
                                <?php foreach ($stores as $store) : ?>
                                    <option value="<?= $store['id_store'] ?>"><?= $store['store_name'] ?></option>
                                <?php endforeach; ?>
                                </select>
                        </div>

                        <div class="col-12">
                            <label for="observacion_report" class="form-label">Observation</label>
                            <textarea class="form-control form-control-sm" id="observacion_report" name="observacion_report" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-sm px-4">Send</button>
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
        function buscarUPC() {
            let upc = $('#upc_asignado_report').val();

            if (upc.trim() === "") return;

            $.ajax({
                url: 'buscar_upc_detalle.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    upc: upc
                },
                success: function(data) {
                    if (data.success) {
                        $('#quantity_report').val(data.quantity_inventory);
                        $('#brand_report').val(data.brand_item);
                        $('#item_report').val(data.item);
                        $('#color_report').val(data.color_item);
                        $('#size_report').val(data.size_item);
                        $('#category_report').val(data.category_item);
                        $('#weight_report').val(data.weight_item);
                        $('#inventory_report').val(data.inventory_item);
                        $('#sku_report').val(data.sku);
                        $('#vendor_report').val(data.ref_item);

                        alert("✅ UPC Found.");
                    } else {
                        limpiarCampos();
                        alert("❌ UPC not found.");
                    }
                },
                error: function() {
                    alert("⚠️ Error .");
                }
            });
        }

        function limpiarCampos() {
            $('#quantity_report').val('');
            $('#brand_report').val('');
            $('#item_report').val('');
            $('#color_report').val('');
            $('#size_report').val('');
            $('#category_report').val('');
            $('#weight_report').val('');
            $('#inventory_report').val('');
            $('#sku_report').val('');
            $('#vendor_report').val('');
        }
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