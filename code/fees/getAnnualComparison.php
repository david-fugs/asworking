<?php
session_start();
include("../../conexion.php");

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if (!isset($_POST['year'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing year parameter']);
    exit;
}

$year = $_POST['year'];

try {
    $monthlyData = [];
    $chartData = [
        'taxes' => [],
        'fees' => [],
        'total' => []
    ];

    // Obtener datos para cada mes
    for ($month = 1; $month <= 12; $month++) {
        // Datos de impuestos por items
        $sqlItems = "
        SELECT 
            SUM(tax) as total_tax,
            SUM(withheld_tax) as total_withheld_tax,
            SUM(international_fee) as total_international_fee,
            SUM(ad_fee) as total_ad_fee,
            SUM(other_fee) as total_other_fee
        FROM sell 
        WHERE YEAR(date) = ? AND MONTH(date) = ?
        ";

        $stmt = $mysqli->prepare($sqlItems);
        $stmt->bind_param("ii", $year, $month);
        $stmt->execute();
        $resultItems = $stmt->get_result();
        $itemsData = $resultItems->fetch_assoc();

        // Datos de fees por sell_order
        $sqlOrders = "
        SELECT 
            SUM(DISTINCT ss.final_fee) as total_final_fee,
            SUM(DISTINCT ss.fixed_charge) as total_fixed_charge
        FROM sell_summary ss
        INNER JOIN sell s ON ss.sell_order = s.sell_order
        WHERE YEAR(s.date) = ? AND MONTH(s.date) = ?
        ";

        $stmt2 = $mysqli->prepare($sqlOrders);
        $stmt2->bind_param("ii", $year, $month);
        $stmt2->execute();
        $resultOrders = $stmt2->get_result();
        $ordersData = $resultOrders->fetch_assoc();

        // Calcular totales del mes
        $taxes = ($itemsData['total_tax'] ?? 0) + ($itemsData['total_withheld_tax'] ?? 0);
        $fees = ($itemsData['total_international_fee'] ?? 0) + 
                ($itemsData['total_ad_fee'] ?? 0) + 
                ($itemsData['total_other_fee'] ?? 0) + 
                ($ordersData['total_final_fee'] ?? 0) + 
                ($ordersData['total_fixed_charge'] ?? 0);
        $total = $taxes + $fees;

        $monthlyData[$month] = [
            'taxes' => $itemsData['total_tax'] ?? 0,
            'withheld_tax' => $itemsData['total_withheld_tax'] ?? 0,
            'international_fee' => $itemsData['total_international_fee'] ?? 0,
            'ad_fee' => $itemsData['total_ad_fee'] ?? 0,
            'other_fee' => $itemsData['total_other_fee'] ?? 0,
            'final_fee' => $ordersData['total_final_fee'] ?? 0,
            'fixed_charge' => $ordersData['total_fixed_charge'] ?? 0,
            'total_taxes' => $taxes,
            'total_fees' => $fees,
            'grand_total' => $total
        ];

        // Datos para el gráfico
        $chartData['taxes'][] = $taxes;
        $chartData['fees'][] = $fees;
        $chartData['total'][] = $total;
    }

    // Calcular estadísticas anuales
    $annualStats = [
        'total_taxes' => array_sum($chartData['taxes']),
        'total_fees' => array_sum($chartData['fees']),
        'grand_total' => array_sum($chartData['total']),
        'avg_monthly_taxes' => array_sum($chartData['taxes']) / 12,
        'avg_monthly_fees' => array_sum($chartData['fees']) / 12,
        'highest_month_taxes' => max($chartData['taxes']),
        'lowest_month_taxes' => min($chartData['taxes']),
        'highest_month_fees' => max($chartData['fees']),
        'lowest_month_fees' => min($chartData['fees'])
    ];

    echo json_encode([
        'status' => 'success',
        'data' => $monthlyData,
        'chartData' => $chartData,
        'annualStats' => $annualStats,
        'year' => $year
    ]);

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
