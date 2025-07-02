<?php
// Simulate a GET request to getSellToReturn.php
$_GET['sell_order'] = '123';

// Capture output
ob_start();
include 'code/cancellations/getSellToReturn.php';
$output = ob_get_clean();

echo "=== Testing getSellToReturn.php for order '123' ===\n\n";
echo "Response:\n";
echo $output;
echo "\n\n";

// Parse JSON response
$response = json_decode($output, true);
if ($response && isset($response['cancellation'])) {
    echo "Parsed response:\n";
    echo "- Has cancellation: " . ($response['cancellation'] ? "Yes" : "No") . "\n";
    if ($response['cancellation']) {
        echo "- Cancellation Date: " . ($response['cancellation']['cancellation_date'] ?: 'NULL') . "\n";
        echo "- Net Cancellation: $" . number_format($response['cancellation']['net_cancellation'], 2) . "\n";
    }
}

echo "\n=== Test completed ===\n";
?>
