<?php
session_start();
include_once('../../conexion.php');

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not authenticated']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

$id_report = isset($_POST['id_report']) ? trim($_POST['id_report']) : '';
if ($id_report === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing id_report']);
    exit;
}

$stmt = $mysqli->prepare("DELETE FROM daily_report WHERE id_report = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => $mysqli->error]);
    exit;
}
$stmt->bind_param('i', $id_report);
if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Delete failed']);
}
$stmt->close();
?>
