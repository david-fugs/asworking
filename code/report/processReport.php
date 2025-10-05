<?php
include("../../conexion.php");
session_start();
// current user id for FK on items.id_usu
$user_id = isset($_SESSION['id']) ? intval($_SESSION['id']) : 0;

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $upc_asignado = $mysqli->real_escape_string($_POST['upc_asignado_report']);
    $upc_final = $mysqli->real_escape_string($_POST['upc_final_report']);
    $cons = $mysqli->real_escape_string($_POST['cons_report']);
    $folder = $mysqli->real_escape_string($_POST['folder_report']);
    $loc = $mysqli->real_escape_string($_POST['loc_report']);
    $quantity = $mysqli->real_escape_string($_POST['quantity_report']);
    $sku = $mysqli->real_escape_string($_POST['sku_report']);
    $brand = $mysqli->real_escape_string($_POST['brand_report']);
    $item = $mysqli->real_escape_string($_POST['item_report']);
    $vendor = $mysqli->real_escape_string($_POST['vendor_report']);
    $color = $mysqli->real_escape_string($_POST['color_report']);
    $size = $mysqli->real_escape_string($_POST['size_report']);    $category = $mysqli->real_escape_string($_POST['category_report']);
    $weight = $mysqli->real_escape_string($_POST['weight_report']);
    $inventory = $mysqli->real_escape_string($_POST['inventory_report']);
    $observacion = $mysqli->real_escape_string($_POST['observacion_report']);

    // VALIDACIÓN 1: Generar SKU único alfanumérico (8 caracteres, letras y números)
    function generateUniqueSKU($mysqli) {
        $max_attempts = 100;
        $attempts = 0;
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $length = 10; // changed to 10 characters as requested
        do {
            $sku = '';
            for ($i = 0; $i < $length; $i++) {
                $sku .= $characters[rand(0, strlen($characters) - 1)];
            }
            // Verificar en tabla items
            $check_items = "SELECT COUNT(*) as count FROM items WHERE sku_item = '$sku'";
            $result_items = $mysqli->query($check_items);
            $exists_items = $result_items->fetch_assoc()['count'] > 0;
            // Verificar en tabla inventory
            $exists_inventory = false;
            $check_inventory = "SELECT COUNT(*) as count FROM inventory WHERE sku_inventory = '$sku'";
            $result_inventory = $mysqli->query($check_inventory);
            if ($result_inventory) {
                $exists_inventory = $result_inventory->fetch_assoc()['count'] > 0;
            }
            $attempts++;
        } while (($exists_items || $exists_inventory) && $attempts < $max_attempts);
        return ($attempts < $max_attempts) ? $sku : false;
    }

    // Respect client-provided SKU if present; otherwise generate one server-side.
        $sku_post = isset($_POST['sku_report']) ? trim($_POST['sku_report']) : '';
        if (!empty($sku_post)) {
            // Verify the provided SKU is unique in items and inventory
            $sku_post_esc = $mysqli->real_escape_string($sku_post);
            $check_items = "SELECT COUNT(*) as count FROM items WHERE sku_item = '$sku_post_esc'";
            $result_items = $mysqli->query($check_items);
            $exists_items = $result_items ? $result_items->fetch_assoc()['count'] > 0 : false;

            $check_inventory = "SELECT COUNT(*) as count FROM inventory WHERE sku_inventory = '$sku_post_esc'";
            $result_inventory = $mysqli->query($check_inventory);
            $exists_inventory = $result_inventory ? $result_inventory->fetch_assoc()['count'] > 0 : false;

            if ($exists_items || $exists_inventory) {
                echo "<script>
                    alert('❌ Error: The provided SKU (" . htmlspecialchars($sku_post_esc) . ") already exists. Please choose another.');
                    window.history.back();
                  </script>";
                exit();
            }

            $sku = $sku_post_esc;
        } else {
            $sku_generated = generateUniqueSKU($mysqli);
            if (!$sku_generated) {
                echo "<script>
                    alert('❌ Error: Could not generate a unique SKU. Please try again.');
                    window.history.back();
                  </script>";
                exit();
            }
            $sku = $sku_generated;
        }

    // Procesar las tiendas seleccionadas
    $stores_selected = [];
    if (isset($_POST['stores']) && is_array($_POST['stores'])) {
        $valid_stores = ['AS001', 'EB001', 'EB002', 'AM002', 'WM001'];
        foreach ($_POST['stores'] as $store) {
            $store = strtoupper(trim($store));
            if (in_array($store, $valid_stores)) {
                $stores_selected[] = $store;
            }
        }
    }

    // Validar que se haya seleccionado al menos una tienda
    if (empty($stores_selected)) {
        echo "<script>
            alert('Error: You must select at least one store.');
            window.history.back();
          </script>";
        exit();
    }

    // Convertir las tiendas a formato JSON
    $stores_json = json_encode($stores_selected);
    $stores_json_escaped = $mysqli->real_escape_string($stores_json);    // Insertar nuevo reporte con estado_reporte = 0 para que aparezca en editLocationFolder
   // Debug: log incoming POST and computed values to help trace why CONS/SKU may be saved as 0
    $debug_log = __DIR__ . '/report_debug.log';
    $dbg = "[" . date('c') . "] processReport POST snapshot:\n" . print_r($_POST, true) . "\n";
    $dbg .= "Computed values before INSERT: cons=[$cons] sku=[$sku] folder=[$folder]" . "\n\n";
    // Append safely
    @file_put_contents($debug_log, $dbg, FILE_APPEND | LOCK_EX);

    // Ensure cons_report column can store textual CONS (not numeric). If it's numeric, attempt to alter it to VARCHAR.
    $colType = null;
    $colRes = $mysqli->query("SHOW COLUMNS FROM daily_report LIKE 'cons_report'");
    if ($colRes && $colRes->num_rows > 0) {
        $colRow = $colRes->fetch_assoc();
        $colType = $colRow['Type'];
    }
    @file_put_contents($debug_log, "[".date('c')."] cons_report column type detected: " . ($colType ?? 'NOT FOUND') . "\n", FILE_APPEND | LOCK_EX);
    if ($colType !== null) {
        // If column type starts with int/decimal/float etc., change to varchar(255)
        if (preg_match('/^(tinyint|smallint|mediumint|int|bigint|decimal|float|double)/i', $colType)) {
            $alterSql = "ALTER TABLE daily_report MODIFY cons_report VARCHAR(255) NULL";
            if ($mysqli->query($alterSql)) {
                @file_put_contents($debug_log, "[".date('c')."] ALTER succeeded: cons_report set to VARCHAR(255)\n", FILE_APPEND | LOCK_EX);
            } else {
                @file_put_contents($debug_log, "[".date('c')."] ALTER failed: " . $mysqli->error . "\n", FILE_APPEND | LOCK_EX);
            }
        }
    } else {
        @file_put_contents($debug_log, "[".date('c')."] cons_report column not found in daily_report\n", FILE_APPEND | LOCK_EX);
    }
    // Also ensure sku_report column can store strings
    $skuColType = null;
    $skuRes = $mysqli->query("SHOW COLUMNS FROM daily_report LIKE 'sku_report'");
    if ($skuRes && $skuRes->num_rows > 0) {
        $skuRow = $skuRes->fetch_assoc();
        $skuColType = $skuRow['Type'];
    }
    @file_put_contents($debug_log, "[".date('c')."] sku_report column type detected: " . ($skuColType ?? 'NOT FOUND') . "\n", FILE_APPEND | LOCK_EX);
    if ($skuColType !== null) {
        if (preg_match('/^(tinyint|smallint|mediumint|int|bigint|decimal|float|double)/i', $skuColType)) {
            $alterSql2 = "ALTER TABLE daily_report MODIFY sku_report VARCHAR(255) NULL";
            if ($mysqli->query($alterSql2)) {
                @file_put_contents($debug_log, "[".date('c')."] ALTER succeeded: sku_report set to VARCHAR(255)\n", FILE_APPEND | LOCK_EX);
            } else {
                @file_put_contents($debug_log, "[".date('c')."] ALTER failed (sku_report): " . $mysqli->error . "\n", FILE_APPEND | LOCK_EX);
            }
        }
    } else {
        @file_put_contents($debug_log, "[".date('c')."] sku_report column not found in daily_report\n", FILE_APPEND | LOCK_EX);
    }

    // If CONS is empty, attempt to generate it server-side based on folder to avoid race conditions
    if (empty($cons) && !empty($folder)) {
        $folder_esc = $mysqli->real_escape_string($folder);
        $q = "SELECT cons_report FROM daily_report WHERE cons_report LIKE '$folder_esc cons %' ORDER BY cons_report DESC LIMIT 1";
        $res = $mysqli->query($q);
        $next_cons = 1;
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $last_cons = $row['cons_report'];
            if (preg_match('/cons (\d+)$/', $last_cons, $m)) {
                $next_cons = intval($m[1]) + 1;
            }
        }
        $cons = $folder . ' cons ' . $next_cons;
        // escape for insert
        $cons = $mysqli->real_escape_string($cons);
    }
    $query = "INSERT INTO daily_report (
        upc_asignado_report, upc_final_report, cons_report, folder_report, 
        loc_report, quantity_report, sku_report, brand_report, item_report, 
        vendor_report, color_report, size_report, category_report, 
        weight_report, inventory_report, observacion_report, stores_report, estado_reporte
    ) VALUES (
        '$upc_asignado', '$upc_final', '$cons', '$folder',
        '$loc', '$quantity', '$sku', '$brand', '$item',
        '$vendor', '$color', '$size', '$category',
        '$weight', '$inventory', '$observacion', '$stores_json_escaped', 1
    )";

// Ejecutar consulta del reporte
    if ($mysqli->query($query)) {
        $report_success = true;
        
        // Si se proporcionó un UPC asignado, intentar actualizar la tabla items
        $item_update_success = true;
        $item_update_message = "";
        if (!empty($upc_asignado)) {
            // Verificar si el UPC existe en la tabla items
            $check_query = "SELECT upc_item FROM items WHERE upc_item = '$upc_asignado'";
            $check_result = $mysqli->query($check_query);
            
            if ($check_result && $check_result->num_rows > 0) {
                // El UPC existe, actualizar las tiendas
                $update_query = "UPDATE items SET stores_item = '$stores_json_escaped' WHERE upc_item = '$upc_asignado'";
                
                if ($mysqli->query($update_query)) {
                    $item_update_message = "\\n✅ Item stores updated successfully.";
                } else {
                    $item_update_success = false;
                    $item_update_message = "\\n⚠️ Warning: Could not update item stores.";
                }
            } else {
                // El UPC no existe, crear nuevo item en tabla items
                // Note: include id_usu to satisfy FK constraint on items.id_usu and remove quantity_inventory from items
                $create_item_query = "INSERT INTO items (upc_item, sku_item, brand_item, item_item, ref_item, color_item, size_item, category_item, cost_item, weight_item, inventory_item, batch_item, stores_item, id_usu) VALUES ('$upc_asignado', '$sku', '$brand', '$item', '$vendor', '$color', '$size', '$category', '0', '$weight', '$loc', '', '$stores_json_escaped', $user_id)";

                // Log the query for debugging
                @file_put_contents($debug_log, "[".date('c')."] create_item_query: " . $create_item_query . "\n", FILE_APPEND | LOCK_EX);

                if ($mysqli->query($create_item_query)) {
                    $item_update_message = "\\n✅ New item created in items table.";

                    // También crear entrada en tabla inventory si no existe
                    $check_inventory = "SELECT id_inventory FROM inventory WHERE upc_inventory = '$upc_asignado' AND sku_inventory = '$sku'";
                    $check_inv_result = $mysqli->query($check_inventory);

                    if ($check_inv_result && $check_inv_result->num_rows == 0) {
                        $create_inventory_query = "INSERT INTO inventory (upc_inventory, sku_inventory, quantity_inventory) VALUES ('$upc_asignado', '$sku', '$quantity')";
                        if ($mysqli->query($create_inventory_query)) {
                            $item_update_message .= "\\n✅ Inventory entry created.";
                        } else {
                            $item_update_message .= "\\n⚠️ Warning: Could not create inventory entry. Error: " . $mysqli->error;
                            @file_put_contents($debug_log, "[".date('c')."] inventory insert error: " . $mysqli->error . "\n", FILE_APPEND | LOCK_EX);
                        }
                    } else {
                        // Actualizar inventario existente
                        $update_inventory_query = "UPDATE inventory SET quantity_inventory = '$quantity' WHERE upc_inventory = '$upc_asignado' AND sku_inventory = '$sku'";
                        if ($mysqli->query($update_inventory_query)) {
                            $item_update_message .= "\\n✅ Inventory updated.";
                        } else {
                            $item_update_message .= "\\n⚠️ Warning: Could not update inventory. Error: " . $mysqli->error;
                            @file_put_contents($debug_log, "[".date('c')."] inventory update error: " . $mysqli->error . "\n", FILE_APPEND | LOCK_EX);
                        }
                    }
                } else {
                    $item_update_success = false;
                    $item_update_message = "\\n⚠️ Warning: Could not create new item in items table. Error: " . $mysqli->error;
                    @file_put_contents($debug_log, "[".date('c')."] items insert error: " . $mysqli->error . "\n", FILE_APPEND | LOCK_EX);
                }
            }
        }
          $stores_list = implode(', ', $stores_selected);
    // Prepare HTML message for SweetAlert (convert any literal \n sequences to real newlines)
    $item_update_message = str_replace('\\n', "\n", $item_update_message);
    $swal_html = "<p><strong>📍 Stores:</strong> " . htmlspecialchars($stores_list) . "</p>" .
                 "<p><strong>🔢 Generated SKU:</strong> " . htmlspecialchars($sku) . "</p>" .
                 "<div style='text-align:left; margin-top:8px;'>" . nl2br(htmlspecialchars($item_update_message)) . "</div>";

    // Return a minimal HTML page that loads SweetAlert2 and shows a modal, then reloads the page
    ?>
    <!doctype html>
    <html>
    <head>
        <meta charset='utf-8'>
        <title>Report result</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 30px; }
            .success-message { background: #d4edda; color: #155724; padding: 20px; border-radius: 5px; margin: 20px; }
            .redirect-info { margin: 20px; color: #666; }
            a { color: #007bff; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
        <!-- SweetAlert2 CDN -->
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <div class='success-message'>
            <h2>✅ Report Processed Successfully!</h2>
            <p><strong>📍 Stores:</strong> <?= htmlspecialchars($stores_list) ?></p>
            <p><strong>🔢 Generated SKU:</strong> <?= htmlspecialchars($sku) ?></p>
            <div style='white-space:pre-wrap; text-align:left; margin: 10px auto; max-width:600px;'><?= nl2br(htmlspecialchars($item_update_message)) ?></div>
        </div>
        <div class='redirect-info'>
            <p>La página se recargará automáticamente.</p>
            <p>If auto-reload doesn't work, <a href='editLocationFolder_test.php'>test page</a> or <a href='editLocationFolder.php'>main page</a></p>
        </div>
        <script>
            // Use SweetAlert2 to show the result and then reload the page when the user confirms
            const swalHtml = <?= json_encode($swal_html) ?>;
            Swal.fire({
                title: '✅ Report Processed Successfully!',
                html: swalHtml,
                icon: 'success',
                allowOutsideClick: false,
                allowEscapeKey: false,
                confirmButtonText: 'OK'
            }).then(function() {
                // Redirect to addReport.php using GET to avoid re-submitting the POST
                window.location.replace('addReport.php');
            });
            // As a fallback, force a redirect after 6 seconds
            setTimeout(function() { try { window.location.replace('addReport.php'); } catch(e){} }, 6000);
        </script>
    </body>
    </html>
    <?php
    
    } else {
    $err_msg = "❌ Error inserting report: " . $mysqli->error;
    ?>
    <!doctype html>
    <html>
    <head>
        <meta charset='utf-8'>
        <title>Error</title>
        <style>
            body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
            .error-message { background: #f8d7da; color: #721c24; padding: 20px; border-radius: 5px; margin: 20px; }
            .redirect-info { margin: 20px; color: #666; }
            a { color: #007bff; text-decoration: none; }
            a:hover { text-decoration: underline; }
        </style>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <div class='error-message'>
            <h2>❌ Error Processing Report</h2>
            <p><?= htmlspecialchars($mysqli->error) ?></p>
        </div>
        <div class='redirect-info'>
            <p>Vuelva a intentar o pulse OK para recargar.</p>
            <p>If you are not redirected automatically, <a href='addReport.php'>click here</a></p>
        </div>
        <script>
            const errMsg = <?= json_encode($err_msg) ?>;
            Swal.fire({
                title: '❌ Error',
                html: '<pre style="text-align:left; white-space:pre-wrap;">' + Swal.escapeHtml(errMsg) + '</pre>',
                icon: 'error',
                confirmButtonText: 'OK'
            }).then(function() {
                // Redirect to addReport.php using GET to avoid re-submitting the POST
                window.location.replace('addReport.php');
            });
        </script>
    </body>
    </html>
    <?php
    }
} else {
    echo "<script>
            alert('Method not valid');
            window.location.href = 'addReport.php';
          </script>";
}
