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
    <title>ASWWORKING</title>
</head>

<body>
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
        <link rel="stylesheet" href="../../css/navbar.css">
        <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <style>
            /* Checkbox styles for stores */
            .checkbox-group {
                padding: 15px;
                border: 1px solid #dee2e6;
                border-radius: 8px;
                background-color: #f8f9fa;
                margin-top: 5px;
            }

            .form-check-inline {
                margin-right: 20px;
                margin-bottom: 10px;
            }

            .form-check-input {
                width: 18px;
                height: 18px;
                margin-top: 0.125em;
                border: 2px solid #6c757d;
                border-radius: 4px;
            }

            .form-check-input:checked {
                background-color: #0d6efd;
                border-color: #0d6efd;
            }

            .form-check-input:focus {
                border-color: #0d6efd;
                box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
            }

            .form-check-label {
                font-weight: 600;
                color: #495057;
                margin-left: 8px;
                cursor: pointer;
            }

            /* Required field indicator */
            .required-field::after {
                content: '*';
                color: #dc3545;
                margin-left: 4px;
            }

            /* Readonly fields styling */
            .form-control[readonly] {
                background-color: #f8f9fa;
                border-color: #dee2e6;
                color: #6c757d;
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
                <a href="seeReport.php" class="btn-add-store">
                    <i class="fas fa-file-alt"></i> See Daily Report
                </a>
            </div>
        </div> <?php
                date_default_timezone_set("America/Bogota");
                include("../../conexion.php");
                require_once("../../zebra.php");
                ?>

        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>ASWWORKING</title>
        </head>

        <body>
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-8">
                        <form action="processReport.php" method="POST" class="form-container">
                            <h5 class="form-title">New Report</h5>
                            <div class="row g-3">
                                <div class="col-md-6 form-group">
                                    <label for="upc_asignado_report" class="form-label"> Assigned UPC</label>
                                    <input type="text" class="form-control form-control-sm" id="upc_asignado_report" name="upc_asignado_report">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="upc_final_report" class="form-label"> Final UPC</label>
                                    <input type="text" class="form-control form-control-sm" id="upc_final_report" name="upc_final_report" onblur="buscarUPC()">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="cons_report" class="form-label">Cons</label>
                                    <input type="text" class="form-control form-control-sm" id="cons_report" name="cons_report" required readonly>
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="folder_report" class="form-label">Folder</label>
                                    <input type="text" class="form-control form-control-sm" id="folder_report" name="folder_report" required onblur="generarCons()">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="loc_report" class="form-label">Location</label>
                                    <input type="text" class="form-control form-control-sm" id="loc_report" name="loc_report">
                                </div>

                                <div class="col-md-6 form-group mt-4">
                                    <label for="quantity_report" class="form-label">Quantity</label>
                                    <input type="number" class="form-control form-control-sm" id="quantity_report" name="quantity_report" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="sku_report" class="form-label">SKU (Auto-generated)</label>
                                    <input type="text" class="form-control form-control-sm" id="sku_report" name="sku_report" readonly placeholder="Will be generated automatically">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="brand_report" class="form-label">Brand</label>
                                    <input type="text" class="form-control form-control-sm" id="brand_report" name="brand_report">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="item_report" class="form-label">Item</label>
                                    <input type="text" class="form-control form-control-sm" id="item_report" name="item_report">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="vendor_report" class="form-label">Style </label>
                                    <input type="text" class="form-control form-control-sm" id="vendor_report" name="vendor_report">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="color_report" class="form-label">Color</label>
                                    <input type="text" class="form-control form-control-sm" id="color_report" name="color_report">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="size_report" class="form-label">Size</label>
                                    <input type="text" class="form-control form-control-sm" id="size_report" name="size_report">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="category_report" class="form-label">Category</label>
                                    <input type="text" class="form-control form-control-sm" id="category_report" name="category_report">
                                </div>

                                <div class="col-md-6 form-group">
                                    <label for="weight_report" class="form-label">Weight</label>
                                    <input type="text" step="0.01" class="form-control form-control-sm" id="weight_report" name="weight_report">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="inventory_report" class="form-label">Batch</label>
                                    <input type="text" class="form-control form-control-sm" id="inventory_report" name="inventory_report">
                                </div>

                                <div class="col-12 form-group">
                                    <label class="form-label required-field">STORES TO PUBLISH</label>
                                    <div class="checkbox-group">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="stores[]" value="AS001" id="store_AS001_report">
                                            <label class="form-check-label" for="store_AS001_report">AS001</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="stores[]" value="EB001" id="store_EB001_report">
                                            <label class="form-check-label" for="store_EB001_report">EB001</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="stores[]" value="EB002" id="store_EB002_report">
                                            <label class="form-check-label" for="store_EB002_report">EB002</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="stores[]" value="AM002" id="store_AM002_report">
                                            <label class="form-check-label" for="store_AM002_report">AM002</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="stores[]" value="WM001" id="store_WM001_report">
                                            <label class="form-check-label" for="store_WM001_report">WM001</label>
                                        </div>
                                    </div>
                                    <div id="stores-error-report" class="text-danger mt-1" style="display: none;">
                                        <small>Please select at least one store.</small>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="observacion_report" class="form-label">Observation</label>
                                    <textarea class="form-control form-control-sm" id="observacion_report" name="observacion_report" rows="2"></textarea>
                                </div>
                            </div>
                            <div class=" mt-4 d-flex justify-content-center " style="margin-left: 90px;">
                                <button type="submit" class="btn-add-store">Send</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- modal opciones -->
            <div class="modal fade" id="modalOpcionesUPC" tabindex="-1" aria-labelledby="modalOpcionesUPCLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Select the correct reference</h5>
                        </div>
                        <div class="modal-body" id="contenedorOpcionesUPC">
                            <!-- Aquí se insertan las opciones -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="back">
                <div></div>
                <div class="">
                    <a href="../../access.php" class="back-btn" title="Go Back">
                        <i class="fas fa-arrow-circle-left fa-xl"></i>
                    </a>
                </div>
            </div>

            <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
            <script>
                $(document).ready(function() {
                    // Función para convertir texto a mayúsculas para campos específicos (excepto item)
                    function convertToUppercase() {
                        $('#folder_report, #loc_report, #brand_report, #vendor_report, #color_report, #size_report, #category_report, #weight_report, #inventory_report').on('input', function() {
                            var cursorPosition = this.selectionStart;
                            var value = $(this).val().toUpperCase();
                            $(this).val(value);
                            this.setSelectionRange(cursorPosition, cursorPosition);
                        });
                    }

                    // Función especial para el campo ITEM (primera letra mayúscula, resto minúscula)
                    function handleItemField() {
                        $('#item_report').on('input', function() {
                            var cursorPosition = this.selectionStart;
                            var value = $(this).val();
                            
                            // Convertir a formato correcto: primera letra mayúscula, resto minúscula
                            if (value.length > 0) {
                                value = value.charAt(0).toUpperCase() + value.slice(1).toLowerCase();
                            }
                            
                            $(this).val(value);
                            this.setSelectionRange(cursorPosition, cursorPosition);
                        });
                    }

                    // Inicializar las funciones
                    convertToUppercase();
                    handleItemField();
                });

                function buscarUPC() {
                    let upc = $('#upc_final_report').val();

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
                                var items = data.data || [];
                                if (!items || items.length === 0) {
                                    limpiarCampos();
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({ icon: 'info', title: 'No items', text: 'No matching items found.' });
                                    } else {
                                        alert('No matching items found.');
                                    }
                                    return;
                                }

                                // Build table HTML
                                var tableHtml = '<div style="overflow:auto;max-width:100%;"><table class="table table-bordered"><thead><tr><th>Select</th><th>Brand</th><th>Item</th><th>SKU</th><th>REF</th><th>Batch</th><th>Quantity</th></tr></thead><tbody>';
                                items.forEach(function(item, idx) {
                                    var qty = item.quantity_inventory || 0;
                                    var costDisplay = (typeof item.cost_item !== 'undefined' && item.cost_item !== null && item.cost_item !== '') ? '$' + parseFloat(item.cost_item).toFixed(2) : '';
                                    var refDisplay = item.ref_item || '';
                                    var batchDisplay = item.batch_item || '';
                                    tableHtml += '<tr>' +
                                        '<td><input type="radio" name="selected_item" value="' + idx + '" ' + (idx === 0 ? 'checked' : '') + '></td>' +
                                        '<td>' + (item.brand_item || '') + '</td>' +
                                        '<td>' + (item.item_item || '') + '</td>' +
                                        '<td>' + (item.sku_item || '') + '</td>' +
                                        '<td>' + refDisplay + '</td>' +
                                        '<td>' + batchDisplay + '</td>' +
                                        '<td>' + qty + '</td>' +
                                        '</tr>';
                                });
                                tableHtml += '</tbody></table></div>';

                                var addQtyHtml = '<div class="form-group text-left">' +
                                    '<label for="add-qty-input">Add Quantity (will redirect to edit location):</label>' +
                                    '<input type="number" min="1" id="add-qty-input" class="form-control" style="width:120px;display:inline-block;" />' +
                                    '</div>';

                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        title: 'UPC references found',
                                        html: '<div style="text-align:left">' + tableHtml + addQtyHtml + '</div>',
                                        icon: 'info',
                                        width: '90%',
                                        showDenyButton: true,
                                        showCancelButton: true,
                                        confirmButtonText: 'Add Quantity & Edit Location',
                                        denyButtonText: 'Add New Batch (Same UPC/SKU)',
                                        cancelButtonText: 'Cancel',
                                        confirmButtonColor: '#632b8b',
                                        denyButtonColor: '#28a745',
                                        preConfirm: () => {
                                            const addQty = parseInt(document.getElementById('add-qty-input').value);
                                            if (isNaN(addQty) || addQty <= 0) {
                                                Swal.showValidationMessage('Please enter a valid quantity to add.');
                                                return false;
                                            }
                                            const selectedRadio = document.querySelector('input[name="selected_item"]:checked');
                                            if (!selectedRadio) {
                                                Swal.showValidationMessage('Please select an item to update.');
                                                return false;
                                            }
                                            return {
                                                addQty: addQty,
                                                selectedIdx: parseInt(selectedRadio.value)
                                            };
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            var addQty = result.value.addQty;
                                            var selectedIdx = result.value.selectedIdx;
                                            var selectedItem = items[selectedIdx];
                                            var currentQty = parseInt(selectedItem.quantity_inventory) || 0;
                                            var newQty = currentQty + addQty;

                                            // Fill the report form fields with selected item
                                            $('#brand_report').val(selectedItem.brand_item || '');
                                            $('#item_report').val(selectedItem.item_item || '');
                                            $('#sku_report').val(selectedItem.sku_item || '');
                                            if (selectedItem.ref_item) { $('#vendor_report').val(selectedItem.ref_item); } else { $('#vendor_report').val(''); }
                                            if (selectedItem.inventory_item) { $('#inventory_report').val(selectedItem.inventory_item); } else { $('#inventory_report').val(''); }
                                            $('#quantity_report').val(newQty);
                                            if (selectedItem.color_item) { $('#color_report').val(selectedItem.color_item); }
                                            if (selectedItem.size_item) { $('#size_report').val(selectedItem.size_item); }
                                            if (selectedItem.category_item) { $('#category_report').val(selectedItem.category_item); } else { $('#category_report').val(''); }
                                            if (selectedItem.weight_item) { $('#weight_report').val(selectedItem.weight_item); } else { $('#weight_report').val(''); }

                                            // Update inventory via AJAX (use the new system for location editing)
                                            $.ajax({
                                                url: '../items/create_report_simple.php',
                                                type: 'POST',
                                                dataType: 'json',
                                                data: {
                                                    upc_item: $('#upc_final_report').val().toUpperCase(),
                                                    sku_item: (selectedItem.sku_item || '').toUpperCase(),
                                                    brand_item: selectedItem.brand_item,
                                                    item_item: selectedItem.item_item,
                                                    ref_item: selectedItem.ref_item || '',
                                                    color_item: selectedItem.color_item || '',
                                                    size_item: selectedItem.size_item || '',
                                                    category_item: selectedItem.category_item || '',
                                                    weight_item: selectedItem.weight_item || '',
                                                    cost_item: selectedItem.cost_item || '',
                                                    batch_item: selectedItem.inventory_item || '',
                                                    current_quantity: currentQty,
                                                    new_quantity: newQty,
                                                    added_quantity: addQty
                                                },
                                                success: function(resp) {
                                                    console.log('Create report response:', resp);
                                                    if (resp.status === 'success') {
                                                        Swal.fire({
                                                            title: 'Success!',
                                                            text: 'Item quantity updated. You will now be redirected to edit the location.',
                                                            icon: 'success',
                                                            confirmButtonColor: '#632b8b',
                                                            confirmButtonText: 'Go to Edit Location'
                                                        }).then((result) => {
                                                            if (result.isConfirmed) {
                                                                // Redirect to editLocationFolder.php
                                                                window.location.href = 'editLocationFolder.php';
                                                            }
                                                        });
                                                    } else {
                                                        Swal.fire({
                                                            title: 'Error',
                                                            text: resp.message || 'Failed to create report entry.',
                                                            icon: 'error',
                                                            confirmButtonColor: '#632b8b'
                                                        });
                                                    }
                                                },
                                                error: function(xhr, status, error) {
                                                    console.log('AJAX Error:', xhr.responseText);
                                                    Swal.fire({
                                                        title: 'Error',
                                                        text: 'Could not connect to server: ' + error,
                                                        icon: 'error',
                                                        confirmButtonColor: '#632b8b'
                                                    });
                                                }
                                            });
                                        } else if (result.isDenied) {
                                            // User clicked "Add New Batch" button
                                            const selectedRadio = document.querySelector('input[name="selected_item"]:checked');
                                            if (selectedRadio) {
                                                var selectedIdx = parseInt(selectedRadio.value);
                                                var selectedItem = items[selectedIdx];
                                                
                                                // Pre-fill form with selected item data but keep the UPC and SKU
                                                $('#brand_report').val(selectedItem.brand_item || '');
                                                $('#item_report').val(selectedItem.item_item || '');
                                                $('#sku_report').val(selectedItem.sku_item || '');
                                                $('#vendor_report').val(selectedItem.ref_item || '');
                                                $('#color_report').val(selectedItem.color_item || '');
                                                $('#size_report').val(selectedItem.size_item || '');
                                                $('#category_report').val(selectedItem.category_item || '');
                                                $('#weight_report').val(selectedItem.weight_item || '');
                                                
                                                // Clear inventory (batch) and quantity to force user to enter NEW values
                                                $('#inventory_report').val('');
                                                $('#quantity_report').val('');
                                                
                                                // Show a message explaining what to do next
                                                Swal.fire({
                                                    title: 'Add New Batch',
                                                    html: '<div style="text-align:left;">' +
                                                        '<p><strong>Instructions:</strong></p>' +
                                                        '<ul>' +
                                                        '<li>The form has been pre-filled with the item information</li>' +
                                                        '<li>Please enter a <strong>NEW BATCH/LOCATION</strong> number (different from existing batches)</li>' +
                                                        '<li>Enter the <strong>QUANTITY</strong> for this new batch</li>' +
                                                        '<li>Add an <strong>OBSERVATION</strong> if needed</li>' +
                                                        '<li>Select the stores to publish</li>' +
                                                        '<li>Click "SAVE" to create the new batch record</li>' +
                                                        '</ul>' +
                                                        '<p style="color: #632b8b; font-weight: bold;">Note: This will create a new separate record with the same UPC and SKU but different batch.</p>' +
                                                        '</div>',
                                                    icon: 'info',
                                                    confirmButtonText: 'Got it!',
                                                    confirmButtonColor: '#632b8b',
                                                    width: '600px'
                                                }).then(() => {
                                                    // Focus on the inventory field
                                                    $('#inventory_report').focus();
                                                });
                                            }
                                        } else if (result.isDismissed) {
                                            // User clicked Cancel or closed the modal
                                            // Get the selected item radio button
                                            const selectedRadio = document.querySelector('input[name="selected_item"]:checked');
                                            if (selectedRadio) {
                                                var selectedIdx = parseInt(selectedRadio.value);
                                                var selectedItem = items[selectedIdx];
                                                
                                                // Preload the values from the selected item
                                                $('#brand_report').val(selectedItem.brand_item || '');
                                                $('#item_report').val(selectedItem.item_item || '');
                                                $('#vendor_report').val(selectedItem.ref_item || '');
                                                $('#color_report').val(selectedItem.color_item || '');
                                                $('#size_report').val(selectedItem.size_item || '');
                                                $('#category_report').val(selectedItem.category_item || '');
                                                $('#weight_report').val(selectedItem.weight_item || '');
                                                $('#inventory_report').val(selectedItem.batch_item || selectedItem.inventory_item || '');
                                            }
                                        }
                                    });
                                } else {
                                    // Fallback: original modal list
                                    mostrarOpcionesUPC(items);
                                }

                            } else {
                                limpiarCampos();
                                console.log("❌ UPC not found.");
                                // If no UPC references found, still check for folder prefill
                                validarUPCFinal();
                            }
                        },
                        error: function() {
                            alert("⚠️ Error .");
                            // On error, still try to validate for folder prefill
                            validarUPCFinal();
                        }
                    });
                }

                // Simple validation for Final UPC (optional - just for folder prefill)
                function validarUPCFinal() {
                    let upcFinal = $('#upc_final_report').val().trim();

                    if (upcFinal === "") return;

                    console.log('validarUPCFinal called, upcFinal=', upcFinal);

                    $.ajax({
                        url: 'validar_upc_final.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            upc_final: upcFinal
                        },
                        success: function(data) {
                            if (data.exists) {
                                // Just prefill folder if available, no popup warning
                                if (data.folder_item && data.folder_item.trim() !== '') {
                                    $('#folder_report').val(data.folder_item);
                                }
                            }
                        },
                        error: function() {
                            console.log("Error validating Final UPC", arguments);
                        }
                    });
                }

                // Generar CONS basado en FOLDER
                function generarCons() {
                    let folder = $('#folder_report').val().trim();

                    if (folder === "") {
                        $('#cons_report').val('');
                        return;
                    }

                    $.ajax({
                        url: 'generar_cons.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            folder: folder
                        },
                        success: function(data) {
                            if (data.success) {
                                $('#cons_report').val(folder + ' cons ' + data.next_cons);
                            }
                        },
                        error: function() {
                            console.log("Error generating CONS");
                        }
                    });
                }

                function llenarFormulario(item) {
                    $('#quantity_report').val(item.quantity_inventory);
                    $('#brand_report').val(item.brand_item);
                    $('#item_report').val(item.item);
                    $('#color_report').val(item.color_item);
                    $('#size_report').val(item.size_item);
                    $('#category_report').val(item.category_item);
                    $('#weight_report').val(item.weight_item);
                    $('#inventory_report').val(item.inventory_item);
                    $('#sku_report').val(item.sku);
                    $('#vendor_report').val(item.ref_item);
                }

                function mostrarOpcionesUPC(opciones) {
                    let contenedor = $('#contenedorOpcionesUPC');
                    contenedor.empty();

                    opciones.forEach((item, index) => {
                        contenedor.append(`
        <div class="card mb-2 shadow-sm border-0">
            <div class="card-body p-2">
                <button 
                    class="btn btn-light text-start w-100 border rounded d-flex flex-column align-items-start" 
                    onclick='seleccionarUPC(${JSON.stringify(item)})'
                >
                    <strong class="text-primary">${item.item}</strong>
                    <small class="text-muted">
                        Color: ${item.color_item} | Size ${item.size_item} | Ref: ${item.ref_item}
                    </small>
                </button>
            </div>
        </div>
    `);
                    });

                    $('#modalOpcionesUPC').modal('show');
                }

                function seleccionarUPC(item) {
                    $('#modalOpcionesUPC').modal('hide');
                    llenarFormulario(item);
                    alert("✅ UPC selected correctly.");
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

                // Validación de checkboxes de tiendas
                function validateStores() {
                    var checkedStores = $('input[name="stores[]"]:checked').length;
                    if (checkedStores === 0) {
                        $('#stores-error-report').show();
                        return false;
                    } else {
                        $('#stores-error-report').hide();
                        return true;
                    }
                }

                // Validar al enviar el formulario
                $('form').on('submit', function(e) {
                    if (!validateStores()) {
                        e.preventDefault();
                        alert('Please select at least one store to publish.');
                        $('html, body').animate({
                            scrollTop: $('#stores-error-report').offset().top - 100
                        }, 500);
                        return false;
                    }
                });

                // Validar en tiempo real cuando se cambian los checkboxes
                $('input[name="stores[]"]').on('change', function() {
                    validateStores();
                });

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

                    // Ensure buscarUPC runs reliably on Final UPC field
                    if (window.jQuery) {
                        $(document).on('blur', '#upc_final_report', function() {
                            try {
                                buscarUPC();
                            } catch (e) {
                                console.error('Error calling buscarUPC()', e);
                            }
                        });
                    }
                });
            </script>