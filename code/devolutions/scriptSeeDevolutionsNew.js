$(document).ready(function() {
    // Manejar el envío del formulario de búsqueda
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        
        const upc = $('#upc').val().trim();
        const sellOrder = $('#sell_order').val().trim();
        
        if (!upc) {
            Swal.fire({
                icon: 'warning',
                title: 'UPC Required',
                text: 'Please enter a UPC code to search'
            });
            return;
        }
        
        // Mostrar spinner de carga
        $('#searchResults').html(`
            <div class="text-center py-4">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Searching...</span>
                </div>
                <p class="mt-2">Searching for returns...</p>
            </div>
        `);
        
        // Ocultar mensaje inicial y mostrar contenedor de resultados
        $('#initialMessage').hide();
        $('#resultsContainer').show();
        
        // Realizar búsqueda AJAX
        $.ajax({
            url: 'searchDevolutions.php',
            method: 'POST',
            data: {
                upc_item: upc,
                sell_order: sellOrder
            },
            success: function(response) {                $('#searchResults').html(response);
                
                // NO usar DataTable - solo funcionalidad manual
                // Inicializar eventos de filas clickeables
                initializeClickableRows();
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                $('#searchResults').html(`
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h5>Error</h5>
                        <p>Error communicating with server</p>
                    </div>
                `);
            }
        });
    });

    // Función para inicializar los event listeners de las filas clickeables
    function initializeClickableRows() {
        document.querySelectorAll(".clickable-row").forEach(function (row) {
            // Remover event listeners previos para evitar duplicados
            const newRow = row.cloneNode(true);
            row.parentNode.replaceChild(newRow, row);
        });
        
        // Reinicializar los event listeners
        document.querySelectorAll(".clickable-row").forEach(function (row) {
            row.addEventListener("click", function () {
                const rowData = this.dataset;                  // Llenar el modal con los datos de la fila
                $('#edit-id-sell').val(rowData.id);
                $('#edit-sell-order').val(rowData.sellOrder);
                $('#edit-date').val(rowData.date.split(' ')[0]); // Solo la fecha, sin la hora
                $('#edit-upc').val(rowData.upc);
                $('#edit-quantity').val(rowData.quantity);
                $('#edit-product-charge').val(rowData.productCharge);
                $('#edit-shipping-paid').val(rowData.shippingPaid);
                $('#edit-tax-return').val(rowData.taxReturn);
                $('#edit-selling-fee-refund').val(rowData.sellingFeeRefund);
                $('#edit-refund-administration-fee').val(rowData.refundAdministrationFee);
                $('#edit-other-refund-fee').val(rowData.otherRefundFee);
                $('#edit-item-profit').val(rowData.itemProfit);
                $('#edit-buyer-comments').val(rowData.buyerComments);
                
                // Calcular Return Cost automáticamente
                calculateReturnCost();
                
                // Mostrar el modal
                $('#editModal').modal('show');
            });
        });
    }    // Manejar guardado de cambios
    $('#saveEdit').click(function() {        const formData = {
            id: $('#edit-id-sell').val(),
            sell_order: $('#edit-sell-order').val(),
            quantity: $('#edit-quantity').val(),
            product_charge: $('#edit-product-charge').val(),
            shipping_paid: $('#edit-shipping-paid').val(),
            tax_return: $('#edit-tax-return').val(),
            selling_fee_refund: $('#edit-selling-fee-refund').val(),
            refund_administration_fee: $('#edit-refund-administration-fee').val(),
            other_refund_fee: $('#edit-other-refund-fee').val(),
            item_profit: $('#edit-item-profit').val(),
            return_cost: $('#edit-return-cost').val(),
            buyer_comments: $('#edit-buyer-comments').val()
        };

        // Validar campos requeridos
        if (!formData.sell_order) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Missing required data'
            });
            return;
        }

        // Enviar datos via AJAX
        $.ajax({
            url: 'saveDevolutions.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#editModal').modal('hide');
                        // Rehacer la búsqueda para actualizar los resultados
                        $('#filterForm').trigger('submit');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error communicating with server'
                });
            }
        });
    });    // Limpiar modal al cerrarse
    $('#editModal').on('hidden.bs.modal', function() {
        $('#editForm')[0].reset();
    });    // Función para calcular Return Cost automáticamente
    function calculateReturnCost() {
        const productCharge = parseFloat($('#edit-product-charge').val()) || 0;
        const shippingPaid = parseFloat($('#edit-shipping-paid').val()) || 0;
        const taxReturn = parseFloat($('#edit-tax-return').val()) || 0;
        const sellingFeeRefund = parseFloat($('#edit-selling-fee-refund').val()) || 0;
        const refundAdministrationFee = parseFloat($('#edit-refund-administration-fee').val()) || 0;
        const otherRefundFee = parseFloat($('#edit-other-refund-fee').val()) || 0;
        const itemProfit = parseFloat($('#edit-item-profit').val()) || 0;
        
        // Fórmula: (Product Charge + Shipping Paid + Tax Return - Selling Fee Refund + Refund Administration Fee + Other Refund Fee + Item Profit)
        const returnCost = productCharge + shippingPaid + taxReturn - sellingFeeRefund + refundAdministrationFee + otherRefundFee + itemProfit;
        
        $('#edit-return-cost').val(returnCost.toFixed(2));
    }

    // Agregar event listeners para recalcular automáticamente cuando cambien los valores
    $('#edit-product-charge, #edit-shipping-paid, #edit-tax-return, #edit-selling-fee-refund, #edit-refund-administration-fee, #edit-other-refund-fee').on('input change', function() {
        calculateReturnCost();
    });
});
