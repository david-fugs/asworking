<?php
include 'conexion.php';

echo "Testing SafetClaim date functionality:\n\n";

// Verificar estructura de la tabla
echo "1. Checking table structure:\n";
$result = $mysqli->query('DESCRIBE safetclaim');
while($row = $result->fetch_assoc()) {
    if($row['Field'] == 'safetclaim_date') {
        echo "✓ safetclaim_date column found: " . $row['Type'] . "\n";
    }
}

echo "\n2. Current safetclaim records:\n";
$query = "SELECT id_safetclaim, sell_order, safet_reimbursement, safetclaim_date 
          FROM safetclaim 
          ORDER BY id_safetclaim LIMIT 5";
$result = $mysqli->query($query);

echo "ID | SELL_ORDER | SAFET_REIMB | SAFETCLAIM_DATE\n";
echo str_repeat("-", 50) . "\n";

while($row = $result->fetch_assoc()) {
    printf("%2d | %10s | %11s | %15s\n", 
           $row['id_safetclaim'], 
           $row['sell_order'], 
           '$' . number_format($row['safet_reimbursement'], 2), 
           $row['safetclaim_date'] ?: 'NULL');
}

echo "\n3. Testing sample date update:\n";
// Actualizar un registro con fecha
$update_sql = "UPDATE safetclaim SET safetclaim_date = '2025-06-26' WHERE sell_order = '123'";
if($mysqli->query($update_sql)) {
    echo "✓ Successfully updated safetclaim_date for sell_order 123\n";
    
    // Verificar la actualización
    $check_sql = "SELECT sell_order, safetclaim_date FROM safetclaim WHERE sell_order = '123'";
    $check_result = $mysqli->query($check_sql);
    if($check_row = $check_result->fetch_assoc()) {
        echo "✓ Verified: sell_order " . $check_row['sell_order'] . " now has date: " . $check_row['safetclaim_date'] . "\n";
    }
} else {
    echo "✗ Error updating: " . $mysqli->error . "\n";
}

$mysqli->close();
echo "\nSafetClaim date functionality test completed!\n";
?>
