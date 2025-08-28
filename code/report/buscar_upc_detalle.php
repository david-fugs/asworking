<?php
include '../../conexion.php'; // conexión con MySQL

header('Content-Type: application/json');

$response = ['success' => false];

if (isset($_POST['upc'])) {
    $upc_raw = trim($_POST['upc']);
    $upc = $mysqli->real_escape_string(strtoupper($upc_raw));

    // Usar la misma lógica que verificar_upc.php: JOIN discriminando por SKU cuando exista.
    $sql = "SELECT i.brand_item, i.item_item, i.sku_item, i.color_item, i.size_item, i.category_item,
                   i.ref_item, i.cost_item, i.inventory_item as inventory_item, i.batch_item as batch_item, i.weight_item,
                   COALESCE(inv.quantity_inventory, 0) as quantity_inventory, inv.upc_inventory, inv.sku_inventory
            FROM items i
            LEFT JOIN inventory inv ON i.upc_item = inv.upc_inventory 
                AND (
                    (i.sku_item IS NOT NULL AND i.sku_item != '' AND i.sku_item = inv.sku_inventory)
                    OR
                    (i.sku_item IS NULL OR i.sku_item = '')
                )
            WHERE i.upc_item = '$upc'
            ORDER BY i.sku_item";

    $result = $mysqli->query($sql);
    if ($result && $result->num_rows > 0) {
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = [
                'brand_item' => $row['brand_item'],
                'item_item' => $row['item_item'],
                'sku_item' => $row['sku_item'],
                'color_item' => $row['color_item'],
                'size_item' => $row['size_item'],
                'category_item' => $row['category_item'],
                'quantity_inventory' => $row['quantity_inventory'],
                'ref_item' => $row['ref_item'],
                'cost_item' => $row['cost_item'],
                'inventory_item' => $row['inventory_item'],
                'batch_item' => $row['batch_item'],
                'weight_item' => $row['weight_item'],
                'date_item' => $row['date_item'] ?? null
            ];
        }

        echo json_encode(['success' => true, 'multiple' => count($items) > 1, 'data' => $items]);
        $result->free();
        exit;
    }
}

echo json_encode($response);
