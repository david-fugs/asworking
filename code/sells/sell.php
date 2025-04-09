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
        <input name="upc" type="text" id="upc" placeholder="UPC">

        <input name="item_name" id="item_name" type="text" placeholder="Item Name">
        <input name="quantitySell" type="number" id="quantitySell" placeholder="Quantity">
        <input name="priceItem" type="text" id="priceItem" placeholder="Price Item">
        <input type="text" id="brandItemInput" name="brand" placeholder="Brand">
        <input type="hidden" name="comisionItem" id="comisionItem">
        <input type="number" name="receivedShipping" id="receivedShipping" placeholder="Received Shipping">
        <input type="number" name="payedShipping" id="payedShipping" placeholder="Payed Shipping">

        <div class="form-inline-group">
          <select id="tienda" name="id_store" class="form-select custom-inline">
            <option value="">-- Selecciona una tienda --</option>
            <?php
            while ($tienda = $resultTiendas->fetch_assoc()) {
              echo "<option value='{$tienda['id_store']}'>{$tienda['store_name']}</option>";
            }
            ?>
          </select>

          <select name="sucursal" id="sucursal" class="form-select custom-inline">
            <option value="">-- Primero selecciona una tienda --</option>
          </select>

          <!-- Campo solo lectura para mostrar el total calculado -->
          <input name="ref" type="text" id="UnitTotal" placeholder="Unit Total" readonly class="custom-inline">

          <button type="submit" id="validateButton" class="btn btn-success custom-inline">Validar</button>
        </div>
      </form>


      <table id="tableItems" border="1"
        style="width: 90%; margin: 20px auto; text-align: center; border-collapse: collapse;">
        <thead style="background-color:rgb(10, 9, 14); color: white;">
          <tr>
            <th>ITEM</th>
            <th>UPC</th>
            <th>CANTIDAD</th>
            <th>TIENDA</th>
            <th>CODIGO-Sucursal</th>
            <th>BRAND</th>
            <th>COMISIÓN</th>
            <th>FECHA</th>
            <th>PAGO RECIVIDO</th>
            <th>PAGO ENVIADO</th>
            <th>COSTO Unitario</th>
            <th>TOTAL ARTICULO</th>
            <th>ELIMINAR</th>

          </tr>
        </thead>
        <tbody id="bodyTable">
          <!-- Aquí se agregarán las filas dinámicamente con JavaScript -->
        </tbody>
      </table>
      <button type="button" id="saveSellButton">Save Sell</button>
    </div>
  </div>
  <center>
    <br /><a href="../access.php"><img src='../../img/atras.png' width="72" height="72" title="Regresar" /></a>
  </center>

  <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
  <script src="scriptSell.js"></script>

</body>

</html>