// Working version of analytics.js with all chart functionality
console.log('Analytics JS loaded successfully!');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded!');
    
    // Load initial data
    loadAnalyticsData();
    
    // Add form submission handler for filters
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Filter form submitted');
            loadAnalyticsData();
        });
        console.log('Filter form handler added');
    } else {
        console.error('Filter form not found');
    }
});

function loadAnalyticsData() {
    console.log('Loading analytics data...');
    
    // Get filter values
    const filterForm = document.getElementById('filterForm');
    let url = 'getAnalyticsData.php';
    
    if (filterForm) {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams();
        
        // Add parameters only if they have values
        if (formData.get('year') && formData.get('year').trim() !== '') {
            params.append('year', formData.get('year'));
        }
        if (formData.get('month') && formData.get('month').trim() !== '') {
            params.append('month', formData.get('month'));
        }
        if (formData.get('sucursal') && formData.get('sucursal').trim() !== '') {
            params.append('sucursal', formData.get('sucursal'));
        }
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        console.log('Request URL:', url);
        console.log('Filter values:', {
            year: formData.get('year'),
            month: formData.get('month'),
            sucursal: formData.get('sucursal')
        });
    }
    
    // Try to load data
    fetch(url)
        .then(response => {
            console.log('API response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Parsed data:', data);
            
            if (data.success && data.totals) {
                // Update totals with proper formatting
                updateTotalCards(data.totals);
                
                // Create charts
                if (data.monthly && data.monthly.length > 0) {
                    console.log('Creating charts with monthly data:', data.monthly);
                    createBarChart(data.monthly);
                    createLineChart(data.monthly);
                    createPieChart(data.totals);
                } else {
                    console.log('No monthly data available for charts');
                    clearCharts();
                }
                
                console.log('Data loaded successfully!');
            } else {
                console.error('Data error:', data.error || 'No totals in response');
                showError('Error loading data: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showError('Failed to load data: ' + error.message);
        });
}

function updateTotalCards(totals) {
    // Helper function to format currency
    function formatCurrency(value) {
        const num = parseFloat(value) || 0;
        return '$' + num.toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
    
    // Update total sales
    const totalSalesElement = document.getElementById('totalSales');
    if (totalSalesElement) {
        totalSalesElement.textContent = formatCurrency(totals.total_sales);
        console.log('Updated total sales:', totals.total_sales);
    }
    
    // Update total discounts
    const totalDiscountsElement = document.getElementById('totalDiscounts');
    if (totalDiscountsElement) {
        totalDiscountsElement.textContent = formatCurrency(totals.total_discounts);
        console.log('Updated total discounts:', totals.total_discounts);
    }
    
    // Update total reimbursements
    const totalReimbursementsElement = document.getElementById('totalReimbursements');
    if (totalReimbursementsElement) {
        totalReimbursementsElement.textContent = formatCurrency(totals.total_reimbursements);
        console.log('Updated total reimbursements:', totals.total_reimbursements);
    }
    
    // Update net profit with color coding
    const netProfitElement = document.getElementById('netProfit');
    if (netProfitElement) {
        const netProfit = parseFloat(totals.net_profit) || 0;
        netProfitElement.textContent = formatCurrency(netProfit);
        
        // Remove existing color classes
        netProfitElement.classList.remove('positive', 'negative');
        
        // Add appropriate color class
        if (netProfit >= 0) {
            netProfitElement.classList.add('positive');
        } else {
            netProfitElement.classList.add('negative');
        }
        
        console.log('Updated net profit:', netProfit);
    }
}

function showError(message) {
    console.error(message);
    // You could add a visual error message here
    alert(message);
}

function clearCharts() {
    // Clear all charts if no data
    if (monthlyBarChart) {
        monthlyBarChart.destroy();
        monthlyBarChart = null;
    }
    if (monthlyLineChart) {
        monthlyLineChart.destroy();
        monthlyLineChart = null;
    }
    if (categoryPieChart) {
        categoryPieChart.destroy();
        categoryPieChart = null;
    }
}

// Chart variables
let monthlyBarChart = null;
let monthlyLineChart = null;
let categoryPieChart = null;

function createBarChart(monthlyData) {
    const ctx = document.getElementById('monthlyBarChart');
    if (!ctx) {
        console.error('monthlyBarChart canvas not found');
        return;
    }
    
    // Destroy existing chart
    if (monthlyBarChart) {
        monthlyBarChart.destroy();
    }
    
    // Prepare data
    const labels = monthlyData.map(item => {
        // Format month labels nicely
        const date = new Date(item.month + '-01');
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
    });
    const salesData = monthlyData.map(item => parseFloat(item.sales) || 0);
    const profitData = monthlyData.map(item => parseFloat(item.net_profit) || 0);
    
    console.log('Bar chart data:', { labels, salesData, profitData });
    
    monthlyBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Sales',
                    data: salesData,
                    backgroundColor: 'rgba(99, 43, 139, 0.7)',
                    borderColor: 'rgba(99, 43, 139, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Net Profit',
                    data: profitData,
                    backgroundColor: 'rgba(40, 167, 69, 0.7)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Sales and Net Profit Comparison'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    console.log('Bar chart created successfully');
}

function createLineChart(monthlyData) {
    const ctx = document.getElementById('monthlyLineChart');
    if (!ctx) {
        console.error('monthlyLineChart canvas not found');
        return;
    }
    
    // Destroy existing chart
    if (monthlyLineChart) {
        monthlyLineChart.destroy();
    }
    
    // Prepare data
    const labels = monthlyData.map(item => {
        const date = new Date(item.month + '-01');
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short' });
    });
    const profitData = monthlyData.map(item => parseFloat(item.net_profit) || 0);
    
    console.log('Line chart data:', { labels, profitData });
    
    monthlyLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Net Profit Trend',
                data: profitData,
                borderColor: 'rgba(99, 43, 139, 1)',
                backgroundColor: 'rgba(99, 43, 139, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: 'rgba(99, 43, 139, 1)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Net Profit Trend Over Time'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Net Profit: $' + context.parsed.y.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    console.log('Line chart created successfully');
}

function createPieChart(totals) {
    const ctx = document.getElementById('categoryPieChart');
    if (!ctx) {
        console.error('categoryPieChart canvas not found');
        return;
    }
    
    // Destroy existing chart
    if (categoryPieChart) {
        categoryPieChart.destroy();
    }
    
    const salesAmount = parseFloat(totals.total_sales) || 0;
    const discountsAmount = parseFloat(totals.total_discounts) || 0;
    const reimbursementsAmount = parseFloat(totals.total_reimbursements) || 0;
    
    const data = [salesAmount, discountsAmount, reimbursementsAmount];
    
    console.log('Pie chart data:', data);
    
    // Only create chart if we have some data
    if (data.every(val => val === 0)) {
        console.log('No data for pie chart');
        return;
    }
    
    categoryPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Sales', 'Discounts', 'Reimbursements'],
            datasets: [{
                data: data,
                backgroundColor: [
                    'rgba(99, 43, 139, 0.8)',
                    'rgba(220, 53, 69, 0.8)',
                    'rgba(255, 193, 7, 0.8)'
                ],
                borderColor: [
                    'rgba(99, 43, 139, 1)',
                    'rgba(220, 53, 69, 1)',
                    'rgba(255, 193, 7, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Financial Distribution'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': $' + context.parsed.toLocaleString() + ' (' + percentage + '%)';
                        }
                    }
                },
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    console.log('Pie chart created successfully');
}
