$(document).ready(function() {
    // Inicializar DataTable
    $('#salesTable').DataTable({
        "pageLength": 15,
        "lengthChange": false,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/Spanish.json"
        },
        "columnDefs": [
            { "orderable": false, "targets": [-1] } // Disable ordering on last column if it contains action buttons
        ]
    });

    // Manejar el formulario de filtros
    $('#filterForm').on('submit', function(e) {
        e.preventDefault();
        
        const upc = $('#upc').val();
        const sellOrder = $('#sell_order').val();
        const date = $('#date').val();
        
        // Construir URL con parámetros
        let url = 'seeCancellations.php?';
        const params = [];
        
        if (upc) params.push(`upc_item=${encodeURIComponent(upc)}`);
        if (sellOrder) params.push(`sell_order=${encodeURIComponent(sellOrder)}`);
        if (date) params.push(`sellDate=${encodeURIComponent(date)}`);
        
        url += params.join('&');
        
        // Redirigir a la URL con filtros
        window.location.href = url;
    });

    // Limpiar filtros
    $('#clearFilters').on('click', function() {
        $('#filterForm')[0].reset();
        window.location.href = 'seeCancellations.php';
    });

    // Agregar funcionalidad de búsqueda en tiempo real
    $('#upc, #sell_order').on('keyup', function() {
        const searchTerm = $(this).val();
        const table = $('#salesTable').DataTable();
        
        if ($(this).attr('id') === 'upc') {
            table.column(2).search(searchTerm).draw(); // UPC column
        } else if ($(this).attr('id') === 'sell_order') {
            table.column(0).search(searchTerm).draw(); // Sell Number column
        }
    });

    // Filtro por fecha
    $('#date').on('change', function() {
        const selectedDate = $(this).val();
        const table = $('#salesTable').DataTable();
        table.column(1).search(selectedDate).draw(); // Date column
    });
});

// Función para mostrar mensajes de éxito/error
function showMessage(message, type = 'success') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    const messageHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Insertar mensaje al inicio del contenedor principal
    $('.table-container').prepend(messageHtml);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        $('.alert').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}

// Función para confirmar eliminación
function confirmDelete(sellOrder) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Deseas eliminar los datos de cancelación para la orden ${sellOrder}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Aquí iría la lógica para eliminar
            deleteCancellation(sellOrder);
        }
    });
}

// Función para eliminar cancelación
function deleteCancellation(sellOrder) {
    fetch('deleteCancellation.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `sell_order=${encodeURIComponent(sellOrder)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire(
                'Eliminado!',
                'Los datos de cancelación han sido eliminados.',
                'success'
            ).then(() => {
                location.reload();
            });
        } else {
            Swal.fire(
                'Error!',
                data.message || 'Error al eliminar los datos de cancelación.',
                'error'
            );
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire(
            'Error!',
            'Error de conexión al eliminar los datos.',
            'error'
        );
    });
}
