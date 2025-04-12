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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</head>

<body>
  <center style="margin-top: 20px;">
    <img src='../../img/logo.png' width="300" height="212" class="responsive">
  </center>
  <h1 style="color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em; font-size: 40px; text-align: center;"><b><i
        class="fa-solid fa-file-signature"></i> SELLS</b></h1>
  <div class="flex">
    <div class="box">
      <input type="date" name="sellDate" id="sellDate" placeholder="Date">

      <form class="form">
        <div class="form_firstline">
          <div class="input-div">
            <label for="upc">UPC</label>
            <div><input name="upc" type="text" id="upc"></div>
          </div>

          <div class="input-div">
            <label for="item_name">Item Name</label>
            <div><input name="item_name" id="item_name" type="text"></div>
          </div>

          <div class="input-div">
            <label for="priceItem">Price Item</label>
            <div><input name="priceItem" type="text" id="priceItem"></div>
          </div>

          <div class="input-div">
            <label for="brandItemInput">Brand</label>
            <div><input type="text" id="brandItemInput" name="brand"></div>
          </div>

          <div class="input-div">
            <label for="receivedShipping">Received Shipping</label>
            <div><input type="number" name="receivedShipping" id="receivedShipping"></div>
          </div>

          <div class="input-div">
            <label for="payedShipping">Payed Shipping</label>
            <div><input type="number" name="payedShipping" id="payedShipping"></div>
          </div>
        </div>

        <div class="form-inline-group">
          <label for="tienda">Store</label>
          <select id="tienda" name="id_store" class="form-select custom-inline">
            <option value="">--Select a store --</option>
            <?php
            while ($tienda = $resultTiendas->fetch_assoc()) {
              echo "<option value='{$tienda['id_store']}'>{$tienda['store_name']}</option>";
            }
            ?>
          </select>

          <label for="sucursal">Sucursal</label>
          <select name="sucursal" id="sucursal" class="form-select custom-inline">
            <option value="">-- First select a store --</option>
          </select>

          <div class="input-div">
            <label for="comisionItem">Commission Shipping</label>
            <div><input type="number" name="comisionItem" id="comisionItem" step="0.01" min="0"></div>
          </div>

          <div class="input-div">
            <label for="comisionItem">Quantity</label>
            <div><input type="number" name="quantitySell" id="quantitySell" step="0.01" min="0"></div>
          </div>

          <div class="input-div">
            <label for="UnitTotal">Unit Total</label>
            <div><input type="number" name="quantitySell" id="quantitySell" step="0.01" min="0"></div>
          </div>

          <label for="UnitTotal">Unit Total</label>
          <input name="ref" type="text" id="UnitTotal" readonly class="custom-inline">

          <button type="submit" id="validateButton" class="btn btn-success custom-inline">Validate</button>
        </div>


      </form>
      <div class="container mt-5">
        <table id="tableItems" class="table table-striped"
          style="width: 90%; margin: 20px auto; text-align: center; border-collapse: collapse;">
          <thead style="background-color:rgb(10, 9, 14); color: white;">
            <tr>
              <th>ITEM</th>
              <th>UPC</th>
              <th>Quantity</th>
              <th>Store</th>
              <th>Sucursal Code</th>
              <th>BRAND</th>
              <th>COMISION</th>
              <th>DATE</th>
              <th>RECEIVED SHIPPING</th>
              <th>PAYED SHIPPING</th>
              <th>ITEM COST</th>
              <th>TOTAL ITEM</th>
              <th>DELETE</th>
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
        <button type="button" id="saveSellButton" class="btn btn-success custom-inline">Save Sell</button>
      </div>

    </div>
  </div>
  <br /><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="back" /></a><br>
  <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
  <script src="scriptSell.js"></script>

</body>

</html>