<?php
require '../../vendor/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

// Verifica si se subió un archivo
if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
    die("Error al subir el archivo.");
}

// Ruta temporal del archivo subido
$filePath = $_FILES['excelFile']['tmp_name'];

// Verifica la extensión del archivo
$fileExt = pathinfo($_FILES['excelFile']['name'], PATHINFO_EXTENSION);
if ($fileExt === 'xlsx') {
    $reader = ReaderEntityFactory::createXLSXReader();
} elseif ($fileExt === 'xls') {
    $reader = ReaderEntityFactory::createXLSReader();
} else {
    die("Formato de archivo no permitido. Solo .xlsx y .xls.");
}

$reader->open($filePath);

echo "<pre>";
echo "Cargando datos del archivo...\n\n";

foreach ($reader->getSheetIterator() as $sheet) {
    $firstRow = true;

    foreach ($sheet->getRowIterator() as $row) {
        if ($firstRow) {
            $firstRow = false;
            continue; // Saltar la fila de encabezados
        }

        $cells = $row->getCells();

        // Extraer valores de cada columna
        $data = [
            'DATE'                   => $cells[0]->getValue(),
            'MES'                    => $cells[1]->getValue(),
            'AÑO'                    => $cells[2]->getValue(),
            'REG'                    => $cells[3]->getValue(),
            'UPC/SKU'                => $cells[4]->getValue(),
            'BRAND'                  => $cells[5]->getValue(),
            'ITEM'                   => $cells[6]->getValue(),
            'REF'                    => $cells[7]->getValue(),
            'COLOR'                  => $cells[8]->getValue(),
            'SIZE'                   => $cells[9]->getValue(),
            'CATEGORY'               => $cells[10]->getValue(),
            'PESO LB/OZ'             => $cells[11]->getValue(),
            'QTY'                    => $cells[12]->getValue(),
            'TRANSACCIÓN'            => $cells[13]->getValue(),
            'STORE'                  => $cells[14]->getValue(),
            'PRICE'                  => $cells[15]->getValue(),
            'ROTACIÓN'               => $cells[16]->getValue(),
            'SHIPPING RECEIVED'      => $cells[17]->getValue(),
            'TAX'                    => $cells[18]->getValue(),
            'SHP EBAY'               => $cells[19]->getValue(),
            'SHP AMAZON'             => $cells[20]->getValue(),
            'SHIPSTATION'            => $cells[21]->getValue(),
            'SHP SHOPIFY'            => $cells[22]->getValue(),
            'SHP WALMART'            => $cells[23]->getValue(),
            'SHOPIFY FEE'            => $cells[24]->getValue(),
            'EBAY FEE'               => $cells[25]->getValue(),
            'AMAZON SHIPPING FEE'    => $cells[26]->getValue(),
            'AMAZON FEE'             => $cells[27]->getValue(),
            'WALMART FEE'            => $cells[28]->getValue(),
            'ADVERTISING'            => $cells[29]->getValue(),
            'COMPRA'                 => $cells[30]->getValue(),
            'COSTO'                  => $cells[31]->getValue(),
            'TIPO DE INVENTARIO'     => $cells[32]->getValue(),
            'UTILIDAD'               => $cells[33]->getValue(),
            'MARGEN/COSTO'           => $cells[34]->getValue(),
            'MARGEN/PRECIO DE VENTA' => $cells[35]->getValue(),
            'CUSTOMER REFUND'        => $cells[36]->getValue(),
            'SHIPPING REFUND'        => $cells[37]->getValue(),
            'SHIPP PAYED REFUND'     => $cells[38]->getValue(),
            'EBAY REFUND'            => $cells[39]->getValue(),
            'AMAZON REFUND'          => $cells[40]->getValue(),
            'AMAZON REFUND FEE'      => $cells[41]->getValue(),
            'WALMART REFUND'         => $cells[42]->getValue(),
            'UTILIDAD A DEVOLVER'    => $cells[43]->getValue(),
            'COSTO DEVOLUCION'       => $cells[44]->getValue(),
            'BUYER COMENTS'          => $cells[45]->getValue(),
        ];

        // Mostrar los valores obtenidos
        echo implode(" | ", $data) . "\n";
    }
}

$reader->close();
echo "Carga completa.\n";
