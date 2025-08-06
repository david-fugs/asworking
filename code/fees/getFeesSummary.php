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
    // Resumen de impuestos con todas las tablas
    $sqlItems = "
    SELECT 
        SUM(s.tax) as total_tax,
        SUM(s.withheld_tax) as total_withheld_tax,
        SUM(s.international_fee) as total_international_fee,
        SUM(s.ad_fee) as total_ad_fee,
        SUM(s.other_fee) as total_other_fee,
        SUM(DISTINCT ss.final_fee) as total_final_fee,
        SUM(DISTINCT ss.fixed_charge) as total_fixed_charge,
        SUM(sc.safet_reimbursement) as total_safet_reimbursement,
        SUM(sc.shipping_reimbursement) as total_shipping_reimbursement,
        SUM(sc.tax_reimbursement) as total_tax_reimbursement,
        SUM(sc.other_fee_reimbursement) as total_other_fee_reimbursement,
        SUM(sh.shipping_paid) as total_shipping_paid,
        SUM(sh.shipping_adjust) as total_shipping_adjust,
        SUM(sr.billing_return) as total_billing_return
    FROM sell s
    LEFT JOIN sell_summary ss ON s.sell_order = ss.sell_order
    LEFT JOIN safetclaim sc ON s.sell_order = sc.sell_order
    LEFT JOIN shipping sh ON s.sell_order = sh.sell_order
    LEFT JOIN shipping_return sr ON s.sell_order = sr.sell_order
    WHERE YEAR(s.date) = ? AND MONTH(s.date) = ?
    ";

    $stmt = $mysqli->prepare($sqlItems);
    if (!$stmt) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'SQL prepare error: ' . $mysqli->error
        ]);
        exit;
    }
    
    $stmt->bind_param("ii", $year, $month);
    $stmt->execute();
    $resultItems = $stmt->get_result();
    $summaryData = $resultItems->fetch_assoc();
    $resultOrders = $stmt2->get_result();
    // Calcular totales incluyendo todas las fuentes de impuestos y fees
    $totalTaxes = ($summaryData['total_tax'] ?? 0) + 
                  ($summaryData['total_withheld_tax'] ?? 0) +
                  ($summaryData['total_tax_reimbursement'] ?? 0);
    
    $totalFees = ($summaryData['total_international_fee'] ?? 0) + 
                 ($summaryData['total_ad_fee'] ?? 0) + 
                 ($summaryData['total_other_fee'] ?? 0) + 
                 ($summaryData['total_final_fee'] ?? 0) + 
                 ($summaryData['total_fixed_charge'] ?? 0) +
                 ($summaryData['total_safet_reimbursement'] ?? 0) +
                 ($summaryData['total_shipping_reimbursement'] ?? 0) +
                 ($summaryData['total_other_fee_reimbursement'] ?? 0) +
                 ($summaryData['total_shipping_paid'] ?? 0) +
                 ($summaryData['total_shipping_adjust'] ?? 0) +
                 ($summaryData['total_billing_return'] ?? 0);
    
    $grandTotal = $totalTaxes + $totalFees;

    // Datos para el gráfico con todos los campos
    $chartData = [
        'taxes' => $summaryData['total_tax'] ?? 0,
        'withheld_taxes' => $summaryData['total_withheld_tax'] ?? 0,
        'international_fees' => $summaryData['total_international_fee'] ?? 0,
        'ad_fees' => $summaryData['total_ad_fee'] ?? 0,
        'other_fees' => $summaryData['total_other_fee'] ?? 0,
        'final_fees' => $summaryData['total_final_fee'] ?? 0,
        'fixed_charges' => $summaryData['total_fixed_charge'] ?? 0,
        'safet_reimbursement' => $summaryData['total_safet_reimbursement'] ?? 0,
        'shipping_reimbursement' => $summaryData['total_shipping_reimbursement'] ?? 0,
        'tax_reimbursement' => $summaryData['total_tax_reimbursement'] ?? 0,
        'other_fee_reimbursement' => $summaryData['total_other_fee_reimbursement'] ?? 0,
        'shipping_paid' => $summaryData['total_shipping_paid'] ?? 0,
        'shipping_adjust' => $summaryData['total_shipping_adjust'] ?? 0,
        'billing_return' => $summaryData['total_billing_return'] ?? 0
    ];

    // Estadísticas adicionales
    $sqlStats = "
    SELECT 
        COUNT(DISTINCT s.sell_order) as total_orders,
        COUNT(s.id) as total_items
    FROM sell s
    WHERE YEAR(s.date) = ? AND MONTH(s.date) = ?
    ";

    $stmt3 = $mysqli->prepare($sqlStats);
    if (!$stmt3) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'SQL prepare error: ' . $mysqli->error
        ]);
        exit;
    }
    
    $stmt3->bind_param("ii", $year, $month);
    $stmt3->execute();
    $resultStats = $stmt3->get_result();
    $statsData = $resultStats->fetch_assoc();

    echo json_encode([
        'status' => 'success',
        'data' => [
            'total_taxes' => $totalTaxes,
            'total_fees' => $totalFees,
            'total_withheld' => $summaryData['total_withheld_tax'] ?? 0,
            'grand_total' => $grandTotal,
            'total_orders' => $statsData['total_orders'] ?? 0,
            'total_items' => $statsData['total_items'] ?? 0
        ],
        'chartData' => $chartData,
        'breakdown' => [
            'tax' => $summaryData['total_tax'] ?? 0,
            'withheld_tax' => $summaryData['total_withheld_tax'] ?? 0,
            'international_fee' => $summaryData['total_international_fee'] ?? 0,
            'ad_fee' => $summaryData['total_ad_fee'] ?? 0,
            'other_fee' => $summaryData['total_other_fee'] ?? 0,
            'final_fee' => $summaryData['total_final_fee'] ?? 0,
            'fixed_charge' => $summaryData['total_fixed_charge'] ?? 0,
            'safet_reimbursement' => $summaryData['total_safet_reimbursement'] ?? 0,
            'shipping_reimbursement' => $summaryData['total_shipping_reimbursement'] ?? 0,
            'tax_reimbursement' => $summaryData['total_tax_reimbursement'] ?? 0,
            'other_fee_reimbursement' => $summaryData['total_other_fee_reimbursement'] ?? 0,
            'shipping_paid' => $summaryData['total_shipping_paid'] ?? 0,
            'shipping_adjust' => $summaryData['total_shipping_adjust'] ?? 0,
            'billing_return' => $summaryData['total_billing_return'] ?? 0
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
