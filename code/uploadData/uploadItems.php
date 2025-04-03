<?php
require '../../vendor/autoload.php';
include("../../conexion.php"); // Se incluye la conexión a la BD

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
    die("Error al subir el archivo.");
}

$filePath = $_FILES['excelFile']['tmp_name'];
$fileExt = pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION);

if ($fileExt === 'xlsx') {
    $reader = ReaderEntityFactory::createXLSXReader();
} elseif ($fileExt === 'xls') {
    $reader = ReaderEntityFactory::createXLSReader();
} else {
    die("Formato de archivo no permitido. Solo .xlsx y .xls.");
}

$reader->open($filePath);

$id_usu = $_SESSION['id_usu'] ?? 1;

foreach ($reader->getSheetIterator() as $sheet) {
    $firstRow = true;

    foreach ($sheet->getRowIterator() as $row) {
        if ($firstRow) {
            $firstRow = false;
            continue; // Saltar encabezados
        }

        $cells = $row->getCells();

        // Convertir la fecha de Excel a formato Y-m-d
        $dateExcel = $cells[0]->getValue();
        if (is_numeric($dateExcel)) {
            $dateFormatted = date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($dateExcel));
        } else {
            // Si la fecha ya está en formato texto, intentamos formatearla correctamente
            $dateFormatted = date('Y-m-d', strtotime($dateExcel));
        }
        // Escapar valores para evitar problemas con comillas
        $upc_sku = $mysqli->real_escape_string($cells[1]->getValue());
        $brand = $mysqli->real_escape_string($cells[2]->getValue());
        $item = $mysqli->real_escape_string($cells[3]->getValue());
        $ref = $mysqli->real_escape_string($cells[4]->getValue());
        $color = $mysqli->real_escape_string($cells[5]->getValue());
        $size = $mysqli->real_escape_string($cells[6]->getValue());
        $category = $mysqli->real_escape_string($cells[7]->getValue());
        $cost =($cells[8]->getValue());
        $weight = ($cells[9]->getValue());
        $inventory = ($cells[11]->getValue());

        // Consulta SQL
        $sql = "INSERT INTO items (upc_item, date_item, brand_item, item_item, ref_item, color_item, size_item, category_item, cost_item, weight_item, inventory_item, estado_item, fecha_alta_item, id_usu) 
                VALUES ('$upc_sku', '$dateFormatted', '$brand', '$item', '$ref', '$color', '$size', '$category', '$cost', '$weight', '$inventory', 1, NOW(), $id_usu)";

        mysqli_query($mysqli, $sql) or die("Error en la consulta: " . mysqli_error($mysqli));
    }
}

$reader->close();
echo "Carga completa e inserción exitosa.";
