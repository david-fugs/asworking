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

if (isset($_GET['delete'])) {
  $id_return = $_GET['delete'];
  deleteReturn($id_return);
}
function deleteReturn($id_return)
{
  global $mysqli; // Asegurar acceso a la conexión global

  $query = "DELETE FROM returns WHERE id_return  = ?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param("s", $id_return);

  if ($stmt->execute()) {
    echo "<script>alert('return deleted correctly');
      window.location = 'seeDevolutions.php';</script>";
  } else {
    echo "<script>alert('Error deleting the return');
      window.location = 'seeDevolutions.php';</script>";
  }

  $stmt->close();
}

//traer lo que traiga el formulario de busqueda
if (isset($_GET['upc_item']) || isset($_GET['date_devolution']) || isset($_GET['sell_order'])) {
  $upc_item = $_GET['upc_item'];
  $date_devolution = $_GET['date_devolution'];
  $sell_order = $_GET['sell_order'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ASWWORKING | SOFT</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Solo jQuery, sin DataTables -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Incluir SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

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
    }

    body {
      background-color: var(--bg-light);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Header styles */
    .header-container {
      width: 100%;
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

    .page-title {
      color: var(--primary);
      font-weight: 700;
      text-shadow: 0 1px 2px rgba(0,0,0,0.1);
      margin-bottom: 30px;
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
      margin-bottom: 30px;
    }

    .search-form input[type="text"],
    .search-form input[type="date"] {
      border: 1px solid var(--secondary);
      border-radius: 6px;
      padding: 10px 15px;
      transition: all 0.3s ease;
    }

    .search-form input[type="text"]:focus,
    .search-form input[type="date"]:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
      outline: none;
    }

    .search-form input[type="submit"] {
      background: linear-gradient(to bottom, var(--primary), var(--primary-light));
      color: white;
      border: none;
      border-radius: 30px;
      padding: 10px 25px;
      font-weight: 600;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .search-form input[type="submit"]:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Table styles */
    .table-container {
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
      background: white;
      margin-bottom: 30px;
    }

    table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    thead {
      background: var(--primary);
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
    }

    tbody tr {
      background-color: rgba(255, 255, 255, 0.8);
      transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    tbody tr:nth-child(even) {
      background-color: rgba(248, 240, 255, 0.8);
    }

    tbody tr:hover {
      background-color: white;
      box-shadow: 0 4px 12px rgba(99, 43, 139, 0.1);
    }    tbody td {
      padding: 12px 10px;
      border-bottom: 1px solid rgba(153, 124, 171, 0.3);
      color: #000000 !important;
      font-size: 0.9rem;
      transition: all 0.2s ease;
      text-align: center;
    }

    /* Action buttons */
    .btn-action {
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
    }

    .btn-edit {
      color: var(--primary);
    }

    .btn-edit:hover {
      background-color: rgba(99, 43, 139, 0.1);
      transform: scale(1.1);
    }

    .btn-delete {
      color: #dc3545;
    }

    .btn-delete:hover {
      background-color: rgba(220, 53, 69, 0.1);
      transform: scale(1.1);
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
      width: 50px;
      height: 50px;
    }

    .back-btn:hover {
      background-color: rgba(93, 51, 122, 0.1);
      color: var(--primary);
      transform: translateX(-3px);
    }

    .back-btn i {
      transition: transform 0.3s ease;
    }

    .back-btn:hover i {
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

    .btn-primary {
      background: linear-gradient(to bottom, var(--primary), var(--primary-light));
      border: none;
    }

    .btn-primary:hover {
      background: linear-gradient(to bottom, var(--primary-light), var(--primary));
    }

    .btn-secondary {
      background-color: var(--secondary);
      border: none;
    }

    .btn-secondary:hover {
      background-color: var(--primary-light);
    }

    /* Flex container for search form */
    .flex {
      display: flex;
      justify-content: center;
      margin-bottom: 30px;
    }

    .box {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      width: 100%;
      max-width: 900px;
    }

    .form {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .form input[type="text"],
    .form input[type="date"] {
      flex: 1;
      min-width: 150px;
      border: 1px solid var(--secondary);
      border-radius: 6px;
      padding: 10px 15px;
      transition: all 0.3s ease;
    }

    .form input[type="text"]:focus,
    .form input[type="date"]:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
      outline: none;
    }

    .form input[type="submit"] {
      background: linear-gradient(to bottom, var(--primary), var(--primary-light));
      color: white;
      border: none;
      border-radius: 6px;
      padding: 10px 25px;
      font-weight: 600;
      transition: all 0.3s ease;
      cursor: pointer;
      min-width: 120px;
    }

    .form input[type="submit"]:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }    /* Responsive adjustments */
    @media (max-width: 768px) {
      .logo {
        height: 80px;
      }
      
      .page-title {
        font-size: 1.8rem;
      }

      .form {
        flex-direction: column;
      }

      .form input[type="submit"] {
        width: 100%;
      }

      .btn-back {
        padding: 10px 20px;
        font-size: 0.9rem;
      }

      .table-responsive {
        font-size: 0.8rem;
      }

      #devolutionsTable thead th {
        padding: 12px 8px;
        font-size: 0.75rem;
      }

      .clickable-row td {
        padding: 12px 8px;
      }
    }

    /* Alert styles */
    .alert-info {
      background-color: #e3f2fd;
      border-color: #1976d2;
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
    }    /* Estilos mejorados para los resultados */
    #searchResults .table {
      margin-bottom: 0;
    }

    #searchResults .alert {
      border-radius: 8px;
      margin-bottom: 0;
    }

    /* Botón de retorno mejorado */
    .btn-back {
      display: inline-flex;
      align-items: center;
      padding: 12px 24px;
      background: linear-gradient(135deg, var(--primary), var(--primary-light));
      color: white;
      text-decoration: none;
      border-radius: 50px;
      font-weight: 600;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(99, 43, 139, 0.3);
      border: none;
      cursor: pointer;
    }

    .btn-back:hover {
      background: linear-gradient(135deg, var(--primary-light), var(--primary-dark));
      transform: translateY(-2px);
      box-shadow: 0 6px 16px rgba(99, 43, 139, 0.4);
      color: white;
      text-decoration: none;
    }

    .btn-back:active {
      transform: translateY(0);
      box-shadow: 0 2px 8px rgba(99, 43, 139, 0.3);
    }

    .btn-back i {
      transition: transform 0.3s ease;
    }

    .btn-back:hover i {
      transform: translateX(-3px);
    }

    /* Estilos mejorados para la tabla */
    .table-striped > tbody > tr:nth-of-type(odd) > td {
      background-color: rgba(248, 249, 250, 0.8);
    }

    .table-hover > tbody > tr:hover > td {
      background-color: rgba(99, 43, 139, 0.05);
      transform: scale(1.002);
      transition: all 0.2s ease;
    }

    .clickable-row {
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .clickable-row:hover {
      background: linear-gradient(45deg, rgba(99, 43, 139, 0.08), rgba(218, 199, 229, 0.15)) !important;
      box-shadow: 0 2px 8px rgba(99, 43, 139, 0.1);
      transform: translateY(-1px);
    }    .clickable-row td {
      vertical-align: middle;
      padding: 12px 8px;
      border-bottom: 1px solid rgba(99, 43, 139, 0.1);
      font-size: 0.85rem;
      color: #000000 !important;
      font-weight: 500;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 120px;
    }/* Header de tabla mejorado */
    #returnsTable thead th {
      background: linear-gradient(135deg, var(--primary), var(--primary-light));
      color: white;
      font-weight: 600;
      text-align: center;
      padding: 12px 8px;
      border: none;
      font-size: 0.75rem;
      letter-spacing: 0.3px;
      text-transform: uppercase;
      position: sticky;
      top: 0;
      z-index: 10;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      white-space: nowrap;
      min-width: 80px;
    }/* Valores monetarios destacados */
    .clickable-row td:nth-child(6),
    .clickable-row td:nth-child(7),
    .clickable-row td:nth-child(8),
    .clickable-row td:nth-child(9),
    .clickable-row td:nth-child(10),
    .clickable-row td:nth-child(11),
    .clickable-row td:nth-child(12) {
      font-weight: 700;
      color: #000000 !important;
    }

    /* Animación suave para la aparición de la tabla */
    #devolutionsTable {
      animation: fadeInUp 0.5s ease-out;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Indicador de estado para filas */
    .clickable-row::before {
      content: '';
      position: absolute;
      left: 0;
      top: 0;
      bottom: 0;
      width: 4px;
      background: linear-gradient(to bottom, var(--primary), var(--secondary));
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .clickable-row:hover::before {
      opacity: 1;
    }    .clickable-row {
      position: relative;
    }

    /* Badges personalizados */
    .badge {
      font-size: 0.8rem;
      font-weight: 500;
    }

    .bg-outline-primary {
      background-color: transparent !important;
      color: var(--primary) !important;
      border: 2px solid var(--primary);
    }

    /* Códigos de barras estilizados */
    code {
      background-color: rgba(99, 43, 139, 0.1);
      color: var(--primary-dark);
      padding: 4px 8px;
      border-radius: 4px;
      font-family: 'Courier New', monospace;
      font-weight: 600;
    }

    /* Texto truncado con tooltip */
    .text-truncate {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }    /* Animación para valores monetarios */
    .text-success, .text-primary, .text-warning, .text-info, .text-danger {
      transition: all 0.3s ease;
      color: #000000 !important;
    }

    .clickable-row:hover .text-success,
    .clickable-row:hover .text-primary,
    .clickable-row:hover .text-warning,
    .clickable-row:hover .text-info,
    .clickable-row:hover .text-danger {
      font-size: 1.05em;
      font-weight: 700;
      color: #000000 !important;
    }    /* Asegurar que todos los elementos de texto sean negros */
    .clickable-row td *,
    .clickable-row td,
    .table td,
    .table td *,
    .badge,
    code,
    em,
    strong,
    span {
      color: #000000 !important;
    }

    /* Corregir badges para que mantengan color de fondo pero texto negro */
    .badge.bg-dark {
      background-color: #6c757d !important;
      color: #ffffff !important;
    }

    code.bg-dark {
      background-color: #6c757d !important;
      color: #ffffff !important;
    }

    /* Asegurar que DataTables no agregue columnas extra */
    .dataTable {
      width: 100% !important;
    }

    .dataTable thead th,
    .dataTable tbody td {
      color: #000000 !important;
      font-weight: 500;
    }

    .dataTable thead th {
      color: #ffffff !important; /* Headers en blanco */
      font-weight: 600;
    }    /* Evitar columnas vacías */
    .table-responsive table {
      table-layout: fixed;
      width: 100%;
    }

    .table-responsive th,
    .table-responsive td {
      word-wrap: break-word;
      overflow-wrap: break-word;
    }

    /* Forzar estructura de tabla correcta */
    #returnsTable {
      border-collapse: separate !important;
      border-spacing: 0 !important;
    }

    #returnsTable thead tr {
      display: table-row !important;
    }

    #returnsTable thead th {
      display: table-cell !important;
      vertical-align: middle !important;
    }

    #returnsTable tbody tr {
      display: table-row !important;
    }

    #returnsTable tbody td {
      display: table-cell !important;
      vertical-align: middle !important;
    }    /* Asegurar que no hay elementos flotantes interfiriendo */
    .table-responsive {
      clear: both;
      overflow-x: auto;
    }    /* Ajustes específicos para columnas */
    #returnsTable th:nth-child(1), #returnsTable td:nth-child(1) { min-width: 100px; } /* Sell Order */
    #returnsTable th:nth-child(2), #returnsTable td:nth-child(2) { min-width: 90px; }  /* Date */
    #returnsTable th:nth-child(3), #returnsTable td:nth-child(3) { min-width: 120px; } /* UPC */
    #returnsTable th:nth-child(4), #returnsTable td:nth-child(4) { min-width: 80px; }  /* SKU */
    #returnsTable th:nth-child(5), #returnsTable td:nth-child(5) { min-width: 70px; }  /* Quantity */
    #returnsTable th:nth-child(6), #returnsTable td:nth-child(6) { min-width: 100px; } /* Product Charge */
    #returnsTable th:nth-child(7), #returnsTable td:nth-child(7) { min-width: 100px; } /* Shipping Paid */
    #returnsTable th:nth-child(8), #returnsTable td:nth-child(8) { min-width: 90px; }  /* Tax Return */
    #returnsTable th:nth-child(9), #returnsTable td:nth-child(9) { min-width: 120px; } /* Selling Fee Refund */
    #returnsTable th:nth-child(10), #returnsTable td:nth-child(10) { min-width: 140px; } /* Refund Administration Fee */
    #returnsTable th:nth-child(11), #returnsTable td:nth-child(11) { min-width: 120px; } /* Other Refund Fee */
    #returnsTable th:nth-child(12), #returnsTable td:nth-child(12) { min-width: 100px; } /* Return Cost */
    #returnsTable th:nth-child(13), #returnsTable td:nth-child(13) { min-width: 150px; max-width: 200px; } /* Buyer Comments */
    #returnsTable th:nth-child(14), #returnsTable td:nth-child(14) { min-width: 80px; }  /* Branch */

    /* Permitir que los comentarios se expandan */
    #returnsTable td:nth-child(13) {
      white-space: normal !important;
      word-wrap: break-word !important;
      max-width: 200px !important;
    }

    /* FORZAR estructura correcta - eliminar cualquier interferencia */
    #returnsTable {
      table-layout: auto !important;
      width: 100% !important;
      border-collapse: collapse !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    #returnsTable::before,
    #returnsTable::after {
      display: none !important;
    }

    #returnsTable thead,
    #returnsTable tbody {
      display: table-header-group !important;
      width: 100% !important;
    }

    #returnsTable tbody {
      display: table-row-group !important;
    }

    #returnsTable tr {
      display: table-row !important;
      width: 100% !important;
    }

    #returnsTable th,
    #returnsTable td {
      display: table-cell !important;
      position: relative !important;
      vertical-align: middle !important;
      width: auto !important;
    }

    /* Eliminar cualquier pseudo-elemento que pueda crear columnas fantasma */
    #returnsTable *::before,
    #returnsTable *::after {
      display: none !important;
    }

    /* Responsive tabla */
    .table-responsive {
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      background: white;
      overflow: hidden;
    }

    /* Mejora del contenedor de resultados */
    #resultsContainer h2 {
      color: var(--primary);
      font-weight: 700;
      text-shadow: 0 1px 2px rgba(0,0,0,0.1);
      margin-bottom: 20px;
    }

    #resultsContainer h2::after {
      content: '';
      display: block;
      width: 50px;
      height: 3px;
      background: linear-gradient(to right, var(--primary), var(--secondary-light));
      margin: 10px auto;
      border-radius: 3px;
    }</style>
</head>

<body>  <!-- Main Content Container -->
    <div class="header-container">
    <div class="container text-center">
      <img src='../../img/logo.png' class="logo" alt="ASWWORKING Logo">
    </div>
  </div>
  <div class="search-form">
    <form id="filterForm" class="row g-3 align-items-center justify-content-center">
      <div class="col-md-4">
        <input name="upc_item" type="text" placeholder="Enter UPC (Optional)" id="upc" class="form-control">
      </div>
      <div class="col-md-3">
        <input name="sell_order" type="text" placeholder="Enter Sell Order (Optional)" id="sell_order" class="form-control">
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
        <h4>Search for Returns</h4>
        <p>Enter a UPC code or Sell Order number above to search for Return records</p>
      </div>
    </div>

    <!-- Tabla de Resultados (inicialmente oculta) -->
    <div class="table-container" id="resultsContainer" style="display: none;">
      <h2 class="text-center mb-4">Search Results</h2>
      <div id="searchResults">
        <!-- Los resultados se cargarán aquí -->
      </div>
    </div>

    <!-- Botón de retorno mejorado -->
    <div class="text-center mt-4 mb-4">
      <a href="../../access.php" class="btn-back">
        <i class="fas fa-arrow-left me-2"></i>
        Back to Menu
      </a>
    </div>

    <!-- Modal de edición -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl"> <!-- Modal extra ancho -->
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Devolution</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">            <form id="editForm">
              <!-- Input oculto para el ID -->
              <input type="hidden" id="edit-id-sell" name="id">

              <!-- Fila 1: 3 columnas -->
              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <label for="edit-sell-order" class="form-label">Sell Order</label>
                  <input type="text" class="form-control" id="edit-sell-order" readonly>
                </div>
                <div class="col-md-4">
                  <label for="edit-date" class="form-label">Date</label>
                  <input type="date" class="form-control" id="edit-date" readonly>
                </div>
                <div class="col-md-4">
                  <label for="edit-devolution-date" class="form-label">Devolution Date</label>
                  <input type="date" class="form-control" id="edit-devolution-date" name="devolution_date">
                </div>
              </div>

              <!-- Fila 2: 3 columnas -->
              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <label for="edit-upc" class="form-label">UPC</label>
                  <input type="text" class="form-control" id="edit-upc" readonly>
                </div>
                <div class="col-md-4">
                  <label for="edit-sku" class="form-label">SKU</label>
                  <input type="text" class="form-control" id="edit-sku" readonly>
                </div>
                <div class="col-md-4">
                  <label for="edit-quantity" class="form-label">Quantity</label>
                  <input type="number" class="form-control" id="edit-quantity" step="0.01">
                </div>
              </div>

              <!-- Fila 3: 3 columnas -->
              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <label for="edit-product-charge" class="form-label">Product Charge</label>
                  <input type="number" class="form-control" id="edit-product-charge" step="0.01">
                </div>
                <div class="col-md-4">
                  <label for="edit-shipping-paid" class="form-label">Shipping Paid</label>
                  <input type="number" class="form-control" id="edit-shipping-paid" step="0.01">
                </div>
                <div class="col-md-4">
                  <label for="edit-tax-return" class="form-label">Tax Return</label>
                  <input type="number" class="form-control" id="edit-tax-return" step="0.01">
                </div>
              </div>

              <!-- Fila 4: 3 columnas -->
              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <label for="edit-selling-fee-refund" class="form-label">Selling Fee Refund</label>
                  <input type="number" class="form-control" id="edit-selling-fee-refund" step="0.01">
                </div>
                <div class="col-md-4">
                  <label for="edit-refund-administration-fee" class="form-label">Refund Administration Fee</label>
                  <input type="number" class="form-control" id="edit-refund-administration-fee" step="0.01">
                </div>
                <div class="col-md-4">
                  <label for="edit-other-refund-fee" class="form-label">Other Refund Fee</label>
                  <input type="number" class="form-control" id="edit-other-refund-fee" step="0.01">
                </div>
              </div>

              <!-- Fila 5: 3 columnas -->
              <div class="row g-3 mb-3">
                <div class="col-md-4">
                  <label for="edit-item-profit" class="form-label">Item Profit</label>
                  <input type="number" class="form-control" id="edit-item-profit" step="0.01" readonly>
                </div>
                <div class="col-md-4">
                  <label for="edit-return-cost" class="form-label">Return Cost (Calculated)</label>
                  <input type="number" class="form-control" id="edit-return-cost" step="0.01" readonly style="background-color: #f8f9fa;">
                </div>
                <div class="col-md-4">
                  <!-- Columna vacía para balance visual -->
                </div>
              </div>

              <!-- Fila 6: Comentarios -->
              <div class="row g-3 mb-3">
                <div class="col-md-12">
                  <label for="edit-buyer-comments" class="form-label">Buyer Comments</label>
                  <textarea class="form-control" id="edit-buyer-comments" rows="3"></textarea>
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

  <script src="scriptSeeDevolutionsNew.js?v=<?php echo time(); ?>"></script>


