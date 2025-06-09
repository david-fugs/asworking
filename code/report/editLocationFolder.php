<?php
session_start();
include("../../conexion.php");
if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
    exit();
}
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usu = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'user';

/*
 * FLUJO DE ESTADOS DE REPORTES:
 * estado_reporte = 1: Reporte nuevo, sin procesar (visible en addReport.php)
 * estado_reporte = 0: Reporte procesado, listo para editar location/folder (visible aquí)
 * estado_reporte = -1: Reporte completamente procesado (no visible en vistas de edición)
 */

// Obtener reportes que ya fueron procesados (estado_reporte = 0) pero necesitan edición de location/folder
// Una vez editados exitosamente, cambiarán a estado_reporte = -1 y ya no aparecerán aquí
$sql = "SELECT * FROM daily_report
        WHERE estado_reporte = 0
        ORDER BY fecha_alta_reporte DESC";
$result = $mysqli->query($sql);
if (!$result) {
    die("Error en la consulta: " . $mysqli->error);
}
$reports = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ASWWORKING | Edit Product Details</title>
    <script src="js/64d58efce2.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Orbitron" rel="stylesheet">
    <link href="../../../fontawesome/css/all.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
            background-color: #632b8b;
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            margin-right: 100px;
            transition: background-color 0.3s;
        }

        .btn-add-store:hover {
            background-color: #5d337a;
            color: white;
        }

        .header-container {
            width: 100%;
            background-color: #dac7e5;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .header {
            background-color: #dac7e5;
            display: flex;
            align-items: center;
            padding: 30px 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            position: relative;
            justify-content: center;
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
            height: 100px;
            width: auto;
            max-height: 100%;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .title {
            margin: 0 auto;
            font-size: 40px;
            font-weight: 700;
            color: #632b8b;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: transparent;
            border: none;
            color: #5d337a;
            font-size: 1.8rem;
            cursor: pointer;
            padding: 15px;
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 50px;
            height: 50px;
        }        .back-btn:hover {
            background-color: rgba(93, 51, 122, 0.1);
            color: #632b8b;
            transform: translateX(-3px);
        }        .table-container {
            border-radius: 10px;
            overflow-x: auto;
            overflow-y: hidden;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            background: white;
            padding: 20px;
            margin: 20px 0;
            max-width: 100%;
            width: 100%;
        }
        
        .table-content {
            min-width: 1600px;
            width: 100%;
        }

        table {
            width: 100%;
            min-width: 1600px;
            border-collapse: separate;
            border-spacing: 0;
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(to right, #f9f5ff, #f0e6ff);
            border: 2px solid rgb(216, 194, 234);
        }

        thead {
            background: rgb(113, 63, 148);
            color: white;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        tbody tr {
            background-color: rgba(255, 255, 255, 0.8);
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        tbody tr:nth-child(even) {
            background-color: rgba(248, 240, 255, 0.8);
        }

        tbody tr:hover {
            background-color: rgba(218, 199, 229, 0.4);
        }

        thead th {
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.3px;
            border-bottom: 2px solid rgba(255, 255, 255, 0.1);
            white-space: nowrap;
        }

        tbody td {
            padding: 10px 8px;
            border-bottom: 1px solid rgba(153, 124, 171, 0.3);
            color: #444;
            font-size: 0.8rem;
            text-align: center;
            vertical-align: middle;
        }

        .form-control-sm {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(153, 124, 171, 0.5);
            border-radius: 4px;
            padding: 4px 6px;
            font-size: 0.75rem;
            transition: all 0.3s ease;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .badge {
            font-size: 0.7rem;
            padding: 4px 8px;
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }

        .form-control-sm:focus {
            outline: none;
            border-color: #632b8b;
            box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
            background-color: white;
        }

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

        .alert-info {
            background-color: #e6f3ff;
            border-color: #b3d9ff;
            color: #0066cc;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }

        .btn-save {
            background: linear-gradient(to bottom, #28a745, #20c997);
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
            box-shadow: 0 4px 6px rgba(40, 167, 69, 0.3);
        }

        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(40, 167, 69, 0.4);
        }
    </style>
</head>

<body>
    <div class="header-container">
        <div class="header">
            <div class="logo-container">
                <img src='../../img/logo.png' class="logo" alt="Logo">
            </div>
            <h1 class="title"><i class="fa-solid fa-edit"></i> EDIT PRODUCT DETAILS</h1>
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
            <a href="seeReport.php" class="btn-add-store">
                <i class="fas fa-file-alt"></i> Back to Reports
            </a>
        </div>
    </div>    <div class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <?php                // Show success/error messages
                if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i>
                        <strong>Success!</strong> <?= htmlspecialchars($_SESSION['success_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['success_message']); ?>
                <?php endif; ?>

                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Error:</strong> <?= htmlspecialchars($_SESSION['error_message']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error_message']); ?>
                <?php endif; ?>                <?php if (count($reports) == 0): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i>
                        <strong>No processed reports available for editing.</strong><br>
                        Reports will appear here after being processed in the main menu.
                    </div>
                <?php else: ?>                    <!-- Debug Info (remove in production) -->
                    <?php if (isset($_GET['debug'])): ?>
                        <div class="alert alert-warning">
                            <strong>Debug Info:</strong><br>
                            Total reports found: <?= count($reports) ?><br>
                            Reports: <?= implode(', ', array_column($reports, 'id_report')) ?>
                        </div>
                    <?php endif; ?>
                      <form action="updateLocationFolder.php" method="POST">
                        <div class="table-container">
                            <h5 class="mb-4 text-center">Edit Product Details - Processed Reports (<?= count($reports) ?> records)</h5>                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h6><i class="fas fa-info-circle"></i> <strong>Instructions:</strong></h6>
                                        <ul class="mb-0">
                                            <li>Select the reports you want to update by checking the checkbox</li>
                                            <li>Modify the product fields as needed</li>
                                            <li>Editable fields: Item, Brand, Vendor, Color, Size, Folder, Location</li>
                                            <li>Click "Update Selected Items" to save changes</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="table-content">
                                <table>
                                <thead>
                                    <tr>
                                        <th>Select</th>
                                        <th>Date</th>
                                        <th>UPC Final</th>
                                        <th>SKU</th>
                                        <th>Current Item</th>
                                        <th>New Item</th>
                                        <th>Current Brand</th>
                                        <th>New Brand</th>
                                        <th>Current Vendor</th>
                                        <th>New Vendor</th>
                                        <th>Current Color</th>
                                        <th>New Color</th>
                                        <th>Current Size</th>
                                        <th>New Size</th>
                                        <th>Current Folder</th>
                                        <th>New Folder</th>
                                        <th>Current Location</th>
                                        <th>New Location</th>
                                    </tr>
                                </thead>                                <tbody>
                                    <?php foreach ($reports as $index => $report): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_reports[]" value="<?= $report['id_report'] ?>">
                                            </td>
                                            <td><?= htmlspecialchars($report['fecha_alta_reporte']) ?></td>
                                            <td><?= htmlspecialchars($report['upc_final_report']) ?></td>
                                            <td><?= htmlspecialchars($report['sku_report']) ?></td>
                                              <!-- Current Item -->
                                            <td style="max-width: 250px;">
                                                <span class="badge bg-secondary" title="<?= htmlspecialchars($report['item_report']) ?>" style="max-width: 100%; white-space: normal; font-size: 0.75rem;">
                                                    <?= htmlspecialchars(substr($report['item_report'], 0, 40)) ?><?= strlen($report['item_report']) > 40 ? '...' : '' ?>
                                                </span>
                                            </td>                                            <!-- New Item -->
                                            <td>
                                                <input style="width: 150px;" type="text" 
                                                       name="new_item[<?= $report['id_report'] ?>]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= htmlspecialchars($report['item_report']) ?>"
                                                       placeholder="New item">
                                            </td>
                                            
                                            <!-- Current Brand -->
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($report['brand_report']) ?></span>
                                            </td>
                                            <!-- New Brand -->
                                            <td>
                                                <input style="width: 120px;" type="text" 
                                                       name="new_brand[<?= $report['id_report'] ?>]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= htmlspecialchars($report['brand_report']) ?>"
                                                       placeholder="New brand">
                                            </td>
                                            
                                            <!-- Current Vendor -->
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($report['vendor_report']) ?></span>
                                            </td>
                                            <!-- New Vendor -->
                                            <td>
                                                <input style="width: 120px;" type="text" 
                                                       name="new_vendor[<?= $report['id_report'] ?>]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= htmlspecialchars($report['vendor_report']) ?>"
                                                       placeholder="New vendor">
                                            </td>
                                            
                                            <!-- Current Color -->
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($report['color_report']) ?></span>
                                            </td>
                                            <!-- New Color -->
                                            <td>
                                                <input style="width: 100px;" type="text" 
                                                       name="new_color[<?= $report['id_report'] ?>]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= htmlspecialchars($report['color_report']) ?>"
                                                       placeholder="New color">
                                            </td>
                                            
                                            <!-- Current Size -->
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($report['size_report']) ?></span>
                                            </td>
                                            <!-- New Size -->
                                            <td>
                                                <input style="width: 80px;" type="text" 
                                                       name="new_size[<?= $report['id_report'] ?>]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= htmlspecialchars($report['size_report']) ?>"
                                                       placeholder="New size">
                                            </td>
                                            
                                            <!-- Current Folder -->
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($report['folder_report']) ?></span>
                                            </td>
                                            <!-- New Folder -->
                                            <td>
                                                <input style="width: 120px;" type="text" 
                                                       name="new_folder[<?= $report['id_report'] ?>]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= htmlspecialchars($report['folder_report']) ?>"
                                                       placeholder="New folder">
                                            </td>
                                            
                                            <!-- Current Location -->
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($report['loc_report']) ?></span>
                                            </td>
                                            <!-- New Location -->
                                            <td>
                                                <input style="width: 120px;" type="text" 
                                                       name="new_location[<?= $report['id_report'] ?>]" 
                                                       class="form-control form-control-sm" 
                                                       value="<?= htmlspecialchars($report['loc_report']) ?>"
                                                       placeholder="New location">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>                                </tbody>
                            </table>
                            </div>
                            
                            <div class="text-center mt-4">
                                <button type="submit" class="btn-save">
                                    <i class="fas fa-save"></i> Update Selected Items
                                </button>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <center>
        <a href="../../access.php" class="back-btn" title="Go Back">
            <i class="fas fa-arrow-circle-left fa-xl"></i>
        </a>
    </center>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"][name="selected_reports[]"]');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const row = this.closest('tr');
                    if (this.checked) {
                        row.style.backgroundColor = 'rgba(40, 167, 69, 0.1)';
                        row.style.border = '2px solid #28a745';
                    } else {
                        row.style.backgroundColor = '';
                        row.style.border = '';
                    }
                });
            });            // Form validation
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const selectedCheckboxes = document.querySelectorAll('input[name="selected_reports[]"]:checked');
                
                if (selectedCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one report to update.');
                    return false;
                }

                // Confirm before submitting
                const confirmUpdate = confirm(`Are you sure you want to update ${selectedCheckboxes.length} report(s)?`);
                if (!confirmUpdate) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    </script>
</body>
</html>
