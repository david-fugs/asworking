<?php
session_start();
include "../../conexion.php";
if (!isset($_SESSION['id'])) {
  header("Location: ../../index.php");
}
$nombre = $_SESSION['nombre'];
$tipo_usu = $_SESSION['tipo_usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ASWWORKING | PROMEDIOS</title>
  <link rel="stylesheet" type="text/css" href="../items/css/styles.css">
  <link rel="stylesheet" type="text/css" href="../items/css/estilos2024.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<style>
  body {
    background-color: #f5f3f7;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  .header-container {
    text-align: center;
    padding: 20px 0;
    background-color: #dac7e5;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
  }

  /* Buscador */
  .search-form {
    background: #fff;
    border-radius: 12px;
    padding: 24px 18px 10px 18px;
    box-shadow: 0 4px 16px rgba(99,43,139,0.07);
    max-width: 950px;
    margin: 0 auto 30px auto;
  }
  .search-form label {
    color: #632b8b;
    font-weight: 600;
    margin-bottom: 6px;
  }
  .search-form .form-select, .search-form .form-control {
    border: 1.5px solid #997cab;
    border-radius: 8px;
    padding: 10px 14px;
    font-size: 1rem;
    background: #f8f6fa;
    color: #4a2568;
    transition: border-color 0.3s, box-shadow 0.3s;
    margin-bottom: 8px;
  }
  .search-form .form-select:focus, .search-form .form-control:focus {
    border-color: #632b8b;
    box-shadow: 0 0 0 2px #dac7e5;
    outline: none;
    background: #fff;
  }
  .search-form button[type="submit"] {
    background: linear-gradient(90deg, #632b8b 60%, #997cab 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.1rem;
    padding: 10px 0;
    box-shadow: 0 2px 8px rgba(99,43,139,0.08);
    transition: background 0.2s, transform 0.2s;
    letter-spacing: 0.5px;
  }
  .search-form button[type="submit"]:hover {
    background: linear-gradient(90deg, #4a2568 60%, #632b8b 100%);
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 4px 16px rgba(99,43,139,0.13);
  }

  /* Tabla */
  .table-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 6px 24px rgba(99,43,139,0.09);
    padding: 24px 18px;
    margin: 0 auto 30px auto;
    max-width: 1100px;
  }
  .table-custom-header {
    background: linear-gradient(90deg, #632b8b 60%, #997cab 100%) !important;
    color: #fff !important;
    font-size: 1rem;
    letter-spacing: 0.5px;
    border-radius: 8px 8px 0 0;
  }
  .table-custom-header th {
    background: transparent !important;
    color: #fff !important;
    border: none !important;
    padding: 14px 8px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    font-size: 0.98rem !important;
    text-align: center !important;
    vertical-align: middle !important;
  }
  .table-bordered {
    border: 1.5px solid #dac7e5 !important;
  }
  .table-bordered th, .table-bordered td {
    border: 1px solid #dac7e5 !important;
  }
  .table-striped tbody tr:nth-child(even) {
    background-color: #f8f6fa !important;
  }
  .table-striped tbody tr:hover {
    background-color: #e9e0f3 !important;
    transition: background 0.2s;
  }
  .table td, .table th {
    vertical-align: middle;
    text-align: center;
  }
  .table-container h2 {
    color: #632b8b;
    font-weight: 700;
    margin-bottom: 18px;
    letter-spacing: 0.5px;
  }
  @media (max-width: 900px) {
    .search-form, .table-container { padding: 10px; }
    .table th, .table td { font-size: 0.95rem; }
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
    color: #632b8b;
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
    background: linear-gradient(to right, #632b8b, #dac7e5);
    border-radius: 3px;
  }
  </style>
  <body>
    <div class="header-container">
      <img src="../../img/logo.png" alt="Logo" class="logo">
      <h1 class="page-title"><i class="fa-solid fa-chart-bar"></i> AVERAGES</h1>
    </div>

    <div class="search-form mb-4">
      <form id="promediosForm" class="row g-3 align-items-center justify-content-center">
        <div class="col-md-2">
          <label for="anio" class="form-label">Year</label>
          <select name="anio" id="anio" class="form-select">
            <option value="">All</option>
            <?php
            $years = [];
            $qYears = $mysqli->query("SELECT DISTINCT YEAR(date) as anio FROM sell ORDER BY anio DESC");
            while ($row = $qYears->fetch_assoc()) {
              $years[] = $row['anio'];
            }
            foreach ($years as $y) {
              echo '<option value="' . $y . '">' . $y . '</option>';
            }
            ?>
          </select>
        </div>
        <div class="col-md-2">
          <label for="mes" class="form-label">Month</label>
          <select name="mes" id="mes" class="form-select">
            <option value="">All</option>
            <?php
            $meses = [1=>'January',2=>'February',3=>'March',4=>'April',5=>'May',6=>'June',7=>'July',8=>'August',9=>'September',10=>'October',11=>'November',12=>'December'];
            foreach ($meses as $num=>$nombre) {
              echo '<option value="' . $num . '">' . $nombre . '</option>';
            }
            ?>
          </select>
        </div>
        <div class="col-md-3">
          <label for="id_store" class="form-label">Store</label>
          <select name="id_store" id="id_store" class="form-select">
            <option value="">All</option>
            <?php
            $qStores = $mysqli->query("SELECT id_store, store_name FROM store ORDER BY store_name ASC");
            while ($row = $qStores->fetch_assoc()) {
              echo '<option value="' . $row['id_store'] . '">' . htmlspecialchars($row['store_name']) . '</option>';
            }
            ?>
          </select>
        </div>
        <div class="col-md-3">
          <label for="id_sucursal" class="form-label">Branch</label>
          <select name="id_sucursal" id="id_sucursal" class="form-select">
            <option value="">All</option>
          </select>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
      </form>
    </div>


    <div class="row" style="margin: 0;">
      <div class="col-lg-6 col-12">
        <div class="table-container" id="resultsContainer">
          <h2 class="text-center mb-4">Sales Results</h2>
          <div id="searchResults">
            <table class="table table-bordered table-striped">
              <thead class="table-custom-header">
                <tr>
                  <th>Year</th>
                  <th>Month</th>
                  <th>Store</th>
                  <th>Branch</th>
                  <th>Total Items Sold</th>
                </tr>
              </thead>
              <tbody id="tablaPromediosBody">
                <tr><td colspan="5" class="text-center">No results</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-lg-6 col-12">
        <div class="table-container" id="returnsContainer">
          <h2 class="text-center mb-4">Returns Results</h2>
          <div id="returnsResults">
            <table class="table table-bordered table-striped">
              <thead class="table-custom-header">
                <tr>
                  <th>Year</th>
                  <th>Month</th>
                  <th>Store</th>
                  <th>Total Returns</th>
                </tr>
              </thead>
              <tbody id="tablaReturnsBody">
                <tr><td colspan="4" class="text-center">No results</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-center mt-4 mb-4">
      <a href="../../access.php" class="btn btn-outline-secondary btn-lg" style="border-radius: 30px; font-weight: 600; padding: 10px 32px; box-shadow: 0 2px 8px rgba(99,43,139,0.08);">
        <i class="fa fa-arrow-left me-2"></i> Back
      </a>
    </div>

  <script>
    // Cargar sucursales según tienda seleccionada
    document.getElementById('id_store').addEventListener('change', function() {
      var id_store = this.value;
      var sucursalSelect = document.getElementById('id_sucursal');
      sucursalSelect.innerHTML = '<option value="">Todas</option>';
      if (id_store) {
        fetch('../../code/promedios/getSucursales.php?id_store=' + id_store)
          .then(r => r.json())
          .then(data => {
            data.forEach(function(suc) {
              var opt = document.createElement('option');
              opt.value = suc.id_sucursal;
              opt.textContent = suc.code_sucursal;
              sucursalSelect.appendChild(opt);
            });
          });
      }
    });

    document.getElementById('promediosForm').addEventListener('submit', function(e) {
      e.preventDefault();
      var params = new URLSearchParams(new FormData(this)).toString();
      // Ventas
      fetch('getPromedios.php?' + params)
        .then(r => r.json())
        .then(data => {
          var tbody = document.getElementById('tablaPromediosBody');
          if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">No results</td></tr>';
          } else {
            var html = '';
            const monthNames = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            data.forEach(function(row) {
              const monthName = monthNames[parseInt(row.mes, 10)] || row.mes;
              html += `<tr>
                <td>${row.anio}</td>
                <td>${monthName}</td>
                <td>${row.store_name}</td>
                <td>${row.code_sucursal}</td>
                <td>${row.total_items}</td>
              </tr>`;
            });
            tbody.innerHTML = html;
          }
        });
      // Returns
      fetch('getReturnsPromedios.php?' + params)
        .then(r => r.json())
        .then(data => {
          var tbody = document.getElementById('tablaReturnsBody');
          if (data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-center">No results</td></tr>';
          } else {
            var html = '';
            const monthNames = ["", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            data.forEach(function(row) {
              const monthName = monthNames[parseInt(row.mes, 10)] || row.mes;
              html += `<tr>
                <td>${row.anio}</td>
                <td>${monthName}</td>
                <td>${row.store_name}</td>
                <td>${row.total_returns}</td>
              </tr>`;
            });
            tbody.innerHTML = html;
          }
        });
    });
  </script>
</body>
</html>
