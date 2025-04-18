<?php
session_start();

if (!isset($_SESSION['id'])) {
  header("Location: index.php");
}

$nombre = $_SESSION['nombre'];
$tipo_usuario = $_SESSION['tipo_usuario'];
?>

<!DOCTYPE html>
<!-- Coding by CodingNepal || www.codingnepalweb.com -->
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Boxicons CSS -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/fed2435e21.js" crossorigin="anonymous"></script>
  <title>ASWWORKING</title>
  <link rel="stylesheet" href="menu/style.css" />
</head>

<body>
  <!-- navbar -->
  <nav class="navbar">
    <div class="logo_item">
      <i class="bx bx-menu" id="sidebarOpen"></i>
      <img src="img/logo.png" alt=""></i>ASW-WORKING
    </div>


    <div class="navbar_content">
      <i class="bi bi-grid"></i>
      <i class="fa-solid fa-sun" id="darkLight"></i><!--<i class='bx bx-sun' id="darkLight"></i>-->
      <a href="logout.php"> <i class="fa-solid fa-door-open"></i></a>
      <img src="img/logo.png" alt="" class="profile" />
    </div>
  </nav>


  <!--********************************INICIA MENÚ DIGITADOR********************************-->

  <?php if ($tipo_usuario == 2) { ?>
    <!-- sidebar -->
    <nav class="sidebar">
      <div class="menu_content">
        <ul class="menu_items">
          <div class="menu_title menu_dahsboard"></div>
          <!-- duplicate or remove this li tag if you want to add or remove navlink with submenu -->
          <!-- start -->
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
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-shop"></i>
                <!--<i class="bx bx-home-alt"></i>-->
              </span>
              <span class="navlink">STORE</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>
            <ul class="menu_items submenu">
              <a href="code/stores/seeStore.php" class="nav_link sublink">See Store</a>
            </ul>
          </li>
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

          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-file-lines"></i> </span>
              <span class="navlink">DAILY REPORT</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>
            <ul class="menu_items submenu">
              <a href="code/report/addReport.php" class="nav_link sublink">Daily Report</a>
              <a href="code/report/seeReport.php" class="nav_link sublink">See Report</a>
            </ul>
          </li>
          <li class="item">
            <div href="#" class="nav_link submenu_item">
              <span class="navlink_icon">
                <i class="fa-solid fa-shop"></i>
                <!--<i class="bx bx-home-alt"></i>-->
              </span>

              <span class="navlink">SELLS</span>
              <i class="bx bx-chevron-right arrow-left"></i>
            </div>

            <ul class="menu_items submenu">
              <a href="code/sells/sell.php" class="nav_link sublink">Sell</a>
              <a href="code/sells/seesells.php" class="nav_link sublink">See Sells</a>
            </ul>
          </li>


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

          <!-- end -->
        </ul>


        <!--<ul class="menu_items">
          <div class="menu_title menu_editor"></div>
          <!-- duplicate these li tag if you want to add or remove navlink only -->
        <!-- Start -->
        <!--<li class="item">
            <a href="#" class="nav_link">
              <span class="navlink_icon">
                <i class="bx bxs-magic-wand"></i>
              </span>
              <span class="navlink">Magic build</span>
            </a>
          </li>-->
        <!-- End -->

        <!--<li class="item">
            <a href="#" class="nav_link">
              <span class="navlink_icon">
                <i class="bx bx-loader-circle"></i>
              </span>
              <span class="navlink">Filters</span>
            </a>
          </li>
          <li class="item">
            <a href="#" class="nav_link">
              <span class="navlink_icon">
                <i class="bx bx-filter"></i>
              </span>
              <span class="navlink">Filter</span>
            </a>
          </li>
          <li class="item">
            <a href="#" class="nav_link">
              <span class="navlink_icon">
                <i class="bx bx-cloud-upload"></i>
              </span>
              <span class="navlink">Upload new</span>
            </a>
          </li>
        </ul>
        <ul class="menu_items">
          <div class="menu_title menu_setting"></div>
          <li class="item">
            <a href="#" class="nav_link">
              <span class="navlink_icon">
                <i class="bx bx-flag"></i>
              </span>
              <span class="navlink">Notice board</span>
            </a>
          </li>
          <li class="item">
            <a href="#" class="nav_link">
              <span class="navlink_icon">
                <i class="bx bx-medal"></i>
              </span>
              <span class="navlink">Award</span>
            </a>
          </li>
          <li class="item">
            <a href="#" class="nav_link">
              <span class="navlink_icon">
                <i class="bx bx-cog"></i>
              </span>
              <span class="navlink">Setting</span>
            </a>
          </li>
          <li class="item">
            <a href="#" class="nav_link">
              <span class="navlink_icon">
                <i class="bx bx-layer"></i>
              </span>
              <span class="navlink">Features</span>
            </a>
          </li>
        </ul>-->

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
  <?php } ?>




  <!-- JavaScript -->
  <script src="menu/script.js"></script>
</body>

</html>