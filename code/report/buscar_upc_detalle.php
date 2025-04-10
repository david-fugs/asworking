<?php
include '../../conexion.php'; // conexión con MySQL

$response = ['success' => false];

if (isset($_POST['upc'])) {
    $upc = $mysqli->real_escape_string($_POST['upc']);

    $sql = "SELECT * FROM items
            LEFT JOIN inventory ON items.upc_item = inventory.upc_inventory
            WHERE items.upc_item = '$upc'";
    $result = $mysqli->query($sql);
    if ($result && $result->num_rows > 0) {
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = [
                'brand_item' => $row['brand_item'],
                'ref_item' => $row['ref_item'],
                'color_item' => $row['color_item'],
                'size_item' => $row['size_item'],
                'category_item' => $row['category_item'],
                'weight_item' => $row['weight_item'],
                'inventory_item' => $row['inventory_item'],
                'quantity_inventory' => $row['quantity_inventory'],
                'item' => $row['item_item'],
                'sku' => $row['sku_item'],
                'date' => $row['date_item'],
                'cost' => $row['cost_item'],
            ];
        }

        $response = [
            'success' => true,
            'multiple' => count($items) > 1,
            'data' => $items // <- SIEMPRE array
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
