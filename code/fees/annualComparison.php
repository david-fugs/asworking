<?php
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
}

$usuario = $_SESSION['usuario'];
$nombre = $_SESSION['nombre'];
$tipo_usuario = $_SESSION['tipo_usuario'];
header("Content-Type: text/html;charset=utf-8");

include("../../conexion.php");

// Obtener año para comparativo
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ASWWORKING | ANNUAL TAX & FEES COMPARISON</title>
    <script type="text/javascript" src="../../js/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <link href="../../fontawesome/css/all.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --primary-dark: #4a2568;
            --primary: #632b8b;
            --primary-light: #5d337a;
            --secondary: #997cab;
            --secondary-light: #dac7e5;
            --text-dark: #2d2d2d;
            --text-light: #f8f9fa;
            --bg-light: #f5f3f7;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
        }

        /* Header styles */
        .header-container {
            background-color: var(--secondary-light);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .logo {
            height: 100px;
            width: auto;
            transition: transform 0.3s ease;
        }

        /* Main container */
        .main-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        /* Title styles */
        .page-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 10px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100px;
            height: 3px;
            background: linear-gradient(to right, var(--primary), var(--secondary-light));
            border-radius: 3px;
        }

        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            height: 400px;
        }

        .comparison-table {
            font-size: 0.9rem;
        }

        .comparison-table th {
            background: var(--primary);
            color: white;
            text-align: center;
            font-weight: 600;
        }

        .comparison-table td {
            text-align: center;
            vertical-align: middle;
        }

        .month-cell {
            font-weight: 600;
            background-color: var(--secondary-light);
            color: var(--primary);
        }

        .total-row {
            background-color: var(--bg-light);
            font-weight: 700;
            border-top: 2px solid var(--primary);
        }
    </style>
</head>

<body>
    <div class="header-container">
        <div class="container text-center">
            <img src='../../img/logo.png' class="logo" alt="ASWWORKING Logo">
        </div>
    </div>

    <div class="container main-container">
        <h1 class="page-title"><i class="fas fa-chart-line"></i> ANNUAL TAX & FEES COMPARISON</h1>
        
        <!-- Filtro de año -->
        <div class="row mb-4">
            <div class="col-md-12">
                <form method="GET" class="form-inline">
                    <div class="form-group mr-3">
                        <label for="year" class="form-label mr-2">Year:</label>
                        <select name="year" id="year" class="form-control">
                            <?php
                            $currentYear = date('Y');
                            for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
                                $selected = ($i == $year) ? 'selected' : '';
                                echo "<option value='$i' $selected>$i</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Update
                    </button>
                    <a href="index.php" class="btn btn-outline-secondary ml-2">
                        <i class="fas fa-calendar"></i> Monthly View
                    </a>
                </form>
            </div>
        </div>

        <!-- Gráfico comparativo -->
        <div class="chart-container">
            <canvas id="annualComparisonChart"></canvas>
        </div>

        <!-- Tabla comparativa -->
        <div class="table-responsive">
            <table class="table table-striped comparison-table" id="comparisonTable">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Taxes</th>
                        <th>Withheld Tax</th>
                        <th>International Fee</th>
                        <th>Ad Fee</th>
                        <th>Other Fee</th>
                        <th>Final Fee</th>
                        <th>Fixed Charge</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody id="comparisonTableBody">
                    <!-- Los datos se cargarán aquí via AJAX -->
                </tbody>
            </table>
        </div>

        <!-- Botón de regreso -->
        <div class="text-right mt-4">
            <button type="button" class="btn btn-outline-dark" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            loadAnnualComparison();
        });

        let comparisonChart;

        function loadAnnualComparison() {
            const year = $('#year').val();

            $.ajax({
                url: 'getAnnualComparison.php',
                type: 'POST',
                data: { year: year },
                dataType: 'json',
                beforeSend: function() {
                    $('#comparisonTableBody').html('<tr><td colspan="9" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        displayComparisonTable(response.data);
                        updateComparisonChart(response.chartData);
                    } else {
                        $('#comparisonTableBody').html('<tr><td colspan="9" class="text-center text-danger">Error: ' + response.message + '</td></tr>');
                    }
                },
                error: function() {
                    $('#comparisonTableBody').html('<tr><td colspan="9" class="text-center text-danger">Error connecting to server</td></tr>');
                }
            });
        }

        function displayComparisonTable(data) {
            let html = '';
            let totals = {
                taxes: 0, withheld_tax: 0, international_fee: 0, 
                ad_fee: 0, other_fee: 0, final_fee: 0, fixed_charge: 0, total: 0
            };

            const months = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            for (let i = 0; i < 12; i++) {
                const monthData = data[i + 1] || {};
                const monthTotal = (monthData.taxes || 0) + (monthData.withheld_tax || 0) + 
                                 (monthData.international_fee || 0) + (monthData.ad_fee || 0) + 
                                 (monthData.other_fee || 0) + (monthData.final_fee || 0) + 
                                 (monthData.fixed_charge || 0);

                html += `
                    <tr>
                        <td class="month-cell">${months[i]}</td>
                        <td>$${formatCurrency(monthData.taxes || 0)}</td>
                        <td>$${formatCurrency(monthData.withheld_tax || 0)}</td>
                        <td>$${formatCurrency(monthData.international_fee || 0)}</td>
                        <td>$${formatCurrency(monthData.ad_fee || 0)}</td>
                        <td>$${formatCurrency(monthData.other_fee || 0)}</td>
                        <td>$${formatCurrency(monthData.final_fee || 0)}</td>
                        <td>$${formatCurrency(monthData.fixed_charge || 0)}</td>
                        <td><strong>$${formatCurrency(monthTotal)}</strong></td>
                    </tr>
                `;

                // Sumar a totales
                totals.taxes += monthData.taxes || 0;
                totals.withheld_tax += monthData.withheld_tax || 0;
                totals.international_fee += monthData.international_fee || 0;
                totals.ad_fee += monthData.ad_fee || 0;
                totals.other_fee += monthData.other_fee || 0;
                totals.final_fee += monthData.final_fee || 0;
                totals.fixed_charge += monthData.fixed_charge || 0;
                totals.total += monthTotal;
            }

            // Fila de totales
            html += `
                <tr class="total-row">
                    <td><strong>TOTAL YEAR</strong></td>
                    <td><strong>$${formatCurrency(totals.taxes)}</strong></td>
                    <td><strong>$${formatCurrency(totals.withheld_tax)}</strong></td>
                    <td><strong>$${formatCurrency(totals.international_fee)}</strong></td>
                    <td><strong>$${formatCurrency(totals.ad_fee)}</strong></td>
                    <td><strong>$${formatCurrency(totals.other_fee)}</strong></td>
                    <td><strong>$${formatCurrency(totals.final_fee)}</strong></td>
                    <td><strong>$${formatCurrency(totals.fixed_charge)}</strong></td>
                    <td style="background-color: var(--primary); color: white;"><strong>$${formatCurrency(totals.total)}</strong></td>
                </tr>
            `;

            $('#comparisonTableBody').html(html);
        }

        function updateComparisonChart(chartData) {
            const ctx = document.getElementById('annualComparisonChart').getContext('2d');
            
            if (comparisonChart) {
                comparisonChart.destroy();
            }

            comparisonChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [
                        {
                            label: 'Total Taxes',
                            data: chartData.taxes,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            tension: 0.1
                        },
                        {
                            label: 'Total Fees',
                            data: chartData.fees,
                            borderColor: '#dc3545',
                            backgroundColor: 'rgba(220, 53, 69, 0.1)',
                            tension: 0.1
                        },
                        {
                            label: 'Grand Total',
                            data: chartData.total,
                            borderColor: '#632b8b',
                            backgroundColor: 'rgba(99, 43, 139, 0.1)',
                            tension: 0.1,
                            borderWidth: 3
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Monthly Tax & Fees Trend - <?php echo $year; ?>'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        function formatCurrency(value) {
            const num = parseFloat(value) || 0;
            return num.toFixed(2);
        }
    </script>
</body>
</html>
