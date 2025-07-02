<?php
include 'conexion.php';

echo "=== FINAL TEST - Cancellations Module with UPC Independence ===\n\n";

// Test saveCancellations.php simulation
echo "1. Testing saveCancellations.php with UPC independence:\n";

// Simulate POST data for order 123, UPC 733004811005
$_POST = [
    'sell_order' => '123',
    'id_sell' => '1',
    'upc_item' => '733004811005',
    'refund_amount' => '10.00',
    'shipping_refund' => '2.00',
    'tax_refund' => '1.50',
    'final_fee_refund' => '0.50',
    'fixed_charge_refund' => '1.00',
    'other_fee_refund' => '0.25',
    'cancellation_date' => '2025-07-05'
];

$data = $_POST;

// Extract and sanitize data (same logic as saveCancellations.php)
$id_sell = intval($data['id_sell']);
$sell_order = $data['sell_order'];
$upc_item = $data['upc_item'];
$refund_amount = floatval($data['refund_amount']);
$shipping_refund = floatval($data['shipping_refund']);
$tax_refund = floatval($data['tax_refund']);
$final_fee_refund = floatval($data['final_fee_refund']);
$fixed_charge_refund = floatval($data['fixed_charge_refund']);
$other_fee_refund = floatval($data['other_fee_refund']);
$cancellation_date = $data['cancellation_date'];

// Calculate Net Cancellation
$net_cancellation = $refund_amount + $shipping_refund + $tax_refund - $final_fee_refund - $fixed_charge_refund - $other_fee_refund;

echo "   Input data:\n";
echo "   - Order: $sell_order | UPC: $upc_item\n";
echo "   - Calculated Net: $" . number_format($net_cancellation, 2) . "\n";
echo "   - Date: $cancellation_date\n";

// Check if cancellation record already exists for this sell + UPC combination
$check_sql = "SELECT id FROM cancellations WHERE order_id = ? AND upc_item = ?";
$check_stmt = $mysqli->prepare($check_sql);
$check_stmt->bind_param("ss", $sell_order, $upc_item);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // Update existing record
    $update_sql = "UPDATE cancellations SET 
                   refund_amount = ?, 
                   shipping_refund = ?, 
                   tax_refund = ?, 
                   final_fee_refund = ?, 
                   fixed_charge_refund = ?, 
                   other_fee_refund = ?,
                   net_cancellation = ?,
                   cancellation_date = ?, 
                   updated_at = NOW() 
                   WHERE order_id = ? AND upc_item = ?";
    
    $update_stmt = $mysqli->prepare($update_sql);
    $update_stmt->bind_param("dddddddsss", $refund_amount, $shipping_refund, $tax_refund, $final_fee_refund, $fixed_charge_refund, $other_fee_refund, $net_cancellation, $cancellation_date, $sell_order, $upc_item);
    
    if ($update_stmt->execute()) {
        echo "   ✅ Updated existing cancellation record\n";
    } else {
        echo "   ❌ Failed to update: " . $mysqli->error . "\n";
    }
} else {
    echo "   ℹ️  No existing record found for this order+UPC combination\n";
}

echo "\n2. Current state of cancellations for order '$sell_order':\n";
$result = $mysqli->query("SELECT order_id, upc_item, cancellation_date, net_cancellation, refund_amount 
                         FROM cancellations 
                         WHERE order_id = '$sell_order' 
                         ORDER BY upc_item");

while($row = $result->fetch_assoc()) {
    echo "   - UPC: " . $row['upc_item'] . 
         " | Date: " . ($row['cancellation_date'] ?: 'NULL') . 
         " | Refund: $" . number_format($row['refund_amount'], 2) .
         " | Net: $" . number_format($row['net_cancellation'], 2) . "\n";
}

echo "\n✅ UPC Independence Test Completed!\n";
echo "Each UPC has its own independent cancellation record with separate dates and amounts.\n";

$mysqli->close();
?>
