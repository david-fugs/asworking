document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".clickable-row").forEach(function (row) {
    row.addEventListener("click", function () {
      const id_sell = this.dataset.id_sell;
      const sell_order = this.dataset.sell_order;
      console.log("Selected Sell Order:", sell_order);
      console.log("sell order:", id_sell);
      fetch(`getSellToReturn.php?sell_order=${encodeURIComponent(sell_order)}`)
        .then((response) => response.json())
        .then((data) => {
          console.log(data);
          if (data.error) {
            document.getElementById(
              "ventasTableContainer"
            ).innerHTML = `<p>Error: ${data.error}</p>`;
            return;
          }
          const items = data.items;
          const cancellation = data.cancellation; // Get existing cancellation data

          // Crear la tabla
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
        </tr>
      `;
            totalGeneral += total_item;
          });

          tableHTML += `
        </tbody>
      </table>
      <div class="row mt-3">
        <div class="col-md-6">
          <strong>Total General: $${totalGeneral.toFixed(2)}</strong>
        </div>
      </div>
    `;

          // Formulario de cancellations con valores existentes si los hay
          tableHTML += `
      <hr>
      <h5>Cancellation Information</h5>
      <form id="cancellationForm">
        <input type="hidden" name="id_sell" value="${items[0].id_sell}">
        <input type="hidden" name="sell_order" value="${items[0].sell_order}">
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="refund_amount" class="form-label">Refund Amount:</label>
              <input type="number" step="0.01" class="form-control" id="refund_amount" name="refund_amount" 
                     value="${cancellation ? cancellation.refund_amount || '' : ''}" placeholder="0.00">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="shipping_refund" class="form-label">Shipping Refund:</label>
              <input type="number" step="0.01" class="form-control" id="shipping_refund" name="shipping_refund" 
                     value="${cancellation ? cancellation.shipping_refund || '' : ''}" placeholder="0.00">
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="tax_refund" class="form-label">Tax Refund:</label>
              <input type="number" step="0.01" class="form-control" id="tax_refund" name="tax_refund" 
                     value="${cancellation ? cancellation.tax_refund || '' : ''}" placeholder="0.00">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="final_fee_refund" class="form-label">Final Fee Refund:</label>
              <input type="number" step="0.01" class="form-control" id="final_fee_refund" name="final_fee_refund" 
                     value="${cancellation ? cancellation.final_fee_refund || '' : ''}" placeholder="0.00">
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label for="fixed_charge_refund" class="form-label">Fixed Charge Refund:</label>
              <input type="number" step="0.01" class="form-control" id="fixed_charge_refund" name="fixed_charge_refund" 
                     value="${cancellation ? cancellation.fixed_charge_refund || '' : ''}" placeholder="0.00">
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label for="other_fee_refund" class="form-label">Other Fee Refund:</label>
              <input type="number" step="0.01" class="form-control" id="other_fee_refund" name="other_fee_refund" 
                     value="${cancellation ? cancellation.other_fee_refund || '' : ''}" placeholder="0.00">
            </div>
          </div>
        </div>
          <div class="row mt-3">
          <div class="col-12 text-end">
            <button type="submit" class="btn" style="background-color: #632b8b; color: white; border: none;">Save Cancellation</button>
          </div>
        </div>
      </form>
    `;

          document.getElementById("ventasTableContainer").innerHTML = tableHTML;

          // Agregar event listener al formulario
          document
            .getElementById("cancellationForm")
            .addEventListener("submit", function (e) {
              e.preventDefault();
              const formData = new FormData(this);

              fetch("saveCancellations.php", {
                method: "POST",
                body: formData,
              })
                .then((response) => response.json())
                .then((result) => {
                  if (result.success) {
                    Swal.fire({
                      title: "Success!",
                      text: result.message,
                      icon: "success",
                      confirmButtonText: "OK",
                    }).then(() => {
                      // Cerrar modal y recargar datos
                      bootstrap.Modal.getInstance(
                        document.getElementById("returnModal")
                      ).hide();
                      location.reload(); // Recargar la página para mostrar los datos actualizados
                    });
                  } else {
                    Swal.fire({
                      title: "Error!",
                      text: result.message,
                      icon: "error",
                      confirmButtonText: "OK",
                    });
                  }
                })
                .catch((error) => {
                  console.error("Error:", error);
                  Swal.fire({
                    title: "Error!",
                    text: "An unexpected error occurred",
                    icon: "error",
                    confirmButtonText: "OK",
                  });
                });
            });

          // Mostrar el modal
          const modal = new bootstrap.Modal(
            document.getElementById("returnModal")
          );
          modal.show();
        })
        .catch((error) => {
          console.error("Error:", error);
          document.getElementById(
            "ventasTableContainer"
          ).innerHTML = `<p>Error loading data</p>`;
        });
    });
  });
});
