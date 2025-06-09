<?php
session_start();
include("conexion.php");

if (!isset($_SESSION['id'])) {
  header("Location: index.php");
}

$nombre = $_SESSION['nombre'];
$tipo_usuario = $_SESSION['tipo_usuario'];

//traer las store
$stores = "SELECT * FROM store";
$result_stores = mysqli_query($mysqli, $stores);
if (!$result_stores) {
  die("Error in the query: " . mysqli_error($mysqli));
}

//cantidad de prendas 
$inventory = "SELECT SUM(quantity_inventory) as total_items FROM inventory";
$result_inventory = mysqli_query($mysqli, $inventory);
if (!$result_inventory) {
  die("Error in the query: " . mysqli_error($mysqli));
}
// Obtener la fila como array asociativo
$row_invetario = mysqli_fetch_assoc($result_inventory);
// Acceder directamente al valor
$total_items = $row_invetario['total_items'];


$query = "SELECT SUM(total_item) AS total_vendido_hoy ,MAX(`date`) AS ultima_fecha  FROM sell WHERE DATE(date) = CURDATE();";
$result = mysqli_query($mysqli, $query);

if (!$result) {
  die("Error en la consulta: " . mysqli_error($mysqli));
}

$row_ventas_diarias = mysqli_fetch_assoc($result);
$ultima_fecha_venta = $row_ventas_diarias['ultima_fecha'];
$total_vendido_hoy = $row_ventas_diarias['total_vendido_hoy'];

//total devoluciones
$total_devoluciones = "SELECT 
                        SUM(total_item) AS total_devoluciones,
                        COUNT(*) AS cantidad_devoluciones,
                        MAX(`date`) AS ultima_fecha
                        FROM devolutions
                          WHERE MONTH(`date`) = MONTH(CURDATE()) 
                          AND YEAR(`date`) = YEAR(CURDATE());
";
$result_devoluciones = mysqli_query($mysqli, $total_devoluciones);
if (!$result_devoluciones) {
  die("Error in the query: " . mysqli_error($mysqli));
}
$row_devoluciones = mysqli_fetch_assoc($result_devoluciones);
$total_devoluciones = $row_devoluciones['total_devoluciones'];
$total_devoluciones_count = $row_devoluciones['COUNT(*)'];
$ultima_devolucion = $row_devoluciones['ultima_fecha'];

$cantidad_reportes = "SELECT COUNT(*) as total_reportes FROM daily_report WHERE estado_reporte = 1";
$result_reportes = mysqli_query($mysqli, $cantidad_reportes);
if (!$result_reportes) {
  die("Error in the query: " . mysqli_error($mysqli));
}
$row_reportes = mysqli_fetch_assoc($result_reportes);
$total_reportes = $row_reportes['total_reportes'];

// Enhanced KPIs for better dashboard insights
// Monthly sales trend (current vs previous month)
$monthly_sales = "SELECT 
    SUM(CASE WHEN MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE()) THEN total_item ELSE 0 END) as current_month_sales,
    SUM(CASE WHEN MONTH(date) = MONTH(CURDATE() - INTERVAL 1 MONTH) AND YEAR(date) = YEAR(CURDATE() - INTERVAL 1 MONTH) THEN total_item ELSE 0 END) as previous_month_sales,
    COUNT(CASE WHEN MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE()) THEN 1 END) as current_month_orders
    FROM sell WHERE estado_sell = 1";
$result_monthly = mysqli_query($mysqli, $monthly_sales);
$row_monthly = mysqli_fetch_assoc($result_monthly);
$current_month_sales = $row_monthly['current_month_sales'] ?? 0;
$previous_month_sales = $row_monthly['previous_month_sales'] ?? 0;
$current_month_orders = $row_monthly['current_month_orders'] ?? 0;

// Calculate sales growth percentage
$sales_growth = 0;
if ($previous_month_sales > 0) {
    $sales_growth = (($current_month_sales - $previous_month_sales) / $previous_month_sales) * 100;
}

// Low stock items (items with less than 10 units)
$low_stock = "SELECT COUNT(*) as low_stock_items FROM inventory WHERE quantity_inventory > 0 AND quantity_inventory < 10";
$result_low_stock = mysqli_query($mysqli, $low_stock);
$row_low_stock = mysqli_fetch_assoc($result_low_stock);
$low_stock_items = $row_low_stock['low_stock_items'];

// Top performing store this month
$top_store = "SELECT s.store_name, SUM(sell.total_item) as store_sales 
              FROM sell 
              JOIN store s ON sell.id_store = s.id_store 
              WHERE MONTH(sell.date) = MONTH(CURDATE()) AND YEAR(sell.date) = YEAR(CURDATE()) AND sell.estado_sell = 1
              GROUP BY sell.id_store, s.store_name 
              ORDER BY store_sales DESC 
              LIMIT 1";
$result_top_store = mysqli_query($mysqli, $top_store);
$row_top_store = mysqli_fetch_assoc($result_top_store);
$top_store_name = $row_top_store['store_name'] ?? 'N/A';
$top_store_sales = $row_top_store['store_sales'] ?? 0;

// Average order value this month
$avg_order = "SELECT AVG(total_item) as avg_order_value 
              FROM sell 
              WHERE MONTH(date) = MONTH(CURDATE()) AND YEAR(date) = YEAR(CURDATE()) AND estado_sell = 1";
$result_avg_order = mysqli_query($mysqli, $avg_order);
$row_avg_order = mysqli_fetch_assoc($result_avg_order);
$avg_order_value = $row_avg_order['avg_order_value'] ?? 0;

// Return rate calculation
$return_rate = 0;
if ($current_month_orders > 0) {
    $return_rate = ($total_devoluciones_count / $current_month_orders) * 100;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Boxicons CSS -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
  <title>ASWWORKING</title>
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
      --warning: #ffc107;
      --danger: #dc3545;
      --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
      background-color: var(--bg-light);
      color: var(--text-dark);
      overflow-x: hidden;
    }

    /* Navbar */
    .navbar {
      background: linear-gradient(135deg, var(--primary), var(--primary-light));
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 25px;
      height: 70px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
    }

    .logo_item {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
      font-size: 1.2rem;
    }

    .logo_item img {
      height: 40px;
      filter: brightness(0) invert(1);
    }

    #sidebarOpen {
      font-size: 1.5rem;
      cursor: pointer;
      transition: var(--transition);
    }

    #sidebarOpen:hover {
      transform: scale(1.1);
    }

    .navbar_content {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .navbar_content i {
      font-size: 1.2rem;
      cursor: pointer;
      transition: var(--transition);
    }

    .navbar_content i:hover {
      transform: scale(1.1);
      color: var(--secondary-light);
    }

    .profile {
      height: 40px;
      width: 40px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid var(--secondary-light);
      cursor: pointer;
      transition: var(--transition);
    }

    .profile:hover {
      transform: scale(1.1);
      border-color: white;
    }

    /* Sidebar */
    .sidebar {
      background: white;
      width: 270px;
      height: 100vh;
      position: fixed;
      top: 70px;
      left: -270px;
      box-shadow: 4px 0 15px rgba(0, 0, 0, 0.05);
      transition: var(--transition);
      z-index: 999;
      overflow-y: auto;
    }

    .sidebar.active {
      left: 0;
    }

    .menu_content {
      height: calc(100% - 60px);
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .menu_items {
      padding: 15px 0;
    }

    .menu_title {
      color: var(--secondary);
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 1px;
      padding: 10px 25px;
      margin-bottom: 5px;
      border-bottom: 1px solid var(--secondary-light);
    }

    .item {
      list-style: none;
    }

    .nav_link {
      display: flex;
      align-items: center;
      padding: 12px 25px;
      color: var(--text-dark);
      text-decoration: none;
      transition: var(--transition);
      cursor: pointer;
    }

    .nav_link:hover {
      background-color: var(--secondary-light);
      color: var(--primary);
    }

    .navlink_icon {
      margin-right: 15px;
      font-size: 1.1rem;
      color: var(--primary);
    }

    .navlink {
      font-size: 0.95rem;
      font-weight: 500;
    }

    .arrow-left {
      margin-left: auto;
      transition: var(--transition);
    }

    .submenu {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
    }

    .submenu.active {
      max-height: 500px;
    }

    .sublink {
      display: block;
      padding: 10px 25px 10px 65px;
      color: var(--text-dark);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 400;
      transition: var(--transition);
      border-left: 3px solid transparent;
    }

    .sublink:hover {
      background-color: rgba(218, 199, 229, 0.3);
      border-left: 3px solid var(--primary);
      color: var(--primary);
    }

    .bottom_content {
      padding: 20px;
      border-top: 1px solid var(--secondary-light);
    }

    .bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
      color: var(--primary);
      font-weight: 500;
      cursor: pointer;
      padding: 10px;
      border-radius: 6px;
      transition: var(--transition);
    }

    .bottom:hover {
      background-color: var(--secondary-light);
    }

    .bottom i {
      font-size: 1.1rem;
    }

    .collapse_sidebar {
      display: none;
    }

    /* Main Content */
    .main-content {
      margin-top: 70px;
      margin-left: 0;
      padding: 25px;
      min-height: calc(100vh - 70px);
      transition: var(--transition);
    }

    .main-content.active {
      margin-left: 270px;
    }    /* Dashboard Cards */
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    @media (min-width: 1200px) {
      .dashboard-cards {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (max-width: 768px) {
      .dashboard-cards {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 15px;
      }
    }

    .card {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: var(--transition);
      border-left: 4px solid var(--primary);
    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .card-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-color: var(--secondary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--primary);
      font-size: 1.5rem;
    }    .card-title {
      font-size: 0.9rem;
      color: var(--secondary);
      font-weight: 600;
    }

    .card-value {
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--primary);
      margin-bottom: 5px;
    }

    .card-subtitle {
      font-size: 0.75rem;
      color: var(--secondary);
      font-weight: 500;
    }

    .card-footer {
      font-size: 0.8rem;
      color: var(--secondary);
    }

    /* Recent Activity */
    .recent-activity {
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .section-title {
      font-size: 1.2rem;
      font-weight: 600;
      color: var(--primary);
      margin-bottom: 20px;
      padding-bottom: 10px;
      border-bottom: 1px solid var(--secondary-light);
    }

    .activity-list {
      list-style: none;
    }

    .activity-item {
      display: flex;
      padding: 15px 0;
      border-bottom: 1px solid var(--secondary-light);
    }

    .activity-item:last-child {
      border-bottom: none;
    }

    .activity-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--secondary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 15px;
      color: var(--primary);
      font-size: 1rem;
    }

    .activity-content {
      flex: 1;
    }

    .activity-title {
      font-weight: bold;
      font-size: 18px;
      margin-bottom: 5px;
    }

    .activity-time {
      font-size: 18px;
      color: var(--secondary);
    }

    /* Dark Mode */
    body.dark-mode {
      background-color: #1a1a2e;
      color: #f0f0f0;
    }

    body.dark-mode .sidebar,
    body.dark-mode .card,
    body.dark-mode .recent-activity {
      background-color: #16213e;
      color: #f0f0f0;
    }

    body.dark-mode .nav_link,
    body.dark-mode .sublink {
      color: #f0f0f0;
    }

    body.dark-mode .nav_link:hover,
    body.dark-mode .sublink:hover {
      background-color: rgba(99, 43, 139, 0.3);
    }

    body.dark-mode .section-title,
    body.dark-mode .activity-title {
      color: #f0f0f0;
    }

    body.dark-mode .activity-time {
      color: #dac7e5;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .sidebar {
        width: 100%;
        left: -100%;
      }

      .sidebar.active {
        left: 0;
      }

      .main-content.active {
        margin-left: 0;
      }
    }

    /* Añade estos estilos para quitar los puntos y manejar los submenús */
    .menu_items {
      list-style: none;
      /* Esto quita los puntos de la lista */
      padding-left: 0;
      /* Elimina el padding izquierdo por defecto */
    }

    .submenu {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.3s ease;
      display: none;
      /* Ocultamos completamente el submenú inicialmente */
    }

    .submenu.active {
      max-height: 500px;
      display: block;
      /* Mostramos el submenú cuando está activo */
    }

    /* Estilo para el ícono de flecha cuando está activo */
    .arrow-left.rotate {
      transform: rotate(90deg);
    }
  </style>
</head>

<body>
  <!-- navbar -->
  <nav class="navbar">
    <div class="logo_item">
      <i class="bx bx-menu" id="sidebarOpen"></i>
      <img src="img/logo.png" alt="">ASW-WORKING
    </div>

    <div class="navbar_content">
      <i class="bi bi-grid"></i>
      <i class="fa-solid fa-sun" id="darkLight"></i>
      <a href="logout.php"> <i class="fa-solid fa-door-open"></i></a>
      <img src="img/logo.png" alt="" class="profile" />
    </div>
  </nav>

  <!-- sidebar -->
  <nav class="sidebar">
    <div class="menu_content">
      <ul class="menu_items">
        <div class="menu_title menu_dahsboard">MENÚ PRINCIPAL</div>

        <!-- ITEMS -->
        <li class="item">
          <div href="#" class="nav_link submenu_item">
            <span class="navlink_icon">
              <i class="fa-solid fa-shirt"></i>
            </span>
            <span class="navlink">ITEMS</span>
            <i class="bx bx-chevron-right arrow-left"></i>
          </div>
          <ul class="menu_items submenu">
            <a href="code/items/additems.php" class="nav_link sublink">Add Items</a>
            <a href="code/items/showitems.php" class="nav_link sublink">Show Items</a>
          </ul>
        </li>

        <!-- STORE -->
        <li class="item">
          <div href="#" class="nav_link submenu_item">
            <span class="navlink_icon">
              <i class="fa-solid fa-shop"></i>
            </span>
            <span class="navlink">STORE</span>
            <i class="bx bx-chevron-right arrow-left"></i>
          </div>
          <ul class="menu_items submenu">
            <a href="code/stores/seeStore.php" class="nav_link sublink">See Store</a>
          </ul>
        </li>

        <!-- SUCURSAL -->
        <li class="item">
          <div href="#" class="nav_link submenu_item">
            <span class="navlink_icon">
              <i class="fa-solid fa-store"></i>
            </span>
            <span class="navlink">SUCURSAL</span>
            <i class="bx bx-chevron-right arrow-left"></i>
          </div>
          <ul class="menu_items submenu">
            <a href="code/stores/seeSucursal.php" class="nav_link sublink">See Sucursal</a>
          </ul>
        </li>        <!-- DAILY REPORT -->
        <li class="item">
          <div href="#" class="nav_link submenu_item">
            <span class="navlink_icon">
              <i class="fa-solid fa-file-lines"></i>
            </span>
            <span class="navlink">DAILY REPORT</span>
            <i class="bx bx-chevron-right arrow-left"></i>
          </div>
          <ul class="menu_items submenu">
            <a href="code/report/addReport.php" class="nav_link sublink">Daily Report</a>
            <a href="code/report/seeReport.php" class="nav_link sublink">See Report</a>
            <a href="code/report/editLocationFolder.php" class="nav_link sublink">Edit Location & Folder</a>
          </ul>
        </li>

        <!-- SALES -->
        <li class="item">
          <div href="#" class="nav_link submenu_item">
            <span class="navlink_icon">
              <i class="fa-regular fa-money-bill-1"></i>
            </span>
            <span class="navlink">TRANSACTIONS</span>
            <i class="bx bx-chevron-right arrow-left"></i>
          </div>
          <ul class="menu_items submenu">
            <a href="code/sells/sell.php" class="nav_link sublink">Sales</a>
            <a href="code/sells/seeSells.php" class="nav_link sublink">See Sales</a>
            <a href="code/shipping/shipping.php" class="nav_link sublink">Shipping</a>
            <a href="code/shippingReturn/shippingReturn.php" class="nav_link sublink">Shipping Return</a>            <a href="code/discount/seeDiscount.php" class="nav_link sublink">Discounts</a>
            <a href="code/safetclaim/seeSafetClaim.php" class="nav_link sublink">Safe T-Claim/Label Avoid </a>
            <a href="code/cancellations/seeCancellations.php" class="nav_link sublink">Cancellations</a>

            <a href="code/devolutions/seeDevolutions.php" class="nav_link sublink">Returns</a>
          </ul>        </li>        <!-- ANALYTICS -->
        <li class="item">
          <div href="#" class="nav_link submenu_item">
            <span class="navlink_icon">
              <i class="fas fa-chart-bar"></i>
            </span>
            <span class="navlink">ANALYTICS</span>
            <i class="bx bx-chevron-right arrow-left"></i>
          </div>
          <ul class="menu_items submenu">
            <a href="code/analytics/seeAnalytics.php" class="nav_link sublink">See Analytics</a>
          </ul>
        </li>

        <!-- INFORMS -->
        <li class="item">
          <div href="#" class="nav_link submenu_item">
            <span class="navlink_icon">
              <i class="fas fa-file-alt"></i>
            </span>
            <span class="navlink">INFORMS</span>
            <i class="bx bx-chevron-right arrow-left"></i>
          </div>
          <ul class="menu_items submenu">
            <a href="code/informs/generateInforms.php" class="nav_link sublink">Generate Informs</a>
          </ul>
        </li>        <!-- USER -->
        <li class="item">
          <div href="#" class="nav_link submenu_item">
            <span class="navlink_icon">
              <i class="fa-solid fa-users"></i>
            </span>
            <span class="navlink">USER</span>
            <i class="bx bx-chevron-right arrow-left"></i>
          </div>
          <ul class="menu_items submenu">
            <a href="#" class="nav_link sublink">Password</a>
          </ul>
        </li>

        <!-- Upload Items -->
        <li class="item">
          <div href="#" class="nav_link submenu_item">
            <span class="navlink_icon">
              <i class="fa-solid fa-chart-pie"></i>
            </span>
            <span class="navlink">Upload Items</span>
            <i class="bx bx-chevron-right arrow-left"></i>
          </div>
          <ul class="menu_items submenu">
            <a href="code/uploadData/loadData.php" class="nav_link sublink">Subir Excel</a>
          </ul>
        </li>
      </ul>

      <!-- Sidebar Open / Close -->
      <div class="bottom_content">
        <div class="bottom expand_sidebar">
          <span> Expand</span>
          <i class='bx bx-log-in'></i>
        </div>
        <div class="bottom collapse_sidebar">
          <span> Collapse</span>
          <i class='bx bx-log-out'></i>
        </div>
      </div>
    </div>
  </nav>
  <!-- Main Content -->
  <div class="main-content">
    <!-- Dashboard Cards -->
    <div class="dashboard-cards">
      <!-- Monthly Sales with Growth Indicator -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Monthly Sales</div>
            <div class="card-value">$<?= number_format($current_month_sales, 2) ?></div>
            <div class="card-subtitle">
              <?php if ($sales_growth > 0): ?>
                <span style="color: var(--success);"><i class="fas fa-arrow-up"></i> +<?= number_format($sales_growth, 1) ?>%</span>
              <?php elseif ($sales_growth < 0): ?>
                <span style="color: var(--danger);"><i class="fas fa-arrow-down"></i> <?= number_format($sales_growth, 1) ?>%</span>
              <?php else: ?>
                <span style="color: #666;"><i class="fas fa-minus"></i> 0%</span>
              <?php endif; ?>
              vs last month
            </div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-chart-line"></i>
          </div>
        </div>
      </div>

      <!-- Today's Sales -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Today's Sales</div>
            <div class="card-value">$<?= number_format($total_vendido_hoy ?? 0, 2) ?></div>
            <div class="card-subtitle"><?= $current_month_orders ?> orders this month</div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-dollar-sign"></i>
          </div>
        </div>
      </div>

      <!-- Inventory Status -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Inventory Status</div>
            <div class="card-value"><?= number_format($total_items) ?></div>
            <div class="card-subtitle">
              <span style="color: <?= $low_stock_items > 0 ? 'var(--warning)' : 'var(--success)' ?>;">
                <?= $low_stock_items ?> low stock items
              </span>
            </div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-boxes-stacked"></i>
          </div>
        </div>
      </div>

      <!-- Return Rate -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Return Rate</div>
            <div class="card-value"><?= number_format($return_rate, 1) ?>%</div>
            <div class="card-subtitle"><?= $total_devoluciones_count ?> returns this month</div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-rotate-left"></i>
          </div>
        </div>
      </div>

      <!-- Average Order Value -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Avg Order Value</div>
            <div class="card-value">$<?= number_format($avg_order_value, 2) ?></div>
            <div class="card-subtitle">Current month average</div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-receipt"></i>
          </div>
        </div>
      </div>

      <!-- Top Performing Store -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Top Store</div>
            <div class="card-value" style="font-size: 1.2rem;"><?= $top_store_name ?></div>
            <div class="card-subtitle">$<?= number_format($top_store_sales, 2) ?> this month</div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-trophy"></i>
          </div>
        </div>
      </div>

      <!-- Pending Reports -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Pending Reports</div>
            <div class="card-value"><?= $total_reportes ?></div>
            <div class="card-subtitle">Require processing</div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-file-lines"></i>
          </div>
        </div>
      </div>

      <!-- Monthly Revenue Loss (Devolutions) -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Revenue Loss</div>
            <div class="card-value">$<?= number_format($total_devoluciones ?? 0, 2) ?></div>
            <div class="card-subtitle">Devolutions this month</div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-chart-line-down"></i>
          </div>
        </div>
      </div>

    </div>    <!-- Recent Activity & Key Insights -->
    <div class="recent-activity">
      <h3 class="section-title">Key Business Insights</h3>
      <ul class="activity-list">
        <li class="activity-item">
          <div class="activity-icon">
            <i class="fa-solid fa-chart-line"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">Monthly Sales Performance</div>
            <div class="activity-time">
              <?php if ($sales_growth > 0): ?>
                Sales are up <?= number_format($sales_growth, 1) ?>% compared to last month
              <?php elseif ($sales_growth < 0): ?>
                Sales are down <?= abs(number_format($sales_growth, 1)) ?>% compared to last month
              <?php else: ?>
                Sales remain steady compared to last month
              <?php endif; ?>
            </div>
          </div>
        </li>
        
        <li class="activity-item">
          <div class="activity-icon">
            <i class="fa-solid fa-exclamation-triangle"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">Inventory Alert</div>
            <div class="activity-time">
              <?php if ($low_stock_items > 0): ?>
                <?= $low_stock_items ?> items are running low on stock (< 10 units)
              <?php else: ?>
                All items have adequate stock levels
              <?php endif; ?>
            </div>
          </div>
        </li>

        <li class="activity-item">
          <div class="activity-icon">
            <i class="fa-solid fa-trophy"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">Top Performing Store</div>
            <div class="activity-time"><?= $top_store_name ?> leads with $<?= number_format($top_store_sales, 2) ?> this month</div>
          </div>
        </li>

        <li class="activity-item">
          <div class="activity-icon">
            <i class="fa-solid fa-rotate-left"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">Return Rate Status</div>
            <div class="activity-time">
              Current return rate is <?= number_format($return_rate, 1) ?>%
              <?php if ($return_rate > 10): ?>
                <span style="color: var(--danger);"> - Requires attention</span>
              <?php elseif ($return_rate > 5): ?>
                <span style="color: var(--warning);"> - Monitor closely</span>
              <?php else: ?>
                <span style="color: var(--success);"> - Within acceptable range</span>
              <?php endif; ?>
            </div>
          </div>
        </li>

        <?php if ($total_reportes > 0): ?>
        <li class="activity-item">
          <div class="activity-icon">
            <i class="fa-solid fa-clipboard-list"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">Pending Tasks</div>
            <div class="activity-time"><?= $total_reportes ?> daily reports require processing</div>
          </div>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    // Sidebar Toggle
    const sidebar = document.querySelector('.sidebar');
    const sidebarOpen = document.querySelector('#sidebarOpen');
    const mainContent = document.querySelector('.main-content');
    const submenuItems = document.querySelectorAll('.submenu_item');
    const expandSidebar = document.querySelector('.expand_sidebar');
    const collapseSidebar = document.querySelector('.collapse_sidebar');

    sidebarOpen.addEventListener('click', () => {
      sidebar.classList.toggle('active');
      mainContent.classList.toggle('active');
    });

    submenuItems.forEach(item => {
      item.addEventListener('click', () => {
        const submenu = item.nextElementSibling;
        const arrow = item.querySelector('.arrow-left');

        submenu.classList.toggle('active');
        arrow.classList.toggle('rotate');
      });
    });

    expandSidebar.addEventListener('click', () => {
      sidebar.classList.add('active');
      mainContent.classList.add('active');
      expandSidebar.style.display = 'none';
      collapseSidebar.style.display = 'flex';
    });

    collapseSidebar.addEventListener('click', () => {
      sidebar.classList.remove('active');
      mainContent.classList.remove('active');
      expandSidebar.style.display = 'flex';
      collapseSidebar.style.display = 'none';
    });

    // Dark/Light Mode Toggle
    const darkLight = document.querySelector('#darkLight');

    darkLight.addEventListener('click', () => {
      document.body.classList.toggle('dark-mode');

      if (document.body.classList.contains('dark-mode')) {
        darkLight.classList.remove('fa-sun');
        darkLight.classList.add('fa-moon');
      } else {
        darkLight.classList.remove('fa-moon');
        darkLight.classList.add('fa-sun');
      }
    });

    // Rotate arrow when submenu is opened
    document.querySelectorAll('.submenu_item').forEach(item => {
      item.addEventListener('click', function() {
        const arrow = this.querySelector('.arrow-left');
        arrow.classList.toggle('rotate');
      });
    });
  </script>
</body>

</html>