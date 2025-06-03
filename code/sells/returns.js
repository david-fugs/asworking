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
            tableHTML += `
        <tr>
          <td>${item.upc_item}</td>
          <td>${item.sku_item || "-"}</td>
          <td>${item.quantity}</td>
          <td>$${parseFloat(item.comision_item).toFixed(2)}</td>
          <td>$${parseFloat(item.cargo_fijo).toFixed(2)}</td>
          <td>$${parseFloat(item.item_profit).toFixed(2)}</td>
          <td>$${parseFloat(item.total_item).toFixed(2)}</td>
        </tr>
      `;
            totalGeneral += parseFloat(item.total_item);
          });

          tableHTML += `
        <tr>
          <td colspan="6" class="text-end"><strong>Total General</strong></td>
          <td><strong>$${totalGeneral.toFixed(2)}</strong></td>
        </tr>
      </tbody>
    </table>

<form method='post' action='saveReturn.php' class='mt-4' id='returnForm'>
    <div class='row mb-3'>
    <div class='col-md-4'>
            <label for='shipping_paid' class='form-label'>Quantity</label>
            <input type='number' step='0.01' name='quantity' ' id='quantity' class='form-control'>
        </div>
        <div class='col-md-4'>
            <label for='shipping_paid' class='form-label'>Product Charge</label>
            <input type='number' step='0.01' name='product_charge' ' id='product_charge' class='form-control'>
        </div>
        <div class='col-md-4'>
            <label for='shipping_other' class='form-label'>Shipping Paid</label>
            <input type='number' step='0.01' name='shipping_paid'  id='shipping_paid' class='form-control' >
        </div>
        
    </div>
    <div class='row mb-3'>
    <div class='col-md-4'>
            <label for='tax_return' class='form-label'>Tax Return</label>
            <input type='number' step='0.01' name='tax_return'   id='tax_return' class='form-control' >
        </div>
        <div class='col-md-4'>
            <label for='selling_fee_refund' class='form-label'>Selling Fee Refund</label>
            <input type='number' step='0.01' name='selling_fee_refund' id='selling_fee_refund' class='form-control' >
        </div>
        <div class='col-md-4'>
            <label for='refund_administration_fee' class='form-label'>Refund Aministration Fee</label>
            <input type='number' step='0.01'  name='refund_administration_fee'  id='refund_administration_fee' class='form-control' >
        </div>
        
    </div>
    <div class='row mb-3'>
        <div class='col-md-4'>
            <label for='return_cost' class='form-label'>Return Cost</label>
            <input type='number' step='0.01' name='return_cost'   id='return_cost' class='form-control'>
        </div>
        <div class='col-md-4'>
            <label for='buyer_comments' class='form-label'>Buyer Comments</label>
            <input type='text' name='buyer_comments'   id='buyer_comments' class='form-control'>
        </div>
        <div class='col-md-4'>
            <label for='other_refund_fee' class='form-label'>Other Refund Fee</label>
            <input type='text' name='other_refund_fee' id='other_refund_fee' class='form-control'>
        </div>
        </div>
    <input type='hidden' name='sell_order' value='${items[0].sell_order }'> 
    <input type='hidden' name='id_sell' value='${items[0].id_sell }'>
    <input type='hidden' name='upc_item' value='${items[0].upc_item }'>
    <input type='hidden' name='sku_item' value='${items[0].sku_item }'>
    
    <div class='text-end'>
        <button type='submit' class='btn' style='background-color: #632b8b; color: #fff; border-color: #632b8b;'>
        Save
        </button>    
    </div>
</form>
    `;
          document.getElementById("ventasTableContainer").innerHTML = tableHTML;
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
