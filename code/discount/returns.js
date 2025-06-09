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
            return;          }
          const items = data.items;
          const discount = data.discount; // Get existing discount data

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

          let totalGeneral = 0;          items.forEach((item) => {
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
        <tr>
          <td colspan="6" class="text-end"><strong>Total General</strong></td>
          <td><strong>$${totalGeneral.toFixed(2)}</strong></td>
        </tr>
      </tbody>
    </table>

<form method='post' action='saveDiscount.php' class='mt-4' id='discountForm'>    <div class='row mb-3'>
        <div class='col-md-6'>
            <label for='price_discount' class='form-label'>Price Discount</label>
            <input type='number' step='0.01' name='price_discount' id='price_discount' class='form-control' value='${discount ? (discount.price_discount || '') : ''}'>
        </div>
        <div class='col-md-6'>
            <label for='shipping_discount' class='form-label'>Shipping Discount </label>
            <input type='number' step='0.01' name='shipping_discount' id='shipping_discount' class='form-control' value='${discount ? (discount.shipping_discount || '') : ''}'>
        </div>
    </div>
    <div class='row mb-3'>
        <div class='col-md-6'>
            <label for='fee_credit' class='form-label'>Fee Credit </label>
            <input type='number' step='0.01' name='fee_credit' id='fee_credit' class='form-control' value='${discount ? (discount.fee_credit || '') : ''}'>
        </div>
        <div class='col-md-6'>
            <label for='tax_return' class='form-label'>Tax Return </label>
            <input type='number' step='0.01' name='tax_return' id='tax_return' class='form-control' value='${discount ? (discount.tax_return || '') : ''}'>
        </div>
    </div><input type='hidden' name='sell_order' value='${items[0].sell_order }'> 
    <input type='hidden' name='id_sell' value='${items[0].id_sell }'>
    
    <div class='text-end'>
        <button type='submit' class='btn' style='background-color: #632b8b; color: #fff; border-color: #632b8b;'>
        Save
        </button>    
    </div>
</form>
    `;
          document.getElementById("ventasTableContainer").innerHTML = tableHTML;
          
          // Add form submission handler
          const form = document.getElementById('discountForm');
          if (form) {
            form.addEventListener('submit', function(e) {
              e.preventDefault();
              
              const formData = new FormData(form);
              
              fetch('saveDiscount.php', {
                method: 'POST',
                body: formData
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message
                  }).then(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('returnModal'));
                    modal.hide();
                    window.location.reload(); // Reload to see updated data
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                  });
                }
              })
              .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'An error occurred while saving the discount information.'
                });
              });
            });
          }
          
          const modal = new bootstrap.Modal(
            document.getElementById("returnModal")
          );
          modal.show();
        })
        .catch((err) => {
          document.getElementById(
            "ventasTableContainer"
          ).innerHTML = `<p>Error: ${err.message}</p>`;
        });
    });
  });

  document.querySelectorAll(".btn-action-icon").forEach((btn) => {
    btn.addEventListener("click", function (e) {
      e.stopPropagation(); // evitar que el clic dispare el modal
    });
  });
});
