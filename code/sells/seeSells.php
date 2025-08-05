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
  <link rel="stylesheet" href="styleSell.css">
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

  /* Estilos para las tarjetas del modal */
  .card {
    border: 1px solid var(--secondary);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }

  .card-header {
    background: linear-gradient(to right, var(--secondary-light), #f8f9fa);
    border-bottom: 1px solid var(--secondary);
  }

  .card-title {
    color: var(--primary);
    font-weight: 600;
  }

  /* Estilos para los iconos de estado */
  .text-success {
    color: #28a745 !important;
  }

  .text-danger {
    color: #dc3545 !important;
  }

  /* Mejorar la apariencia de las celdas con iconos */
  .text-center {
    vertical-align: middle !important;
  }
</style>

<body>
  <div class="header-container">
    <img src='../../img/logo.png' class="logo" alt="ASWWORKING Logo">
    <h1 class="page-title"><i class="fa-solid fa-file-signature"></i> SALES</h1>
  </div>


  <div class="search-form">
    <form id="filterForm" class="row g-3 align-items-center justify-content-center">
      <div class="col-md-4">
        <input name="upc_item" type="text" placeholder="Enter UPC to search" id="upc" class="form-control">
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
      <h4>Search for Sales</h4>
      <p>Enter a UPC code or Sell Order above to search for sales records</p>
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

  <!-- Modal de edición -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Modal extra ancho -->
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Editar Venta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editForm">
            <!-- Input oculto para el ID (no visible pero se envía) -->
            <input type="hidden" id="edit-id-sell" name="id">
            <input type="hidden" id="edit-store-id" name="store_id">
            <input type="hidden" id="edit-sucursal-id" name="sucursal_id">


            <!-- Fila 1: 3 columnas -->
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label for="edit-sell-order" class="form-label">Sell Order</label>
                <input type="text" class="form-control" id="edit-sell-order" readonly>
              </div>
              <div class="col-md-4">
                <label for="edit-date" class="form-label">Date</label>
                <input type="date" class="form-control" id="edit-date">
              </div>
              <div class="col-md-4">
                <label for="edit-upc" class="form-label">UPC</label>
                <input type="text" class="form-control" id="edit-upc">
              </div>
            </div>

            <!-- Fila 2: 3 columnas -->
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label for="edit-store" class="form-label">Store</label>
                <select class="form-select" id="edit-store">
                  <option value="" selected disabled hidden>--Select a store--</option>
                  <?php
                  $resultTiendas->data_seek(0); // Reinicia el puntero del resultado
                  while ($tienda = $resultTiendas->fetch_assoc()) {
                    echo "<option value='{$tienda['id_store']}'>{$tienda['store_name']}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-4">
                <label for="edit-sucursal" class="form-label">Sucursal</label>
                <select class="form-select" id="edit-sucursal">
                  <option value="">Select a Sucursal</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="edit-quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="edit-quantity">
              </div>
            </div>

            <!-- Fila 3: 3 columnas -->
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label for="edit-comision" class="form-label">Comision</label>
                <input type="text" class="form-control" id="edit-comision">
              </div>
              <div class="col-md-4">
                <label for="edit-rec-shipping" class="form-label">Received Shipping</label>
                <input type="text" class="form-control" id="edit-rec-shipping">
              </div>
              <div class="col-md-4">
                <label for="edit-pay-shipping" class="form-label">Paid shipping</label>
                <input type="text" class="form-control" id="edit-pay-shipping">
              </div>
            </div>

            <!-- Fila 4: 1 campo + espacio para botones -->
            <div class="row g-3">
              <div class="col-md-4">
                <label for="edit-item_price" class="form-label">Item Price</label>
                <input type="text" class="form-control" id="edit-item_price">
              </div>
              <div class="col-md-4">
                <label for="edit-total-item" class="form-label">Total</label>
                <input type="text" class="form-control" id="edit-total-item">
              </div>
              <!-- Columnas vacías para mantener alineación -->
              <div class="col-md-4">
                <input type="hidden" id="editComision" name="comision_item">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveEdit">Save Changes</button>
        </div>
      </div>
    </div>
  </div>
  </div>

  <!-- MODAL DE RETURNS -->
  <div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="returnModalLabel">Sales Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div id="ventasTableContainer" class="px-4 pb-4"></div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>




  <script src="returns.js"></script>
  <script src="scriptSeeSells.js"></script>
  <script>
    // Función para inicializar los event listeners de las filas clickeables
    function initializeClickableRows() {
      document.querySelectorAll(".clickable-row").forEach(function (row) {
        // Remover event listeners previos para evitar duplicados
        const newRow = row.cloneNode(true);
        row.parentNode.replaceChild(newRow, row);
      });
      
      // Reinicializar los event listeners usando la misma lógica que returns.js
      document.querySelectorAll(".clickable-row").forEach(function (row) {
        row.addEventListener("click", function () {
          const sell_order = this.dataset.sell_order;
          console.log("Selected Sell Order:", sell_order);
          
          // Fetch both items and summary data
          Promise.all([
            fetch(`getSellToReturn.php?sell_order=${encodeURIComponent(sell_order)}`).then(r => r.json()),
            fetch(`getSellSummary.php?sell_order=${encodeURIComponent(sell_order)}`).then(r => r.json())
          ])
            .then(([itemsData, summaryData]) => {
              console.log('Items data:', itemsData);
              console.log('Summary data:', summaryData);
              
              if (itemsData.error) {
                document.getElementById("ventasTableContainer").innerHTML = `<p>Error: ${itemsData.error}</p>`;
                return;
              }
              
              const items = itemsData.items;
              const summary = summaryData.summary || null;
              
              // Crear la tabla (mismo código que en returns.js)
              let tableHTML = `
                <h4>Sell Order: ${items[0].sell_order}</h4>
                
                <!-- Información detallada de la venta -->
                <div class="row mb-4">
                  <div class="col-md-12">
                    <div class="card">
                      <div class="card-header">
                        <h5 class="card-title mb-0">Sale Details</h5>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <div class="col-md-3">
                            <strong>UPC:</strong><br>
                            <span class="text-muted">${items[0].upc_item || '-'}</span>
                          </div>
                          <div class="col-md-3">
                            <strong>SKU:</strong><br>
                            <span class="text-muted">${items[0].sku_item || '-'}</span>
                          </div>
                          <div class="col-md-3">
                            <strong>Quantity:</strong><br>
                            <span class="text-muted">${items[0].quantity || 0}</span>
                          </div>
                          <div class="col-md-3">
                            <strong>Date:</strong><br>
                            <span class="text-muted">${items[0].date || '-'}</span>
                          </div>
                        </div>
                        <hr>
                        <div class="row">
                          <div class="col-md-3">
                            <strong>Received Shipping:</strong><br>
                            <span class="text-muted">$${(parseFloat(items[0].received_shipping) || 0).toFixed(2)}</span>
                          </div>
                          <div class="col-md-3">
                            <strong>Payed Shipping:</strong><br>
                            <span class="text-muted">$${(parseFloat(items[0].payed_shipping) || 0).toFixed(2)}</span>
                          </div>
                          <div class="col-md-3">
                            <strong>Comision:</strong><br>
                            <span class="text-muted">$${(parseFloat(items[0].comision_item) || 0).toFixed(2)}</span>
                          </div>
                          <div class="col-md-3">
                            <strong>Item Price:</strong><br>
                            <span class="text-muted">$${(parseFloat(items[0].item_price) || 0).toFixed(2)}</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                
                <!-- Tabla de items -->
                <table class="table table-bordered table-sm mt-3">
                  <thead>
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
                    <td>${item.sku_item || "-"}</td>
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
              </table>
              `;

              document.getElementById("ventasTableContainer").innerHTML = tableHTML;
              
              const modal = new bootstrap.Modal(document.getElementById("returnModal"));
              modal.show();
            })
            .catch((err) => {
              document.getElementById("ventasTableContainer").innerHTML = `<p>Error: ${err.message}</p>`;
            });
        });
      });
    }

    // Manejar el formulario de búsqueda
    document.getElementById('filterForm').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const upc = document.getElementById('upc').value.trim();
      const sellOrder = document.getElementById('sell_order').value.trim();
      
      if (!upc && !sellOrder) {
        alert('Please enter either a UPC code or Sell Order to search');
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
          <p class="mt-2">Searching for sales...</p>
        </div>
      `;
      
      // Realizar búsqueda AJAX
      const formData = new FormData();
      formData.append('upc_item', upc);
      formData.append('sell_order', sellOrder);
      
      fetch('searchSells.php', {
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