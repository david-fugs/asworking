<?php
// Include database connection
include_once '../../conexion.php';

if (isset($_POST['upc_item'])) {
    $upc_item = strtoupper(trim($_POST['upc_item']));

    // Consulta con LEFT JOIN entre items e inventory por UPC y SKU
    $stmt = $mysqli->prepare("SELECT i.brand_item, i.item_item, i.sku_item, i.color_item, i.size_item, i.category_item, inv.quantity_inventory FROM items i LEFT JOIN inventory inv ON i.upc_item = inv.upc_inventory AND i.sku_item = inv.sku_inventory WHERE i.upc_item = ? AND i.sku_item = inv.sku_inventory");
    $stmt->bind_param("s", $upc_item);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($brand_item, $item_item, $sku_item, $color_item, $size_item, $category_item, $quantity_inventory);
        $items = [];
        while ($stmt->fetch()) {
            $items[] = [
                'brand_item' => $brand_item,
                'item_item' => $item_item,
                'sku_item' => $sku_item,
                'color_item' => $color_item,
                'size_item' => $size_item,
                'category_item' => $category_item,
                'quantity_inventory' => $quantity_inventory
            ];
        }
        echo json_encode(['status' => 'existe', 'items' => $items]);
    } else {
        echo json_encode(['status' => 'no_existe']);
    }
    $stmt->close();
}
?>
