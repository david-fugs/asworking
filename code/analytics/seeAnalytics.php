<?php
session_start();
include "../../conexion.php";
// if (!isset($_SESSION['id'])) {
//   header("Location: ../../index.php");
// }
$nombre = $_SESSION['nombre'];
$tipo_usu = $_SESSION['tipo_usuario'];

// Obtener lista de sucursales para el filtro
$querySucursales = "SELECT DISTINCT s.id_sucursal, s.code_sucursal, st.store_name 
                    FROM sucursal s 
                    INNER JOIN store st ON s.id_store = st.id_store 
                    ORDER BY st.store_name, s.code_sucursal ASC";
$resultSucursales = $mysqli->query($querySucursales);

// Obtener años disponibles
$queryYears = "SELECT DISTINCT YEAR(date) as year FROM sell ORDER BY year DESC";
$resultYears = $mysqli->query($queryYears);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>ASWWORKING | ANALYTICS</title>
  <link rel="stylesheet" type="text/css" href="../items/css/styles.css">
  <link rel="stylesheet" type="text/css" href="../items/css/estilos2024.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

  /* Filter form */
  .filter-form {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    margin: 20px auto;
    max-width: 1200px;
  }

  .filter-form select, .filter-form input {
    border: 1px solid var(--secondary);
    border-radius: 6px;
    padding: 10px 15px;
    margin: 5px;
    transition: all 0.3s ease;
  }

  .filter-form select:focus, .filter-form input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
    outline: none;
  }

  .btn-filter {
    background: linear-gradient(to bottom, var(--primary), var(--primary-light));
    color: white;
    border: none;
    cursor: pointer;
    font-weight: 600;
    border-radius: 6px;
    padding: 10px 20px;
    margin: 5px;
  }

  .btn-filter:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  }

  /* Chart containers */
  .chart-container {
    background: white;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin: 20px auto;
    max-width: 1200px;
  }

  .chart-wrapper {
    position: relative;
    height: 400px;
  }

  /* Summary cards */
  .summary-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin: 20px auto;
    max-width: 1200px;
  }

  .summary-card {
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    text-align: center;
  }

  .summary-card h3 {
    color: var(--primary);
    margin-bottom: 10px;
  }

  .summary-card .amount {
    font-size: 2rem;
    font-weight: 700;
    margin: 10px 0;
  }

  .amount.positive {
    color: var(--success);
  }

  .amount.negative {
    color: var(--danger);
  }

  /* Loading animation */
  .loading {
    text-align: center;
    padding: 40px;
  }

  .spinner-border {
    color: var(--primary);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .filter-form {
      padding: 15px;
    }
    
    .chart-wrapper {
      height: 300px;
    }
    
    .summary-card .amount {
      font-size: 1.5rem;
    }
  }
</style>

<body>  <div class="header-container">
    <img src="../../img/logo.png" alt="ASWWORKING Logo" class="logo">
    <h1 class="page-title"><i class="fa-solid fa-chart-line"></i> SALES ANALYTICS</h1>
  </div>

  <!-- Filter Form -->
  <div class="filter-form">
    <form id="filterForm" class="row g-3 align-items-center">      <div class="col-md-3">
        <label for="year" class="form-label">Year:</label>        <select name="year" id="year" class="form-select">
          <option value="">All years</option>
          <?php
          while ($year = $resultYears->fetch_assoc()) {
            // No preseleccionar ningún año por defecto para mostrar todos los datos
            echo "<option value='{$year['year']}'>{$year['year']}</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-4">
        <label for="sucursal" class="form-label">Branch:</label>
        <select name="sucursal" id="sucursal" class="form-select">
          <option value="">All branches</option>
          <?php
          while ($sucursal = $resultSucursales->fetch_assoc()) {
            echo "<option value='{$sucursal['id_sucursal']}'>{$sucursal['store_name']} - {$sucursal['code_sucursal']}</option>";
          }
          ?>
        </select>
      </div>
      <div class="col-md-3">
        <label for="month" class="form-label">Month:</label>
        <select name="month" id="month" class="form-select">
          <option value="">All months</option>
          <option value="1">January</option>
          <option value="2">February</option>
          <option value="3">March</option>
          <option value="4">April</option>
          <option value="5">May</option>
          <option value="6">June</option>
          <option value="7">July</option>
          <option value="8">August</option>
          <option value="9">September</option>
          <option value="10">October</option>
          <option value="11">November</option>
          <option value="12">December</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">&nbsp;</label>
        <button type="submit" class="btn btn-filter w-100">
          <i class="fas fa-search"></i> Filter
        </button>
      </div>
    </form>
  </div>
  <!-- Summary Cards -->
  <div class="summary-cards" id="summaryCards">
    <div class="summary-card">
      <h3><i class="fas fa-dollar-sign"></i> Total Sales</h3>
      <div class="amount positive" id="totalSales">$0.00</div>
    </div>
    <div class="summary-card">
      <h3><i class="fas fa-minus-circle"></i> Total Discounts</h3>
      <div class="amount negative" id="totalDiscounts">$0.00</div>
    </div>
    <div class="summary-card">
      <h3><i class="fas fa-plus-circle"></i> Total Reimbursements</h3>
      <div class="amount positive" id="totalReimbursements">$0.00</div>
    </div>
    <div class="summary-card">
      <h3><i class="fas fa-chart-line"></i> Net Profit</h3>
      <div class="amount" id="netProfit">$0.00</div>
    </div>
  </div>
  <!-- Charts -->
  <div class="chart-container">
    <h3 class="text-center mb-4"><i class="fas fa-chart-bar"></i> Monthly Analysis - Bar Chart</h3>
    <div class="chart-wrapper">
      <canvas id="monthlyBarChart"></canvas>
    </div>
  </div>

  <div class="chart-container">
    <h3 class="text-center mb-4"><i class="fas fa-chart-line"></i> Monthly Trend - Line Chart</h3>
    <div class="chart-wrapper">
      <canvas id="monthlyLineChart"></canvas>
    </div>
  </div>

  <div class="chart-container">
    <h3 class="text-center mb-4"><i class="fas fa-chart-line"></i> Last Year Trend - Comparison Chart</h3>
    <div class="chart-wrapper">
      <canvas id="lastYearLineChart"></canvas>
    </div>
  </div>

  <div class="chart-container">
    <h3 class="text-center mb-4"><i class="fas fa-chart-pie"></i> Distribution by Categories</h3>
    <div class="chart-wrapper">
      <canvas id="categoryPieChart"></canvas>
    </div>
  </div>  <br /><a href="../../access.php"><img src='../../img/atras.png' width="72" height="72" title="back" /></a><br>

  <script src="analytics.js"></script>
</body>

</html>
