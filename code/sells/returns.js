document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".clickable-row").forEach(function (row) {
    row.addEventListener("click", function () {
      const id_sell = this.dataset.id_sell;
      console.log("sell order:", id_sell);
      fetch(`getSellToReturn.php?id_sell=${encodeURIComponent(id_sell)}`)
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

    <form method='post' action='saveShipping.php' class='mt-4'>
    <div class='row mb-3'>
        <div class='col-md-4'>
            <label for='shipping_paid' class='form-label'>Shipping Paid</label>
            <input type='number' step='0.01' name='shipping_paid' value='" . (isset($shipping['shipping_paid']) ? htmlspecialchars($shipping['shipping_paid']) : '') . "' id='shipping_paid' class='form-control'>
        </div>
        <div class='col-md-4'>
            <label for='shipping_other' class='form-label'>Shipping Other Carriers</label>
            <input type='number' step='0.01' name='shipping_other_carrier' value='" . (isset($shipping['shipping_other_carrier']) ? htmlspecialchars($shipping['shipping_other_carrier']) : '') . "' id='shipping_other' class='form-control' required>
        </div>
        <div class='col-md-4'>
            <label for='shipping_adjust' class='form-label'>Shipping Label Adjustment</label>
            <input type='number' step='0.01' name='shipping_adjust' value='" . (isset($shipping['shipping_adjust']) ? htmlspecialchars($shipping['shipping_adjust']) : '') . "' id='shipping_adjust' class='form-control' required>
        </div>
    </div>
    <input type='hidden' name='sell_order' value='" . htmlspecialchars($sell_order) . "'>
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
