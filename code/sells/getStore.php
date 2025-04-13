<?php
// getStores.php

session_start();
include("../../conexion.php");

if (!isset($_SESSION['id'])) {
  header("Location: ../../index.php");
  exit();
}

$queryTiendas = "SELECT id_store, store_name FROM store ORDER BY store_name ASC";
$resultTiendas = $mysqli->query($queryTiendas);

