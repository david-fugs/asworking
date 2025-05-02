<?php
session_start();
include "../../conexion.php";
if (!isset($_SESSION['id'])) {
    header("Location: ../../index.php");
}
$nombre = $_SESSION['nombre'];
$tipo_usu = $_SESSION['tipo_usuario'];

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



</head>

<body>
    <style>
        #print-area {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 40px;
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
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <button onclick="window.printChart()" class="btn btn-secondary mt-2">Print Report</button>

    <!-- Aquí va el contenedor para mostrar el total de ventas -->

    <!-- aparece el grafico -->
    <div id="print-area">
        <div id="totalSales" class="mb-4"></div>
        <canvas id="salesChart" width="300" height="100"></canvas>
    </div>
    <script>
        const startDate = '<?= $_GET['start_date'] ?? '' ?>';
        const endDate = '<?= $_GET['end_date'] ?? '' ?>';

        // Realizamos el fetch para obtener los datos de las ventas
        fetch(`get_sales_data.php?start_date=${startDate}&end_date=${endDate}`)
            .then(response => response.json())
            .then(data => {
                // Obtener las etiquetas (meses) y las ventas totales por mes
                const labels = data.map(item => item.month);
                const totals = data.map(item => parseFloat(item.total_sales)); // Aseguramos que sea un número

                // Calcular el total de todas las ventas
                const totalSales = totals.reduce(function(acc, curr) {
                    return acc + curr;
                }, 0);

                // Mostrar el total de ventas en la parte superior
                const totalSalesElement = document.getElementById('totalSales');
                totalSalesElement.innerHTML = `<h3>Total Sales: $${totalSales.toFixed(2)}</h3>`; // Formateado a 2 decimales

                // Crear el gráfico
                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Monthly Sales ($)',
                            data: totals,
                            backgroundColor: 'rgba(75, 192, 192, 0.6)',
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            });
    </script>
    <script>
        function printChart() {
            const canvas = document.getElementById("salesChart");
            const dataUrl = canvas.toDataURL("image/png");

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
                    }
                    img {
                        max-width: 100%;
                        height: auto;
                    }
                </style>
            </head>
            <body>
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