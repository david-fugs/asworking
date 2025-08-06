<?php
session_start();
include("../../conexion.php");

if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
    exit;
}

// Obtener parámetros del filtro
$month = isset($_GET['month']) ? str_pad($_GET['month'], 2, '0', STR_PAD_LEFT) : date('m');
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');

// Obtener datos directamente en PHP (sin AJAX)
$data = [];
$summary = [
    'total_taxes' => 0,
    'total_fees' => 0,
    'total_records' => 0
];

try {
    // Consulta con subconsultas para las tablas adicionales (más seguro)
    // Consulta base con LEFT JOINs para cada tabla adicional
    // Puedes ir agregando más LEFT JOINs siguiendo el ejemplo
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
        COALESCE(ss.final_fee, 0) as final_fee,
        COALESCE(ss.fixed_charge, 0) as fixed_charge,
        COALESCE(st.store_name, 'Unknown') as store_name,
        -- Ejemplo: LEFT JOIN safetclaim
        COALESCE(sc.safet_reimbursement, 0) as safet_reimbursement,
        COALESCE(sc.shipping_reimbursement, 0) as shipping_reimbursement,
        COALESCE(sc.tax_reimbursement, 0) as tax_reimbursement,
        COALESCE(sc.other_fee_reimbursement, 0) as other_fee_reimbursement,
        -- Ejemplo: LEFT JOIN shipping
        COALESCE(sh.shipping_paid, 0) as shipping_paid,
        COALESCE(sh.shipping_adjust, 0) as shipping_adjust,
        -- Ejemplo: LEFT JOIN shipping_return
        COALESCE(sr.billing_return, 0) as billing_return
    FROM sell s
    LEFT JOIN sell_summary ss ON s.sell_order = ss.sell_order
    LEFT JOIN store st ON s.id_store = st.id_store
    -- Puedes agregar más LEFT JOINs aquí, uno por cada tabla que quieras incluir
    LEFT JOIN safetclaim sc ON s.sell_order = sc.sell_order
    LEFT JOIN shipping sh ON s.sell_order = sh.sell_order
    LEFT JOIN shipping_return sr ON s.sell_order = sr.sell_order
    -- Ejemplo para agregar otra tabla:
    -- LEFT JOIN otra_tabla ot ON s.sell_order = ot.sell_order
    WHERE YEAR(s.date) = ? AND MONTH(s.date) = ?
    ORDER BY s.date DESC, s.sell_order ASC
    LIMIT 500
    ";

    // Consulta directa, interpolando los valores de mes y año
    // $sql = "
    // SELECT 
    //     s.sell_order,
    //     s.quantity,
    //     s.tax,
    //     s.withheld_tax,
    //     s.international_fee,
    //     s.ad_fee,
    //     s.other_fee,
    //     s.date,
    //     COALESCE(ss.final_fee, 0) as final_fee,
    //     COALESCE(ss.fixed_charge, 0) as fixed_charge,
    //     COALESCE(st.store_name, 'Unknown') as store_name,
    //     COALESCE(sc.safet_reimbursement, 0) as safet_reimbursement,
    //     COALESCE(sc.shipping_reimbursement, 0) as shipping_reimbursement,
    //     COALESCE(sc.tax_reimbursement, 0) as tax_reimbursement,
    //     COALESCE(sc.other_fee_reimbursement, 0) as other_fee_reimbursement,
    //     COALESCE(sh.shipping_paid, 0) as shipping_paid,
    //     COALESCE(sh.shipping_adjust, 0) as shipping_adjust,
    //     COALESCE(sr.billing_return, 0) as billing_return
    //     -- Puedes agregar más columnas aquí
    // FROM sell s
    // LEFT JOIN sell_summary ss ON s.sell_order = ss.sell_order
    // LEFT JOIN store st ON s.id_store = st.id_store
    // LEFT JOIN safetclaim sc ON s.sell_order = sc.sell_order
    // LEFT JOIN shipping sh ON s.sell_order = sh.sell_order
    // LEFT JOIN shipping_return sr ON s.sell_order = sr.sell_order
    // -- Puedes agregar más LEFT JOINs aquí
    // WHERE YEAR(s.date) = $year AND MONTH(s.date) = $month
    // ORDER BY s.date DESC, s.sell_order ASC
    // LIMIT 500
    // ";
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
        COALESCE(ss.final_fee, 0) as final_fee,
        COALESCE(ss.fixed_charge, 0) as fixed_charge,
        COALESCE(st.store_name, 'Unknown') as store_name

       
        -- Puedes agregar más columnas aquí
    FROM sell s
    LEFT JOIN sell_summary ss ON s.sell_order = ss.sell_order
    LEFT JOIN store st ON s.id_store = st.id_store
   

    ORDER BY s.date DESC, s.sell_order ASC
    LIMIT 500;
    ";

    $result = $mysqli->query($sql);
    $sellOrderFees = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Solo agregar final_fee y fixed_charge una vez por sell_order
            if (!isset($sellOrderFees[$row['sell_order']])) {
                $sellOrderFees[$row['sell_order']] = true;
                $row['final_fee'] = $row['final_fee'] ?? 0;
                $row['fixed_charge'] = $row['fixed_charge'] ?? 0;
            } else {
                $row['final_fee'] = 0;
                $row['fixed_charge'] = 0;
            }

            // Puedes agregar aquí el manejo de nuevas columnas de otras tablas
            // Ejemplo:
            // $row['nueva_columna'] = $row['nueva_columna'] ?? 0;

            $data[] = $row;

            // Calcular totales incluyendo todos los fees
            // Si agregas nuevas columnas, inclúyelas aquí
            $summary['total_taxes'] += ($row['tax'] ?? 0) + ($row['withheld_tax'] ?? 0) + ($row['tax_reimbursement'] ?? 0);
            $summary['total_fees'] += ($row['international_fee'] ?? 0) + ($row['ad_fee'] ?? 0) + 
                                     ($row['other_fee'] ?? 0) + $row['final_fee'] + $row['fixed_charge'] +
                                     ($row['safet_reimbursement'] ?? 0) + ($row['shipping_reimbursement'] ?? 0) +
                                     ($row['other_fee_reimbursement'] ?? 0) + ($row['shipping_paid'] ?? 0) +
                                     ($row['shipping_adjust'] ?? 0) + ($row['billing_return'] ?? 0);
            // Ejemplo para sumar nueva columna:
            // $summary['total_fees'] += ($row['nueva_columna'] ?? 0);
        }
        $summary['total_records'] = count($data);
    }
} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax & Fees Report</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../fontawesome/css/all.min.css">
    
    <style>
        :root {
            --primary: #632B8B;
            --primary-light: #8A6BB3;
            --secondary: #997CAB;
            --secondary-light: #F8F5FF;
            --background: #F5F3FF;
        }

        body {
            background: linear-gradient(135deg, var(--background), #ffffff);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            min-height: 100vh;
        }

        .main-container {
            max-width: 90%;
            margin: 0 auto;
            padding: 20px;
        }

        .header-card {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(99, 43, 139, 0.3);
        }

        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .summary-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .summary-card {
            flex: 1;
            min-width: 200px;
            background: white;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .summary-card h3 {
            color: var(--primary);
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px 10px;
            font-weight: 600;
            text-align: center;
        }

        .table tbody td {
            padding: 12px 10px;
            border-color: rgba(153, 124, 171, 0.2);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background-color: rgba(99, 43, 139, 0.05);
        }

        .btn-primary {
            background: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: var(--primary-light);
        }

        .form-control, .form-select {
            border: 1px solid var(--secondary);
            border-radius: 6px;
            padding: 10px 15px;
        }

        .text-right {
            text-align: right;
        }

        .no-data {
            text-align: center;
            padding: 50px;
            color: #666;
        }

        .export-btn {
            background: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-block;
            margin-left: 10px;
        }

        .export-btn:hover {
            background: #218838;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Header -->
        <div class="header-card">
            <h1><i class="fas fa-chart-line"></i> Tax & Fees Report</h1>
            <p class="mb-0">Comprehensive tax and fees analysis for your business</p>
        </div>

        <!-- Filtros -->
        <div class="filter-card">
            <form method="GET" class="d-flex justify-content-center align-items-end flex-wrap gap-3">
                <div class="form-group">
                    <label for="month" class="form-label">Month</label>
                    <select name="month" id="month" class="form-control" style="min-width: 140px; height: 44px;">
                        <?php
                        $months = [
                            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
                            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
                            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
                        ];
                        foreach ($months as $num => $name) {
                            $selected = ($num == $month) ? 'selected' : '';
                            echo "<option value='$num' $selected>$name</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="year" class="form-label">Year</label>
                    <select name="year" id="year" class="form-control" style="min-width: 120px;height: 44px;">
                        <?php
                        $currentYear = date('Y');
                        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                            $selected = ($i == $year) ? 'selected' : '';
                            echo "<option value='$i' $selected>$i</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="exportFeesExcel.php?month=<?php echo $month; ?>&year=<?php echo $year; ?>" 
                       class="export-btn">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card">
                <h3>$<?php echo number_format($summary['total_taxes'], 2); ?></h3>
                <p>Total Taxes</p>
            </div>
            <div class="summary-card">
                <h3>$<?php echo number_format($summary['total_fees'], 2); ?></h3>
                <p>Total Fees</p>
            </div>
            <div class="summary-card">
                <h3><?php echo $summary['total_records']; ?></h3>
                <p>Total Records</p>
            </div>
            <div class="summary-card">
                <h3>$<?php echo number_format($summary['total_taxes'] + $summary['total_fees'], 2); ?></h3>
                <p>Grand Total</p>
            </div>
        </div>

        <!-- Tabla de datos -->
        <div class="table-container">
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php elseif (empty($data)): ?>
                <div class="no-data">
                    <i class="fas fa-inbox fa-3x mb-3"></i>
                    <h4>No data found</h4>
                    <p>No records found for the selected period.</p>
                </div>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Sell Order</th>
                            <th>Store</th>
                            <th>Qty</th>
                            <th>Tax</th>
                            <th>Withheld Tax</th>
                            <th>International Fee</th>
                            <th>Ad Fee</th>
                            <th>Other Fee</th>
                            <th>Final Fee</th>
                            <th>Fixed Charge</th>
                            <th>Safet Reimbursement</th>
                            <th>Shipping Reimbursement</th>
                            <th>Tax Reimbursement</th>
                            <th>Other Fee Reimbursement</th>
                            <th>Shipping Paid</th>
                            <th>Shipping Adjust</th>
                            <th>Billing Return</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $row): 
                            $totalRow = ($row['tax'] ?? 0) + ($row['withheld_tax'] ?? 0) + 
                                       ($row['international_fee'] ?? 0) + ($row['ad_fee'] ?? 0) + 
                                       ($row['other_fee'] ?? 0) + ($row['final_fee'] ?? 0) + 
                                       ($row['fixed_charge'] ?? 0) + ($row['safet_reimbursement'] ?? 0) +
                                       ($row['shipping_reimbursement'] ?? 0) + ($row['tax_reimbursement'] ?? 0) +
                                       ($row['other_fee_reimbursement'] ?? 0) + ($row['shipping_paid'] ?? 0) +
                                       ($row['shipping_adjust'] ?? 0) + ($row['billing_return'] ?? 0);
                        ?>
                        <tr>
                            <td><?php echo date('m/d/Y', strtotime($row['date'])); ?></td>
                            <td><?php echo htmlspecialchars($row['sell_order'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['store_name'] ?? 'N/A'); ?></td>
                            <td><?php echo $row['quantity'] ?? 0; ?></td>
                            <td class="text-right">$<?php echo number_format($row['tax'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['withheld_tax'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['international_fee'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['ad_fee'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['other_fee'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['final_fee'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['fixed_charge'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['safet_reimbursement'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['shipping_reimbursement'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['tax_reimbursement'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['other_fee_reimbursement'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['shipping_paid'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['shipping_adjust'] ?? 0, 2); ?></td>
                            <td class="text-right">$<?php echo number_format($row['billing_return'] ?? 0, 2); ?></td>
                            <td class="text-right"><strong>$<?php echo number_format($totalRow, 2); ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
