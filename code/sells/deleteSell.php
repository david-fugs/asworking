<?php
session_start();
include("../../conexion.php");

if (isset($_POST['id_sell'])) {
  $id_sell = $_POST['id_sell'];

  $stmt = $mysqli->prepare("DELETE FROM sell WHERE id_sell = ?");
  $stmt->bind_param("s", $id_sell);

  if ($stmt->execute()) {
    echo "success";
  } else {
    echo "error";
  }

  $stmt->close();
  $mysqli->close();
} else {
  echo "no_id";
}
?>