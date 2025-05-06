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
  $id_devolution = $_GET['delete'];
  deleteMember($id_devolution);
}
function deleteMember($id_devolution)
{
  global $mysqli; // Asegurar acceso a la conexión global

  $query = "DELETE FROM devolutions WHERE id_devolution  = ?";
  $stmt = $mysqli->prepare($query);
  $stmt->bind_param("s", $id_devolution);

  if ($stmt->execute()) {
    echo "<script>alert('devolution deleted correctly');
      window.location = 'seeDevolutions.php';</script>";
  } else {
    echo "<script>alert('Error deleting the devolution');
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
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Librerías de DataTables -->
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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
    }

    tbody td {
      padding: 12px 10px;
      border-bottom: 1px solid rgba(153, 124, 171, 0.3);
      color: var(--text-dark);
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
    }

    /* Responsive adjustments */
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
    }
  </style>
</head>

<body>
  <div class="header-container">
    <div class="container text-center">
      <img src='../../img/logo.png' class="logo" alt="ASWWORKING Logo">
    </div>
  </div>

  <div class="container">
    <h1 class="page-title text-center"><i class="fa-solid fa-file-alt"></i> DEVOLUTIONS</h1>

    <!-- Search Form -->
    <div class="flex">
      <div class="box">
        <form action="seeDevolutions.php" method="get" class="form">
          <input name="upc_item" type="text" placeholder="Upc" value="<?= isset($_GET['upc_item']) ? htmlspecialchars($_GET['upc_item']) : '' ?>">
          <input name="date_devolution" type="date" placeholder="Devolution Date" value="<?= isset($_GET['date_devolution']) ? htmlspecialchars($_GET['date_devolution']) : '' ?>">
          <input name="sell_order" type="text" placeholder="Sell Order" value="<?= isset($_GET['sell_order']) ? htmlspecialchars($_GET['sell_order']) : '' ?>">
          <input value="Search" type="submit">
        </form>
      </div>
    </div>

    <!-- Tabla de Ventas -->
    <div class="table-container">
      <table class="table" id="salesTable">
        <thead>
          <tr>
            <th>Sell Number</th>
            <th>Date</th>
            <th>UPC</th>
            <th>Received Shipping</th>
            <th>Payeed Shipping</th>
            <th>Store</th>
            <th>Sucursal</th>
            <th>Comision</th>
            <th>Quantity</th>
            <th>Item Price</th>
            <th>Total Item</th>
            <th>Devolution Date</th>
            <th>Delete Sell</th>
          </tr>
        </thead>
        <tbody>
          <?php include "getDevolutions.php"; ?>
        </tbody>
      </table>
    </div>

    <div class="text-center mt-4">
      <button type="button" class="back-btn" onclick="window.location.href='../../access.php'">
        <i class="fas fa-arrow-left"></i>
      </button>
    </div>

    <!-- Modal de edición -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl"> <!-- Modal extra ancho -->
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editModalLabel">Edit Devolution</h5>
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

  <script src="../sells/scriptSeeSells.js"></script>
</body>

</html>