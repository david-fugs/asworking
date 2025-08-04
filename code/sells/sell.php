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
  <title>ASWWORKING | SALES</title>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
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
      font-size: 16px;
    }

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
      color: var(--text-dark);
    }

    /* Header styles */
    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 0;
      background-color: var(--secondary-light);
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .logo {
      height: 80px;
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
      text-align: center;
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
      padding: 10px;
      border-radius: 50%;
      transition: all 0.3s ease;
    }

    .back-btn:hover {
      background-color: rgba(93, 51, 122, 0.1);
      color: var(--primary);
      transform: translateX(-3px);
    }

    /* Form styles */
    .form-container {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      margin-bottom: 30px;
    }

    .form-label {
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 5px;
    }

    .form-control,
    .form-select {
      border: 1px solid var(--secondary);
      border-radius: 6px;
      padding: 10px 15px;
      transition: all 0.3s ease;
    }

    .form-control:focus,
    .form-select:focus {
      border-color: var(--primary);
      box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
      outline: none;
    }

    /* Button styles */
    .btn {
      border-radius: 30px;
      padding: 10px 25px;
      font-weight: 600;
      transition: all 0.3s ease;
      border: none;
    }

    .btn-success {
      background: #4a2568;
      color: white;
    }

    .btn-success:hover {
      background: #4a2568;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-primary {
      background: linear-gradient(to bottom, var(--primary), var(--primary-light));
      color: white;
    }

    .btn-primary:hover {
      background: linear-gradient(to bottom, var(--primary-light), var(--primary));
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
      overflow-x: auto;
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
      padding: 12px 10px;
      text-align: center;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      border-bottom: 2px solid rgba(255, 255, 255, 0.1);
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
      padding: 10px 8px;
      border-bottom: 1px solid rgba(153, 124, 171, 0.3);
      color: var(--text-dark);
      font-size: 0.85rem;
      transition: all 0.2s ease;
      text-align: center;
      vertical-align: middle;
    }

    /* Delete button in table */
    .btn-delete {
      color: #dc3545;
    }

    .btn-delete:hover {
      background-color: rgb(236, 221, 223);
      transform: scale(1.1);
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .logo {
        height: 60px;
      }

      .page-title {
        font-size: 1.5rem;
      }

      .form-control,
      .form-select {
        padding: 8px 12px;
      }

      .btn {
        padding: 8px 20px;
        font-size: 0.9rem;
      }

      thead th {
        font-size: 0.65rem;
        padding: 8px 5px;
      }

      tbody td {
        font-size: 0.75rem;
        padding: 8px 5px;
      }
    }

    /* Custom styles for this page */
    .flex-container {
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .form-section {
      width: 100%;
      max-width: 1200px;
    }

    .save-button-container {
      text-align: right;
      padding: 50px;
      margin-bottom: 50px;
    }

    /* Estilos para la sección de totales */
    #tableTotals tr {
      border-top: 3px solid var(--primary) !important;
    }

    #tableTotals td {
      padding: 15px 8px !important;
      font-weight: 600;
      background-color: #f8f9fa !important;
    }

    #tableTotals .form-control-sm {
      font-size: 0.9rem;
      font-weight: 600;
      border: 2px solid var(--primary);
      text-align: center;
    }

    #tableTotals .form-control-sm:focus {
      border-color: var(--success);
      box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.2);
    }

    #totalAllItems {
      color: var(--primary);
      font-size: 1.1rem;
    }

    #finalOrderTotal {
      color: var(--success) !important;
      font-size: 1.2rem;
      font-weight: 700;
    }
  </style>
</head>

<body>
  <div class="header-container">
    <div class="container text-center">
      <img src='../../img/logo.png' class="logo" alt="ASWWORKING Logo">
    </div>
  </div>
  <div class="flex-container">


    <h1 class="page-title"><i class="fa-solid fa-file-signature"></i> SALES</h1>

    <div class="form-section">
      <div class="form-container">
        <div class="mb-3">
          <label for="sellDate" class="form-label">Date</label>
          <input type="date" class="form-control" name="sellDate" id="sellDate">
        </div>

        <!-- Selectores de Tienda y Sucursal -->
        <div class="row g-3 mb-3">
          <div class="col-md-6" style="display:none;">
            <label for="tienda" class="form-label" style="display:none;">Marketplace</label>
            <select id="tienda" name="tienda" class="form-select">
              <option value="">--Select a store--</option>
              <?php
              $resultTiendas->data_seek(0);
              while ($tienda = $resultTiendas->fetch_assoc()) {
                echo "<option value='{$tienda['id_store']}'>{$tienda['store_name']}</option>";
              }
              ?>
            </select>
          </div>
          <div class="col-md-6" style="display:none;">
            <label for="sucursal" class="form-label" style="display:none;">Store Code</label>
            <select name="sucursal" id="sucursal" class="form-select">
              <option value="">--First select store--</option>
            </select>
          </div>
        </div>

        <form class="form">
          <div class="container">
            <div class="row g-3">
              <div class="col-md-3">
                <label for="upc" class="form-label">UPC</label>
                <input name="upc" type="text" id="upc" class="form-control">
              </div>
              <div class="col-md-3">
                <label for="sku" class="form-label">SKU</label>
                <input name="sku" type="text" id="sku" class="form-control">
              </div>

              <div class="col-md-3">
                <label for="item_name" class="form-label">Item Name</label>
                <input name="item_name" id="item_name" type="text" class="form-control">
              </div>
              <?php if ($_SESSION['tipo_usuario'] == 2): ?>
                <div class="col-md-3">
                  <label for="itemCost" class="form-label">Item Cost</label>
                  <input name="itemCost" type="text" id="itemCost" class="form-control">
                </div>
              <?php endif; ?>

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
              <!-- campo de Tax -->
              <div class="col-md-3">
                <label for="tax" class="form-label">Tax</label>
                <input type="number" name="tax" id="taxItem" class="form-control">
              </div>              <!-- campo de Withheld Tax -->
              <div class="col-md-3">
                <label for="withheld_tax" class="form-label">Withheld Tax</label>
                <input type="number" name="withheld_tax" id="withheld_tax" step="0.01" min="0" class="form-control">
              </div>

              <!-- <div class="col-md-3">
                <label for="payedShipping" class="form-label">Paid Shipping</label>
                <input type="number" name="payedShipping" id="payedShipping" class="form-control">
              </div> -->

              <div class="col-md-3">
                <label for="" class="form-label">Incentives offered </label>
                <input type="number" name="incentives" id="incentives" step="0.01" min="0" class="form-control">
              </div>
              <div class="col-md-3">
                <label for="" class="form-label">International Fee</label>
                <input type="number" name="international_fee" id="international_fee" step="0.01" min="0" class="form-control">
              </div>
              <div class="col-md-3">
                <label for="ad_fee" class="form-label">Advertising Fee</label>
                <input type="number" name="ad_fee" id="ad_fee" step="0.01" min="0" class="form-control">
              </div>
              <div class="col-md-3">
                <label for="other_fee" class="form-label">others Fees</label>
                <input type="number" name="other_fee" id="other_fee" step="0.01" min="0" class="form-control">
              </div>
              <div class="col-md-3">
                <label for="quantitySell" class="form-label">Quantity</label>
                <input type="number" name="quantitySell" id="quantitySell" step="0.01" min="0" class="form-control">
              </div>
              <?php if ($_SESSION['tipo_usuario'] == 2) : ?>
                <div class="col-md-3">
                  <label for="UnitTotal" class="form-label">Total Order</label>
                  <input name="ref" type="text" id="UnitTotal" readonly class="form-control">
                </div>
                <div class="col-md-3">
                  <label for="" class="form-label"> Item Profit </label>
                  <input name="profit" type="text" id="itemProfit" readonly class="form-control">
                </div>
                <div class="col-md-3">
                  <label for="" class="form-label"> Markup Percentage </label>
                  <input name="ref" type="text" id="markup" readonly class="form-control">
                </div>
                <div class="col-md-3">
                  <label for="" class="form-label"> Profit Percentage on Cost </label>
                  <input name="ref" type="text" id="profitMargin" readonly class="form-control">
                </div>
              <?php endif; ?>
              <div class="col-12 text-end mt-3">
                <button type="submit" id="validateButton" class="btn btn-success">
                  <i class="fas fa-check"></i> Validate
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>

      <!-- Sección de configuración de orden (después del botón Validate) -->
      <div class="form-container" id="orderConfigSection" style="display: none;">
        <h5 class="mb-3" style="color: var(--primary); font-weight: 600;">
          <i class="fas fa-cog"></i> Order Configuration
        </h5>
        <div class="container">
          <div class="row g-3">
            <div class="col-md-3">
              <label for="orderMarketplace" class="form-label">Marketplace</label>
              <select id="orderMarketplace" name="order_store" class="form-select">
                <option value="">--Select a store--</option>
                <?php
                $resultTiendas->data_seek(0);
                while ($tienda = $resultTiendas->fetch_assoc()) {
                  echo "<option value='{$tienda['id_store']}'>{$tienda['store_name']}</option>";
                }
                ?>
              </select>
            </div>
            
            <div class="col-md-3">
              <label for="orderStoreCod" class="form-label">Store Cod</label>
              <select name="order_sucursal" id="orderStoreCod" class="form-select">
                <option value="">--First select a store--</option>
              </select>
            </div>
            
            <!-- <div class="col-md-3">
              <label for="orderFinalFee" class="form-label">Final Fee</label>
              <input type="number" name="order_final_fee" id="orderFinalFee" step="0.01" min="0" class="form-control">
            </div>
            
            <div class="col-md-3">
              <label for="orderFixedCharge" class="form-label">Fixed Charge</label>
              <input type="number" name="order_fixed_charge" id="orderFixedCharge" step="0.01" min="0" class="form-control">
            </div> -->
          </div>
        </div>
      </div>

      <div class="">
        <table id="tableItems" class="">
          <thead>
            <tr>
              <!-- <th>Item</th> -->
              <th>UPC</th>
              <th>Quantity</th>
              <!-- <th>Brand</th> -->
              <!-- <th>Date</th> -->
              <th>Shipping Received</th>
              <th>Tax</th>
              <th>Withheld Tax</th>
              <!-- <th>Paid Shipping</th> -->
              <th>Price Item</th>
              <th>Total Item</th>
              <th>Delete</th>
            </tr>
          </thead>
          <tbody id="bodyTable">
            <!-- Aquí se agregarán las filas dinámicamente con JavaScript -->
          </tbody>
          <tfoot id="tableTotals" style="display: none;">
            <!-- Nueva fila para mostrar la información de la orden -->
            <tr style="background-color: #e3f2fd; font-weight: bold; border-top: 2px solid var(--primary);">
            
              <td></td>
            </tr>
            <tr style="background-color: #f8f9fa; font-weight: bold; border-top: 2px solid #dee2e6;">
              <td colspan="2" style="text-align: center; font-size: 1rem; color: var(--primary);">
                <strong>TOTAL ALL ITEMS</strong>
              </td>
              <td colspan="5" style="text-align: right; font-size: 1rem;">
                <strong id="totalAllItems">$0.00</strong>
              </td>
              <td></td>
            </tr>
            <tr style="background-color: #f0f0f0; font-weight: bold;">
              <td colspan="2" style="text-align: center; font-size: 0.9rem; color: var(--primary);">
                <strong>ORDER FEES</strong>
              </td>
              <td style="text-align: center; font-size: 0.8rem;">Final Fee</td>
              <td style="text-align: center; font-size: 0.8rem;">Fixed Charge</td>
              <td colspan="2" style="text-align: right; font-size: 1rem;">
                <strong>FINAL TOTAL: <span id="finalOrderTotal" style="color: var(--success);">$0.00</span></strong>
              </td>
              <td style="text-align: center;">
                <input type="number" id="footerFinalFee" step="0.01" min="0" class="form-control form-control-sm" style="font-weight: bold; text-align: center;" placeholder="0.00">
              </td>
              <td style="text-align: center;">
                <input type="number" id="footerFixedCharge" step="0.01" min="0" class="form-control form-control-sm" style="font-weight: bold; text-align: center;" placeholder="0.00">
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <div class="save-button-container">
        <button type="button" id="saveSellButton" class="btn btn-primary">
          <i class="fas fa-save"></i> Save Sale
        </button>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Font Awesome JS -->
  <script src="https://www.jose-aguilar.com/scripts/fontawesome/js/all.min.js" data-auto-replace-svg="nest"></script>
  <!-- Custom JS -->
  <script src="scriptSell.js"></script>
</body>

</html>