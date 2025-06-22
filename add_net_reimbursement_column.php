<?php
include 'conexion.php';

// Verificar si la columna net_reimbursement existe en la tabla safetclaim
$check_column_sql = "SHOW COLUMNS FROM safetclaim LIKE 'net_reimbursement'";
$result = $mysqli->query($check_column_sql);

if ($result->num_rows == 0) {
    // La columna no existe, agregarla
    $add_column_sql = "ALTER TABLE safetclaim ADD COLUMN net_reimbursement DECIMAL(10,2) DEFAULT 0.00";
    
    if ($mysqli->query($add_column_sql)) {
        echo "✅ Column 'net_reimbursement' added successfully to 'safetclaim' table.\n";
        
        // Actualizar registros existentes calculando net_reimbursement
        $update_sql = "UPDATE safetclaim SET net_reimbursement = (
            COALESCE(safet_reimbursement, 0) + 
            COALESCE(shipping_reimbursement, 0) + 
            COALESCE(label_avoid, 0) + 
            COALESCE(other_fee_reimbursement, 0)
        ) WHERE net_reimbursement = 0.00 OR net_reimbursement IS NULL";
        
        if ($mysqli->query($update_sql)) {
            $affected_rows = $mysqli->affected_rows;
            echo "✅ Updated $affected_rows existing records with calculated net_reimbursement values.\n";
        } else {
            echo "❌ Error updating existing records: " . $mysqli->error . "\n";
        }
        
    } else {
        echo "❌ Error adding column: " . $mysqli->error . "\n";
    }
} else {
    echo "✅ Column 'net_reimbursement' already exists in 'safetclaim' table.\n";
    
    // Verificar si hay registros que necesiten actualización
    $check_nulls_sql = "SELECT COUNT(*) as count FROM safetclaim WHERE net_reimbursement IS NULL OR net_reimbursement = 0.00";
    $null_result = $mysqli->query($check_nulls_sql);
    $null_row = $null_result->fetch_assoc();
    
    if ($null_row['count'] > 0) {
        echo "📝 Found {$null_row['count']} records with null or zero net_reimbursement values.\n";
        echo "🔄 Updating these records...\n";
        
        $update_sql = "UPDATE safetclaim SET net_reimbursement = (
            COALESCE(safet_reimbursement, 0) + 
            COALESCE(shipping_reimbursement, 0) + 
            COALESCE(label_avoid, 0) + 
            COALESCE(other_fee_reimbursement, 0)
        ) WHERE net_reimbursement IS NULL OR net_reimbursement = 0.00";
        
        if ($mysqli->query($update_sql)) {
            $affected_rows = $mysqli->affected_rows;
            echo "✅ Updated $affected_rows records with calculated net_reimbursement values.\n";
        } else {
            echo "❌ Error updating records: " . $mysqli->error . "\n";
        }
    } else {
        echo "✅ All records already have valid net_reimbursement values.\n";
    }
}

// Mostrar estructura actual de la tabla
echo "\n📋 Current structure of 'safetclaim' table:\n";
$structure_sql = "DESCRIBE safetclaim";
$structure_result = $mysqli->query($structure_sql);

while ($column = $structure_result->fetch_assoc()) {
    echo "   {$column['Field']} - {$column['Type']} - {$column['Null']} - {$column['Default']}\n";
}

$mysqli->close();
?>
