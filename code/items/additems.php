<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: index.php");
}

$usuario      = $_SESSION['usuario'];
$nombre       = $_SESSION['nombre'];
$tipo_usuario = $_SESSION['tipo_usuario'];
header("Content-Type: text/html;charset=utf-8");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ASWWORKING</title>
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href="../../fontawesome/css/all.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-dark: #4a2568;
            --primary: #632b8b;
            --primary-light: #5d337a;
            --secondary: #997cab;
            --secondary-light: #dac7e5;
            --text-dark: #2d2d2d;
            --text-light: #f8f9fa;
            --bg-light: #f5f3f7;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
        }

        /* Header styles */
        .header-container {
            background-color: var(--secondary-light);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .logo {
            height: 100px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        /* Main container */
        .main-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        /* Title styles */
        .page-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background: linear-gradient(to right, var(--primary), var(--secondary-light));
            border-radius: 3px;
        }        /* Form styles */
        .form-label {
            font-weight: 600;
            color: var(--primary-light);
            margin-bottom: 8px;
        }

        .form-control {
            border: 1px solid var(--secondary);
            border-radius: 6px;
            padding: 10px 15px;
            transition: var(--transition);
        }

        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
        }        /* Select dropdown styles */
        select.form-control {
            background-color: white;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23997cab' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        select.form-control:focus {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%23632b8b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        }

        /* Checkbox styles */
        .checkbox-group {
            padding: 10px;
            border: 1px solid var(--secondary);
            border-radius: 6px;
            background-color: #fafafa;
        }

        .form-check-inline {
            margin-right: 15px;
            margin-bottom: 8px;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            margin-top: 0.125em;
            border: 2px solid var(--secondary);
            border-radius: 4px;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(99, 43, 139, 0.2);
        }

        .form-check-label {
            font-weight: 600;
            color: var(--primary-light);
            margin-left: 8px;
            cursor: pointer;
        }

        /* Button styles */
        .btn-primary {
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
            border: none;
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: var(--transition);
            box-shadow: 0 4px 6px rgba(99, 43, 139, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(99, 43, 139, 0.4);
            background: linear-gradient(to bottom, var(--primary-light), var(--primary));
        }

        .btn-outline-dark {
            border-color: var(--secondary);
            color: var(--primary);
            border-radius: 30px;
            padding: 10px 25px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-outline-dark:hover {
            background-color: var(--secondary-light);
            color: var(--primary);
            border-color: var(--secondary);
        }

        /* Alert messages */
        .mensaje-error {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-weight: 500;
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid var(--danger);
            color: var(--danger);
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-left: 4px solid var(--success);
            color: var(--success);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .main-container {
                padding: 20px;
            }
            
            .logo {
                height: 80px;
            }
        }

        /* Zebra striping for form rows */
        .form-group {
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
            transition: var(--transition);
        }

        .form-group:nth-child(even) {
            background-color: rgba(248, 240, 255, 0.3);
        }

        .form-group:hover {
            background-color: rgba(218, 199, 229, 0.2);
        }        /* Required field indicator */
        .required-field::after {
            content: '*';
            color: #c68615;
            margin-left: 4px;
        }

        /* Input group button styling */
        .input-group-append .btn {
            border-left: none;
            border-color: var(--secondary);
            color: var(--primary);
            background-color: white;
        }

        .input-group-append .btn:hover {
            background-color: var(--secondary-light);
            border-color: var(--primary);
            color: var(--primary);
        }

        .input-group .form-control:focus + .input-group-append .btn {
            border-color: var(--primary);
        }
    </style>
</head>

<body>
    <div class="header-container">
        <div class="container text-center">
            <img src='../../img/logo.png' class="logo" alt="ASWWORKING Logo">
        </div>
    </div>

    <?php
    date_default_timezone_set("America/Bogota");
    include("../../conexion.php");
    require_once("../../zebra.php");
    ?>

    <div class="container main-container">
        <h1 class="page-title"><i class="fa-solid fa-dolly"></i> ADD ITEMS</h1>
        <p class="text-muted mb-4"><i class="fas fa-info-circle"></i> Fields marked with <span class="text-warning">*</span> are required</p>

        <div class="row">
            <div class="col-md-12">
                <form id="form_contacto" action='additems1.php' method="POST">
                    <div class="row">
                        <div class="col">
                            <div id="result-upc_sku_item"></div>
                        </div>
                    </div>
                    <div id="mensaje-upc" class="mensaje-error"></div>
                    
                    <!-- First row of fields -->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-3">
                                <label for="date_item" class="form-label required-field">DATE</label>
                                <input type='date' name='date_item' class='form-control' id="date_item" required autofocus />
                            </div>
                            <div class="col-12 col-sm-3">
                                <label for="upc_item" class="form-label required-field">UPC</label>
                                <input type='text' name='upc_item' id="upc_item" class='form-control' style="text-transform:uppercase;" required />
                            </div>                            <div class="col-12 col-sm-3">
                                <label for="sku_item" class="form-label required-field">SKU</label>
                                <div class="input-group">
                                    <input type='text' name='sku_item' id="sku_item" class='form-control' style="text-transform:uppercase;" required />
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-outline-secondary" id="generate-sku-btn" title="Generate Random SKU">
                                            <i class="fas fa-random"></i>
                                        </button>
                                    </div>
                                </div>
                                <div id="mensaje-sku" class="mensaje-error mt-1" style="display: none;"></div>
                            </div>
                            <div class="col-12 col-sm-3">
                                <label for="brand_item" class="form-label required-field">BRAND</label>
                                <input type='text' name='brand_item' class='form-control' style="text-transform:uppercase;" id="brand_item" required />
                            </div>
                        </div>
                    </div>

                    <!-- Second row of fields -->
                    <div class="form-group">
                        <div class="row">                            <div class="col-12 col-sm-6">
                                <label for="item_item" class="form-label required-field">ITEM</label>
                                <input type='text' name='item_item' class='form-control' style="text-transform:capitalize;" id="item_item" required />
                            </div>
                            <div class="col-12 col-sm-3">
                                <label for="ref_item" class="form-label required-field">REF</label>
                                <input type='text' name='ref_item' class='form-control' style="text-transform:uppercase;" id="ref_item" required />
                            </div>
                            <div class="col-12 col-sm-3">
                                <label for="color_item" class="form-label required-field">COLOR</label>
                                <input type='text' name='color_item' class='form-control' style="text-transform:uppercase;" id="color_item" required />
                            </div>
                        </div>
                    </div>

                    <!-- Third row of fields -->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-3">
                                <label for="size_item" class="form-label required-field">SIZE</label>
                                <input type='text' name='size_item' class='form-control' style="text-transform:uppercase;" id="size_item" required />
                            </div>
                            <div class="col-12 col-sm-3">
                                <label for="category_item" class="form-label required-field">CATEGORY</label>
                                <input type='text' name='category_item' class='form-control' style="text-transform:uppercase;" id="category_item" required />
                            </div>
                            <div class="col-12 col-sm-3">
                                <label for="cost_item" class="form-label required-field">COST</label>
                                <input type='number' name='cost_item' class='form-control' id="cost_item" step="0.01" required />
                            </div>
                            <div class="col-12 col-sm-3">
                                <label for="weight_item" class="form-label required-field">WEIGHT</label>
                                <input type='text' name='weight_item' class='form-control' style="text-transform:uppercase;" id="weight_item" required />
                            </div>
                        </div>
                    </div>                    <!-- Fourth row of fields -->
                    <div class="form-group">
                        <div class="row">
                            <div class="col-12 col-sm-3">
                                <label for="inventory_item" class="form-label required-field">BATCH</label>
                                <input type='text' name='inventory_item' class='form-control' style="text-transform:uppercase;" id="inventory_item" required />
                            </div>
                            <div class="col-12 col-sm-3">
                                <label for="quantity_inventory" class="form-label required-field">QUANTITY</label>
                                <input type='number' name='quantity_inventory' class='form-control' style="text-transform:uppercase;" id="quantity_inventory" required />
                            </div>
                            <div class="col-12 col-sm-6">
                                <label class="form-label required-field">STORES TO PUBLISH</label>
                                <div class="checkbox-group">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="stores[]" value="AS001" id="store_AS001">
                                        <label class="form-check-label" for="store_AS001">AS001</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="stores[]" value="EB001" id="store_EB001">
                                        <label class="form-check-label" for="store_EB001">EB001</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="stores[]" value="EB002" id="store_EB002">
                                        <label class="form-check-label" for="store_EB002">EB002</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="stores[]" value="AM002" id="store_AM002">
                                        <label class="form-check-label" for="store_AM002">AM002</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="stores[]" value="WM001" id="store_WM001">
                                        <label class="form-check-label" for="store_WM001">WM001</label>
                                    </div>
                                </div>
                                <div id="stores-error" class="text-danger mt-1" style="display: none;">
                                    <small>Please select at least one store.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form buttons -->
                    <div class="form-group text-right mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-2"></i> ADD ITEM
                        </button>
                        <button type="reset" class="btn btn-outline-dark ml-2" role='link' onclick="history.back();" type='reset'>
                            <i class="fas fa-arrow-left mr-2"></i> BACK
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>    <script>
        $(document).ready(function() {
            // Función para convertir texto a mayúsculas (excepto ITEM)
            function convertToUppercase() {
                $('input[type="text"]:not(#item_item)').on('input', function() {
                    var cursorPosition = this.selectionStart;
                    var value = $(this).val().toUpperCase();
                    $(this).val(value);
                    this.setSelectionRange(cursorPosition, cursorPosition);
                });
            }

            // Función especial para el campo ITEM (primera letra mayúscula, resto minúscula)
            function handleItemField() {
                $('#item_item').on('input', function() {
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

            // Validación de checkboxes
            function validateStores() {
                var checkedStores = $('input[name="stores[]"]:checked').length;
                if (checkedStores === 0) {
                    $('#stores-error').show();
                    return false;
                } else {
                    $('#stores-error').hide();
                    return true;
                }
            }

            // Validar al enviar el formulario
            $('#form_contacto').on('submit', function(e) {
                if (!validateStores()) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please select at least one store to publish.',
                        icon: 'error',
                        confirmButtonText: 'Ok',
                        confirmButtonColor: '#632b8b'
                    });
                    return false;
                }
            });

            // Validar en tiempo real cuando se cambian los checkboxes
            $('input[name="stores[]"]').on('change', function() {
                validateStores();
            });            // Inicializar las funciones
            convertToUppercase();
            handleItemField();

            // Función para generar SKU único
            function generateUniqueSKU() {
                $.ajax({
                    url: 'generar_sku.php',
                    type: 'POST',
                    data: {
                        action: 'generate_sku'
                    },
                    beforeSend: function() {
                        $('#generate-sku-btn').prop('disabled', true);
                        $('#generate-sku-btn').html('<i class="fas fa-spinner fa-spin"></i>');
                    },
                    success: function(respuesta) {
                        var data = JSON.parse(respuesta);
                        if (data.status === 'success') {
                            $('#sku_item').val(data.sku);
                            $('#mensaje-sku').removeClass('alert alert-danger');
                            $('#mensaje-sku').addClass('alert alert-success');
                            $('#mensaje-sku').text('Unique SKU generated successfully.').css('color', 'green');
                            $('#mensaje-sku').show();
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'Error generating SKU. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'Ok',
                            confirmButtonColor: '#632b8b'
                        });
                    },
                    complete: function() {
                        $('#generate-sku-btn').prop('disabled', false);
                        $('#generate-sku-btn').html('<i class="fas fa-random"></i>');
                    }
                });
            }

            // Función para verificar SKU existente
            function verifySKU(sku) {
                if (sku.trim() !== '') {
                    $.ajax({
                        url: 'verificar_sku.php',
                        type: 'POST',
                        data: {
                            sku_item: sku
                        },
                        success: function(respuesta) {
                            var data = JSON.parse(respuesta);

                            if (data.status === 'existe') {
                                $('#mensaje-sku').removeClass('alert alert-success');
                                $('#mensaje-sku').addClass('alert alert-danger');
                                $('#mensaje-sku').text('This SKU already exists in the database.').css('color', 'red');
                                $('#mensaje-sku').show();
                            } else {
                                $('#mensaje-sku').removeClass('alert alert-danger');
                                $('#mensaje-sku').addClass('alert alert-success');
                                $('#mensaje-sku').text('This SKU is available.').css('color', 'green');
                                $('#mensaje-sku').show();
                            }
                        }
                    });
                } else {
                    $('#mensaje-sku').hide();
                }
            }

            // Event listener para el botón de generar SKU
            $('#generate-sku-btn').on('click', function() {
                generateUniqueSKU();
            });

            // Event listener para verificar SKU cuando se cambia manualmente
            $('#sku_item').on('blur', function() {
                var sku = $(this).val().toUpperCase();
                verifySKU(sku);
            });

            // Generar SKU automáticamente al cargar la página
            generateUniqueSKU();

            $('#upc_item').on('blur', function() {
                var upc = $(this).val().toUpperCase();

                if (upc.trim() !== '') {
                    $.ajax({
                        url: 'verificar_upc.php',
                        type: 'POST',
                        data: {
                            upc_item: upc
                        },
                        success: function(respuesta) {
                            var data = JSON.parse(respuesta);

                            // Si se encontró el UPC
                            if (data.status === 'existe') {
                                var itemsMessage = '';

                                // Si hay múltiples coincidencias, mostramos todas
                                data.items.forEach(function(item) {
                                    itemsMessage += 'Brand: ' + item.brand_item + ', Item: ' + item.item_item + '\n';

                                    // Llenar los campos con los primeros valores encontrados
                                    $('#brand_item').val(item.brand_item);
                                    $('#item_item').val(item.item_item);
                                });

                                // Usamos SweetAlert para mostrar las coincidencias
                                Swal.fire({
                                    title: 'UPC already exists!',
                                    text: itemsMessage,
                                    icon: 'warning',
                                    confirmButtonText: 'Ok',
                                    confirmButtonColor: '#632b8b'
                                });

                                // Cambiar el atributo de mensaje-error de display none a display
                                $('#mensaje-upc').show();
                                $('#mensaje-upc').text('This UPC already exists in the database.').css('color', 'red');
                                $('#mensaje-upc').addClass('alert alert-danger');
                            } else {
                                // Cambiar el color de mensaje-upc a verde
                                $('#mensaje-upc').removeClass('alert alert-danger');
                                $('#mensaje-upc').addClass('alert alert-success');
                                $('#mensaje-upc').text('This UPC is available.').css('color', 'green');
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>