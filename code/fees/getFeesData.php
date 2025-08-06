<?php
session_start();
include("../../conexion.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if (!isset($_POST['month']) || !isset($_POST['year'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    exit;
}

$month = str_pad($_POST['month'], 2, '0', STR_PAD_LEFT);
$year = $_POST['year'];

try {
    // Consulta principal que combina datos de todas las tablas con impuestos y fees
    $sql = "
    SELECT 
        s.sell_order,
        s.quantity,
        s.tax,
        s.withheld_tax,
        s.international_fee,
        s.ad_fee,
        s.other_fee,
        s.date,
        ss.final_fee,
        ss.fixed_charge,
        st.store_name,
        suc.code_sucursal,
        sc.safet_reimbursement,
        sc.shipping_reimbursement,
        sc.tax_reimbursement,
        sc.other_fee_reimbursement,
        sh.shipping_paid,
        sh.shipping_adjust,
        sr.billing_return
    FROM sell s
    LEFT JOIN sell_summary ss ON s.sell_order = ss.sell_order
    LEFT JOIN store st ON s.id_store = st.id_store
    LEFT JOIN sucursal suc ON s.id_sucursal = suc.id_sucursal
    LEFT JOIN safetclaim sc ON s.sell_order = sc.sell_order
    LEFT JOIN shipping sh ON s.sell_order = sh.sell_order
    LEFT JOIN shipping_return sr ON s.sell_order = sr.sell_order
    WHERE YEAR(s.date) = ? AND MONTH(s.date) = ?
    ORDER BY s.date DESC, s.sell_order ASC
    ";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'SQL prepare error: ' . $mysqli->error
        ]);
        exit;
    }
    
    $stmt->bind_param("ii", $year, $month);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    $sellOrderFees = []; // Para evitar duplicar final_fee y fixed_charge por sell_order

    while ($row = $result->fetch_assoc()) {
        // Solo agregar final_fee y fixed_charge una vez por sell_order
        if (!isset($sellOrderFees[$row['sell_order']])) {
            $sellOrderFees[$row['sell_order']] = [
                'final_fee' => $row['final_fee'] ?? 0,
                'fixed_charge' => $row['fixed_charge'] ?? 0
            ];
            $row['final_fee'] = $row['final_fee'] ?? 0;
            $row['fixed_charge'] = $row['fixed_charge'] ?? 0;
        } else {
            // Para items adicionales del mismo sell_order, no mostrar final_fee y fixed_charge
            $row['final_fee'] = 0;
            $row['fixed_charge'] = 0;
        }

        $data[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'data' => $data,
        'total_records' => count($data)
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
