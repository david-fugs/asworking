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

<body>
  <center style="margin-top: 20px;">
    <img src='../../img/logo.png' width="300" height="212" class="responsive">
  </center>
  <h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em; font-size: 40px; text-align: center;"><b><i
        class="fa-solid fa-file-signature"></i> ITEMS</b></h1>

  <div class="flex">
    <div class="box">
      <form action="showitems.php" method="get" class="form">
        <input name="upc_item" type="text" placeholder="Upc ">
        <input name="item" type="text" placeholder="Item">
        <input name="ref" type="text" placeholder="Reference">
        <input value="Search" type="submit">
      </form>
    </div>
  </div>

  <!-- Tabla de Ventas -->
  <div class="container mt-5">
    <h2 class="text-center">Ventas Registradas</h2>
    <table class="table table-striped" id="salesTable">
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
          <th>Total Item</th>
          <th>Edit Sell</th>
          <th>Delete Sell</th>
        </tr>
      </thead>
      <tbody>
        <?php include "getSells.php"; ?>
      </tbody>
    </table>
  </div>

  <br /><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="back" /></a><br>

  <!-- Modal de Edición -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content p-4">
        <div class="modal-header">
          <h5 class="modal-title">Editar Venta</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <form id="editForm">
            <input type="hidden" id="edit-id-sell" name="id_sell">

            <div class="row mb-3">
              <div class="col-md-4">
                <label class="form-label">Sell Order</label>
                <input type="text" class="form-control" id="edit-sell-order" name="sell_order" readonly>
              </div>
              <div class="col-md-4">
                <label class="form-label">Fecha</label>
                <input type="date" class="form-control" id="edit-date" name="date">
              </div>
              <div class="col-md-4">
                <label class="form-label">UPC</label>
                <input type="text" class="form-control" id="edit-upc" name="upc_item">
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-4">
                <label class="form-label">Comisión</label>
                <input type="text" class="form-control" id="edit-comision" name="comision_item">
              </div>
              <div class="col-md-4">
                <label class="form-label">Shipping Recibido</label>
                <input type="text" class="form-control" id="edit-rec-shipping" name="received_shipping">
              </div>
              <div class="col-md-4">
                <label class="form-label">Shipping Pagado</label>
                <input type="text" class="form-control" id="edit-pay-shipping" name="payed_shipping">
              </div>
            </div>

            <div class="col-md-4">
              <label class="form-label">Tienda</label>
              <select class="form-control" id="edit-store" name="store_name">
                <option value="">Seleccione una tienda</option>
                <?php
                while ($tienda = $resultTiendas->fetch_assoc()) {
                  echo "<option value='{$tienda['id_store']}'>{$tienda['store_name']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-4">
              <label class="form-label">Sucursal</label>
              <input type="text" class="form-control" id="edit-sucursal" name="code_sucursal">
            </div>
            <div class="col-md-4">
              <label class="form-label">Cantidad</label>
              <input type="number" class="form-control" id="edit-quantity" name="quantity">
            </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Total Item</label>
            <input type="text" class="form-control" id="edit-total-item" name="total_item">
          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Guardar cambios</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
        </form>
      </div>
    </div>
  </div>
  </div>



  <script src="scriptSeeSells.js"></script>
</body>

</html>