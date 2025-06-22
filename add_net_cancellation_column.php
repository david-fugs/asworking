<?php
include("conexion.php");

// Verificar si la columna net_cancellation existe en la tabla cancellations
$table_check = "SHOW COLUMNS FROM cancellations LIKE 'net_cancellation'";
$result = $mysqli->query($table_check);

if ($result->num_rows == 0) {
    // La columna no existe, crearla
    $add_column = "ALTER TABLE cancellations ADD COLUMN net_cancellation DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER other_fee_refund";
    if ($mysqli->query($add_column)) {
        echo "✅ Column 'net_cancellation' added successfully to 'cancellations' table.\n";
    } else {
        echo "❌ Error adding column: " . $mysqli->error . "\n";
        exit(1);
    }
} else {
    echo "✅ Column 'net_cancellation' already exists in 'cancellations' table.\n";
}

// Actualizar registros existentes con net_cancellation calculado
$update_sql = "UPDATE cancellations SET 
               net_cancellation = (refund_amount + shipping_refund + tax_refund - final_fee_refund - fixed_charge_refund - other_fee_refund)
               WHERE net_cancellation IS NULL OR net_cancellation = 0";

$result = $mysqli->query("SELECT COUNT(*) as count FROM cancellations WHERE net_cancellation IS NULL OR net_cancellation = 0");
$count = $result->fetch_assoc()['count'];

if ($count > 0) {
    echo "📝 Found $count records with null or zero net_cancellation values.\n";
    echo "🔄 Updating these records...\n";
    
    if ($mysqli->query($update_sql)) {
        $affected_rows = $mysqli->affected_rows;
        echo "✅ Updated $affected_rows records with calculated net_cancellation values.\n";
    } else {
        echo "❌ Error updating records: " . $mysqli->error . "\n";
    }
} else {
    echo "✅ All records already have net_cancellation values.\n";
}

// Mostrar la estructura actual de la tabla
echo "📋 Current structure of 'cancellations' table:\n";
$structure = $mysqli->query("DESCRIBE cancellations");
while ($row = $structure->fetch_assoc()) {
    $null = $row['Null'] == 'YES' ? 'YES' : 'NO';
    $default = $row['Default'] ? $row['Default'] : '-';
    echo "   {$row['Field']} - {$row['Type']} - $null - $default\n";
}

$mysqli->close();
?>
