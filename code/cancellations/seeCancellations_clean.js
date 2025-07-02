// JavaScript para seeCancellations.php - Versión limpia
document.addEventListener('DOMContentLoaded', function() {
  
  // Función para calcular Net Cancellation
  function calculateNetCancellation() {
    const refundAmount = parseFloat(document.getElementById('refund_amount')?.value) || 0;
    const shippingRefund = parseFloat(document.getElementById('shipping_refund')?.value) || 0;
    const taxRefund = parseFloat(document.getElementById('tax_refund')?.value) || 0;
    const finalFeeRefund = parseFloat(document.getElementById('final_fee_refund')?.value) || 0;
    const fixedChargeRefund = parseFloat(document.getElementById('fixed_charge_refund')?.value) || 0;
    const otherFeeRefund = parseFloat(document.getElementById('other_fee_refund')?.value) || 0;
    
    const netCancellation = refundAmount + shippingRefund + taxRefund - finalFeeRefund - fixedChargeRefund - otherFeeRefund;
    
    const netCancellationField = document.getElementById('net_cancellation');
    if (netCancellationField) {
      netCancellationField.style.backgroundColor = '#e8f5e8';
      netCancellationField.value = netCancellation.toFixed(2);
      setTimeout(() => {
        netCancellationField.style.backgroundColor = '';
      }, 300);
    }
  }

  // Función para agregar eventos de cálculo
  function setupCalculationEvents() {
    const fields = ['refund_amount', 'shipping_refund', 'tax_refund', 'final_fee_refund', 'fixed_charge_refund', 'other_fee_refund'];
    
    fields.forEach(fieldId => {
      const field = document.getElementById(fieldId);
      if (field) {
        field.addEventListener('input', calculateNetCancellation);
        field.addEventListener('change', calculateNetCancellation);
        field.addEventListener('keyup', calculateNetCancellation);
        field.addEventListener('paste', () => {
          setTimeout(calculateNetCancellation, 10);
        });
      }
    });
  }

  // Función para inicializar los event listeners de las filas clickeables
  function initializeClickableRows() {
    document.querySelectorAll(".clickable-row").forEach(function (row) {
      const newRow = row.cloneNode(true);
      row.parentNode.replaceChild(newRow, row);
    });
    
    document.querySelectorAll(".clickable-row").forEach(function (row) {
      row.addEventListener("click", function () {
        const sell_order = this.dataset.sell_order;
        console.log("Selected Sell Order:", sell_order);
        
        fetch(`getSellToReturn.php?sell_order=${encodeURIComponent(sell_order)}`)
          .then((response) => response.json())
          .then((data) => {
            console.log(data);
            if (data.error) {
              document.getElementById("ventasTableContainer").innerHTML = `<p>Error: ${data.error}</p>`;
              return;
            }
            
            const items = data.items;
            const cancellations = data.cancellations || {};

            let tableHTML = `
              <h4>Sell Order: ${items[0].sell_order}</h4>
              <table class="table table-bordered table-sm mt-3">
                <thead>
                  <tr>
                    <th>UPC</th>
                    <th>SKU</th>
                    <th>Quantity</th>
                    <th>Final Fee</th>
                    <th>Fixed Charge</th>
                    <th>Item Profit</th>
                    <th>Total Item</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
            `;

            let totalGeneral = 0;
            items.forEach((item) => {
              const quantity = item.quantity || 0;
              const comision_item = parseFloat(item.comision_item) || 0;
              const cargo_fijo = parseFloat(item.cargo_fijo) || 0;
              const item_profit = parseFloat(item.item_profit) || 0;
              const total_item = parseFloat(item.total_item) || 0;
              
              tableHTML += `
                <tr>
                  <td>${item.upc_item}</td>
                  <td>${item.sku_item || "-"}</td>
                  <td>${quantity}</td>
                  <td>$${comision_item.toFixed(2)}</td>
                  <td>$${cargo_fijo.toFixed(2)}</td>
                  <td>$${item_profit.toFixed(2)}</td>
                  <td>$${total_item.toFixed(2)}</td>
                  <td><button class='btn btn-sm btn-primary edit-cancellation' data-sell-order='${item.sell_order}' data-id-sell='${item.id_sell}' data-upc='${item.upc_item}'>Edit Cancellation</button></td>
                </tr>
              `;
              totalGeneral += total_item;
            });

            tableHTML += `
                <tr>
                  <td colspan="7" class="text-end"><strong>Total General</strong></td>
                  <td><strong>$${totalGeneral.toFixed(2)}</strong></td>
                </tr>
              </tbody>
            </table>
            `;

            document.getElementById("ventasTableContainer").innerHTML = tableHTML;

            // Inicializar event listeners para los botones de editar cancelación
            document.querySelectorAll('.edit-cancellation').forEach(button => {
              button.addEventListener('click', function() {
                const sellOrder = this.dataset.sellOrder;
                const idSell = this.dataset.idSell;
                const upcItem = this.dataset.upc;
                
                const cancellation = cancellations[upcItem] || null;
                showCancellationForm(sellOrder, idSell, upcItem, cancellation);
              });
            });
          })
          .catch((err) => {
            document.getElementById("ventasTableContainer").innerHTML = `<p>Error: ${err.message}</p>`;
          });
      });
    });
  }

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

    document.body.insertAdjacentHTML('beforeend', formHTML);

    const modalEl = document.getElementById('cancellationModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    const form = document.getElementById('cancellationForm');
    const inputs = form.querySelectorAll('input[type="number"]:not(#net_cancellation)');
    
    inputs.forEach(input => {
      input.addEventListener('input', calculateNetCancellation);
    });

    calculateNetCancellation();

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

  // Manejar el formulario de búsqueda
  document.getElementById('filterForm').addEventListener('submit', function(e) {
    console.log('Form submit event triggered');
    e.preventDefault();
    console.log('Default prevented');
    
    const upc = document.getElementById('upc').value.trim();
    const sellOrder = document.getElementById('sell_order').value.trim();
    
    console.log('UPC:', upc, 'Sell Order:', sellOrder);
    
    if (!upc && !sellOrder) {
      alert('Please enter either a UPC code or Sell Order to search');
      return;
    }
    
    // Mostrar loading
    document.getElementById('initialMessage').style.display = 'none';
    document.getElementById('resultsContainer').style.display = 'block';
    document.getElementById('searchResults').innerHTML = `
      <div class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Searching...</span>
        </div>
        <p class="mt-2">Searching for Cancellations...</p>
      </div>
    `;
    
    // Realizar búsqueda AJAX
    const formData = new FormData();
    formData.append('upc_item', upc);
    formData.append('sell_order', sellOrder);
    
    fetch('searchCancellations.php', {
      method: 'POST',
      body: formData
    })
    .then(response => response.text())
    .then(data => {
      document.getElementById('searchResults').innerHTML = data;
      
      // Reinicializar los event listeners para las filas clickeables
      initializeClickableRows();
    })
    .catch(error => {
      console.error('Error:', error);
      document.getElementById('searchResults').innerHTML = `
        <div class="alert alert-danger text-center">
          <i class="fas fa-exclamation-triangle"></i>
          <h5>Error</h5>
          <p>An error occurred while searching. Please try again.</p>
        </div>
      `;
    });
  });

});
