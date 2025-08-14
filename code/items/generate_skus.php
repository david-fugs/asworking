<?php
// generate_skus.php
// Genera SKUs alfanuméricos únicos de 10 caracteres para items.sku_item y copia a inventory.sku_inventory
// Uso (web): /code/items/generate_skus.php        -> dry-run
//          /code/items/generate_skus.php?apply=1 -> aplica cambios
// Uso (cli): php generate_skus.php               -> dry-run
//          php generate_skus.php --apply         -> aplica cambios

include_once __DIR__ . '/../../conexion.php';

// Config
$SKU_LENGTH = 10;
$MAX_ATTEMPTS = 10; // intentos para evitar colisiones

// Detect apply flag
$apply = false;
if (php_sapi_name() === 'cli') {
    global $argv;
    $apply = in_array('--apply', $argv);
} else {
    $apply = isset($_GET['apply']) && ($_GET['apply'] == '1' || $_GET['apply'] === 'true');
}

function gen_sku($len = 10) {
    $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $max = strlen($chars) - 1;
    $sku = '';
    for ($i = 0; $i < $len; $i++) {
        $sku .= $chars[random_int(0, $max)];
    }
    return $sku;
}

function sku_exists($mysqli, $sku) {
    $sku_esc = $mysqli->real_escape_string($sku);
    $sql = "SELECT 1 FROM items WHERE sku_item = '$sku_esc' LIMIT 1";
    $r1 = $mysqli->query($sql);
    if ($r1 && $r1->num_rows > 0) { if ($r1) $r1->free(); return true; }
    if ($r1) $r1->free();
    $sql2 = "SELECT 1 FROM inventory WHERE sku_inventory = '$sku_esc' LIMIT 1";
    $r2 = $mysqli->query($sql2);
    if ($r2 && $r2->num_rows > 0) { if ($r2) $r2->free(); return true; }
    if ($r2) $r2->free();
    return false;
}

// Collect items without SKU
$sql_items = "SELECT id_item, upc_item, sku_item FROM items WHERE sku_item IS NULL OR sku_item = ''";
$res_items = $mysqli->query($sql_items);
if (! $res_items) {
    echo "Error al consultar items: " . $mysqli->error . PHP_EOL;
    exit(1);
}

$rows = [];
while ($r = $res_items->fetch_assoc()) {
    $rows[] = $r;
}
$res_items->free();

if (count($rows) === 0) {
    echo "No hay items sin SKU. Nada que hacer.\n";
    exit(0);
}

$plan = [];
$attempts_total = 0;
$skipped = 0;

foreach ($rows as $row) {
    $id = $row['id_item'];
    $upc = $row['upc_item'];

    $newsku = null;
    $attempt = 0;
    while ($attempt < $MAX_ATTEMPTS) {
        $attempt++;
        $candidate = gen_sku($SKU_LENGTH);
        $attempts_total++;
        if (!sku_exists($mysqli, $candidate)) {
            $newsku = $candidate;
            break;
        }
    }
    if (! $newsku) {
        $plan[] = [
            'id_item' => $id,
            'upc_item' => $upc,
            'new_sku' => null,
            'inventory_will_update' => false,
            'reason' => 'collisiones',
        ];
        $skipped++;
        continue;
    }

    // Check if inventory row exists and if sku_inventory is empty
    $upc_esc = $mysqli->real_escape_string($upc);
    $sql_inv = "SELECT id_inventory, sku_inventory FROM inventory WHERE upc_inventory = '$upc_esc'";
    $rinv = $mysqli->query($sql_inv);
    $inv_rows = [];
    if ($rinv) {
        while ($ir = $rinv->fetch_assoc()) {
            $inv_rows[] = $ir;
        }
        $rinv->free();
    }

    $will_update_inventory = false;
    foreach ($inv_rows as $ir) {
        if (!isset($ir['sku_inventory']) || $ir['sku_inventory'] === '') {
            $will_update_inventory = true;
            break;
        }
    }

    $plan[] = [
        'id_item' => $id,
        'upc_item' => $upc,
        'new_sku' => $newsku,
        'inventory_will_update' => $will_update_inventory,
    ];
}

// Mostrar plan
echo ($apply ? "APLICANDO CAMBIOS\n" : "DRY RUN - sin aplicar cambios (use --apply o ?apply=1 para aplicar)\n");
$applied_items = 0;
$applied_inventory = 0;

foreach ($plan as $p) {
    echo "Item ID: {$p['id_item']} UPC: {$p['upc_item']} => SKU: ".($p['new_sku'] ?: '[SKIP]')."\n";
}

if (! $apply) {
    echo "\nResumen: total items sin sku: " . count($plan) . ", intentos generacion totales: $attempts_total, skipped por colisiones: $skipped\n";
    exit(0);
}

// Aplicar cambios en transacción
$mysqli->begin_transaction();
$errors = [];
foreach ($plan as $p) {
    if (empty($p['new_sku'])) continue;
    $id = (int)$p['id_item'];
    $upc = $mysqli->real_escape_string($p['upc_item']);
    $sku = $mysqli->real_escape_string($p['new_sku']);

    // Update items
    $sql_u = "UPDATE items SET sku_item = '$sku' WHERE id_item = $id";
    if ($mysqli->query($sql_u)) {
        $applied_items++;
    } else {
        $errors[] = "Failed items update id $id: " . $mysqli->error;
    }

    // Update inventory rows that match UPC and have empty sku_inventory
    $sql_ui = "UPDATE inventory SET sku_inventory = '$sku' WHERE upc_inventory = '$upc' AND (sku_inventory IS NULL OR sku_inventory = '')";
    if ($mysqli->query($sql_ui)) {
        $applied_inventory += $mysqli->affected_rows;
    } else {
        $errors[] = "Failed inventory update upc $upc: " . $mysqli->error;
    }
}

if (count($errors) === 0) {
    $mysqli->commit();
    echo "\nAPLICADO: items actualizados: $applied_items, inventory filas actualizadas: $applied_inventory\n";
    exit(0);
} else {
    $mysqli->rollback();
    echo "\nERRORES, se ha hecho rollback:\n";
    foreach ($errors as $e) echo " - $e\n";
    exit(1);
}

?>
