$(document).ready(function() {
    // Configurar DataTable
    $('#salesTable').DataTable({
        "pageLength": 25,
        "order": [[1, "desc"]], // Ordenar por fecha descendente
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            },
            "emptyTable": "No devolutions found"
        }
    });    // Manejar click en filas de la tabla para abrir el modal
    $(document).on('click', '.editable-row', function(e) {
        e.preventDefault();
        
        const rowData = $(this).data();
        
        // Llenar el modal con los datos de la fila
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
        $('#edit-return-cost').val(rowData.returnCost);
        $('#edit-buyer-comments').val(rowData.buyerComments);
        
        // Mostrar el modal
        $('#editModal').modal('show');
    });

    // Manejar guardado de cambios
    $('#saveEdit').click(function() {
        const formData = {
            id: $('#edit-id-sell').val(),
            quantity: $('#edit-quantity').val(),
            product_charge: $('#edit-product-charge').val(),
            shipping_paid: $('#edit-shipping-paid').val(),
            tax_return: $('#edit-tax-return').val(),
            selling_fee_refund: $('#edit-selling-fee-refund').val(),
            refund_administration_fee: $('#edit-refund-administration-fee').val(),
            other_refund_fee: $('#edit-other-refund-fee').val(),
            return_cost: $('#edit-return-cost').val(),
            buyer_comments: $('#edit-buyer-comments').val()
        };

        // Validar campos requeridos
        if (!formData.id) {
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
                        // Recargar la tabla
                        location.reload();
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
    });

    // Limpiar modal al cerrarse
    $('#editModal').on('hidden.bs.modal', function() {
        $('#editForm')[0].reset();
    });
});
