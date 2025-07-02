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

  /* Estilos para header personalizado de tabla */
  .table-custom-header {
    background: linear-gradient(135deg, #4a2568, #632b8b) !important;
    color: white !important;
  }

  .table-custom-header th {
    background: transparent !important;
    color: white !important;
    border: none !important;
    padding: 15px 8px !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    font-size: 0.8rem !important;
    letter-spacing: 0.5px !important;
    text-align: center !important;
    vertical-align: middle !important;
    position: relative !important;
  }

  .table-custom-header th::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: rgba(255, 255, 255, 0.3);
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

  /* Action buttons in table */
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
      <h4>Search for Cancellations</h4>
      <p>Enter a UPC code or Sell Order above to search for cancellation records</p>
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

  <script src="seeCancellations_clean.js"></script>
</body>

</html>
