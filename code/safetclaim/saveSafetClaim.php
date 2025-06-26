<?php
include("../../conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {        $sell_order = $_POST['sell_order'] ?? '';
        $id_sell = $_POST['id_sell'] ?? '';
        $safet_reimbursement = (float)($_POST['safet_reimbursement'] ?? 0);
        $shipping_reimbursement = (float)($_POST['shipping_reimbursement'] ?? 0);
        $tax_reimbursement = (float)($_POST['tax_reimbursement'] ?? 0);
        $label_avoid = (float)($_POST['label_avoid'] ?? 0);
        $other_fee_reimbursement = (float)($_POST['other_fee_reimbursement'] ?? 0);
        $safetclaim_date = !empty($_POST['safetclaim_date']) ? $_POST['safetclaim_date'] : null;
        
        // Calculate Net Reimbursement
        $net_reimbursement = $safet_reimbursement + $shipping_reimbursement + $label_avoid + $other_fee_reimbursement;

        // Validate required fields
        if (empty($sell_order) || empty($id_sell)) {
            throw new Exception('Missing required fields: sell_order or id_sell');
        }

        // Check if record already exists
        $check_sql = "SELECT id_safetclaim FROM safetclaim WHERE id_sell = ?";
        $check_stmt = $mysqli->prepare($check_sql);
        $check_stmt->bind_param("i", $id_sell);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {            // Update existing record
            $update_sql = "UPDATE safetclaim SET 
                          safet_reimbursement = ?, 
                          shipping_reimbursement = ?, 
                          tax_reimbursement = ?, 
                          label_avoid = ?, 
                          other_fee_reimbursement = ?,
                          net_reimbursement = ?,
                          safetclaim_date = ?,
                          updated_at = NOW()
                          WHERE id_sell = ?";
            $stmt = $mysqli->prepare($update_sql);
            $stmt->bind_param("ddddddsi", $safet_reimbursement, $shipping_reimbursement, $tax_reimbursement, $label_avoid, $other_fee_reimbursement, $net_reimbursement, $safetclaim_date, $id_sell);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Safe-T Claim information updated successfully']);
            } else {
                throw new Exception('Error updating Safe-T Claim information: ' . $stmt->error);
            }
        } else {            // Insert new record
            $insert_sql = "INSERT INTO safetclaim (id_sell, sell_order, safet_reimbursement, shipping_reimbursement, tax_reimbursement, label_avoid, other_fee_reimbursement, net_reimbursement, safetclaim_date, created_at, updated_at) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $mysqli->prepare($insert_sql);
            $stmt->bind_param("isdddddds", $id_sell, $sell_order, $safet_reimbursement, $shipping_reimbursement, $tax_reimbursement, $label_avoid, $other_fee_reimbursement, $net_reimbursement, $safetclaim_date);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Safe-T Claim information saved successfully']);
            } else {
                throw new Exception('Error saving Safe-T Claim information: ' . $stmt->error);
            }
        }

        $stmt->close();
        $check_stmt->close();

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}

$mysqli->close();
?>
