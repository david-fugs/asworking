<?php
require '../../vendor/autoload.php';
include("../../conexion.php");

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

// Verificación básica
if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
    die(json_encode(['success' => false, 'message' => 'Error al subir el archivo.']));
}

// Configuración inicial
$id_usu = isset($_SESSION['id_usu']) ? (int)$_SESSION['id_usu'] : 1;
$fileTmpPath = $_FILES['excelFile']['tmp_name'];
$fileExt = strtolower(pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION));

// Validar extensión
if (!in_array($fileExt, ['xlsx', 'xls'])) {
    die(json_encode(['success' => false, 'message' => 'Formato no permitido. Solo .xlsx o .xls']));
}

// Crear reader según extensión
$reader = ($fileExt === 'xlsx')
    ? ReaderEntityFactory::createXLSXReader()
    : ReaderEntityFactory::createXLSReader();

// Desactivar autocommit para transacción
$mysqli->autocommit(false);

try {
    $reader->open($fileTmpPath);
    $category = "";
    $weight = 0;
    
    $batchSize = 1000; // Procesar en lotes para mejor rendimiento
    $currentBatch = 0;
    $queries = [];

    foreach ($reader->getSheetIterator() as $sheet) {
        $firstRow = true;

        foreach ($sheet->getRowIterator() as $row) {
            if ($firstRow) {
                $firstRow = false;
                continue;
            }

            $cells = $row->getCells();
            if (count($cells) < 9) continue;

            // // Procesar fecha
            // $dateExcel = $cells[0]->getValue();
            // $dateFormatted = is_numeric($dateExcel)
            //     ? date('Y-m-d', \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($dateExcel))
            //     : date('Y-m-d', strtotime($dateExcel));

            $upc_sku = $mysqli->real_escape_string(trim($cells[0]->getValue()));
            $brand = $mysqli->real_escape_string(trim($cells[1]->getValue()));
            $item = $mysqli->real_escape_string(trim($cells[2]->getValue()));
            $ref = $mysqli->real_escape_string(trim($cells[3]->getValue()));
            $color = $mysqli->real_escape_string(trim($cells[4]->getValue()));
            $size = $mysqli->real_escape_string(trim($cells[5]->getValue()));
            $cost = (float)$cells[6]->getValue();
            $quantity = (int)$cells[7]->getValue();
            $costo_inventario = $cells[8]->getValue();

            if ($quantity <= 0) {
                continue; // Saltar este registro
            }

                // Insertar nuevo
                $queries[] = "INSERT INTO inventory (
                    upc_inventory, brand_inventory, item_inventory, ref_inventory, color_inventory, size_inventory, 
                     cost_inventory, quantity_inventory , costo_inventario, estado_inventory, fecha_alta_inventory
                ) VALUES (
                    '$upc_sku', '$brand', '$item', '$ref', '$color', '$size', 
                     $cost, $quantity,'$costo_inventario', 1, NOW()
                )";
        

            // Ejecutar batch cada 1000 queries
            if (++$currentBatch >= $batchSize) {
                foreach ($queries as $query) {
                    $mysqli->query($query);
                }
                $queries = [];
                $currentBatch = 0;
            }
        }
    }
    // Ejecutar queries restantes
    foreach ($queries as $query) {
        $mysqli->query($query);
    }

    $mysqli->commit();
    echo json_encode(['success' => true, 'message' => 'Carga completa. Datos importados: ' . count($queries)]);
} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    if (isset($reader)) $reader->close();
    $mysqli->autocommit(true);
    $mysqli->close();
}
