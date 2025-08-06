$(document).ready(function() {
    loadFeesData();
    loadSummary();
    initChart();
});

let feesChart;

function loadFeesData() {
    const urlParams = new URLSearchParams(window.location.search);
    const month = urlParams.get('month') || new Date().getMonth() + 1;
    const year = urlParams.get('year') || new Date().getFullYear();

    $.ajax({
        url: 'getFeesData.php',
        type: 'POST',
        data: {
            month: month,
            year: year
        },
        dataType: 'json',
        beforeSend: function() {
            $('#feesTableBody').html('<tr><td colspan="14" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');
        },
        success: function(response) {
            if (response.status === 'success') {
                displayFeesData(response.data);
            } else {
                $('#feesTableBody').html('<tr><td colspan="14" class="text-center text-danger">Error loading data: ' + response.message + '</td></tr>');
            }
        },
        error: function() {
            $('#feesTableBody').html('<tr><td colspan="14" class="text-center text-danger">Error connecting to server</td></tr>');
        }
    });
}

function loadSummary() {
    const urlParams = new URLSearchParams(window.location.search);
    const month = urlParams.get('month') || new Date().getMonth() + 1;
    const year = urlParams.get('year') || new Date().getFullYear();

    $.ajax({
        url: 'getFeesSummary.php',
        type: 'POST',
        data: {
            month: month,
            year: year
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                displaySummary(response.data);
                updateChart(response.chartData);
            }
        },
        error: function() {
            console.error('Error loading summary data');
        }
    });
}

function displayFeesData(data) {
    let html = '';
    
    if (data.length === 0) {
        html = '<tr><td colspan="19" class="text-center text-muted">No data found for the selected period</td></tr>';
    } else {
        data.forEach(function(row) {
            const totalFees = parseFloat(row.tax || 0) + 
                            parseFloat(row.withheld_tax || 0) + 
                            parseFloat(row.international_fee || 0) + 
                            parseFloat(row.ad_fee || 0) + 
                            parseFloat(row.other_fee || 0) + 
                            parseFloat(row.final_fee || 0) + 
                            parseFloat(row.fixed_charge || 0) +
                            parseFloat(row.safet_reimbursement || 0) +
                            parseFloat(row.shipping_reimbursement || 0) +
                            parseFloat(row.tax_reimbursement || 0) +
                            parseFloat(row.other_fee_reimbursement || 0) +
                            parseFloat(row.shipping_paid || 0) +
                            parseFloat(row.shipping_adjust || 0) +
                            parseFloat(row.billing_return || 0);

            html += `
                <tr>
                    <td>${formatDate(row.date)}</td>
                    <td>${row.sell_order}</td>
                    <td>${row.store_name || 'N/A'}</td>
                    <td>${row.quantity}</td>
                    <td>$${formatCurrency(row.tax)}</td>
                    <td>$${formatCurrency(row.withheld_tax)}</td>
                    <td>$${formatCurrency(row.international_fee)}</td>
                    <td>$${formatCurrency(row.ad_fee)}</td>
                    <td>$${formatCurrency(row.other_fee)}</td>
                    <td>$${formatCurrency(row.final_fee)}</td>
                    <td>$${formatCurrency(row.fixed_charge)}</td>
                    <td>$${formatCurrency(row.safet_reimbursement)}</td>
                    <td>$${formatCurrency(row.shipping_reimbursement)}</td>
                    <td>$${formatCurrency(row.tax_reimbursement)}</td>
                    <td>$${formatCurrency(row.other_fee_reimbursement)}</td>
                    <td>$${formatCurrency(row.shipping_paid)}</td>
                    <td>$${formatCurrency(row.shipping_adjust)}</td>
                    <td>$${formatCurrency(row.billing_return)}</td>
                    <td><strong>$${formatCurrency(totalFees)}</strong></td>
                </tr>
            `;
        });
    }
    
    $('#feesTableBody').html(html);
}

function displaySummary(data) {
    const summaryHtml = `
        <div class="col-md-3">
            <div class="summary-card" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <h3>$${formatCurrency(data.total_taxes)}</h3>
                <p>Total Taxes</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card" style="background: linear-gradient(135deg, #dc3545, #e83e8c);">
                <h3>$${formatCurrency(data.total_fees)}</h3>
                <p>Total Fees</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
                <h3>$${formatCurrency(data.total_withheld)}</h3>
                <p>Withheld Taxes</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card">
                <h3>$${formatCurrency(data.grand_total)}</h3>
                <p>Grand Total</p>
            </div>
        </div>
    `;
    
    $('#summary-section').html(summaryHtml);
}

function initChart() {
    const ctx = document.getElementById('feesChart').getContext('2d');
    feesChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Taxes', 'International Fees', 'Ad Fees', 'Other Fees', 'Final Fees', 'Fixed Charges'],
            datasets: [{
                data: [0, 0, 0, 0, 0, 0],
                backgroundColor: [
                    '#28a745',
                    '#17a2b8',
                    '#ffc107',
                    '#dc3545',
                    '#6f42c1',
                    '#6c757d'
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                title: {
                    display: true,
                    text: 'Fees Distribution'
                }
            }
        }
    });
}

function updateChart(chartData) {
    if (feesChart && chartData) {
        feesChart.data.datasets[0].data = [
            chartData.taxes || 0,
            chartData.international_fees || 0,
            chartData.ad_fees || 0,
            chartData.other_fees || 0,
            chartData.final_fees || 0,
            chartData.fixed_charges || 0
        ];
        feesChart.update();
    }
}

function formatCurrency(value) {
    const num = parseFloat(value) || 0;
    return num.toFixed(2);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
}

function exportToExcel() {
    const urlParams = new URLSearchParams(window.location.search);
    const month = urlParams.get('month') || new Date().getMonth() + 1;
    const year = urlParams.get('year') || new Date().getFullYear();
    
    window.open(`exportFeesExcel.php?month=${month}&year=${year}`, '_blank');
}

// Función para filtrar por tienda (si se implementa en el futuro)
function filterByStore() {
    loadFeesData();
    loadSummary();
}
