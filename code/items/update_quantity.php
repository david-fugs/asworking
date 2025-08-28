<?php
// update_quantity.php
include_once '../../conexion.php';

header('Content-Type: application/json');

// Require UPC and SKU
if (!isset($_POST['upc_item']) || !isset($_POST['sku_item'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing upc_item or sku_item.']);
    exit;
}

$upc_item = strtoupper(trim($_POST['upc_item']));
$sku_item = strtoupper(trim($_POST['sku_item']));

$upc_esc = $mysqli->real_escape_string($upc_item);
$sku_esc = $mysqli->real_escape_string($sku_item);

// If addQty is provided, perform an atomic increment
if (isset($_POST['addQty'])) {
    $addQty = intval($_POST['addQty']);

    // Try to update existing inventory row
    $sql = "UPDATE inventory SET quantity_inventory = quantity_inventory + ? WHERE upc_inventory = ? AND sku_inventory = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $mysqli->error]);
        exit;
    }
    $stmt->bind_param('iss', $addQty, $upc_esc, $sku_esc);
    if (!$stmt->execute()) {
        echo json_encode(['status' => 'error', 'message' => 'Execute failed: ' . $stmt->error]);
        $stmt->close();
        exit;
    }

    // If no rows were updated, insert a new inventory row for this UPC+SKU
    if ($stmt->affected_rows === 0) {
        $stmt->close();
        $insertSql = "INSERT INTO inventory (upc_inventory, sku_inventory, quantity_inventory) VALUES (?, ?, ?)";
        $ins = $mysqli->prepare($insertSql);
        if (!$ins) {
            echo json_encode(['status' => 'error', 'message' => 'Insert prepare failed: ' . $mysqli->error]);
            exit;
        }
        $ins->bind_param('ssi', $upc_esc, $sku_esc, $addQty);
        if (!$ins->execute()) {
            echo json_encode(['status' => 'error', 'message' => 'Insert execute failed: ' . $ins->error]);
            $ins->close();
            exit;
        }
        $ins->close();
    } else {
        $stmt->close();
    }

    echo json_encode(['status' => 'success', 'message' => 'Quantity incremented by ' . $addQty]);
    exit;
}

// Backward compatible: if quantity_inventory is provided, set absolute value
if (isset($_POST['quantity_inventory'])) {
    $quantity_inventory = intval($_POST['quantity_inventory']);

    $sql = "UPDATE inventory SET quantity_inventory = ? WHERE upc_inventory = ? AND sku_inventory = ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed: ' . $mysqli->error]);
        exit;
    }
    $stmt->bind_param('iss', $quantity_inventory, $upc_esc, $sku_esc);
    if ($stmt->execute()) {
        if ($stmt->affected_rows === 0) {
            // Insert if not exists
            $stmt->close();
            $insertSql = "INSERT INTO inventory (upc_inventory, sku_inventory, quantity_inventory) VALUES (?, ?, ?)";
            $ins = $mysqli->prepare($insertSql);
            if (!$ins) {
                echo json_encode(['status' => 'error', 'message' => 'Insert prepare failed: ' . $mysqli->error]);
                exit;
            }
            $ins->bind_param('ssi', $upc_esc, $sku_esc, $quantity_inventory);
            if (!$ins->execute()) {
                echo json_encode(['status' => 'error', 'message' => 'Insert execute failed: ' . $ins->error]);
                $ins->close();
                exit;
            }
            $ins->close();
            echo json_encode(['status' => 'success', 'message' => 'Quantity set (inserted)']);
            exit;
        }
        $stmt->close();
        echo json_encode(['status' => 'success', 'message' => 'Quantity updated successfully.']);
        exit;
    } else {
        $err = $stmt->error;
        $stmt->close();
        echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity: ' . $err]);
        exit;
    }
}

echo json_encode(['status' => 'error', 'message' => 'Missing parameters.']);
?>
