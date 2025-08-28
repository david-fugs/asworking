<?php
// Include database connection
include_once '../../conexion.php';

if (isset($_POST['upc_item'])) {
    $upc_item = strtoupper(trim($_POST['upc_item']));

    // Consulta plana (escapando la entrada) con LEFT JOIN entre items e inventory por UPC
    // Si SKU está vacío, solo hacer JOIN por UPC
    $upc_esc = $mysqli->real_escape_string($upc_item);
    $sql = "SELECT i.brand_item, i.item_item, i.sku_item, i.color_item, i.size_item, i.category_item,
                   i.ref_item, i.cost_item, i.inventory_item as item_inventory, i.batch_item as batch_item, i.weight_item,
                   COALESCE(inv.quantity_inventory, 0) as quantity_inventory
            FROM items i
            LEFT JOIN inventory inv ON i.upc_item = inv.upc_inventory 
                AND (
                    (i.sku_item IS NOT NULL AND i.sku_item != '' AND i.sku_item = inv.sku_inventory) 
                    OR 
                    (i.sku_item IS NULL OR i.sku_item = '')
                )
            WHERE i.upc_item = '$upc_esc'
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
        'inventory_item' => $row['item_inventory'],
        'batch_item' => $row['batch_item'],
        'weight_item' => $row['weight_item']
        ];
        }
        echo json_encode(['status' => 'existe', 'items' => $items]);
        $result->free();
    } else {
        echo json_encode(['status' => 'no_existe']);
    }
}
?>
