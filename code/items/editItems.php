<?php
include("../../conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Capturar datos del formulario
    $id_item = (int) $_POST["id"];
    $upc = $mysqli->real_escape_string($_POST["upc"]);
    $sku = $mysqli->real_escape_string($_POST["sku"]);
    $date = $mysqli->real_escape_string($_POST["date"]);
    $brand = $mysqli->real_escape_string($_POST["brand"]);
    $item = $mysqli->real_escape_string($_POST["item"]);
    $ref = $mysqli->real_escape_string($_POST["ref"]);
    $color = $mysqli->real_escape_string($_POST["color"]);
    $size = $mysqli->real_escape_string($_POST["size"]);
    $category = $mysqli->real_escape_string($_POST["category"]);
    $cost = $mysqli->real_escape_string($_POST["cost"]);
    $weight = $mysqli->real_escape_string($_POST["weight"]);    $batch = $mysqli->real_escape_string($_POST["batch"]);
    $stock = (int) $_POST["stock"];
    $observation = isset($_POST["observation"]) ? $mysqli->real_escape_string($_POST["observation"]) : '';

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

    // Convertir las tiendas a formato JSON
    $stores_json = json_encode($stores_selected);
    $stores_json_escaped = $mysqli->real_escape_string($stores_json);

    // Obtener el valor actual de upc_item antes de actualizar
    $query_upc = "SELECT upc_item FROM items WHERE id_item = $id_item";
    $result_upc = $mysqli->query($query_upc);

    if ($result_upc->num_rows > 0) {
        $row = $result_upc->fetch_assoc();
        $old_upc = $row["upc_item"]; // Guardamos el UPC actual
    } else {
        echo json_encode(["success" => false, "error" => "ID no encontrado"]);
        exit;
    }
    // Actualizar la tabla items (sin cambiar quantity_inventory)
    $sql_update_items = "UPDATE items SET 
                            upc_item = '$upc', 
                            sku_item = '$sku', 
                            date_item = '$date', 
                            brand_item = '$brand', 
                            item_item = '$item', 
                            ref_item = '$ref', 
                            color_item = '$color', 
                            size_item = '$size', 
                            category_item = '$category', 
                            cost_item = '$cost', 
                            weight_item = '$weight', 
                            inventory_item = '$batch',
                            stores_item = '$stores_json_escaped'
                        WHERE id_item = $id_item";    if ($mysqli->query($sql_update_items) === TRUE) {
        // Si el UPC cambió, actualizarlo también en inventory
        if ($old_upc !== $upc) {
            $sql_update_inventory = "UPDATE inventory SET upc_inventory = '$upc', quantity_inventory= $stock, observation_inventory = '$observation' WHERE upc_inventory = '$old_upc'";
            $mysqli->query($sql_update_inventory);
        }

        // Actualizar siempre el stock en inventory
        $sql_update_stock = "UPDATE inventory SET quantity_inventory = $stock, observation_inventory = '$observation' WHERE upc_inventory = '$upc'";
        $mysqli->query($sql_update_stock);
        
        // Crear mensaje de éxito con las tiendas
        $stores_list = empty($stores_selected) ? 'None' : implode(', ', $stores_selected);
        
        echo "<script>
            alert('✅ Item updated successfully!\\n📍 Stores: $stores_list');
            window.location.href = 'showitems.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('❌ Error updating item: " . addslashes($mysqli->error) . "');
            window.location.href = 'showitems.php';
        </script>";
    }
    // Cerrar conexión
    $mysqli->close();
}
