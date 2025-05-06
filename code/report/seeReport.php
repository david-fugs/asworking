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

        

        .fixed-save-button {
            position: fixed;
            bottom: 20px;
            right: 30px;
            z-index: 99;
        }

        .header-container {
        width: 100%;
        background-color: #dac7e5; /* Color de fondo */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
        /* ENCABEZADO ORIGINAL */
        .header {
        background-color: #dac7e5; /* Nuevo color de fondo */
        display: flex;
        align-items: center;
        padding: 30px 20px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: relative;
        justify-content: center; /* Centramos el contenido */
    }

    .logo-container {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        height: 100%;
        display: flex;
        align-items: center;
    }

    .logo {
        height: 100px; /* Tamaño ligeramente reducido */
        width: auto;
        max-height: 100%;
        transition: transform 0.3s ease;
    }

    .logo:hover {
        transform: scale(1.05); /* Efecto hover sutil */
    }
    .title {
        margin: 0 auto;
        font-size: 40px; /* Tamaño ajustado */
        font-weight: 700;
        color: #632b8b; /* Color morado oscuro para contraste */
        text-transform: uppercase;
        letter-spacing: 2px; /* Mayor espaciado */
        text-align: center;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
    }
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
            background-color: #632b8b; /* Cambiado a morado principal */
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 100px;
            transition: background-color 0.3s;
        }

        .btn-add-store:hover {
            background-color: #5d337a; /* Cambiado a morado oscuro */
            color: white;
        }
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
  /* Estilos generales para la tabla */
  .table-container {
        border-radius: 100px;
        overflow: hidden;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        background: white;
        padding: 2px;
    }
    
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-family: 'Montserrat', sans-serif;
        background: linear-gradient(to right, #f9f5ff, #f0e6ff);
    }
    
    /* Bordes mejorados para toda la tabla */
    table {
        border: 2px solidrgb(216, 194, 234);
        box-shadow: 0 0 0 1px #4a2568; /* Borde interior para efecto de doble línea */
    }

    /* Encabezado con bordes */
    thead th {
        border-right: 1px solid rgba(238, 192, 246, 0.2);
        border-bottom: 2px solidrgb(231, 212, 246);
        position: relative;
    }

    thead th::before {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 1px;
        background: rgba(255, 255, 255, 0.1);
    }

    /* Celdas del cuerpo con bordes */
    tbody td {
        border-right: 1px solid rgba(74, 37, 104, 0.5);
        border-bottom: 1px solid rgba(74, 37, 104, 0.5);
        position: relative;
    }

    /* Eliminar bordes duplicados */
    thead th:last-child,
    tbody td:last-child {
        border-right: none;
    }

    tbody tr:last-child td {
        border-bottom: none;
    }

    /* Efecto hover con bordes destacados */
    tbody tr:hover td {
        border-color: #632b8b;
        border-width: 1px;
        box-shadow: 
            inset 0 0 0 1px #632b8b,
            0 0 0 1px #632b8b;
    }

    /* Esquinas redondeadas */
    thead tr:first-child th:first-child {
        border-top-left-radius: 10px;
    }

    thead tr:first-child th:last-child {
        border-top-right-radius: 10px;
    }

    tbody tr:last-child td:first-child {
        border-bottom-left-radius: 10px;
    }

    tbody tr:last-child td:last-child {
        border-bottom-right-radius: 10px;
    }
    /* Estilos para el encabezado - Efecto vidrio (glassmorphism) */
    thead {
        background:rgb(113, 63, 148);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
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
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    thead th:hover {
        background: rgba(109, 53, 149, 0.9);
    }
    
    thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: white;
        transition: width 0.3s ease;
    }
    
    thead th:hover::after {
        width: 70%;
    }
    
    /* Estilos para las filas - Efecto hover mejorado */
    tbody tr {
        background-color: rgba(255, 255, 255, 0.8);
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
    }
    
    tbody tr:nth-child(even) {
        background-color: rgba(248, 240, 255, 0.8);
    }
    
    tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(99, 43, 139, 0.15);
        background-color: white;
        z-index: 2;
    }
    
    tbody tr::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 3px;
        height: 100%;
        background: linear-gradient(to bottom, #632b8b, #dac7e5);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    tbody tr:hover::before {
        opacity: 1;
    }
    
    /* Estilos para las celdas */
    tbody td {
        padding: 12px 10px;
        border-bottom: 1px solid rgba(153, 124, 171, 0.3);
        color: #444;
        font-size: 0.9rem;
        position: relative;
        transition: all 0.2s ease;
    }
    
    tbody tr:hover td {
        color: #333;
    }
    
    /* Efecto hover para celdas individuales */
    tbody td:hover {
        background: rgba(218, 199, 229, 0.4);
    }
    
    /* Estilos para los inputs - Diseño moderno */
    .form-control-sm {
        background-color: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(153, 124, 171, 0.5);
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
    }
    
    .form-control-sm:focus {
        outline: none;
        border-color: #632b8b;
        box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
        background-color: white;
    }
    
    /* Estilos para el checkbox */
    input[type="checkbox"] {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border: 2px solid #997cab;
        border-radius: 4px;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
    }
    
    input[type="checkbox"]:checked {
        background-color: #632b8b;
        border-color: #632b8b;
    }
    
    input[type="checkbox"]:checked::after {
        content: '✓';
        position: absolute;
        color: white;
        font-size: 12px;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
    }
    
    /* Estilos para el botón - Efecto 3D */
    .btn-add-store {
        background: linear-gradient(to bottom, #632b8b, #5d337a);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(99, 43, 139, 0.3),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
        position: relative;
        overflow: hidden;
    }
    
    .btn-add-store:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(99, 43, 139, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    
    .btn-add-store:active {
        transform: translateY(1px);
        box-shadow: 0 2px 4px rgba(99, 43, 139, 0.4),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
    }
    
    .btn-add-store::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            to bottom right,
            rgba(255, 255, 255, 0) 0%,
            rgba(255, 255, 255, 0) 45%,
            rgba(255, 255, 255, 0.3) 48%,
            rgba(255, 255, 255, 0) 52%,
            rgba(255, 255, 255, 0) 100%
        );
        transform: rotate(30deg);
        transition: all 0.5s ease;
    }
    
    .btn-add-store:hover::after {
        left: 100%;
    }
    
    /* Bordes redondeados para la tabla */
    table {
        border-radius: 10px;
    }
    
    thead tr:first-child th:first-child {
        border-top-left-radius: 10px;
    }
    
    thead tr:first-child th:last-child {
        border-top-right-radius: 10px;
    }
    
    tbody tr:last-child td:first-child {
        border-bottom-left-radius: 10px;
    }
    
    tbody tr:last-child td:last-child {
        border-bottom-right-radius: 10px;
    }
    
    /* Efecto de título flotante */
    h5 {
        color: #632b8b;
        font-weight: 700;
        margin-bottom: 1.8rem !important;
        text-align: center;
        position: relative;
        display: inline-block;
        left: 50%;
        transform: translateX(-50%);
        padding: 0 20px;
    }
    
    h5::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(to right, transparent, #632b8b, transparent);
        border-radius: 3px;
    }
    </style>
</head>

<body>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"></script>
    <div class="header-container">
    <div class="header">
        <div class="logo-container">
            <img src='../../img/logo.png' class="logo" alt="Logo">
        </div>
        <h1 class="title"><i class="fa-solid fa-file-signature"></i> DAILY REPORT</h1>
    </div>
</div>
<div class="top-bar">
    <div></div>
    <div class="center">
        <a href="../../access.php" class="back-btn" title="Go Back">
            <i class="fas fa-arrow-circle-left fa-xl"></i> 
        </a>
    </div>
    <div style="display: flex; justify-content: flex-end; margin: 20px 0;">
        <a href="addReport.php" class="btn-add-store">
            <i class="fas fa-file-alt"></i> Go to Report
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
    <div class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <form action="procesar_articulos.php" method="POST">

                    <div class="">
                        <h5 class="mb-4 text-center">Daily Reports</h5>
                        <table class="">
                            <thead class="">
                                <tr>
                                    <th></th>
                                    <th class="">Date</th>
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
                                    <th class=""></th>
                                    <th class=""></th>
                                    <th>Assigned</th>
                                    <th>Final</th>
                                    <th>Cons</th>
                                    <th>Folder</th>
                                    <th>Location</th>
                                    <th>Quantity</th>
                                    <th class="">SKU</th>
                                    <th>Brand</th>
                                    <th class="">Item</th>
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
                            <button type="submit" class="btn-add-store">Save Selected</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <center>
        <a href="../../access.php" class="back-btn" title="Go Back">
            <i class="fas fa-arrow-circle-left fa-xl"></i> 
        </a>
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