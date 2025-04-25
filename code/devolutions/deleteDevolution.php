<?php
session_start();
include("../../conexion.php");

$response = array(); // Para almacenar la respuesta

if (isset($_POST['id_devolution'])) {
  $id_devolution = $_POST['id_devolution'];

  $stmt = $mysqli->prepare("DELETE FROM devolutions WHERE id_devolution = ?");
  $stmt->bind_param("s", $id_devolution);

  if ($stmt->execute()) {
    $response['status'] = 'success';
    $response['message'] = 'The record has been deleted.';
  } else {
    $response['status'] = 'error';
    $response['message'] = 'Error deleting the record.';
  }

  $stmt->close();
  $mysqli->close();
} else {
  $response['status'] = 'no_id';
  $response['message'] = 'No ID provided.';
}

// Devuelve la respuesta en formato JSON
echo json_encode($response);
?>
