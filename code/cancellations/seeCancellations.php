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
      <div class="col-md-3">
        <input name="upc_item" type="text" placeholder="UPC" id="upc" class="form-control">
      </div>
      <div class="col-md-3">
        <input name="item" type="text" placeholder="#Order" id="sell_order" class="form-control">
      </div>
      <div class="col-md-3">
        <input type="date" name="sellDate" id="date" class="form-control">
      </div>
      <div class="col-md-2">
        <input value="Search" type="submit" class="btn btn-primary">
      </div>
    </form>
  </div>
  <!-- Tabla de Ventas -->
  <div class="table-container">
    <h2 class="text-center mb-4">Registered Sales</h2>
    <table class="" id="salesTable">
      <thead>
        <tr>
          <th>Sell Number</th>
          <th>Date</th>
          <th>UPC</th>
          <th>Brand</th>
          <th>Item</th>
          <th>Color</th>
          <th>Reference</th>
          <th>Store</th>
          <th>Sucursal</th>
          <th>Refund Amount</th>
          <th>Shipping Refund</th>
          <th>Tax Refund</th>
          <th>Final Fee Refund</th>
          <th>Fixed Charge Refund</th>
          <th>Other Fee Refund</th>
        </tr>
      </thead>
      <tbody>
        <?php include "getSells.php"; ?>
      </tbody>
    </table>
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




  <script src="returns.js"></script>
  <script src="scriptSeeSells.js"></script>
</body>

</html>
