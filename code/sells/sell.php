<?php
session_start();
include("../../conexion.php");
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</head>

<body class="my-page-container">
  <div class="flex">
    <div class="flex">
      <br /><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="back" /></a><br>
    </div>
    <div>
      <center style="margin-top: 20px;">
        <img src='../../img/logo.png' width="300" height="212" class="responsive">
      </center>

      <h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em; font-size: 40px; text-align: center;"><b><i
            class="fa-solid fa-file-signature"></i> SALES</b></h1>

    </div>

  </div>
  <div class="flex">
    <div class="box">
      <div class="mb-3 d-inline-block " style="width: 275px; margin: 0 10px">
        <label for="sellDate" class="form-label">Date</label>
        <input type="date" class="form-control" name="sellDate" id="sellDate">
      </div>


      <form class="form">
        <div class="container">
          <div class="row g-3">
            <div class="col-md-3">
              <label for="upc" class="form-label">UPC</label>
              <input name="upc" type="text" id="upc" class="form-control">
            </div>

            <div class="col-md-3">
              <label for="item_name" class="form-label">Item Name</label>
              <input name="item_name" id="item_name" type="text" class="form-control">
            </div>

            <div class="col-md-3">
              <label for="priceItem" class="form-label">Price Item</label>
              <input name="priceItem" type="text" id="priceItem" class="form-control">
            </div>

            <div class="col-md-3">
              <label for="brandItemInput" class="form-label">Brand</label>
              <input type="text" id="brandItemInput" name="brand" class="form-control">
            </div>

            <div class="col-md-3">
              <label for="receivedShipping" class="form-label">Received Shipping</label>
              <input type="number" name="receivedShipping" id="receivedShipping" class="form-control">
            </div>

            <div class="col-md-3">
              <label for="payedShipping" class="form-label">Paid Shipping</label>
              <input type="number" name="payedShipping" id="payedShipping" class="form-control">
            </div>

            <div class="col-md-3">
              <label for="tienda" class="form-label">Store</label>
              <select id="tienda" name="id_store" class="form-select">
                <option value="">--Select a store--</option>
                <?php
                $resultTiendas->data_seek(0); // Reinicia el puntero del resultado
                while ($tienda = $resultTiendas->fetch_assoc()) {
                  echo "<option value='{$tienda['id_store']}'>{$tienda['store_name']}</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-3">
              <label for="sucursal" class="form-label">Sucursal</label>
              <select name="sucursal" id="sucursal" class="form-select">
                <option value="">--First select a store--</option>
              </select>
            </div>

            <div class="col-md-3">
              <label for="comisionItem" class="form-label">Commission Shipping</label>
              <input type="number" name="comisionItem" id="comisionItem" step="0.01" min="0" class="form-control">
            </div>

            <div class="col-md-3">
              <label for="quantitySell" class="form-label">Quantity</label>
              <input type="number" name="quantitySell" id="quantitySell" step="0.01" min="0" class="form-control">
            </div>

            <div class="col-md-3">
              <label for="UnitTotal" class="form-label">Total Item</label>
              <input name="ref" type="text" id="UnitTotal" readonly class="form-control">
            </div>

            <div class="col-12 text-end mt-3">
              <button type="submit" id="validateButton" class="btn btn-success">Validate</button>
            </div>
          </div>
        </div>
      </form>

      <div class="container mt-4">
        <table id="tableItems" class="table table-striped"
          style="width: 100%; margin: 20px auto; text-align: center; border-collapse: collapse;">
          <thead style="background-color:rgb(10, 9, 14); color: white;">
            <tr>
              <th>Item</th>
              <th>UPC</th>
              <th>Quantity</th>
              <th>Store</th>
              <th>Sucursal Code</th>
              <th>Brand</th>
              <th>Comision</th>
              <th>Date</th>
              <th>Received Shipping</th>
              <th>Paid Shipping</th>
              <th>Price Item </th>
              <th>Total Item</th>
              <th>Delete</th>
            </tr>
          </thead>
          <tbody id="bodyTable">
            <!-- Aquí se agregarán las filas dinámicamente con JavaScript -->
          </tbody>
          <!--<tfoot>
          <tr id="totalRow">
            <td colspan="11">Total</td>
            <td id="totalAmount">0</td>
            <td></td>
          </tr>
       // </tfoot>-->
        </table>
      </div>
      <div class="text-end mt-3" style="width: 90%; margin: 0 auto;">
        <button type="button" id="saveSellButton" class="btn btn-success custom-inline">Save Sale</button>
      </div>

    </div>
  </div>

  <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
  <script src="scriptSell.js"></script>

</body>

</html>