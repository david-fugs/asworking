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
        const upc_item = this.dataset.upc_item || null;
        const id_sell = this.dataset.id_sell || null;
        const sku_item = this.dataset.sku_item || null;
        console.log("Selected Sell Order:", sell_order, "UPC:", upc_item, "ID Sell:", id_sell, "SKU:", sku_item);
        
        // Abrir el modal returnModal directamente con el formulario de cancelación para este item específico
        fetch(`getSellToReturn.php?sell_order=${encodeURIComponent(sell_order)}${upc_item ? '&upc_item=' + encodeURIComponent(upc_item) : ''}${id_sell ? '&id_sell=' + encodeURIComponent(id_sell) : ''}`)
          .then((response) => response.json())
          .then((data) => {
            console.log(data);
            if (data.error) {
              document.getElementById("ventasTableContainer").innerHTML = `<p>Error: ${data.error}</p>`;
              return;
            }
            
            const items = data.items;
            const cancellations = data.cancellations || {};
            
            // Guardar datos en variable global para uso posterior
            window.lastCancellationsData = cancellations;
            
            // Buscar el item específico por id_sell y upc_item
            const specificItem = items.find(item => 
              item.id_sell == id_sell && item.upc_item == upc_item
            );
            
            if (!specificItem) {
              document.getElementById("ventasTableContainer").innerHTML = `<p>Error: Item not found</p>`;
              return;
            }

            
            // Crear la clave única usando id_sell + upc_item para buscar cancelación existente
            const uniqueKey = id_sell + '_' + upc_item;
            const cancellation = cancellations[uniqueKey] || null;
            
            console.log('Looking for cancellation with key:', uniqueKey);
            console.log('Available cancellations:', cancellations);
            console.log('Found cancellation:', cancellation);
            
            // Debug adicional para verificar los datos
            if (upc_item === '733004811005') {
              console.log('DEBUG UPC 733004811005:');
              console.log('- id_sell:', id_sell);
              console.log('- upc_item:', upc_item);
              console.log('- uniqueKey:', uniqueKey);
              console.log('- cancellations object:', JSON.stringify(cancellations, null, 2));
              console.log('- cancellation found:', JSON.stringify(cancellation, null, 2));
            }
            
            // Crear el HTML del modal con la tabla completa de la orden Y el formulario específico
            let tableHTML = `
              <h4>Sell Order: ${sell_order}</h4>
              
              <!-- Tabla completa de items de la orden -->
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
                    <th>Status</th>
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
              
              // Marcar el item seleccionado
              const isSelected = item.id_sell == id_sell && item.upc_item == upc_item;
              const rowClass = isSelected ? 'table-warning' : '';
              const statusText = isSelected ? 'SELECTED' : '';
              
              tableHTML += `
                <tr class="${rowClass}">
                  <td>${item.upc_item}</td>
                  <td>${item.sku_item || "-"}</td>
                  <td>${quantity}</td>
                  <td>$${comision_item.toFixed(2)}</td>
                  <td>$${cargo_fijo.toFixed(2)}</td>
                  <td>$${item_profit.toFixed(2)}</td>
                  <td>$${total_item.toFixed(2)}</td>
                  <td><strong>${statusText}</strong></td>
                </tr>
              `;
              totalGeneral += total_item;
            });

            tableHTML += `
                <tr>
                  <td colspan="6" class="text-end"><strong>Total General</strong></td>
                  <td><strong>$${totalGeneral.toFixed(2)}</strong></td>
                  <td></td>
                </tr>
              </tbody>
            </table>
            
            <!-- Información del Item Seleccionado -->
            <div class="card mb-3">
              <div class="card-header bg-warning">
                <h6 class="mb-0">Selected Item - Cancellation Details</h6>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3"><strong>UPC:</strong> ${specificItem.upc_item}</div>
                  <div class="col-md-3"><strong>SKU:</strong> ${specificItem.sku_item || "-"}</div>
                  <div class="col-md-3"><strong>Quantity:</strong> ${specificItem.quantity || 0}</div>
                  <div class="col-md-3"><strong>Total Item:</strong> $${(parseFloat(specificItem.total_item) || 0).toFixed(2)}</div>
                </div>
                <div class="row mt-2">
                  <div class="col-md-3"><strong>Final Fee:</strong> $${(parseFloat(specificItem.comision_item) || 0).toFixed(2)}</div>
                  <div class="col-md-3"><strong>Fixed Charge:</strong> $${(parseFloat(specificItem.cargo_fijo) || 0).toFixed(2)}</div>
                  <div class="col-md-3"><strong>Item Profit:</strong> $${(parseFloat(specificItem.item_profit) || 0).toFixed(2)}</div>
                  <div class="col-md-3"></div>
                </div>
              </div>
            </div>
            
            <!-- Formulario de Cancelación -->
            <div class="card">
              <div class="card-header">
                <h5 class="card-title mb-0">Cancellation Information</h5>
              </div>
              <div class="card-body">
                <form id="cancellationForm" class="row g-3">
                  <div class="col-md-6">
                    <label for="refund_amount" class="form-label">Refund Amount</label>
                    <input type="number" step="0.01" name="refund_amount" id="refund_amount" class="form-control" value="${cancellation && cancellation.refund_amount !== null && cancellation.refund_amount !== undefined ? cancellation.refund_amount : ''}">
                  </div>
                  <div class="col-md-6">
                    <label for="shipping_refund" class="form-label">Shipping Refund</label>
                    <input type="number" step="0.01" name="shipping_refund" id="shipping_refund" class="form-control" value="${cancellation && cancellation.shipping_refund !== null && cancellation.shipping_refund !== undefined ? cancellation.shipping_refund : ''}">
                  </div>
                  <div class="col-md-6">
                    <label for="tax_refund" class="form-label">Tax Refund</label>
                    <input type="number" step="0.01" name="tax_refund" id="tax_refund" class="form-control" value="${cancellation && cancellation.tax_refund !== null && cancellation.tax_refund !== undefined ? cancellation.tax_refund : ''}">
                  </div>
                  <div class="col-md-6">
                    <label for="final_fee_refund" class="form-label">Final Fee Refund</label>
                    <input type="number" step="0.01" name="final_fee_refund" id="final_fee_refund" class="form-control" value="${cancellation && cancellation.final_fee_refund !== null && cancellation.final_fee_refund !== undefined ? cancellation.final_fee_refund : ''}">
                  </div>
                  <div class="col-md-6">
                    <label for="fixed_charge_refund" class="form-label">Fixed Charge Refund</label>
                    <input type="number" step="0.01" name="fixed_charge_refund" id="fixed_charge_refund" class="form-control" value="${cancellation && cancellation.fixed_charge_refund !== null && cancellation.fixed_charge_refund !== undefined ? cancellation.fixed_charge_refund : ''}">
                  </div>
                  <div class="col-md-6">
                    <label for="other_fee_refund" class="form-label">Other Fee Refund</label>
                    <input type="number" step="0.01" name="other_fee_refund" id="other_fee_refund" class="form-control" value="${cancellation && cancellation.other_fee_refund !== null && cancellation.other_fee_refund !== undefined ? cancellation.other_fee_refund : ''}">
                  </div>
                  <div class="col-md-6">
                    <label for="cancellation_date" class="form-label">Cancellation Date</label>
                    <input type="date" name="cancellation_date" id="cancellation_date" class="form-control" value="${cancellation && cancellation.cancellation_date ? cancellation.cancellation_date : ''}">
                  </div>
                  <div class="col-md-6">
                    <label for="net_cancellation" class="form-label"><strong>Net Cancellation</strong></label>
                    <input type="number" step="0.01" name="net_cancellation" id="net_cancellation" class="form-control bg-light" readonly>
                  </div>
                  <input type="hidden" name="sell_order" id="hidden_sell_order" value="${sell_order}">
                  <input type="hidden" name="id_sell" id="hidden_id_sell" value="${id_sell}">
                  <input type="hidden" name="upc_item" id="hidden_upc_item" value="${upc_item}">
                  <div class="col-12">
                    <button type="submit" class="btn" style="background-color: #632b8b; color: #fff; border-color: #632b8b;">
                      <i class="fas fa-save"></i> Save Cancellation
                    </button>
                    <button type="button" class="btn btn-secondary ms-2" data-bs-dismiss="modal">
                      <i class="fas fa-times"></i> Close
                    </button>
                  </div>
                </form>
              </div>
            </div>
            `;

            document.getElementById("ventasTableContainer").innerHTML = tableHTML;

            // Llenar los valores de los inputs después de crear el HTML (método alternativo)
            if (cancellation) {
              console.log('Filling form with cancellation data:', cancellation);
              
              // Llenar cada campo individualmente
              if (cancellation.refund_amount !== null && cancellation.refund_amount !== undefined) {
                document.getElementById('refund_amount').value = cancellation.refund_amount;
              }
              if (cancellation.shipping_refund !== null && cancellation.shipping_refund !== undefined) {
                document.getElementById('shipping_refund').value = cancellation.shipping_refund;
              }
              if (cancellation.tax_refund !== null && cancellation.tax_refund !== undefined) {
                document.getElementById('tax_refund').value = cancellation.tax_refund;
              }
              if (cancellation.final_fee_refund !== null && cancellation.final_fee_refund !== undefined) {
                document.getElementById('final_fee_refund').value = cancellation.final_fee_refund;
              }
              if (cancellation.fixed_charge_refund !== null && cancellation.fixed_charge_refund !== undefined) {
                document.getElementById('fixed_charge_refund').value = cancellation.fixed_charge_refund;
              }
              if (cancellation.other_fee_refund !== null && cancellation.other_fee_refund !== undefined) {
                document.getElementById('other_fee_refund').value = cancellation.other_fee_refund;
              }
              if (cancellation.cancellation_date) {
                document.getElementById('cancellation_date').value = cancellation.cancellation_date;
              }
            } else {
              console.log('No cancellation data found for key:', uniqueKey);
            }

            // Configurar el formulario de cancelación
            setupCancellationForm();
            
            // Calcular el net cancellation inicial
            calculateFormNetCancellation();

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('returnModal'));
            modal.show();
          })
          .catch((err) => {
            console.error('Error:', err);
            Swal.fire({
              icon: 'error',
              title: 'Connection Error',
              text: `Error: ${err.message}`,
              confirmButtonColor: '#632b8b'
            });
          });
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
      Swal.fire({
        icon: 'warning',
        title: 'Missing Information',
        text: 'Please enter either a UPC code or Sell Order to search',
        confirmButtonColor: '#632b8b'
      });
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
      
      Swal.fire({
        icon: 'error',
        title: 'Search Error',
        text: 'An error occurred while searching. Please try again.',
        confirmButtonColor: '#632b8b'
      });
    });
  });

});

// Función global para mostrar el formulario de cancelación
window.showCancellationForm = function(sellOrder, idSell, upcItem, skuItem) {
  console.log('showCancellationForm called for:', { sellOrder, idSell, upcItem, skuItem });
  
  // Crear la clave única usando id_sell + upc_item
  const uniqueKey = idSell + '_' + upcItem;
  
  // Obtener los datos de cancelación existentes
  const cancellation = window.lastCancellationsData && window.lastCancellationsData[uniqueKey] ? window.lastCancellationsData[uniqueKey] : null;
  console.log('Cancellation data for key', uniqueKey, ':', cancellation);
  
  // Llenar los campos del formulario
  document.getElementById('hidden_sell_order').value = sellOrder;
  document.getElementById('hidden_id_sell').value = idSell;
  document.getElementById('hidden_upc_item').value = upcItem;
  
  // Llenar los campos con datos existentes si los hay
  document.getElementById('refund_amount').value = cancellation ? (cancellation.refund_amount || '') : '';
  document.getElementById('shipping_refund').value = cancellation ? (cancellation.shipping_refund || '') : '';
  document.getElementById('tax_refund').value = cancellation ? (cancellation.tax_refund || '') : '';
  document.getElementById('final_fee_refund').value = cancellation ? (cancellation.final_fee_refund || '') : '';
  document.getElementById('fixed_charge_refund').value = cancellation ? (cancellation.fixed_charge_refund || '') : '';
  document.getElementById('other_fee_refund').value = cancellation ? (cancellation.other_fee_refund || '') : '';
  document.getElementById('cancellation_date').value = cancellation ? (cancellation.cancellation_date || '') : '';
  
  // Actualizar el título
  document.getElementById('cancellationTitle').textContent = `Edit Cancellation - Order: ${sellOrder} | ID: ${idSell} | UPC: ${upcItem}${skuItem ? ' | SKU: ' + skuItem : ''}`;
  
  // Calcular net cancellation inicial
  calculateFormNetCancellation();
  
  // Mostrar el formulario
  document.getElementById('cancellationFormContainer').style.display = 'block';
  
  // Scroll al formulario
  document.getElementById('cancellationFormContainer').scrollIntoView({ behavior: 'smooth' });
};

// Función global para ocultar el formulario
window.hideCancellationForm = function() {
  document.getElementById('cancellationFormContainer').style.display = 'none';
};

// Función para calcular Net Cancellation en el formulario
function calculateFormNetCancellation() {
  const refundAmount = parseFloat(document.getElementById('refund_amount')?.value) || 0;
  const shippingRefund = parseFloat(document.getElementById('shipping_refund')?.value) || 0;
  const taxRefund = parseFloat(document.getElementById('tax_refund')?.value) || 0;
  const finalFeeRefund = parseFloat(document.getElementById('final_fee_refund')?.value) || 0;
  const fixedChargeRefund = parseFloat(document.getElementById('fixed_charge_refund')?.value) || 0;
  const otherFeeRefund = parseFloat(document.getElementById('other_fee_refund')?.value) || 0;
  
  const netCancellation = refundAmount + shippingRefund + taxRefund - finalFeeRefund - fixedChargeRefund - otherFeeRefund;
  
  const netCancellationField = document.getElementById('net_cancellation');
  if (netCancellationField) {
    netCancellationField.value = netCancellation.toFixed(2);
  }
}

// Función para configurar el formulario de cancelación
function setupCancellationForm() {
  const form = document.getElementById('cancellationForm');
  const inputs = form.querySelectorAll('input[type="number"]:not(#net_cancellation)');
  
  // Agregar event listeners para cálculo automático
  inputs.forEach(input => {
    input.addEventListener('input', calculateFormNetCancellation);
  });
  
  // Manejar submit del formulario
  form.addEventListener('submit', function(e) {
    e.preventDefault();
    console.log('Formulario de cancelación enviado');
    
    const formData = new FormData(this);
    
    // Debug: mostrar datos del formulario
    console.log('Datos del formulario:');
    for (let pair of formData.entries()) {
      console.log(pair[0] + ': ' + pair[1]);
    }
    
    fetch('saveCancellations.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      console.log('Response status:', response.status);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.text();
    })
    .then(text => {
      console.log('Raw response:', text);
      
      // Limpiar la respuesta de cualquier contenido no JSON
      const cleanText = text.trim();
      let jsonStartIndex = cleanText.indexOf('{');
      let jsonEndIndex = cleanText.lastIndexOf('}');
      
      if (jsonStartIndex === -1 || jsonEndIndex === -1) {
        throw new Error('No valid JSON found in response');
      }
      
      const jsonText = cleanText.substring(jsonStartIndex, jsonEndIndex + 1);
      console.log('Cleaned JSON:', jsonText);
      
      try {
        const result = JSON.parse(jsonText);
        console.log('Parsed result:', result);
        
        if (result.success) {
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: result.message || 'Cancellation saved successfully!',
            confirmButtonColor: '#632b8b',
            timer: 2000,
            timerProgressBar: true
          }).then(() => {
            // Cerrar el modal después del éxito
            const modal = bootstrap.Modal.getInstance(document.getElementById('returnModal'));
            if (modal) {
              modal.hide();
            }
            
            // Recargar automáticamente los resultados de búsqueda si están disponibles
            const searchResults = document.getElementById('searchResults');
            if (searchResults && searchResults.innerHTML.trim() !== '') {
              // Re-ejecutar la última búsqueda
              const lastUpc = document.getElementById('upc').value.trim();
              const lastSellOrder = document.getElementById('sell_order').value.trim();
              
              if (lastUpc || lastSellOrder) {
                setTimeout(() => {
                  document.getElementById('filterForm').dispatchEvent(new Event('submit'));
                }, 500);
              }
            }
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: result.message || 'An error occurred while saving',
            confirmButtonColor: '#632b8b'
          });
        }
      } catch (parseError) {
        console.error('Error parsing JSON:', parseError);
        console.error('Raw response that failed to parse:', text);
        
        Swal.fire({
          icon: 'error',
          title: 'Server Response Error',
          text: 'The server returned an invalid response. Please check the console for details.',
          confirmButtonColor: '#632b8b'
        });
      }
    })
    .catch(error => {
      console.error('Fetch error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Network Error',
        text: `Error saving cancellation: ${error.message}`,
        confirmButtonColor: '#632b8b'
      });
    });
  });
}
