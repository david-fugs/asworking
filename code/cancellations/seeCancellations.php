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
  <title>ASWWORKING | SOFT</title>  <link rel="stylesheet" type="text/css" href="../items/css/styles.css">
  <link rel="stylesheet" type="text/css" href="../items/css/estilos2024.css">
  <!-- <link rel="stylesheet" href="styleSell.css"> --> <!-- Archivo no encontrado, comentado -->
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
    width: 100%;
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
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: none;
    margin: 0 2px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
  }

  .btn-edit {
    background-color: #17a2b8;
    color: white;
  }

  .btn-edit:hover {
    background-color: #138496;
    transform: scale(1.1);
    color: white;
  }

  .btn-delete {
    background-color: #dc3545;
    color: white;
  }

  .btn-delete:hover {
    background-color: #c82333;
    transform: scale(1.1);
    color: white;
  }

  /* Modal content */
  .modal-content {
    border-radius: 10px;
  }

  .modal-header {
    background: var(--primary);
    color: white;
    border-radius: 10px 10px 0 0;
  }

  /* Navigation */
  .navigation {
    margin: 20px 0;
    text-align: center;
  }

  .nav-link {
    display: inline-block;
    margin: 0 10px;
    text-decoration: none;
    transition: transform 0.3s ease;
  }

  .nav-link:hover {
    transform: scale(1.1);
  }

  .nav-link img {
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  /* Pagination styles */
  .zebra_pagination {
    margin: 20px 0;
    text-align: center;
  }

  .zebra_pagination a {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 2px;
    background: white;
    color: var(--primary);
    text-decoration: none;
    border-radius: 5px;
    border: 1px solid var(--secondary);
    transition: all 0.3s ease;
  }

  .zebra_pagination a:hover {
    background: var(--primary);
    color: white;
  }

  .zebra_pagination .current {
    background: var(--primary);
    color: white;
  }

  /* Modal form styles */
  .form-group {
    margin-bottom: 1rem;
  }

  .form-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
  }

  .form-control {
    border: 1px solid var(--secondary);
    border-radius: 6px;
    padding: 10px 15px;
    transition: all 0.3s ease;
  }

  .form-control:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
  }

  /* Clickable row styling */
  .clickable-row {
    cursor: pointer;
  }
  .clickable-row:hover {
    background-color: rgba(99, 43, 139, 0.1) !important;
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

  /* Toast notifications */
  .toast {
    background-color: white;
    border-left: 4px solid var(--success);
  }

  .toast.error {
    border-left-color: var(--danger);
  }

  /* Success message styling */
  .alert-success {
    background-color: rgba(40, 167, 69, 0.1);
    border: 1px solid var(--success);
    color: var(--success);
    border-radius: 10px;
  }

  /* Loading spinner */
  .spinner-border {
    color: var(--primary);
  }

  /* Responsive adjustments */
  @media (max-width: 768px) {
    .table-container {
      padding: 10px;
    }

    #salesTable {
      font-size: 0.8rem;
    }

    .btn-action-icon {
      width: 28px;
      height: 28px;
      font-size: 12px;
    }

    .page-title {
      font-size: 1.5rem;
    }
  }
</style>

<body>
  <div class="header-container">
    <img src="../../img/logo.png" alt="Logo" class="logo">
    <h1 class="page-title"><i class="fa-solid fa-ban"></i> CANCELLATIONS</h1>
  </div>

  <div class="search-form">
    <form id="filterForm" class="row g-3 align-items-center justify-content-center">
      <div class="col-md-4">
        <input name="upc_item" type="text" placeholder="Enter UPC to search" id="upc" class="form-control" required>
      </div>
      <div class="col-md-3">
        <input name="item" type="text" placeholder="#Order (Optional)" id="sell_order" class="form-control">
      </div>
      <div class="col-md-2">
        <input value="Search" type="submit" class="btn btn-primary">
      </div>
    </form>
  </div>

  <!-- Mensaje inicial -->
  <div id="initialMessage" class="table-container text-center">
    <div class="alert alert-info">
      <i class="fas fa-search fa-2x mb-3"></i>
      <h4>Search for Cancellations</h4>
      <p>Enter a UPC code above to search for cancellation records</p>
    </div>
  </div>

  <!-- Tabla de Ventas (inicialmente oculta) -->
  <div class="table-container" id="resultsContainer" style="display: none;">
    <h2 class="text-center mb-4">Search Results</h2>
    <div id="searchResults">
      <!-- Los resultados se cargarán aquí -->
    </div>
  </div>

  <br /><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="back" /></a><br>


  <!-- MODAL DE CANCELLATIONS -->
  <div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="returnModalLabel">Cancellations</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div id="ventasTableContainer" class="px-4 pb-4"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Comentamos returns.js para evitar conflictos con la nueva lógica AJAX -->
  <!-- <script src="returns.js"></script> -->
  <!-- Comentamos scriptSeeSells.js para evitar conflictos con la nueva lógica AJAX -->
  <!-- <script src="scriptSeeSells.js"></script> -->
    <script>    document.addEventListener('DOMContentLoaded', function() {      // Función para calcular Net Cancellation
      function calculateNetCancellation() {
        const refundAmount = parseFloat(document.getElementById('refund_amount')?.value) || 0;
        const shippingRefund = parseFloat(document.getElementById('shipping_refund')?.value) || 0;
        const taxRefund = parseFloat(document.getElementById('tax_refund')?.value) || 0;
        const finalFeeRefund = parseFloat(document.getElementById('final_fee_refund')?.value) || 0;
        const fixedChargeRefund = parseFloat(document.getElementById('fixed_charge_refund')?.value) || 0;
        const otherFeeRefund = parseFloat(document.getElementById('other_fee_refund')?.value) || 0;
        
        // Formula: Refund amount + Shipping Refund + Tax Refund - Final Fee Refund - Fixed Charge Refund - Other Fee Refund
        const netCancellation = refundAmount + shippingRefund + taxRefund - finalFeeRefund - fixedChargeRefund - otherFeeRefund;
        
        const netCancellationField = document.getElementById('net_cancellation');
        if (netCancellationField) {
          // Agregar efecto visual de actualización
          netCancellationField.style.backgroundColor = '#e8f5e8';
          netCancellationField.value = netCancellation.toFixed(2);
          
          // Remover el efecto después de un momento
          setTimeout(() => {
            netCancellationField.style.backgroundColor = '';
          }, 300);
        }
      }

      // Función para agregar eventos de cálculo a todos los campos
      function setupCalculationEvents() {
        const fields = ['refund_amount', 'shipping_refund', 'tax_refund', 'final_fee_refund', 'fixed_charge_refund', 'other_fee_refund'];
        
        fields.forEach(fieldId => {
          const field = document.getElementById(fieldId);
          if (field) {
            // Agregar múltiples eventos para máxima responsividad
            field.addEventListener('input', calculateNetCancellation);
            field.addEventListener('change', calculateNetCancellation);
            field.addEventListener('keyup', calculateNetCancellation);
            field.addEventListener('paste', () => {
              // Pequeño delay para que el valor se procese después del paste
              setTimeout(calculateNetCancellation, 10);
            });
          }
        });
      }

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
          
          fetch(`getSellToReturn.php?sell_order=${encodeURIComponent(sell_order)}`)
            .then((response) => response.json())
            .then((data) => {
              console.log(data);
              if (data.error) {
                document.getElementById("ventasTableContainer").innerHTML = `<p>Error: ${data.error}</p>`;
                return;
              }
              
              const items = data.items;
              const cancellation = data.cancellation;

              // Crear la tabla (adaptada para Cancellations)
              let tableHTML = `
                <h4>Sell Order: ${items[0].sell_order}</h4>
                <table class="table table-bordered table-sm mt-3">
                  <thead>
                    <tr>
                      <th>UPC</th>
                      <th>SKU</th>
                      <th>Quantity</th>
                      <th>Final Fee</th>
                      <th>Fixed Charge</th>
                      <th>Item Profit</th>
                      <th>Total Item</th>
                    </tr>
                  </thead>
                  <tbody>
              `;

              let totalGeneral = 0;
              items.forEach((item) => {
                const quantity = item.quantity || 0;
                const comision_item = parseFloat(item.comision_item) || 0;
                const cargo_fijo = parseFloat(item.cargo_fijo) || 0;
                const item_profit = parseFloat(item.item_profit) || 0;
                const total_item = parseFloat(item.total_item) || 0;
                
                tableHTML += `
                  <tr>
                    <td>${item.upc_item}</td>
                    <td>${item.sku_item || "-"}</td>
                    <td>${quantity}</td>
                    <td>$${comision_item.toFixed(2)}</td>
                    <td>$${cargo_fijo.toFixed(2)}</td>
                    <td>$${item_profit.toFixed(2)}</td>
                    <td>$${total_item.toFixed(2)}</td>
                  </tr>
                `;
                totalGeneral += total_item;
              });

              tableHTML += `
                  <tr>
                    <td colspan="6" class="text-end"><strong>Total General</strong></td>
                    <td><strong>$${totalGeneral.toFixed(2)}</strong></td>
                  </tr>
                </tbody>
              </table>

              <form method='post' action='saveCancellations.php' class='mt-4' id='cancellationForm'>
                <div class='row mb-3'>                  <div class='col-md-6'>
                    <label for='refund_amount' class='form-label'>Refund Amount</label>
                    <input type='number' step='0.01' name='refund_amount' id='refund_amount' class='form-control' value='${cancellation ? (cancellation.refund_amount || '') : ''}'>
                  </div>
                  <div class='col-md-6'>
                    <label for='shipping_refund' class='form-label'>Shipping Refund</label>
                    <input type='number' step='0.01' name='shipping_refund' id='shipping_refund' class='form-control' value='${cancellation ? (cancellation.shipping_refund || '') : ''}'>
                  </div>
                </div>
                <div class='row mb-3'>
                  <div class='col-md-6'>
                    <label for='tax_refund' class='form-label'>Tax Refund</label>
                    <input type='number' step='0.01' name='tax_refund' id='tax_refund' class='form-control' value='${cancellation ? (cancellation.tax_refund || '') : ''}'>
                  </div>
                  <div class='col-md-6'>
                    <label for='final_fee_refund' class='form-label'>Final Fee Refund</label>
                    <input type='number' step='0.01' name='final_fee_refund' id='final_fee_refund' class='form-control' value='${cancellation ? (cancellation.final_fee_refund || '') : ''}'>
                  </div>
                </div>
                <div class='row mb-3'>
                  <div class='col-md-6'>
                    <label for='fixed_charge_refund' class='form-label'>Fixed Charge Refund</label>
                    <input type='number' step='0.01' name='fixed_charge_refund' id='fixed_charge_refund' class='form-control' value='${cancellation ? (cancellation.fixed_charge_refund || '') : ''}'>
                  </div>
                  <div class='col-md-6'>
                    <label for='other_fee_refund' class='form-label'>Other Fee Refund</label>
                    <input type='number' step='0.01' name='other_fee_refund' id='other_fee_refund' class='form-control' value='${cancellation ? (cancellation.other_fee_refund || '') : ''}'>
                  </div>
                </div>                <div class='row mb-3'>
                  <div class='col-md-12'>
                    <label for='net_cancellation' class='form-label'><strong>Net Cancellation</strong></label>
                    <input type='number' step='0.01' name='net_cancellation' id='net_cancellation' class='form-control bg-light' value='${cancellation ? (cancellation.net_cancellation || '') : ''}' readonly>
                  </div>
                </div>
                <input type='hidden' name='sell_order' value='${items[0].sell_order}'>
                <input type='hidden' name='id_sell' value='${items[0].id_sell}'>
                <div class='text-end'>
                  <button type='submit' class='btn' style='background-color: #632b8b; color: #fff; border-color: #632b8b;'>Save</button>
                </div>
              </form>
              `;

              document.getElementById("ventasTableContainer").innerHTML = tableHTML;
              
              // Add form submission handler
              const form = document.getElementById('cancellationForm');
              if (form) {
                form.addEventListener('submit', function(e) {
                  e.preventDefault();
                  
                  const formData = new FormData(form);
                  
                  fetch('saveCancellations.php', {
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
                        const modal = bootstrap.Modal.getInstance(document.getElementById('returnModal'));
                        modal.hide();
                        // Recargar los resultados de búsqueda en lugar de toda la página
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
                      text: 'An error occurred while saving the cancellation information.'
                    });
                  });
                });
              }              // Calculate initial Net Cancellation
              const modal = new bootstrap.Modal(document.getElementById("returnModal"));
              
              // Ejecutar cálculo cuando el modal se muestre completamente
              document.getElementById("returnModal").addEventListener('shown.bs.modal', function () {
                console.log('Modal shown, setting up calculation events...');
                // Configurar eventos de cálculo en tiempo real
                setupCalculationEvents();
                // Calcular valor inicial
                setTimeout(() => {
                  calculateNetCancellation();
                }, 200);
              }, { once: true });
              
              modal.show();
            })
            .catch((err) => {
              document.getElementById("ventasTableContainer").innerHTML = `<p>Error: ${err.message}</p>`;
            });
        });
      });
    }    // Manejar el formulario de búsqueda
    document.getElementById('filterForm').addEventListener('submit', function(e) {
      console.log('Form submit event triggered');
      e.preventDefault();
      console.log('Default prevented');
      
      const upc = document.getElementById('upc').value.trim();
      const sellOrder = document.getElementById('sell_order').value.trim();
      
      console.log('UPC:', upc, 'Sell Order:', sellOrder);
      
      if (!upc) {
        alert('Please enter a UPC code to search');
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
          <p class="mt-2">Searching for Cancellations...</p>
        </div>
      `;
      
      // Realizar búsqueda AJAX
      const formData = new FormData();
      formData.append('upc_item', upc);
      formData.append('sell_order', sellOrder);
      
      fetch('searchCancellations.php', {
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
        console.error('Error:', error);        document.getElementById('searchResults').innerHTML = `
          <div class="alert alert-danger text-center">
            <i class="fas fa-exclamation-triangle"></i>
            <h5>Error</h5>
            <p>An error occurred while searching. Please try again.</p>
          </div>
        `;
      });
    }); // Cerrar event listener del formulario
    }); // Cerrar DOMContentLoaded
  </script>
</body>

</html>
