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
    }

    /* Dashboard Cards */
    .dashboard-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
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
    }

    .card-title {
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
            <a href="code/items/addrecord.php" class="nav_link sublink">Record</a>
            <a href="code/items/showrecord.php" class="nav_link sublink">Show Record</a>
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
        </li>

        <!-- DAILY REPORT -->
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
          </ul>
        </li>

        <!-- SALES -->
        <li class="item">
          <div href="#" class="nav_link submenu_item">
            <span class="navlink_icon">
              <i class="fa-regular fa-money-bill-1"></i>
            </span>
            <span class="navlink">SALES</span>
            <i class="bx bx-chevron-right arrow-left"></i>
          </div>
          <ul class="menu_items submenu">
            <a href="code/sells/sell.php" class="nav_link sublink">Sales</a>
            <a href="code/sells/seesells.php" class="nav_link sublink">See Sales</a>
          </ul>
        </li>

        <!-- DEVOLUTIONS -->
        <li class="item">
          <div href="#" class="nav_link submenu_item">
            <span class="navlink_icon">
              <i class="fa-solid fa-arrows-rotate"></i>
            </span>
            <span class="navlink">DEVOLUTIONS</span>
            <i class="bx bx-chevron-right arrow-left"></i>
          </div>
          <ul class="menu_items submenu">
            <a href="code/devolutions/seeDevolutions.php" class="nav_link sublink">See Devolutions</a>
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
        </li>

        <!-- USER -->
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
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Total Items</div>
            <div class="card-value"><?= $total_items; ?></div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-shirt"></i>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Today's Sales</div>
            <div class="card-value"> $ <?= $total_vendido_hoy ?></div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-dollar-sign"></i>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Devolutions This Month</div>
            <div class="card-value"> <?= $total_devoluciones_count ?></div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-file-lines"></i>
          </div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Pending Reports</div>
            <div class="card-value"><?= $total_reportes ?></div>
          </div>
          <div class="card-icon">
            <i class="fa-solid fa-file-lines"></i>
          </div>
        </div>
      </div>

    </div>

    <!-- Recent Activity -->
    <div class="recent-activity">
      <h3 class="section-title">Recent Activity</h3>
      <ul class="activity-list">
        <li class="activity-item">
          <div class="activity-icon">
            <i class="fa-solid fa-dollar-sign"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">Devolutions This Month </div>
            <div class="activity-time"> $ <?= $total_devoluciones ?></div>
          </div>
        </li>
        <li class="activity-item">
          <div class="activity-icon">
            <i class="fa-solid fa-file-lines"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">Latest Devolution </div>
            <div class="activity-time"><?= $ultima_devolucion ?></div>
          </div>
        </li>
        <li class="activity-item">
          <div class="activity-icon">
            <i class="fa-solid fa-arrows-rotate"></i>
          </div>
          <div class="activity-content">
            <div class="activity-title">Latest Sell</div>
            <div class="activity-time"><?= $ultima_fecha_venta ?> </div>
          </div>
        </li>
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