<?php
include 'conexion.php';

echo "=== Testing Cancellation Date Update ===\n\n";

// Update the cancellation date for order '123'
$order_id = '123';
$new_date = '2025-07-02';

echo "Updating cancellation_date for order '$order_id' to '$new_date'...\n";

$update_sql = "UPDATE cancellations SET cancellation_date = ?, updated_at = NOW() WHERE order_id = ?";
$stmt = $mysqli->prepare($update_sql);
$stmt->bind_param("ss", $new_date, $order_id);

if ($stmt->execute()) {
    echo "✓ Update successful!\n\n";
    
    // Verify the update
    echo "Verifying update:\n";
    $check_sql = "SELECT order_id, net_cancellation, cancellation_date, updated_at FROM cancellations WHERE order_id = ?";
    $check_stmt = $mysqli->prepare($check_sql);
    $check_stmt->bind_param("s", $order_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        echo "   Order: " . $row['order_id'] . "\n";
        echo "   Net Cancellation: $" . number_format($row['net_cancellation'], 2) . "\n";
        echo "   Cancellation Date: " . $row['cancellation_date'] . "\n";
        echo "   Updated At: " . $row['updated_at'] . "\n";
    }
} else {
    echo "✗ Update failed: " . $mysqli->error . "\n";
}

echo "\n=== Test completed ===\n";
$mysqli->close();
?>
