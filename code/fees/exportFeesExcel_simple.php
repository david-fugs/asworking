<?php
session_start();
include("../../conexion.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
    exit;
}

if (!isset($_GET['month']) || !isset($_GET['year'])) {
    die('Missing parameters');
}

$month = str_pad($_GET['month'], 2, '0', STR_PAD_LEFT);
$year = $_GET['year'];

// Configurar headers para descarga de Excel
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Tax_Fees_Report_' . $year . '_' . $month . '.xls"');
header('Cache-Control: max-age=0');

try {
    // Consulta simplificada
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
        s.item_price,
        s.total_item,
        COALESCE(sc.label_avoid, 0) as label_avoid,
        COALESCE(sc.net_reimbursement, 0) as net_reimbursement,
        COALESCE(sc.safetclaim_date, NULL) as safetclaim_date
    FROM sell s
    LEFT JOIN sell_summary ss ON s.sell_order = ss.sell_order
    LEFT JOIN store st ON s.id_store = st.id_store
    LEFT JOIN safetclaim sc ON s.sell_order = sc.sell_order
    WHERE YEAR(s.date) = ? AND MONTH(s.date) = ?
    ORDER BY s.date DESC, s.sell_order ASC
    ";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        echo '<h3>Error in SQL query</h3>';
        echo '<p>' . htmlspecialchars($mysqli->error) . '</p>';
        exit;
    }
    
    $stmt->bind_param("ii", $year, $month);
    $stmt->execute();
    $result = $stmt->get_result();

    // Iniciar tabla HTML para Excel
    echo '<table border="1">';
    echo '<tr style="background-color: #632B8B; color: white; font-weight: bold;">';
    echo '<th>Date</th>';
    echo '<th>Sell Order</th>';
    echo '<th>Store</th>';
    echo '<th>Quantity</th>';
    echo '<th>Item Price</th>';
    echo '<th>Total Item</th>';
    echo '<th>Tax</th>';
    echo '<th>Withheld Tax</th>';
    echo '<th>International Fee</th>';
    echo '<th>Ad Fee</th>';
    echo '<th>Other Fee</th>';
    echo '<th>Final Fee</th>';
    echo '<th>Fixed Charge</th>';
    echo '<th>Label Avoid</th>';
    echo '<th>Net Reimbursement</th>';
    echo '<th>Safe-T Claim Date</th>';
    echo '<th>Total Fees</th>';
    echo '</tr>';

    $sellOrderFees = [];
    $grandTotalFees = 0;
    $totalRows = 0;

    while ($row = $result->fetch_assoc()) {
        $totalRows++;
        
        // Solo contar final_fee y fixed_charge una vez por sell_order
        $finalFee = 0;
        $fixedCharge = 0;
        
        if (!isset($sellOrderFees[$row['sell_order']])) {
            $sellOrderFees[$row['sell_order']] = true;
            $finalFee = $row['final_fee'] ?? 0;
            $fixedCharge = $row['fixed_charge'] ?? 0;
        }

        $tax = $row['tax'] ?? 0;
        $withheldTax = $row['withheld_tax'] ?? 0;
        $internationalFee = $row['international_fee'] ?? 0;
        $adFee = $row['ad_fee'] ?? 0;
        $otherFee = $row['other_fee'] ?? 0;

        $labelAvoid = $row['label_avoid'] ?? 0;
        $netReimbursement = $row['net_reimbursement'] ?? 0;
        $safetclaimDate = $row['safetclaim_date'] ?? '';

        $totalFees = $tax + $withheldTax + $internationalFee + $adFee + $otherFee + $finalFee + $fixedCharge + $labelAvoid + $netReimbursement;
        $grandTotalFees += $totalFees;

        echo '<tr>';
        echo '<td>' . date('m/d/Y', strtotime($row['date'])) . '</td>';
        echo '<td>' . ($row['sell_order'] ?? '') . '</td>';
        echo '<td>' . ($row['store_name'] ?? 'N/A') . '</td>';
        echo '<td>' . ($row['quantity'] ?? 0) . '</td>';
        echo '<td>$' . number_format($row['item_price'] ?? 0, 2) . '</td>';
        echo '<td>$' . number_format($row['total_item'] ?? 0, 2) . '</td>';
        echo '<td>$' . number_format($tax, 2) . '</td>';
        echo '<td>$' . number_format($withheldTax, 2) . '</td>';
        echo '<td>$' . number_format($internationalFee, 2) . '</td>';
        echo '<td>$' . number_format($adFee, 2) . '</td>';
        echo '<td>$' . number_format($otherFee, 2) . '</td>';
        echo '<td>$' . number_format($finalFee, 2) . '</td>';
        echo '<td>$' . number_format($fixedCharge, 2) . '</td>';
        echo '<td>$' . number_format($labelAvoid, 2) . '</td>';
        echo '<td>$' . number_format($netReimbursement, 2) . '</td>';
        echo '<td>' . ($safetclaimDate ? date('m/d/Y', strtotime($safetclaimDate)) : '') . '</td>';
        echo '<td style="background-color: #f0f0f0; font-weight: bold;">$' . number_format($totalFees, 2) . '</td>';
        echo '</tr>';
    }

    // Fila de totales
    echo '<tr style="background-color: #632B8B; color: white; font-weight: bold;">';
    echo '<td colspan="16">GRAND TOTAL</td>';
    echo '<td>$' . number_format($grandTotalFees, 2) . '</td>';
    echo '</tr>';

    echo '</table>';

    // Información adicional
    echo '<br><br>';
    echo '<h3>Report Summary</h3>';
    echo '<table border="1">';
    echo '<tr><td><strong>Report Period:</strong></td><td>' . date('F Y', mktime(0, 0, 0, $month, 1, $year)) . '</td></tr>';
    echo '<tr><td><strong>Total Records:</strong></td><td>' . $totalRows . '</td></tr>';
    echo '<tr><td><strong>Total Fees Amount:</strong></td><td>$' . number_format($grandTotalFees, 2) . '</td></tr>';
    echo '<tr><td><strong>Generated On:</strong></td><td>' . date('Y-m-d H:i:s') . '</td></tr>';
    echo '</table>';

} catch (Exception $e) {
    echo '<h3>Error generating report</h3>';
    echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>
