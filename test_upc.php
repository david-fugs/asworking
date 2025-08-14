<?php
include_once 'conexion.php';

$upc_item = '733004811005';
$upc_esc = $mysqli->real_escape_string($upc_item);

echo "=== TESTING UPC: $upc_item ===\n\n";

// 1. Primero veamos qué hay en items
echo "1. Items con este UPC:\n";
$sql1 = "SELECT * FROM items WHERE upc_item = '$upc_esc'";
$result1 = $mysqli->query($sql1);
if ($result1 && $result1->num_rows > 0) {
    while ($row = $result1->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No items encontrados con este UPC\n";
}

echo "\n2. Inventory con este UPC:\n";
$sql2 = "SELECT * FROM inventory WHERE upc_inventory = '$upc_esc'";
$result2 = $mysqli->query($sql2);
if ($result2 && $result2->num_rows > 0) {
    while ($row = $result2->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No inventory encontrado con este UPC\n";
}

echo "\n3. JOIN actual (como está en verificar_upc.php):\n";
$sql3 = "SELECT i.brand_item, i.item_item, i.sku_item, i.color_item, i.size_item, i.category_item, 
               COALESCE(inv.quantity_inventory, 0) as quantity_inventory
        FROM items i
        LEFT JOIN inventory inv ON i.upc_item = inv.upc_inventory AND i.sku_item = inv.sku_inventory
        WHERE i.upc_item = '$upc_esc'
        ORDER BY i.sku_item";
$result3 = $mysqli->query($sql3);
if ($result3 && $result3->num_rows > 0) {
    while ($row = $result3->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No resultados del JOIN\n";
}

echo "\n4. JOIN simple solo por UPC:\n";
$sql4 = "SELECT i.brand_item, i.item_item, i.sku_item, i.color_item, i.size_item, i.category_item, 
               inv.quantity_inventory
        FROM items i
        LEFT JOIN inventory inv ON i.upc_item = inv.upc_inventory
        WHERE i.upc_item = '$upc_esc'
        ORDER BY i.sku_item";
$result4 = $mysqli->query($sql4);
if ($result4 && $result4->num_rows > 0) {
    while ($row = $result4->fetch_assoc()) {
        print_r($row);
    }
} else {
    echo "No resultados del JOIN simple\n";
}
?>
