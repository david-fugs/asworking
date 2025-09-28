<?php
session_start();
include("../../conexion.php");

// Debug log
error_log("editLocationFolder.php - Starting page load");
error_log("Session ID: " . (isset($_SESSION['id']) ? $_SESSION['id'] : 'NOT SET'));

if (!isset($_SESSION['id'])) {
    error_log("editLocationFolder.php - No session ID, redirecting to index");
    header("Location: ../../index.php");
    exit();
}
$nombre = isset($_SESSION['nombre']) ? $_SESSION['nombre'] : 'Usuario';
$tipo_usu = isset($_SESSION['tipo_usuario']) ? $_SESSION['tipo_usuario'] : 'user';

error_log("editLocationFolder.php - User logged in: " . $nombre);

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

error_log("editLocationFolder.php - SQL Query: " . $sql);

$result = $mysqli->query($sql);
if (!$result) {
    error_log("editLocationFolder.php - Query error: " . $mysqli->error);
    die("Error en la consulta: " . $mysqli->error);
}
$reports = $result->fetch_all(MYSQLI_ASSOC);
error_log("editLocationFolder.php - Found " . count($reports) . " reports with estado_reporte = 0");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ASWWORKING | Edit Product Details</title>
    <script src="../../js/64d58efce2.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Lobster" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Orbitron" rel="stylesheet">
    <link href="../../fontawesome/css/all.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        .btn-add-store {
            background: linear-gradient(135deg, #632b8b, #5d337a);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 12px 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(99, 43, 139, 0.3);
            margin: 10px 5px;
        }

        .btn-add-store:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(99, 43, 139, 0.4);
            text-decoration: none;
            color: white;
        }

        .header-container {
            background: linear-gradient(135deg, #632b8b, #5d337a);
            color: white;
            padding: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .logo-container {
            margin-right: 20px;
        }

        .logo {
            height: 80px;
            width: auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .logo:hover {
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        .title {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .back-btn {
            color: #632b8b;
            font-size: 2rem;
            text-decoration: none;
            transition: all 0.3s ease;
            margin: 0 10px;
        }

        .back-btn:hover {
            color: #5d337a;
            transform: scale(1.1);
            text-decoration: none;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 20px 0;
        }

        .table-content {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        thead {
            background: linear-gradient(135deg, #632b8b, #5d337a);
            color: white;
        }

        tbody tr {
            transition: all 0.3s ease;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e3f2fd !important;
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        thead th {
            padding: 15px 10px;
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        tbody td {
            padding: 12px 8px;
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
        }

        .form-control-sm {
            border: 1px solid #632b8b;
            border-radius: 5px;
            padding: 6px 10px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }        .form-control-sm:focus {
            border-color: #5d337a;
            box-shadow: 0 0 0 2px rgba(99, 43, 139, 0.2);
            outline: none;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #632b8b;
            cursor: pointer;
        }

        input[type="checkbox"]:checked {
            background-color: #632b8b;
        }

        input[type="checkbox"]:checked::after {
            content: '✓';
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        h5 {
            color: #632b8b;
            font-weight: 700;
            text-align: center;
            margin-bottom: 25px;
            position: relative;
        }

        h5::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(to right, #632b8b, #5d337a);
            border-radius: 3px;
        }

        .alert-info {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border: none;
            border-left: 4px solid #2196f3;
            border-radius: 10px;
            color: #1565c0;
        }

        .btn-save {
            background: linear-gradient(135deg, #28a745, #20c997);
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

        /* Estilos para el buscador UPC */
        .search-container {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .search-input {
            border: 2px solid #632b8b;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 1rem;
            width: 300px;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
            border-color: #5d337a;
        }

        .search-btn {
            background: linear-gradient(135deg, #632b8b, #5d337a);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 12px 25px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-left: 10px;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(99, 43, 139, 0.3);
        }

        /* Estilos para el modal */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #632b8b, #5d337a);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .item-card {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 12px;
            padding: 20px;
            margin: 15px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #632b8b;
        }

        .item-detail {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .item-label {
            font-weight: 600;
            color: #632b8b;
        }

        .item-value {
            color: #495057;
            font-weight: 500;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background: #f8f9fa;
        }

        .center {
            flex-grow: 1;
            text-align: center;
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
    </div>

    <!-- Buscador de UPC -->
    <div class="container-fluid">
        <div class="search-container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h4 style="color: #632b8b; margin-bottom: 20px;">
                        <i class="fas fa-search"></i> Search Product Information by UPC
                    </h4>
                    <div class="d-flex justify-content-center align-items-center">
                        <input type="text"
                            id="upcSearch"
                            class="search-input"
                            placeholder="Enter UPC code..."
                            maxlength="20">
                        <button type="button"
                            class="search-btn"
                            onclick="searchUPC()">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid mt-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <?php 
                // Show success/error messages
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
                <?php endif; ?>

                <?php if (count($reports) == 0): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i>
                        <strong>No processed reports available for editing.</strong><br>
                        Reports will appear here after being processed in the main menu.
                    </div>
                <?php else: ?>
                    <!-- Debug Info (remove in production) -->
                    <?php if (isset($_GET['debug'])): ?>
                        <div class="alert alert-warning">
                            <strong>Debug Info:</strong><br>
                            Total reports found: <?= count($reports) ?><br>
                            Reports: <?= implode(', ', array_column($reports, 'id_report')) ?>
                        </div>
                    <?php endif; ?>

                    <form action="updateLocationFolder.php" method="POST">
                        <div class="table-container">
                            <h5 class="mb-4 text-center">Edit Product Details - Processed Reports (<?= count($reports) ?> records)</h5>
                            <div class="alert alert-info">
                                <div class="row">
                                    <div class="col-md-12">                                        <h6><i class="fas fa-info-circle"></i> <strong>Instructions:</strong></h6>
                                        <ul class="mb-0">
                                            <li>Use the UPC search above to query product information</li>
                                            <li>Select the reports you want to update by checking the checkbox</li>
                                            <li><strong>Update only:</strong> New Folder and New Location fields (no validation required)</li>
                                            <li>Other fields are in read-only mode for reference</li>
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
                                            <th>Quantity</th>
                                            <th>Observation</th>
                                            <th>SKU</th>
                                            <th>Item</th>
                                            <th>Brand</th>
                                            <th>Style</th>
                                            <th>Color</th>
                                            <th>Size</th>
                                            <th>Current Folder</th>
                                            <th>New Folder</th>
                                            <th>Current Location</th>
                                            <th>New Location</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($reports as $index => $report): 
                                            // Determine current location: prefer report loc_report, but if empty or '0' try to read from items.inventory_item using upc_final_report
                                            $current_loc = isset($report['loc_report']) ? trim($report['loc_report']) : '';
                                            if ($current_loc === '' || $current_loc === '0') {
                                                $upc_lookup = isset($report['upc_final_report']) ? $report['upc_final_report'] : '';
                                                if ($upc_lookup !== '') {
                                                    $stmt = $mysqli->prepare("SELECT inventory_item FROM items WHERE upc_item = ? LIMIT 1");
                                                    if ($stmt) {
                                                        $stmt->bind_param('s', $upc_lookup);
                                                        $stmt->execute();
                                                        $stmt->bind_result($inv_item);
                                                        if ($stmt->fetch()) {
                                                            if (!empty($inv_item)) $current_loc = $inv_item;
                                                        }
                                                        $stmt->close();
                                                    }
                                                }
                                            }
                                            // Normalize display: treat '0' as empty for both current and new location
                                            $current_loc_display = ($current_loc === '0' ? '' : $current_loc);
                                            $new_loc_value = (isset($report['loc_report']) && trim($report['loc_report']) !== '0') ? $report['loc_report'] : '';
                                        ?>
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="selected_reports[]" value="<?= $report['id_report'] ?>">
                                                </td>
                                                <td><?= htmlspecialchars($report['fecha_alta_reporte']) ?></td>
                                                <td><?= htmlspecialchars($report['upc_final_report']) ?></td>
                                                <td style="width:120px;">
                                                    <input type="text" name="edited_quantity[<?= $report['id_report'] ?>]" class="form-control form-control-sm edited-quantity" placeholder="<?= htmlspecialchars($report['quantity_report']) ?>" value="">
                                                </td>
                                                
                                                <!-- Observation (para observaciones generales, no cantidad agregada) -->
                                                <td style="max-width: 200px;">
                                                    <span class="badge bg-info" style="font-size: 0.7rem; white-space: normal;">
                                                        <?= isset($report['observacion_report']) && !empty($report['observacion_report']) ? htmlspecialchars($report['observacion_report']) : 'No observation' ?>
                                                    </span>
                                                </td>
                                                
                                                <td><?= htmlspecialchars($report['sku_report']) ?></td>

                                                <!-- Item (solo lectura) -->
                                                <td style="max-width: 250px;">
                                                    <span class="badge bg-secondary" title="<?= htmlspecialchars($report['item_report']) ?>" style="max-width: 100%; white-space: normal; font-size: 0.75rem;">
                                                        <?= htmlspecialchars(substr($report['item_report'], 0, 40)) ?>
                                                        <?= strlen($report['item_report']) > 40 ? '...' : '' ?>
                                                    </span>
                                                </td>

                                                <!-- Brand (solo lectura) -->
                                                <td>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($report['brand_report']) ?></span>
                                                </td>

                                                <!-- Vendor/Style (solo lectura): preferir style_report de daily_report si existe -->
                                                <td>
                                                    <input style="width: 120px;" type="text"
                                                        name="new_vendor[<?= $report['id_report'] ?>]"
                                                        class="form-control form-control-sm"
                                                        value="<?= htmlspecialchars(!empty($report['style_report']) ? $report['style_report'] : ($report['vendor_report'] ?? '')) ?>"
                                                        placeholder="Style" readonly>
                                                </td>

                                                <!-- Color (solo lectura) -->
                                                <td>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($report['color_report']) ?></span>
                                                </td>

                                                <!-- Size (solo lectura) -->
                                                <td>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($report['size_report']) ?></span>
                                                </td>

                                                <!-- Current Folder -->
                                                <td>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($report['folder_report']) ?></span>
                                                </td>                                                <!-- New Folder (editable, sin validación) -->
                                                <td>
                                                    <input style="width: 120px;" type="text"
                                                        name="new_folder[<?= $report['id_report'] ?>]"
                                                        class="form-control form-control-sm"
                                                        value="<?= htmlspecialchars($report['folder_report']) ?>"
                                                        placeholder="New folder">
                                                </td>

                                                <!-- Current Location -->
                                                <td>
                                                    <span class="badge bg-secondary"><?= htmlspecialchars($current_loc_display) ?></span>
                                                </td>
                                                <!-- New Location (editable, sin validación) -->
                                                <td>
                                                    <input style="width: 120px;" type="text"
                                                        name="new_location[<?= $report['id_report'] ?>]"
                                                        class="form-control form-control-sm new-location-input"
                                                        data-report-id="<?= $report['id_report'] ?>"
                                                        value=""
                                                        placeholder="New location">
                                                </td>
                                                <!-- Delete button -->
                                                <td>
                                                    <button type="button" class="btn btn-danger btn-sm delete-report-btn" data-id="<?= $report['id_report'] ?>">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
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

    <!-- Modal para mostrar información del UPC -->
    <div class="modal fade" id="upcModal" tabindex="-1" aria-labelledby="upcModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="upcModalLabel">
                        <i class="fas fa-info-circle"></i> Product Information
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="upcModalBody">
                    <!-- Content will be loaded here dynamically -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Function to search UPC
        function searchUPC() {
            const upc = document.getElementById('upcSearch').value.trim();

            if (upc === '') {
                alert('Please enter a UPC code');
                return;
            }

            // Show loading
            document.getElementById('upcModalBody').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Searching...</span>
                    </div>
                    <p class="mt-2">Searching for UPC information...</p>
                </div>
            `;

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('upcModal'));
            modal.show();

            // Perform AJAX search
            fetch('searchUPC.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'upc=' + encodeURIComponent(upc)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayItemInfo(data.item);
                    } else {
                        displayError(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    displayError('Error searching UPC. Please try again.');
                });
        }

        // Toggle required on new location when a row is selected
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="updateLocationFolder.php"]');
            if (!form) return;

            // Attach change listeners to checkboxes
            const checkboxes = form.querySelectorAll('input[type="checkbox"][name="selected_reports[]"]');
            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    const reportId = this.value;
                    // new location input
                    const input = form.querySelector('input.new-location-input[data-report-id="' + reportId + '"]');
                    // quantity input
                    const qtyInput = form.querySelector('input.edited-quantity[name="edited_quantity[' + reportId + ']"]');
                    if (input) {
                        if (this.checked) {
                            input.setAttribute('required', 'required');
                            input.classList.add('required-highlight');
                        } else {
                            input.removeAttribute('required');
                            input.classList.remove('required-highlight');
                        }
                    }
                    if (qtyInput) {
                        if (this.checked) {
                            qtyInput.setAttribute('required', 'required');
                            qtyInput.classList.add('required-highlight');
                        } else {
                            qtyInput.removeAttribute('required');
                            qtyInput.classList.remove('required-highlight');
                        }
                    }
                });
            });

            // Before submit validate selected rows have new location
            form.addEventListener('submit', function(e) {
                const selected = form.querySelectorAll('input[type="checkbox"][name="selected_reports[]"]:checked');
                let invalid = false;
                selected.forEach(cb => {
                    const reportId = cb.value;
                    const input = form.querySelector('input.new-location-input[data-report-id="' + reportId + '"]');
                    const qtyInput = form.querySelector('input.edited-quantity[name="edited_quantity[' + reportId + ']"]');
                    if (input && input.value.trim() === '') {
                        invalid = true;
                        input.classList.add('is-invalid');
                    }
                    if (qtyInput && qtyInput.value.trim() === '') {
                        invalid = true;
                        qtyInput.classList.add('is-invalid');
                    }
                });
                if (invalid) {
                    e.preventDefault();
                    Swal.fire({ icon: 'warning', title: 'Validation', text: 'Please provide New Location for every selected row.' });
                    return false;
                }
            });
        });

        // Function to display item information
        function displayItemInfo(item) {
            const modalBody = document.getElementById('upcModalBody');
            modalBody.innerHTML = `
                <div class="item-card">
                    <h6 style="color: #632b8b; margin-bottom: 15px;">
                        <i class="fas fa-barcode"></i> Product Details
                    </h6>
                    <div class="item-detail">
                        <span class="item-label">ID:</span>
                        <span class="item-value">${item.id || 'N/A'}</span>
                    </div>
                    <div class="item-detail">
                        <span class="item-label">UPC:</span>
                        <span class="item-value">${item.upc || 'N/A'}</span>
                    </div>
                    <div class="item-detail">
                        <span class="item-label">SKU:</span>
                        <span class="item-value">${item.sku || 'N/A'}</span>
                    </div>
                    <div class="item-detail">
                        <span class="item-label">Item:</span>
                        <span class="item-value">${item.item || 'N/A'}</span>
                    </div>
                    <div class="item-detail">
                        <span class="item-label">Brand:</span>
                        <span class="item-value">${item.brand || 'N/A'}</span>
                    </div>
                    <div class="item-detail">
                        <span class="item-label">Reference:</span>
                        <span class="item-value">${item.ref || 'N/A'}</span>
                    </div>
                    <div class="item-detail">
                        <span class="item-label">Color:</span>
                        <span class="item-value">${item.color || 'N/A'}</span>
                    </div>
                    <div class="item-detail">
                        <span class="item-label">Size:</span>
                        <span class="item-value">${item.size || 'N/A'}</span>
                    </div>
                    <div class="item-detail">
                        <span class="item-label">Category:</span>
                        <span class="item-value">${item.category || 'N/A'}</span>
                    </div>
            <div class="item-detail">
                <span class="item-label">Weight:</span>
                <span class="item-value">${item.weight || 'N/A'}</span>
            </div>
            <!-- Inventory row removed, only Location will be shown -->
            <div class="item-detail">
                <span class="item-label">Location:</span>
                <span class="item-value">${item.location || 'N/A'}</span>
            </div>
                    <div class="item-detail">
                        <span class="item-label">Status:</span>
                        <span class="item-value">
                            <span class="badge ${item.status == 1 ? 'bg-success' : 'bg-danger'}">
                                ${item.status == 1 ? 'Active' : 'Inactive'}
                            </span>
                        </span>
                    </div>
                </div>
            `;
        }

        // Function to display error
        function displayError(message) {
            const modalBody = document.getElementById('upcModalBody');
            modalBody.innerHTML = `
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h6>Product not found</h6>
                    <p class="mb-0">${message}</p>
                </div>
            `;
        }

        // Allow search with Enter key
        document.getElementById('upcSearch').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchUPC();
            }
        });

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
            });            // Simplified form validation - only check for selected reports
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const selectedCheckboxes = document.querySelectorAll('input[name="selected_reports[]"]:checked');

                if (selectedCheckboxes.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one report to update.');
                    return false;
                }

                // Require that each selected report has a quantity typed (cannot be empty)
                for (const cb of selectedCheckboxes) {
                    const row = cb.closest('tr');
                    const reportId = cb.value;
                    const qtyInput = row.querySelector('.edited-quantity');
                    if (!qtyInput || qtyInput.value.trim() === '') {
                        e.preventDefault();
                        alert('Please type the quantity for each selected report (enter the daily report quantity or a new value).');
                        return false;
                    }
                }

                // Confirm before submitting
                const confirmUpdate = confirm(`Are you sure you want to update ${selectedCheckboxes.length} report(s)?`);
                if (!confirmUpdate) {
                    e.preventDefault();
                    return false;
                }
            });

            // Show alert if edited quantity differs from daily report quantity (one-time per field)
            const qtyInputs = document.querySelectorAll('.edited-quantity');
            qtyInputs.forEach(function(input) {
                let alerted = false;
                input.addEventListener('blur', function() {
                    const placeholder = input.getAttribute('placeholder') || '';
                    const val = input.value.trim();
                    if (!alerted && val !== '' && val !== placeholder) {
                        alerted = true;
                        const message = 'The daily report quantity was ' + placeholder + '.';
                        if (typeof Swal !== 'undefined' && Swal.fire) {
                            Swal.fire({
                                title: 'Quantity differs',
                                text: message,
                                icon: 'info',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            alert(message);
                        }
                    }
                });
            });

            // Delete report button handler
            document.querySelectorAll('.delete-report-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const row = this.closest('tr');
                    if (!id) return;

                    if (typeof Swal !== 'undefined' && Swal.fire) {
                        Swal.fire({
                            title: 'Delete report?',
                            text: 'This will remove the report from daily_report permanently.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Delete',
                            confirmButtonColor: '#d33'
                        }).then((res) => {
                            if (res.isConfirmed) doDelete(id, row);
                        });
                    } else {
                        if (confirm('Delete this report?')) doDelete(id, row);
                    }
                });
            });

            function doDelete(id, row) {
                fetch('delete_report.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'id_report=' + encodeURIComponent(id)
                }).then(r => r.json()).then(data => {
                    if (data && data.status === 'success') {
                        // remove row from DOM
                        if (row) row.remove();
                        if (typeof Swal !== 'undefined' && Swal.fire) {
                            Swal.fire({ title: 'Deleted', icon: 'success', timer: 1200, showConfirmButton: false });
                        }
                    } else {
                        const msg = data && data.message ? data.message : 'Delete failed';
                        if (typeof Swal !== 'undefined' && Swal.fire) {
                            Swal.fire({ title: 'Error', text: msg, icon: 'error' });
                        } else {
                            alert(msg);
                        }
                    }
                }).catch(err => {
                    console.error('Delete error', err);
                    if (typeof Swal !== 'undefined' && Swal.fire) {
                        Swal.fire({ title: 'Error', text: 'Network error', icon: 'error' });
                    } else {
                        alert('Network error');
                    }
                });
            }
        });
    </script>
</body>
</html>
