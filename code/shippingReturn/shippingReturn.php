<?php
session_start();
include "../../conexion.php";
if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
}
$nombre = $_SESSION['nombre'];
$tipo_usu = $_SESSION['tipo_usuario'];

// Obtener los filtros desde el formulario
$store_name = isset($_GET['store_name']) ? trim($_GET['store_name']) : '';
$code_sucursal = isset($_GET['code_sucursal']) ? trim($_GET['code_sucursal']) : '';

$queryTiendas = "SELECT id_store, store_name FROM store ORDER BY store_name ASC";
$resultTiendas = $mysqli->query($queryTiendas);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ASWWORKING | SOFT</title>
    <link rel="stylesheet" type="text/css" href="../items/css/styles.css">
    <link rel="stylesheet" type="text/css" href="../items/css/estilos2024.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Librerías de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <!-- Incluir SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</head>

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
        --danger: #dc3545;
    }

    body {
        background-color: var(--bg-light);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Header styles */
    .header-container {
        text-align: center;
        padding: 20px 0;
        background-color: var(--secondary-light);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
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

    .page-title {
        color: var(--primary);
        font-weight: 700;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        margin: 20px 0;
        position: relative;
        padding-bottom: 10px;
    }

    .page-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 3px;
        background: linear-gradient(to right, var(--primary), var(--secondary-light));
        border-radius: 3px;
    }

    /* Search form */
    .search-form {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin: 20px auto;
        max-width: 800px;
    }

    .search-form input {
        border: 1px solid var(--secondary);
        border-radius: 6px;
        padding: 10px 15px;
        margin: 5px;
        transition: all 0.3s ease;
    }

    .search-form input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
        outline: none;
    }

    .search-form input[type="submit"] {
        background: linear-gradient(to bottom, var(--primary), var(--primary-light));
        color: white;
        border: none;
        cursor: pointer;
        font-weight: 600;
    }

    .search-form input[type="submit"]:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Button styles */
    .btn-action {
        border-radius: 30px;
        padding: 8px 20px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success {
        background: #4a2568;
        color: white;
        border: none;
    }

    .btn-success:hover {
        background: #4a2568;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Table styles */
    .table-container {
        background: white;
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        padding: 20px;
        margin: 20px auto;
    }

    #salesTable {
        width: 70%;
    }

    #salesTable thead {
        background: var(--primary);
        color: white;
    }

    #salesTable th {
        padding: 12px 10px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
    }

    #salesTable tbody tr {
        transition: all 0.3s ease;
    }

    #salesTable tbody tr:nth-child(even) {
        background-color: rgba(248, 240, 255, 0.8);
    }

    #salesTable tbody tr:hover {
        background-color: rgba(218, 199, 229, 0.4);
    }

    /* Action buttons in table - ESTILOS ACTUALIZADOS */
    .btn-action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        transition: all 0.3s ease;
        border: none;
        background: transparent;
        cursor: pointer;
        font-size: 16px;
        margin: 0 3px;
    }

    .btn-edit {
        color: var(--primary);
    }

    .btn-edit:hover {
        background-color: rgba(99, 43, 139, 0.1);
        transform: scale(1.1);
    }

    .btn-delete {
        color: var(--danger);
    }

    .btn-delete:hover {
        background-color: rgba(220, 53, 69, 0.1);
        transform: scale(1.1);
    }

    /* Modal styles */
    .modal-header {
        background-color: var(--secondary-light);
        border-bottom: 1px solid var(--secondary);
    }

    .modal-title {
        color: var(--primary);
        font-weight: 600;
    }

    /* Back button */
    .back-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: transparent;
        border: none;
        color: var(--primary-light);
        font-size: 1.8rem;
        cursor: pointer;
        padding: 15px;
        border-radius: 50%;
        transition: all 0.3s ease;
        margin: 20px auto;
        text-align: center;
    }

    .back-btn:hover {
        background-color: rgba(93, 51, 122, 0.1);
        color: var(--primary);
        transform: translateX(-3px);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .logo {
            height: 80px;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .search-form {
            padding: 15px;
        }

        .search-form input {
            width: 100%;
            margin: 5px 0;
        }

        #bulkReturnBtn {
            position: relative;
            margin: 10px auto !important;
            display: block;
            width: 100%;
            max-width: 200px;
        }
    }    td {
        text-align: center;
    }

    th {
        text-align: center;
    }

    .clickable-row {
        cursor: pointer;
    }

    /* Estilos para el mensaje inicial */
    .alert-info {
        background: linear-gradient(135deg, #e3f2fd, #bbdefb);
        border: 1px solid #2196f3;
        color: #1976d2;
        border-radius: 10px;
    }

    .alert-info i {
        color: #1976d2;
    }

    /* Estilos para el spinner de carga */
    .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    /* Estilos mejorados para los resultados */
    #searchResults .table {
        margin-bottom: 0;
    }

    #searchResults .alert {
        border-radius: 8px;
        margin-bottom: 0;
    }

    /* Estilo personalizado para thead con colores armoniosos */
    .table-custom-header {
        background: linear-gradient(to bottom, var(--primary), var(--primary-light)) !important;
        color: white !important;
    }

    .table-custom-header th {
        background: linear-gradient(to bottom, var(--primary), var(--primary-light)) !important;
        border-color: var(--primary-dark) !important;
        padding: 12px 10px !important;
        font-weight: 600 !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        color: white !important;
        border: 1px solid var(--primary-dark) !important;
        vertical-align: middle !important;
    }

    /* Asegurar que las tablas dinámicas también tengan el estilo */
    .table .table-custom-header th {
        background: linear-gradient(to bottom, var(--primary), var(--primary-light)) !important;
        color: white !important;
        border-color: var(--primary-dark) !important;
    }

    /* Override específico para Bootstrap table classes */
    .table-bordered .table-custom-header th {
        background: linear-gradient(to bottom, var(--primary), var(--primary-light)) !important;
        background-color: var(--primary) !important;
        color: white !important;
        border: 1px solid var(--primary-dark) !important;
    }

    /* Para tablas con hover */
    .table-hover .table-custom-header th {
        background: linear-gradient(to bottom, var(--primary), var(--primary-light)) !important;
        color: white !important;
    }

    /* Para tablas pequeñas */
    .table-sm .table-custom-header th {
        background: linear-gradient(to bottom, var(--primary), var(--primary-light)) !important;
        color: white !important;
        padding: 8px 10px !important;
    }

    /* Estilo más específico para forzar el color completo */
    thead.table-custom-header,
    thead.table-custom-header tr,
    thead.table-custom-header tr th,
    tfoot.table-custom-header,
    tfoot.table-custom-header tr,
    tfoot.table-custom-header tr th {
        background: linear-gradient(to bottom, var(--primary), var(--primary-light)) !important;
        background-color: var(--primary) !important;
        color: white !important;
        border-color: var(--primary-dark) !important;
    }

    /* Remover cualquier hover effect de Bootstrap en headers */
    .table-custom-header th:hover {
        background: linear-gradient(to bottom, var(--primary), var(--primary-light)) !important;
        color: white !important;
    }
</style>

<body>
    <div class="header-container">
        <img src='../../img/logo.png' class="logo" alt="ASWWORKING Logo">
        <h1 class="page-title"><i class="fa-solid fa-file-signature"></i> SHIPPING RETURNS</h1>
    </div>
    <div class="search-form">
        <form id="filterForm" class="row g-3 align-items-center justify-content-center">
            <div class="col-md-4">
                <input name="sell_order" type="text" placeholder="Enter Sell Order to search" id="sell_order" class="form-control" required>
            </div>
            <div class="col-md-2">
                <input value="Search" type="submit" class="btn btn-primary">
            </div>
        </form>
    </div>    <!-- Mensaje inicial -->
    <div id="initialMessage" class="table-container text-center">
        <div class="alert alert-info">
            <i class="fas fa-search fa-2x mb-3"></i>
            <h4>Search for Shipping Returns</h4>
            <p>Enter a Sell Order above to search for shipping return records</p>
        </div>
    </div>

    <!-- Tabla de Shipping Returns (inicialmente oculta) -->
    <div class="table-container" id="resultsContainer" style="display: none;">
        <h2 class="text-center mb-4">Search Results</h2>
        <div id="searchResults">
            <!-- Los resultados se cargarán aquí -->
        </div>
    </div>

    <br /><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="back" /></a><br>    <div class="modal fade" id="shippingReturnModal" tabindex="-1" aria-labelledby="shippingReturnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="shippingReturnModalLabel">Shipping Return Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div id="shippingReturnTableContainer" class="px-4 pb-4"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>


    <script src="shipping.js"></script>
    <script>
        // Función para inicializar los event listeners de las filas clickeables
        function initializeClickableRows() {
            document.querySelectorAll(".clickable-row").forEach(function (row) {
                // Remover event listeners previos para evitar duplicados
                const newRow = row.cloneNode(true);
                row.parentNode.replaceChild(newRow, row);
            });
            
            // Reinicializar los event listeners
            document.querySelectorAll(".clickable-row").forEach(function (row) {
                row.addEventListener("click", function () {
                    const sell_order = this.dataset.sell_order;
                    console.log("Selected Sell Order:", sell_order);
                    
                    // Fetch both items and summary data
                    Promise.all([
                        fetch(`getShippingReturnDetails.php?sell_order=${encodeURIComponent(sell_order)}`).then(r => r.json()),
                        fetch(`../sells/getSellSummary.php?sell_order=${encodeURIComponent(sell_order)}`).then(r => r.json())
                    ])
                        .then(([itemsData, summaryData]) => {
                            console.log('Items data:', itemsData);
                            console.log('Summary data:', summaryData);
                            
                            if (itemsData.error) {
                                document.getElementById("shippingReturnTableContainer").innerHTML = `<p>Error: ${itemsData.error}</p>`;
                                return;
                            }
                            
                            const items = itemsData.items;
                            const shippingReturn = itemsData.shipping_return;
                            const summary = summaryData.summary || null;

                            // Crear la tabla de shipping return
                            let tableHTML = `
                                <h4>Sell Order: ${items[0].sell_order}</h4>
                                <table class="table table-bordered">
                                    <thead class="table-custom-header">
                                        <tr>
                                            <th>UPC</th>
                                            <th>SKU</th>
                                            <th>Quantity</th>
                                            <th>Item Profit</th>
                                            <th>Total Item</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            `;

                            let totalGeneral = 0;
                            items.forEach((item) => {
                                const quantity = item.quantity || 0;
                                const item_profit = parseFloat(item.item_profit) || 0;
                                const total_item = parseFloat(item.total_item) || 0;
                                
                                tableHTML += `
                                    <tr>
                                        <td>${item.upc_item}</td>
                                        <td>${item.sku_item}</td>
                                        <td>${quantity}</td>
                                        <td>$${item_profit.toFixed(2)}</td>
                                        <td>$${total_item.toFixed(2)}</td>
                                    </tr>
                                `;
                                totalGeneral += total_item;
                            });

                            tableHTML += `
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Total General</strong></td>
                                        <td><strong>$${totalGeneral.toFixed(2)}</strong></td>
                                    </tr>
                            `;
                            
                            // Add order-level fees if summary data exists
                            if (summary) {
                                const finalFee = parseFloat(summary.final_fee) || 0;
                                const fixedCharge = parseFloat(summary.fixed_charge) || 0;
                                const finalTotal = parseFloat(summary.final_total) || (totalGeneral - finalFee - fixedCharge);
                                
                                tableHTML += `
                                    <tr style="background-color: #f8f9fa;">
                                        <td colspan="4" class="text-end"><strong>Final Fee (Order Level)</strong></td>
                                        <td><strong>-$${finalFee.toFixed(2)}</strong></td>
                                    </tr>
                                    <tr style="background-color: #f8f9fa;">
                                        <td colspan="4" class="text-end"><strong>Fixed Charge (Order Level)</strong></td>
                                        <td><strong>-$${fixedCharge.toFixed(2)}</strong></td>
                                    </tr>
                                    <tr style="background-color: #e9ecef; font-weight: bold;">
                                        <td colspan="4" class="text-end"><strong>FINAL TOTAL</strong></td>
                                        <td><strong style="color: #28a745;">$${finalTotal.toFixed(2)}</strong></td>
                                    </tr>
                                `;
                            }

                            tableHTML += `
                                    </tbody>
                                    <tfoot class="table-custom-header">
                                        <tr>
                                            <th colspan="6">Total General:</th>
                                            <th>$${totalGeneral.toFixed(2)}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            `;

                            // Agregar formulario de shipping return
                            tableHTML += `
                                <hr>
                                <h5>Shipping Return Information</h5>
                                <form id='shippingReturnForm' class='row g-3'>
                                    <input type='hidden' name='sell_order' value='${items[0].sell_order}' />
                                      <div class='col-md-6'>
                                        <label for='billing_return' class='form-label'>Billing for Return Postage</label>
                                        <input type='number' step='0.01' class='form-control' name='billing_return' id='billing_return' value='${shippingReturn ? shippingReturn.billing_return || '' : ''}' />
                                    </div>

                                    <div class='col-md-6'>
                                        <label for='shipping_return_date' class='form-label'>Shipping Return Date</label>
                                        <input type='date' class='form-control' name='shipping_return_date' id='shipping_return_date' value='${shippingReturn ? shippingReturn.shipping_return_date || '' : ''}' />
                                    </div>
                                    
                                    <div class='col-12'>
                                        <div class='text-end'>
                                            <button type='submit' class='btn' style='background: linear-gradient(to bottom, var(--primary), var(--primary-light)); color: #fff; border: none; font-weight: 600; padding: 8px 20px; border-radius: 6px;'>Save</button>
                                        </div>
                                    </div>
                                </form>
                            `;

                            document.getElementById("shippingReturnTableContainer").innerHTML = tableHTML;
                            
                            // Add form submission handler
                            const form = document.getElementById('shippingReturnForm');
                            if (form) {
                                form.addEventListener('submit', function(e) {
                                    e.preventDefault();
                                    
                                    const formData = new FormData(form);
                                    
                                    fetch('saveShippingReturn.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Success!',
                                                text: data.message
                                            }).then(() => {
                                                const modal = bootstrap.Modal.getInstance(document.getElementById('shippingReturnModal'));
                                                modal.hide();
                                                // Recargar los resultados de búsqueda
                                                document.getElementById('filterForm').dispatchEvent(new Event('submit'));
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: data.message
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'An error occurred while saving the shipping return information.'
                                        });
                                    });
                                });
                            }
                            
                            // Mostrar el modal
                            const modal = new bootstrap.Modal(document.getElementById('shippingReturnModal'));
                            modal.show();
                        })
                        .catch((error) => {
                            console.error("Error:", error);
                            Swal.fire('Error', 'An error occurred while loading shipping return details.', 'error');
                        });
                });
            });
        }

        // Manejar el formulario de búsqueda
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const sellOrder = document.getElementById('sell_order').value.trim();
            
            if (!sellOrder) {
                alert('Please enter a Sell Order to search');
                return;
            }
            
            // Mostrar loading
            document.getElementById('initialMessage').style.display = 'none';
            document.getElementById('resultsContainer').style.display = 'block';
            document.getElementById('searchResults').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Searching...</span>
                    </div>
                    <p class="mt-2">Searching for shipping return records...</p>
                </div>
            `;
            
            // Realizar búsqueda AJAX
            const formData = new FormData();
            formData.append('sell_order', sellOrder);
            
            fetch('searchShippingReturn.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('searchResults').innerHTML = data;
                
                // Reinicializar los event listeners para las filas clickeables
                initializeClickableRows();
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('searchResults').innerHTML = `
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h5>Error</h5>
                        <p>An error occurred while searching. Please try again.</p>
                    </div>
                `;
            });
        });
    </script>
</body>

</html>