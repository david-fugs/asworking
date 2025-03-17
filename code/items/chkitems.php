<?php 
require('../../conexion.php');
sleep(1);
if (isset($_POST)) {
    $upc_sku_item = (string)$_POST['upc_sku_item'];
    
    $result = $mysqli->query(
        'SELECT * FROM items WHERE upc_sku_item = "'.strtolower($upc_sku_item).'"'
    );
    
    if ($result->num_rows > 0) {
        echo '<div class="alert alert-danger"><strong>CHECK THE UPC/SKU!</strong> THERE IS ALREADY THE SAME ONE.</div>';
    } else {
        echo '<div class="alert alert-success"><strong>NEW REGISTRATION!</strong></div>';
    }
}