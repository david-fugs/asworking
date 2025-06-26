<?php

include("../../conexion.php");

if (isset($_GET['sell_order']) && isset($_GET['upc_item'])) {
    $sell_order = $_GET['sell_order'];
    $upc_item = $_GET['upc_item'];
    $id_sell = isset($_GET['id_sell']) ? $_GET['id_sell'] : null;
    $response = [];

    // Consulta principal - filtramos por sell_order y upc_item (y opcionalmente id_sell) para obtener el registro específico
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
            WHERE s.sell_order = ? AND s.upc_item = ?";
    
    // Si tenemos id_sell específico, agregarlo al filtro para mayor precisión
    if ($id_sell) {
        $sql .= " AND s.id_sell = ?";
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ssi", $sell_order, $upc_item, $id_sell);
    } else {
        $stmt = $mysqli->prepare($sql);
        $stmt->bind_param("ss", $sell_order, $upc_item);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $response['items'] = $result->fetch_all(MYSQLI_ASSOC);
    
    // Obtener el id_sell específico para la consulta de discount
    if (!empty($response['items'])) {
        $target_id_sell = $response['items'][0]['id_sell'];
        
        // Get existing discount data if any - usar id_sell y upc_item para identificación única
        $discount_sql = "SELECT * FROM discounts WHERE id_sell = ? AND upc_item = ?";
        $discount_stmt = $mysqli->prepare($discount_sql);
        $discount_stmt->bind_param("is", $target_id_sell, $upc_item);
        $discount_stmt->execute();
        $discount_result = $discount_stmt->get_result();
        
        if ($discount_result->num_rows > 0) {
            $response['discount'] = $discount_result->fetch_assoc();
        } else {
            $response['discount'] = null;
        }
        
        $discount_stmt->close();
    } else {
        $response['discount'] = null;
    }
    
    $stmt->close();

    echo json_encode($response);
} else {
    echo json_encode(['error' => 'No sell_order or upc_item provided']);
}