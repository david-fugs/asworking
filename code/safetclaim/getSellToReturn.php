<?php

include("../../conexion.php");

if (isset($_GET['sell_order'])) {
    $sell_order = $_GET['sell_order'];
    $response = [];
      // Consulta principal
    $sql = "SELECT 
                s.id_sell,
                s.sell_order,
                s.date,
                s.upc_item,
                s.sku_item,
                s.quantity,
                s.comision_item,
                s.cargo_fijo,
                s.item_profit,
                s.item_price,
                s.total_item,
                store.store_name,
                sucursal.code_sucursal,
                items.brand_item,
                items.item_item,
                items.color_item,
                items.ref_item,
                r.product_charge, 
                r.shipping_paid, 
                r.tax_return, 
                r.selling_fee_refund,
                r.refund_administration_fee, 
                r.other_refund_fee, 
                r.return_cost, 
                r.buyer_comments, 
                r.quantity AS return_quantity
            FROM sell AS s
            LEFT JOIN returns as r ON s.id_sell = r.id_sell
            LEFT JOIN store ON store.id_store = s.id_store
            LEFT JOIN sucursal ON sucursal.id_sucursal = s.id_sucursal
            LEFT JOIN items ON items.sku_item = s.sku_item 
                            AND (items.upc_item = s.upc_item OR items.upc_item IS NULL)
            WHERE s.sell_order = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $sell_order);
    $stmt->execute();
    $result = $stmt->get_result();
    $response['items'] = $result->fetch_all(MYSQLI_ASSOC);
    
    // Get existing safetclaim data if any - use id_sell from the first item for accuracy
    if (!empty($response['items'])) {
        $id_sell = $response['items'][0]['id_sell'];
        $safetclaim_sql = "SELECT * FROM safetclaim WHERE id_sell = ?";
        $safetclaim_stmt = $mysqli->prepare($safetclaim_sql);
        $safetclaim_stmt->bind_param("i", $id_sell);
        $safetclaim_stmt->execute();
        $safetclaim_result = $safetclaim_stmt->get_result();
        
        if ($safetclaim_result->num_rows > 0) {
            $response['safetclaim'] = $safetclaim_result->fetch_assoc();
        } else {
            $response['safetclaim'] = null;
        }
        
        $safetclaim_stmt->close();
    } else {
        $response['safetclaim'] = null;
    }
    $stmt->close();

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'No sell_order provided']);
}
?>
