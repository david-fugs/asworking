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
    // Consulta con subconsultas para evitar problemas con LEFT JOINs múltiples
    $sql = "
    SELECT 
        s.sell_order,
        MAX(s.date) as date,
        SUM(s.quantity) as quantity,
        SUM(s.tax) as tax, -- Taxes
        SUM(s.withheld_tax) as withheld_tax, -- Taxes
        SUM(s.international_fee) as international_fee, -- Fees
        SUM(s.ad_fee) as ad_fee, -- Fees
        SUM(s.other_fee) as other_fee, -- Fees
        COALESCE(MAX(ss.final_fee), 0) as final_fee, -- Fees
        COALESCE(MAX(ss.fixed_charge), 0) as fixed_charge, -- Fees
        COALESCE(MAX(st.store_name), 'Unknown') as store_name,
        MAX(s.item_price) as item_price,
        SUM(s.total_item) as total_item,
        -- SAFETCLAIM: Sumar los valores relevantes
        COALESCE(SUM(sc.safet_reimbursement), 0) as safet_reimbursement, -- Fees
        COALESCE(SUM(sc.shipping_reimbursement), 0) as shipping_reimbursement, -- Fees
        COALESCE(SUM(sc.tax_reimbursement), 0) as tax_reimbursement, -- Taxes (puede ser negativo)
        COALESCE(SUM(sc.other_fee_reimbursement), 0) as other_fee_reimbursement, -- Fees
        COALESCE(SUM(sc.label_avoid), 0) as label_avoid, -- Fees
        COALESCE(SUM(sc.net_reimbursement), 0) as net_reimbursement, -- Fees
        COALESCE(MAX(sc.safetclaim_date), NULL) as safetclaim_date,
        -- SHIPPING y SHIPPING_RETURN: Sumar los valores relevantes
        COALESCE(SUM(sh.shipping_paid), 0) as shipping_paid, -- Fees
        COALESCE(SUM(sh.shipping_adjust), 0) as shipping_adjust, -- Fees
        COALESCE(SUM(sr.billing_return), 0) as billing_return -- Fees
    FROM sell s
    LEFT JOIN sell_summary ss ON s.sell_order = ss.sell_order
    LEFT JOIN store st ON s.id_store = st.id_store
    LEFT JOIN safetclaim sc ON s.sell_order = sc.sell_order
    LEFT JOIN shipping sh ON s.sell_order = sh.sell_order
    LEFT JOIN shipping_return sr ON s.sell_order = sr.sell_order
    WHERE YEAR(s.date) = $year AND MONTH(s.date) = $month
    GROUP BY s.sell_order
    ORDER BY date DESC, s.sell_order ASC
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
    echo '<th>Safet Reimbursement</th>';
    echo '<th>Shipping Reimbursement</th>';
    echo '<th>Tax Reimbursement</th>';
    echo '<th>Other Fee Reimbursement</th>';
    echo '<th>Shipping Paid</th>';
    echo '<th>Shipping Adjust</th>';
    echo '<th>Billing Return</th>';
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
        $safetReimbursement = $row['safet_reimbursement'] ?? 0;
        $shippingReimbursement = $row['shipping_reimbursement'] ?? 0;
        $taxReimbursement = $row['tax_reimbursement'] ?? 0;
        $otherFeeReimbursement = $row['other_fee_reimbursement'] ?? 0;
        $shippingPaid = $row['shipping_paid'] ?? 0;
        $shippingAdjust = $row['shipping_adjust'] ?? 0;
        $billingReturn = $row['billing_return'] ?? 0;
        $labelAvoid = $row['label_avoid'] ?? 0;
        $netReimbursement = $row['net_reimbursement'] ?? 0;
        $safetclaimDate = $row['safetclaim_date'] ?? '';

        // Sumar los nuevos campos al total de fees
        $totalFees = $tax + $withheldTax + $internationalFee + $adFee + $otherFee + 
                     $finalFee + $fixedCharge + $safetReimbursement + $shippingReimbursement +
                     $taxReimbursement + $otherFeeReimbursement + $shippingPaid + 
                     $shippingAdjust + $billingReturn + $labelAvoid + $netReimbursement;
        
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
        echo '<td>$' . number_format($safetReimbursement, 2) . '</td>';
        echo '<td>$' . number_format($shippingReimbursement, 2) . '</td>';
        echo '<td>$' . number_format($taxReimbursement, 2) . '</td>';
        echo '<td>$' . number_format($otherFeeReimbursement, 2) . '</td>';
        echo '<td>$' . number_format($shippingPaid, 2) . '</td>';
        echo '<td>$' . number_format($shippingAdjust, 2) . '</td>';
        echo '<td>$' . number_format($billingReturn, 2) . '</td>';
        echo '<td>$' . number_format($labelAvoid, 2) . '</td>';
        echo '<td>$' . number_format($netReimbursement, 2) . '</td>';
        echo '<td>' . ($safetclaimDate ? date('m/d/Y', strtotime($safetclaimDate)) : '') . '</td>';
        echo '<td style="background-color: #f0f0f0; font-weight: bold;">$' . number_format($totalFees, 2) . '</td>';
        echo '</tr>';
    }

    // Fila de totales
    echo '<tr style="background-color: #632B8B; color: white; font-weight: bold;">';
    echo '<td colspan="20">GRAND TOTAL</td>';
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