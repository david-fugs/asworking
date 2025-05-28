<?php
session_start();
include("../../conexion.php");

//si viene por post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produc_charge = isset($_POST['product_charge']) ? $_POST['product_charge'] : 0;
    $shipping_paid = isset($_POST['shipping_paid']) ? $_POST['shipping_paid'] : 0;
    $tax_return = isset($_POST['tax_return']) ? $_POST['tax_return'] : 0;
    $selling_fee_refund = isset($_POST['selling_fee_refund']) ? $_POST['selling_fee_refund'] : 0;
    $refund_administration_fee = isset($_POST['refund_administration_fee']) ? $_POST['refund_administration_fee'] : 0;
    $other_refund_fee = isset($_POST['other_refund_fee']) ? $_POST['other_refund_fee'] : 0;
    $return_cost = isset($_POST['return_cost']) ? $_POST['return_cost'] : 0;
    $buyer_comments = isset($_POST['buyer_comments']) ? $_POST['buyer_comments'] : '';
    $id_sell = isset($_POST['id_sell']) ? $_POST['id_sell'] : '';
    $quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 0;
    $sell_order = isset($_POST['sell_order']) ? $_POST['sell_order'] : '';
    $id_sell = isset($_POST['id_sell']) ? $_POST['id_sell'] : '';
    $upc_item = isset($_POST['upc_item']) ? $_POST['upc_item'] : '';
    $sku_item = isset($_POST['sku_item']) ? $_POST['sku_item'] : '';

    $sql = "INSERT INTO returns (
        id_sell,
        sell_order,
        upc_item,
        sku_item,
        quantity,
        product_charge,
        shipping_paid,
        tax_return,
        selling_fee_refund,
        refund_administration_fee,
        other_refund_fee,
        return_cost,
        buyer_comments
    ) VALUES (
    $id_sell,
    '$sell_order',
    '$upc_item',
    '$sku_item',
    $quantity,
    $produc_charge,
    $shipping_paid,
    $tax_return,
    $selling_fee_refund,
    $refund_administration_fee,
    $other_refund_fee,
    $return_cost,
    '$buyer_comments'
    )";
    $result = $mysqli->query($sql);
    $message = '';
    $icon = '';

    if ($result) {
        //hacer el update restando la cantidad de la venta  y sumarle a lo que actualmente tiene en inventory todo del upc
        // UPDATE al inventario
        $sql_update = "UPDATE inventory SET quantity_inventory = quantity_inventory + $quantity WHERE upc_inventory = '$upc_item'";
        if (!$mysqli->query($sql_update)) {
            echo "Error en UPDATE de inventory: " . $mysqli->error;
        }

        // UPDATE a la venta
        $sql_update_sell = "UPDATE sell SET quantity = quantity - $quantity WHERE id_sell = $id_sell AND upc_item = '$upc_item'";
        if (!$mysqli->query($sql_update_sell)) {
            echo "Error en UPDATE de sell: " . $mysqli->error;
        }
        $message = "Return saved .";
        $icon = "success";
    } else {
        $message = "Error al guardar la Return saved: " . $mysqli->error;
        $icon = "error";
    }

    echo '
<html>
<head>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
Swal.fire({
    icon: "' . $icon . '",
    title: "' . ($icon === "success" ? "¡Saved!" : "Error") . '",
    text: "' . $message . '"
}).then(() => {
    window.location.href = "seeSells.php"; // Cambia por la página a la que quieres ir
});
</script>
</body>
</html>
';
    exit;
} else {
    echo json_encode(["success" => false, "message" => "Método no permitido. Use POST."]);
    http_response_code(405);
}
