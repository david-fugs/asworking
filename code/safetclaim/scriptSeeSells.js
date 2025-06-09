document.addEventListener("DOMContentLoaded", function () {

//eliminar
 document.querySelectorAll(".btn-delete").forEach(function (button) {
    button.addEventListener("click", function () {
      const id = this.getAttribute("data-id");

      Swal.fire({
        title: 'Are you sure?',
        text: "This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          // Send AJAX request
          fetch('deleteSell.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `id_sell=${id}`
          })
          .then(response => response.text())
          .then(data => {
            if (data.trim() === 'success') {
              Swal.fire(
                'Deleted!',
                'The record has been deleted.',
                'success'
              ).then(() => {
                // Optionally remove the row from the table
                const row = button.closest("tr");
                if (row) row.remove();
              });
            } else {
              Swal.fire('Error', 'There was a problem deleting the record.', 'error');
            }
          });
        }
      });
    });
  });
  //end eliminar

  document.getElementById("bulkReturnBtn")
    .addEventListener("click", function () {
      const selectedCheckboxes = document.querySelectorAll(
        ".select-sell:checked"
      );
      const ids = Array.from(selectedCheckboxes).map((cb) => cb.value);

      if (ids.length === 0) {
        Swal.fire("Nothing selected", "Select at least 1 record", "info");
        return;
      }
      Swal.fire({
        title: "¿Are you sure to process Safe T-Claims?",
        text: "The records will be processed for Safe T-Claim.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, process",
        cancelButtonText: "Cancel",
      }).then((result) => {
        if (result.isConfirmed) {
          // For now, just show success message as this is a different module
          Swal.fire(
            "Processed!",
            "Records are ready for Safe T-Claim processing.",
            "success"
          );
        }
      });
    });

  // Función para filtrar ventas
  document.getElementById("filterForm")
    .addEventListener("submit", function (e) {
      e.preventDefault(); // Prevenir el envío del formulario

      const upc = document.getElementById("upc").value;
      const sell_order = document.getElementById("sell_order").value;
      const date = document.getElementById("date").value;

      // Crear el objeto con los filtros
      const filters = {
        upc: upc,
        sell_order: sell_order,
        date: date,
      };

      // Hacer la petición al servidor
      fetch("filterSells.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `upc=${encodeURIComponent(
          filters.upc
        )}&sell_order=${encodeURIComponent(
          filters.sell_order
        )}&date=${encodeURIComponent(filters.date)}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            console.log(data.ventas); // Verifica la respuesta del servidor

            // Si la respuesta es exitosa, actualizar la tabla
            const ventasTableBody = document
              .getElementById("salesTable")
              .getElementsByTagName("tbody")[0];

            ventasTableBody.innerHTML = ""; // Limpiar la tabla antes de llenarla

            data.ventas.forEach((venta) => {
              const row = ventasTableBody.insertRow();
              row.innerHTML = `
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>${venta.sell_order}</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>${venta.date}</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>${venta.upc_item}</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>${venta.brand_item || '-'}</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>${venta.item_item || '-'}</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>${venta.color_item || '-'}</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>${venta.ref_item || '-'}</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>${venta.store_name}</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>${venta.code_sucursal}</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>-</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>-</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>-</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>-</td>
              <td class='clickable-row' data-sell_order='${venta.sell_order}'>-</td>
              <td>
                <button class='btn-action-icon btn-delete' data-id='${venta.id_sell}'><i class='fas fa-trash-alt'></i></button>
              </td>
            `;
            });
            // Re-bind click events for new rows
            bindClickableRows();
          } else {
            alert("No se encontraron ventas con esos filtros.");
          }
        })
        .catch((error) => {
          console.error("Error al filtrar ventas:", error);
        });
    });

  function bindClickableRows() {
    document.querySelectorAll(".clickable-row").forEach(function (row) {
      row.addEventListener("click", function () {
        const sell_order = this.dataset.sell_order;
        console.log("Selected Sell Order:", sell_order);
        // Add your modal opening logic here
      });
    });
  }
});
