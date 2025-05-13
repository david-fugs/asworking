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




</head>

<body>
    <style>
        #totalSales {
            text-align: center;
            margin-bottom: 20px;
        }

        #print-area {
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
            width: 800px;
            height: 400px;


        }

        btn-danger:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
    <center style="margin-top: 20px;">
        <img src='../../img/logo.png' width="300" height="212" class="responsive">
    </center>
    <h1 style=" margin-top:12px;color: #412fd1; text-shadow: #FFFFFF 0.1em 0.1em 0.2em; font-size: 40px; text-align: center;"><b><i
                class="fa-solid fa-file-signature"></i> GENERATE INFORMS </b></h1>
    <div class="flex">
        <div class="box">
            <form action="generateInforms.php" method="get" class="form p-3 border rounded shadow-sm bg-light">
                <div class="mb-3">
                    <label class="form-label fw-bold fs-5 text-primary">Sales within the selected date range</label>
                    <div class="d-flex flex-wrap gap-2">
                        <input name="start_date" type="date" class="form-control" value="<?= isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : '' ?>">
                        <input name="end_date" type="date" class="form-control" value="<?= isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : '' ?>">
                        <select name="store" class="form-select">
                            <option value="">Select Store</option>
                            <?php
                            while ($row = mysqli_fetch_assoc($result_stores)) {
                                print_r($row);
                                $selected = (isset($_GET['store']) && $_GET['store'] == $row['id_store']) ? 'selected' : '';
                                echo "<option value='" . $row['id_store'] . "' " . $selected . ">" . $row['store_name'] . "</option>";
                            }
                            ?>
                        </select>

                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                    <!-- tambien agregar filtro para store -->


                </div>
            </form>

        </div>
    </div>
    <div class="d-flex justify-content-end ">
        <button onclick="window.printChart()" class="btn btn-secondary mt-2 me-5">Print Report</button>

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