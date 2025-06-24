<?php
session_start();
include("../../conexion.php");

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

// Get filter parameters
$year = isset($_GET['year']) && $_GET['year'] !== '' ? (int)$_GET['year'] : null;
$month = isset($_GET['month']) && $_GET['month'] !== '' ? (int)$_GET['month'] : null;
$sucursal = isset($_GET['sucursal']) && $_GET['sucursal'] !== '' ? (int)$_GET['sucursal'] : null;

// Build WHERE conditions
$conditions = ["s.estado_sell = 1"];

if ($year) {
    $conditions[] = "YEAR(s.date) = $year";
}

if ($month) {
    $conditions[] = "MONTH(s.date) = $month";
}

if ($sucursal) {
    $conditions[] = "s.id_sucursal = $sucursal";
}

$whereClause = "WHERE " . implode(" AND ", $conditions);

try {    // Main query to get consolidated financial data
    $query = "SELECT 
            s.sell_order,
            s.date,
            s.id_sucursal,
            s.total_item,
            s.withheld_tax,
            COALESCE(sh.shipping_paid, 0) as shipping_paid,
            COALESCE(sh.shipping_other_carrier, 0) as shipping_other_carrier,
            COALESCE(sh.shipping_adjust, 0) as shipping_adjust,
            COALESCE(r.product_charge, 0) as product_charge,
            COALESCE(r.shipping_paid, 0) as returns_shipping_paid,
            COALESCE(r.tax_return, 0) as returns_tax_return,
            COALESCE(r.selling_fee_refund, 0) as selling_fee_refund,
            COALESCE(r.refund_administration_fee, 0) as refund_administration_fee,
            COALESCE(r.other_refund_fee, 0) as other_refund_fee,
            COALESCE(sr.billing_return, 0) as billing_return,
            COALESCE(d.price_discount, 0) as price_discount,
            COALESCE(d.shipping_discount, 0) as shipping_discount,
            COALESCE(d.fee_credit, 0) as fee_credit,
            COALESCE(d.tax_return, 0) as discount_tax_return,
            COALESCE(sc.safet_reimbursement, 0) as safet_reimbursement,
            COALESCE(sc.shipping_reimbursement, 0) as shipping_reimbursement,
            COALESCE(sc.tax_reimbursement, 0) as tax_reimbursement,
            COALESCE(sc.label_avoid, 0) as label_avoid,
            COALESCE(sc.other_fee_reimbursement, 0) as other_fee_reimbursement,
            COALESCE(c.refund_amount, 0) as refund_amount,
            COALESCE(c.shipping_refund, 0) as shipping_refund,
            COALESCE(c.tax_refund, 0) as tax_refund,
            COALESCE(c.final_fee_refund, 0) as final_fee_refund,
            COALESCE(c.fixed_charge_refund, 0) as fixed_charge_refund,
            COALESCE(c.other_fee_refund, 0) as other_fee_refund,
            su.code_sucursal,
            st.store_name
        FROM sell s
        LEFT JOIN shipping sh ON BINARY s.sell_order = BINARY sh.sell_order
        LEFT JOIN returns r ON s.id_sell = r.id_sell
        LEFT JOIN shipping_return sr ON BINARY s.sell_order = BINARY sr.sell_order
        LEFT JOIN discounts d ON BINARY s.sell_order = BINARY d.sell_order
        LEFT JOIN safetclaim sc ON BINARY s.sell_order = BINARY sc.sell_order
        LEFT JOIN cancellations c ON BINARY s.sell_order = BINARY c.order_id        LEFT JOIN sucursal su ON s.id_sucursal = su.id_sucursal
        LEFT JOIN store st ON s.id_store = st.id_store
        $whereClause
        ORDER BY s.date DESC
    ";

    // Use mysqli connection correctly
    $result = mysqli_query($mysqli, $query);
    
    if (!$result) {
        throw new Exception("Query failed: " . mysqli_error($mysqli));
    }

    $data = [];
    $monthlyData = [];
    $totals = [
        'total_sales' => 0,
        'total_discounts' => 0,
        'total_reimbursements' => 0,
        'net_profit' => 0
    ];

    while ($row = mysqli_fetch_assoc($result)) {
        // Calculate net profit according to business rules
        $netProfit = $row['total_item']; // Start with sell.total_item (suma)
        
        // Subtract shipping costs
        $netProfit -= ($row['shipping_paid'] + $row['shipping_other_carrier'] + $row['shipping_adjust']);
        
        // Returns adjustments
        $netProfit -= $row['product_charge']; // resta
        $netProfit -= $row['returns_shipping_paid']; // resta
        $netProfit -= $row['returns_tax_return']; // resta
        $netProfit += $row['selling_fee_refund']; // suma
        $netProfit -= $row['refund_administration_fee']; // resta
        $netProfit -= $row['other_refund_fee']; // resta
        
        // Shipping return adjustments
        $netProfit -= $row['billing_return']; // resta
        
        // Discount adjustments
        $netProfit -= $row['price_discount']; // resta
        $netProfit -= $row['shipping_discount']; // resta
        $netProfit += $row['fee_credit']; // suma
        $netProfit -= $row['discount_tax_return']; // resta
        
        // SafetClaim adjustments
        $netProfit += $row['safet_reimbursement']; // suma
        $netProfit += $row['shipping_reimbursement']; // suma
        $netProfit -= $row['tax_reimbursement']; // resta
        $netProfit += $row['label_avoid']; // suma
        $netProfit += $row['other_fee_reimbursement']; // suma
        
        // Cancellations adjustments
        $netProfit -= $row['refund_amount']; // resta
        $netProfit -= $row['shipping_refund']; // resta
        $netProfit -= $row['tax_refund']; // resta
        $netProfit += $row['final_fee_refund']; // suma
        $netProfit += $row['fixed_charge_refund']; // suma
        $netProfit += $row['other_fee_refund']; // suma

        $row['calculated_net_profit'] = $netProfit;
        $data[] = $row;

        // Accumulate totals
        $totals['total_sales'] += $row['total_item'];        $totals['total_discounts'] += ($row['price_discount'] + $row['shipping_discount'] + $row['discount_tax_return']);
        $totals['total_reimbursements'] += ($row['safet_reimbursement'] + $row['shipping_reimbursement'] + $row['label_avoid'] + $row['other_fee_reimbursement']);
        $totals['net_profit'] += $netProfit;

        // Group by month for charts
        $monthKey = date('Y-m', strtotime($row['date']));
        if (!isset($monthlyData[$monthKey])) {
            $monthlyData[$monthKey] = [
                'month' => $monthKey,
                'sales' => 0,
                'discounts' => 0,
                'reimbursements' => 0,
                'net_profit' => 0,
                'count' => 0
            ];
        }
        
        $monthlyData[$monthKey]['sales'] += $row['total_item'];
        $monthlyData[$monthKey]['discounts'] += ($row['price_discount'] + $row['shipping_discount'] + $row['discount_tax_return']);
        $monthlyData[$monthKey]['reimbursements'] += ($row['safet_reimbursement'] + $row['shipping_reimbursement'] + $row['label_avoid'] + $row['other_fee_reimbursement']);
        $monthlyData[$monthKey]['net_profit'] += $netProfit;
        $monthlyData[$monthKey]['count']++;
    }

    // Sort monthly data by month
    ksort($monthlyData);

    // Get sucursal distribution
    $sucursalQuery = "
        SELECT 
            su.code_sucursal,
            st.store_name,
            COUNT(*) as count,
            SUM(s.total_item) as total_sales
        FROM sell s
        LEFT JOIN sucursal su ON s.id_sucursal = su.id_sucursal
        LEFT JOIN store st ON s.id_store = st.id_store
        $whereClause
        GROUP BY s.id_sucursal, su.code_sucursal, st.store_name
        ORDER BY total_sales DESC
    ";

    $resultSucursal = mysqli_query($mysqli, $sucursalQuery);
    
    if (!$resultSucursal) {
        throw new Exception("Sucursal query failed: " . mysqli_error($mysqli));
    }

    $sucursalData = [];
    while ($row = mysqli_fetch_assoc($resultSucursal)) {
        $sucursalData[] = $row;
    }

    $response = [
        'success' => true,
        'data' => $data,
        'monthly' => array_values($monthlyData),
        'sucursal' => $sucursalData,
        'totals' => $totals,
        'filters' => [
            'year' => $year,
            'month' => $month,
            'sucursal' => $sucursal
        ]
    ];

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}

mysqli_close($mysqli);
?>
