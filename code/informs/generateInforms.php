<?php
session_start();
include "../../conexion.php";
if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
}
$nombre = $_SESSION['nombre'];
$tipo_usu = $_SESSION['tipo_usuario'];

//traer las store
$stores = "SELECT * FROM store";
$result_stores = mysqli_query($mysqli, $stores);
if (!$result_stores) {
    die("Error in the query: " . mysqli_error($mysqli));
}

function getStoreName($id_store)
{
    global $mysqli;
    $query = "SELECT store_name FROM store WHERE id_store = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $id_store);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        return $row['store_name'];
    }
    return null;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ASWWORKING | SOFT</title>
    <link rel="stylesheet" type="text/css" href="../items/css/styles.css">
    <link rel="stylesheet" type="text/css" href="../items/css/estilos2024.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Librerías de DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <!-- Incluir SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">




</head>

<body>
    <style>
        #totalSales {
            text-align: center;
            margin-bottom: 20px;
        }

        .custom-label {
            color: #4a2568;
            font-family: 'Poppins', sans-serif;
            text-align: center;
            display: block;
        }

        .custom-title {
            color: #4a2568;
            font-family: 'Poppins', sans-serif;
            text-align: center;
        }

        #print-area {
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
            width: 800px;
            height: 400px;


        }

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
            --danger: #dc3545;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-bottom: 50px;
        }

        .header-container {
            text-align: center;
            padding: 20px 0;
            margin-bottom: 20px;
        }

        .logo {
            height: 120px;
            width: auto;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .page-title {
            color: var(--primary);
            font-weight: 700;
            margin: 20px 0;
            position: relative;
            padding-bottom: 10px;
        }

        .page-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background: linear-gradient(to right, var(--primary), var(--secondary-light));
            border-radius: 3px;
        }

        .search-form {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            margin: 20px auto;
            max-width: 800px;
        }

        .search-form label {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 10px;
        }

        .search-form input {
            border: 1px solid var(--secondary);
            border-radius: 6px;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .search-form input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 43, 139, 0.2);
        }

        .btn-primary {
            background: linear-gradient(to bottom, var(--primary), var(--primary-light));
            border: none;
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(to bottom, var(--primary-light), var(--primary));
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background: var(--secondary);
            border: none;
        }

        .btn-secondary:hover {
            background: var(--primary-light);
        }

        .chart-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            margin: 30px auto;
            max-width: 900px;
        }

        .total-sales {
            background: var(--secondary-light);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary);
        }

        @media (max-width: 768px) {
            .logo {
                height: 80px;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .search-form {
                padding: 15px;
            }
        }

        btn-danger:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
    <div class="header-container">
        <img src='../../img/logo.png' class="logo" alt="ASWWORKING Logo">
        <h1 class="page-title"><i class="fa-solid fa-chart-line"></i> SALES REPORTS</h1>
    </div>
    <div class="flex">
        <div class="box">
            <form action="generateInforms.php" method="get" class="form p-4 border rounded shadow-sm bg-light">
                <div class="mb-4">
                    <label class="form-label fw-bold fs-5 custom-title">Sales within the selected date range</label>
                    <div class="row g-3 text-center">
                        <div class="col-md-4">
                             <label for="start_date" class="form-label custom-label">Start Date</label>
                            <input name="start_date" id="start_date" type="date" class="form-control"
                                value="<?= isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : '' ?>">
                        </div>

                        <div class="col-md-4 text-center">
                            <label for="end_date" class="form-label custom-label">End Date</label>
                            <input name="end_date" id="end_date" type="date" class="form-control"
                                value="<?= isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : '' ?>">
                        </div>

                        <div class="col-md-4 text-center">
                            <label for="store" class="form-label custom-label">Store</label>
                            <select name="store" id="store" class="form-control" style="height: 54px; margin-top: 6px; ">
                                <option value="">Select Store</option>
                                <?php
                                while ($row = mysqli_fetch_assoc($result_stores)) {
                                    $selected = (isset($_GET['store']) && $_GET['store'] == $row['id_store']) ? 'selected' : '';
                                    echo "<option value='" . $row['id_store'] . "' $selected>" . $row['store_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-center">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <div class="text-center mb-4">
        <button onclick="window.printChart()" class="btn btn-secondary me-2">
            <i class="fas fa-print"></i> Print Report
        </button>
    </div>
    <!-- aparece el grafico -->
    <center style="margin-bottom: 50px;">
        <div id="print-area">
            <div id="totalSales" class="mb-4"></div>
            <canvas id="salesChart" width="800" height="400"></canvas>
        </div>
    </center>

    <script>
        const startDate = '<?= $_GET['start_date'] ?? '' ?>';
        const endDate = '<?= $_GET['end_date'] ?? '' ?>';
        const store = '<?= $_GET['store'] ?? '' ?>';
        const store_name = '<?= isset($_GET['store']) ? getStoreName($_GET['store']) : '' ?>';

        fetch(`get_sales_data.php?start_date=${startDate}&end_date=${endDate}&store=${store}`)
            .then(response => response.json())
            .then(data => {
                const labels = data.map(item => item.month);
                const totals = data.map(item => parseFloat(item.total_sales));

                const totalSales = totals.reduce((acc, curr) => acc + curr, 0);
                document.getElementById('totalSales').innerHTML = `<h3>Total Sales: $${totalSales.toFixed(2)}</h3>`;
                if (store != '') {
                    document.getElementById('totalSales').innerHTML += `<h3>Store: ${store_name}</h3>`;
                }

                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: totals,
                            backgroundColor: 'rgba(199, 110, 215, 0.6)',
                            borderColor: 'rgb(116, 36, 125)',
                            borderWidth: 1,
                            barThickness: 60,
                        }]
                    },
                    options: {
                        plugins: {
                            // Agrega el título arriba del gráfico
                            title: {
                                display: true,
                                text: 'Monthly Sales ($)',
                                font: {
                                    size: 18,
                                    weight: 'bold'
                                },
                                padding: {
                                    top: 20,
                                    bottom: 10
                                }
                            },
                            // Etiquetas de datos encima de cada barra
                            datalabels: {
                                anchor: 'end',
                                align: 'top',
                                formatter: (value) => '$' + value.toFixed(2),
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                }
                            },
                            // Opcional: Oculta leyenda si ya no hay label
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                suggestedMax: Math.max(...totals) + 3 // Aumenta un poco el máximo
                            }
                        }
                    },
                    plugins: [ChartDataLabels]
                });

            });
    </script>
    <script>
        function printChart() {
            //evitar envio de form
            event.preventDefault();
            const canvas = document.getElementById("salesChart");
            const dataUrl = canvas.toDataURL("image/png");

            const totalSalesHTML = document.getElementById("totalSales").innerHTML;

            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
        <html>
            <head>
                <title>Print Chart</title>
                <style>
                    body {
                        text-align: center;
                        margin: 0;
                        padding: 20px;
                        font-family: Arial, sans-serif;
                    }
                    img {
                        max-width: 100%;
                        height: auto;
                        margin-top: 20px;
                    }
                    .total-sales {
                        font-size: 20px;
                        font-weight: bold;
                        margin-bottom: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="total-sales">${totalSalesHTML}</div>
                <img src="${dataUrl}" alt="Chart"/>
                <script>
                    window.onload = function() {
                        window.print();
                        window.onafterprint = function() {
                            window.close();
                        };
                    }
                <\/script>
            </body>
        </html>
    `);
            printWindow.document.close();
        }
    </script>