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
          <th>Total Item</th>
        </tr>
      </thead>
      <tbody>
        <?php include "getSells.php"; ?>
      </tbody>
    </table>
  </div>

  <br /><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="back" /></a><br>

  <!-- Script para inicializar DataTables -->


  <script src="scriptSeeSells.js"></script>
</body>

</html>