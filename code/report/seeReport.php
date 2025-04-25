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
function getSucursal($id_sucursal)
{
    global $mysqli; // Asegurar acceso a la conexión global

    $query = "SELECT code_sucursal  FROM sucursal WHERE id_sucursal  = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $id_sucursal);

    $stmt->execute();
    $stmt->bind_result($code_sucursal);
    $stmt->fetch();
    $stmt->close();
    return $code_sucursal;
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
            margin-left: 300px;

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

        .table-responsive {
            overflow-x: auto;
            max-height: 600px;
        }

        th,
        td {
            white-space: nowrap;
        }

        /* Opcional: resaltar la primera columna */
        td:first-child,
        th:first-child {
            position: sticky;
            left: 0;
            z-index: 1;
        }


        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }


        /* Fijar encabezado superior */
        thead tr:first-child th {
            background-color: #343a40;
            color: #fff;
            text-align: center;
            font-weight: bold;
        }

        .fixed-save-button {
            position: fixed;
            bottom: 20px;
            right: 30px;
            z-index: 99;
        }

        td input[type="checkbox"] {
            display: block;
            margin: auto;
            margin-top: 15px;
            height: 27px;
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
        <div style="display: flex; justify-content: flex-end; margin: 20px 0;">
            <a href="addReport.php" class="btn-add-store">
                Go To Report
            </a>
        </div>
    </div>
    <?php
    date_default_timezone_set("America/Bogota");
    include("../../conexion.php");
    require_once("../../zebra.php");
    //traigo las tiendas para el select del modal
    $sql_store = "SELECT * FROM store 
    JOIN sucursal ON store.id_store = sucursal.id_store 
    ORDER BY store_name ASC";
    $result_store = $mysqli->query($sql_store);
    if (!$result_store) {
        die("Error en la consulta: " . $mysqli->error);
    }
    $stores = $result_store->fetch_all(MYSQLI_ASSOC);

    //traer todo de daily_report ordenado por fecha en la fecha_alta_report
    $sql = "SELECT * FROM daily_report
    WHERE estado_reporte = 1
     ORDER BY fecha_alta_reporte DESC";
    $result = $mysqli->query($sql);
    if (!$result) {
        die("Error en la consulta: " . $mysqli->error);
    }
    $reports = $result->fetch_all(MYSQLI_ASSOC);



    ?>
    <div class=" mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <form action="procesar_articulos.php" method="POST">

                    <div class="table-responsive bg-white p-4 rounded shadow-sm border">
                        <h5 class="mb-4 text-center">Daily Reports</h5>
                        <table class="table table-striped table-bordered table-sm">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th></th>
                                    <th class="columna">Date</th>
                                    <th colspan="2">UPC</th>
                                    <th colspan="4" >Info</th>
                                    
                                    <th colspan="4">Product Info</th>
                                    <th colspan="3">Specs</th>
                                    
                                    <th colspan="2">Inventory</th>
                                    <th></th>
                                    <th></th>
                                    <th>Observation</th>
                                </tr>
                                <tr>
                                    <th class="columna"></th>
                                    <th class="columna"></th>
                                    <th>Assigned</th>
                                    <th>Final</th>
                                    <th>Cons</th>
                                    <th>Folder</th>
                                    <th>Location</th>
                                    <th>Quantity</th>
                                    <th class="columna">SKU</th>
                                    <th>Brand</th>
                                    <th class="columna">Item</th>
                                    <th>Vendor</th>
                                    <th>Color</th>
                                    <th>Size</th>
                                    <th>Cost</th>
                                    <th>Category</th>
                                    <th>Weight</th>
                                    <th>Inventory</th>
                                    <th>Sucursal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $index => $report): ?>
                                    <tr>
                                        <input type="hidden" name="id_report[]" value="<?= $report['id_report'] ?>">
                                        <td>
                                            <input type="checkbox" name="seleccionados[]" value="<?= $index ?>">
                                        </td>
                                        <td><input style="width: 120px;" type="text" name="fecha_alta_reporte[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['fecha_alta_reporte']) ?>"></td>
                                        <td><input style="width: 140px;" type="text" name="upc_asignado_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['upc_asignado_report']) ?>"></td>
                                        <td><input style="width: 140px;" type="text" name="upc_final_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['upc_final_report']) ?>"></td>
                                        <td><input style="width: 80px;" type="text" name="cons_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['cons_report']) ?>"></td>
                                        <td><input style="width: 90px;" type="text" name="folder_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['folder_report']) ?>"></td>
                                        <td><input type="text" style="width: 90px;" name="loc_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['loc_report']) ?>"></td>
                                        <td><input style="width: 60px;" type="text" name="quantity_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['quantity_report']) ?>"></td>
                                        <td><input style="width: 160px;" type="text" name="sku_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['sku_report']) ?>"></td>
                                        <td><input style="width: 160px;" type="text" name="brand_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['brand_report']) ?>"></td>
                                        <td><input type="text" style="width: 240px;" name="item_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['item_report']) ?>"></td>
                                        <td><input style="width: 80px;" type="text" name="vendor_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['vendor_report']) ?>"></td>
                                        <td><input type="text" style="width: 110px;" name="color_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['color_report']) ?>"></td>
                                        <td><input style="width: 110px;" type="text" name="size_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['size_report']) ?>"></td>
                                        <td><input style="width: 110px;" type="text" name="cost_report[]" class="form-control form-control-sm"></td>
                                        <td><input type="text" style="width: 100px;" name="category_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['category_report']) ?>"></td>
                                        <td><input type="text" style="width: 140px;" name="weight_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['weight_report']) ?>"></td>
                                        <td><input style="width: 140px;" type="text" name="inventory_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['inventory_report']) ?>"></td>
                                        <td><input style="width: 140px;" type="text" name="sucursal_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars(getSucursal($report['sucursal_report'])) ?>"></td>
                                        <td><input style="width: 180px;" type="text" name="observacion_report[]" class="form-control form-control-sm" value="<?= htmlspecialchars($report['observacion_report']) ?>"></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <div class="text-center mt-3">
                            <button type="submit" class="btn btn-success">Guardar Seleccionados</button>
                        </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="seleccionados[]"]');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const row = this.closest('tr');
                    if (this.checked) {
                        row.classList.add('table-success');
                    } else {
                        row.classList.remove('table-success');
                    }
                });
            });
        });
    </script>
</body>

</html>