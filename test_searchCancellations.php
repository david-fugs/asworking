<?php
// Simulate a request to searchCancellations.php
$_POST['sell_order'] = '123';

// Set the working directory to the cancellations folder
chdir('code/cancellations');

// Capture output
ob_start();
include 'searchCancellations.php';
$output = ob_get_clean();

echo "=== Testing searchCancellations.php for order '123' ===\n\n";
echo "HTML Output:\n";
echo $output;

echo "\n=== Test completed ===\n";
?>
