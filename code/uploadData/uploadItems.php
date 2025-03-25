<?php
require '../../vendor/autoload.php';
ini_set('memory_limit', '-1');
ini_set('max_execution_time', 600); // Aumenta el tiempo de ejecución

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
        die(json_encode(['error' => 'Error al subir el archivo']));
    }

    $tempFile = $_FILES['excelFile']['tmp_name'];
    $reader = ReaderEntityFactory::createXLSXReader();
    $reader->setShouldFormatDates(false); // Evita formateo de fechas (más rápido)
    $reader->setShouldPreserveEmptyRows(false); // Ignora filas vacías (menos memoria usada)
    $reader->open($tempFile);
    
    include("../../conexion.php");

    $csvFile = str_replace("/", "\\", __DIR__ . '/temp_data.csv');
    $fileHandle = fopen($csvFile, 'w');

        if (!$fileHandle) {
            die(json_encode(['error' => 'No se pudo crear el archivo CSV. Verifica permisos.']));
        }
    // Escribimos la primera línea (encabezados)
    fputcsv($fileHandle, [
        'upsi_item', 'date_item', 'brand_item', 'item_item', 'ref_item', 'color_item', 'size_item',
        'category_item', 'cost_item', 'weight_item', 'inventory_item', 'id_usu'
    ]);

    $contadorRegistros = 0;

    foreach ($reader->getSheetIterator() as $sheet) {
        foreach ($sheet->getRowIterator() as $row) {
            $data = [];
            foreach ($row->getCells() as $cell) {
                $data[] = $cell->getValue();
            }

            if ($contadorRegistros == 0) { // Saltar encabezados
                $contadorRegistros++;
                continue;
            }

            if (count($data) < 31) {
                continue;
            }

            list(
                $date, $month, $year, $upc_sku, $brand, $item, $reference, $color, $size, $category,
                $weight, $quantity, $transaccion, $store, $price, $rotacion, $shipping_received, $tax,
                $ship_amazon, $shipstation, $ship_shopify, $ship_walmart, $shopify_fee, $amazon_shipping_fee,
                $walmart_fee, $ad, $costo, $tipo_inventario, $utilidad, $margen_costo, $margen_precio_venta
            ) = $data;

            // Validación de encabezados
            if ($upc_sku == 'UPC/SKU' || $date == 'DATE' || $brand == 'BRAND') {
                continue;
            }

            // Convertir fecha a formato correcto
            if ($date instanceof DateTime) {
                $date = $date->format('Y-m-d');
            }

            $id_usu = 1; // ID del usuario que sube los datos

            // Escribimos en el archivo CSV
            fputcsv($fileHandle, [$upc_sku, $date, $brand, $item, $reference, $color, $size, $category, $costo, $weight, $tipo_inventario, $id_usu]);

            $contadorRegistros++;
        }
    }

    $reader->close();
    fclose($fileHandle); // Cerramos el archivo CSV

    // Cargamos el CSV en MySQL con `LOAD DATA INFILE`
    $mysqli->query("SET autocommit=0;");
    $mysqli->query("SET UNIQUE_CHECKS=0;");
    $mysqli->query("SET FOREIGN_KEY_CHECKS=0;");

    $sql = "LOAD DATA INFILE '$csvFile'
            INTO TABLE items
            FIELDS TERMINATED BY ','  
            ENCLOSED BY '\"'  
            LINES TERMINATED BY '\n'  
            IGNORE 1 LINES  
            (upsi_item, date_item, brand_item, item_item, ref_item, color_item, size_item, category_item, cost_item, weight_item, inventory_item, id_usu)
            SET date_item = STR_TO_DATE(date_item, '%Y-%m-%d')"; // Convertir fecha si es necesario

    if (!$mysqli->query($sql)) {
        die(json_encode(["error" => "Error al insertar datos: " . $mysqli->error]));
    }

    $mysqli->query("SET UNIQUE_CHECKS=1;");
    $mysqli->query("SET FOREIGN_KEY_CHECKS=1;");
    $mysqli->query("COMMIT;");

    unlink($csvFile); // Eliminamos el archivo temporal

    echo json_encode(["finalizado" => "Carga completada con éxito, $contadorRegistros registros insertados."]);
}
?>
