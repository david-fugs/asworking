// Función para mostrar el formulario de cancelación
function showCancellationForm(sellOrder, idSell, upcItem, cancellation) {
  const formHTML = `
          <div class="modal fade" id="cancellationModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Edit Cancellation - Order: ${sellOrder} | UPC: ${upcItem}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
          <form method='post' action='saveCancellations.php' class='mt-4' id='cancellationForm'>
            <div class='row mb-3'>
              <div class='col-md-6'>
                <label for='refund_amount' class='form-label'>Refund Amount</label>
                <input type='number' step='0.01' name='refund_amount' id='refund_amount' class='form-control' value='${cancellation ? (cancellation.refund_amount || '') : ''}'>
              </div>
              <div class='col-md-6'>
                <label for='shipping_refund' class='form-label'>Shipping Refund</label>
                <input type='number' step='0.01' name='shipping_refund' id='shipping_refund' class='form-control' value='${cancellation ? (cancellation.shipping_refund || '') : ''}'>
              </div>
            </div>
            <div class='row mb-3'>
              <div class='col-md-6'>
                <label for='tax_refund' class='form-label'>Tax Refund</label>
                <input type='number' step='0.01' name='tax_refund' id='tax_refund' class='form-control' value='${cancellation ? (cancellation.tax_refund || '') : ''}'>
              </div>
              <div class='col-md-6'>
                <label for='final_fee_refund' class='form-label'>Final Fee Refund</label>
                <input type='number' step='0.01' name='final_fee_refund' id='final_fee_refund' class='form-control' value='${cancellation ? (cancellation.final_fee_refund || '') : ''}'>
              </div>
            </div>
            <div class='row mb-3'>
              <div class='col-md-6'>
                <label for='fixed_charge_refund' class='form-label'>Fixed Charge Refund</label>
                <input type='number' step='0.01' name='fixed_charge_refund' id='fixed_charge_refund' class='form-control' value='${cancellation ? (cancellation.fixed_charge_refund || '') : ''}'>
              </div>
              <div class='col-md-6'>
                <label for='other_fee_refund' class='form-label'>Other Fee Refund</label>
                <input type='number' step='0.01' name='other_fee_refund' id='other_fee_refund' class='form-control' value='${cancellation ? (cancellation.other_fee_refund || '') : ''}'>
              </div>
            </div>
            <div class='row mb-3'>
              <div class='col-md-6'>
                <label for='cancellation_date' class='form-label'>Cancellation Date</label>
                <input type='date' name='cancellation_date' id='cancellation_date' class='form-control' value='${cancellation ? (cancellation.cancellation_date || '') : ''}'>
              </div>
              <div class='col-md-6'>
                <label for='net_cancellation' class='form-label'><strong>Net Cancellation</strong></label>
                <input type='number' step='0.01' name='net_cancellation' id='net_cancellation' class='form-control bg-light' value='${cancellation ? (cancellation.net_cancellation || '') : ''}' readonly>
              </div>
            </div>
            <input type='hidden' name='sell_order' value='${sellOrder}'>
            <input type='hidden' name='id_sell' value='${idSell}'>
            <input type='hidden' name='upc_item' value='${upcItem}'>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button type='submit' class='btn' style='background-color: #632b8b; color: #fff; border-color: #632b8b;'>Save Cancellation</button>
                </div>
          </form>
              </div>
            </div>
          </div>
          `;

  // Remover modal existente si existe
  const existingModal = document.getElementById('cancellationModal');
  if (existingModal) {
    existingModal.remove();
  }

  // Agregar el nuevo modal al DOM
  document.body.insertAdjacentHTML('beforeend', formHTML);

  // Inicializar y mostrar el modal
  const modalEl = document.getElementById('cancellationModal');
  const modal = new bootstrap.Modal(modalEl);
  modal.show();

  // Calcular automáticamente el Net Cancellation cuando cambien los valores
  const form = document.getElementById('cancellationForm');
  const inputs = form.querySelectorAll('input[type="number"]:not(#net_cancellation)');
  
  function calculateNetCancellation() {
    const refund_amount = parseFloat(form.refund_amount.value) || 0;
    const shipping_refund = parseFloat(form.shipping_refund.value) || 0;
    const tax_refund = parseFloat(form.tax_refund.value) || 0;
    const final_fee_refund = parseFloat(form.final_fee_refund.value) || 0;
    const fixed_charge_refund = parseFloat(form.fixed_charge_refund.value) || 0;
    const other_fee_refund = parseFloat(form.other_fee_refund.value) || 0;
    
    const net = refund_amount + shipping_refund + tax_refund - final_fee_refund - fixed_charge_refund - other_fee_refund;
    form.net_cancellation.value = net.toFixed(2);
  }

  inputs.forEach(input => {
    input.addEventListener('input', calculateNetCancellation);
  });

  // Calcular inicialmente
  calculateNetCancellation();

  // Manejar envío del formulario
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('saveCancellations.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(result => {
      if (result.success) {
        alert('Cancellation saved successfully!');
        modal.hide();
        // Actualizar la búsqueda para reflejar cambios
        if (window.lastSearchParams) {
          searchCancellations(window.lastSearchParams);
        }
      } else {
        alert('Error: ' + result.message);
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Error saving cancellation');
    });
  });
}
