
<?php 
include("../../conexion.php");
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $upsi = $_POST["upsi"];
    $sku = $_POST["sku"];
    $date = $_POST["date"];
    $brand = $_POST["brand"];
    $item = $_POST["item"];
    $ref = $_POST["ref"];
    $color = $_POST["color"];
    $size = $_POST["size"];
    $category = $_POST["category"];
    $cost = $_POST["cost"];
    $weight = $_POST["weight"];

    $sql = "UPDATE items SET 
            sku_item = ?, date_item = ?, brand_item = ?, item_item = ?, ref_item = ?, 
            color_item = ?, size_item = ?, category_item = ?, cost_item = ?, weight_item = ?
            WHERE upsi_item = ?";

    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssssssssss", $sku, $date, $brand, $item, $ref, $color, $size, $category, $cost, $weight, $upsi);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }

    $stmt->close();
    $mysqli->close();
}