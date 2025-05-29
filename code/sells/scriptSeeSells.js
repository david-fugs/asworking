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


  // agregarEventosEditar();
  // function agregarEventosEliminar() {
  //   const deleteButtons = document.querySelectorAll(".delete-btn");

  //   deleteButtons.forEach((button) => {
  //     button.addEventListener("click", function () {
  //       const id_sell = this.getAttribute("data-id");
  //       const row = this.closest("tr");

  //       Swal.fire({
  //         title: "¿Are you sure?",
  //         text: "¡This action can not be undone!",
  //         icon: "warning",
  //         showCancelButton: true,
  //         confirmButtonColor: "#d33",
  //         cancelButtonColor: "#3085d6",
  //         confirmButtonText: "Yes, Delete it",
  //         cancelButtonText: "Cancel",
  //       }).then((result) => {
  //         if (result.isConfirmed) {
  //           fetch("deleteSell.php", {
  //             method: "POST",
  //             headers: {
  //               "Content-Type": "application/x-www-form-urlencoded",
  //             },
  //             body: "id_sell=" + encodeURIComponent(id_sell),
  //           })
  //             .then((response) => response.text())
  //             .then((data) => {
  //               if (data.trim() === "success") {
  //                 Swal.fire(
  //                   "¡Eliminated!",
  //                   "The record has been deleted.",
  //                   "success"
  //                 );
  //                 row.remove();
  //               } else {
  //                 Swal.fire("Error", "Error deleting the record.", "error");
  //               }
  //             })
  //             .catch((error) => {
  //               console.error("Error:", error);
  //               Swal.fire("Error", "Error conecting to the server.", "error");
  //             });
  //         }
  //       });
  //     });
  //   });
  // }

  document.getElementById("bulkReturnBtn")
    .addEventListener("click", function () {
      const selectedCheckboxes = document.querySelectorAll(
        ".select-sell:checked"
      );
      const ids = Array.from(selectedCheckboxes).map((cb) => cb.value);

      if (ids.length === 0) {
        Swal.fire("Nothing selected", "Select at leat 1 record", "info");
        return;
      }
      Swal.fire({
        title: "¿Are you sure to do this devolutions?",
        text: "The records will be send to the devolution menu.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, enviar",
        cancelButtonText: "Cancelar",
      }).then((result) => {
        if (result.isConfirmed) {
          fetch("devolution.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: "id_sell[]=" + ids.join("&id_sell[]="),
          })
            .then((response) => response.text())
            .then((data) => {
              if (data.trim() === "success") {
                Swal.fire(
                  "¡Sent!",
                  "Records are now on Devolutions.",
                  "success"
                );
                selectedCheckboxes.forEach((cb) => cb.closest("tr").remove());
              } else {
                Swal.fire("Error", data);
              }
            })
            .catch((error) => {
              console.error("Error:", error);
              Swal.fire("Error", "error.");
            });
        }
      });
    });
  function agregarEventosEditar() {
    const editButtons = document.querySelectorAll(".btn-edit");
    const editModal = new bootstrap.Modal(document.getElementById("editModal"));

    editButtons.forEach((button) => {
      button.addEventListener("click", function () {
        document.getElementById("edit-id-sell").value = this.dataset.id;
        document.getElementById("edit-sell-order").value =
          this.dataset.sell_order;
        document.getElementById("edit-date").value = this.dataset.date;
        document.getElementById("edit-upc").value = this.dataset.upc;
        document.getElementById("edit-comision").value = this.dataset.comision;
        document.getElementById("edit-rec-shipping").value =
          this.dataset.received_shipping;
        document.getElementById("edit-pay-shipping").value =
          this.dataset.payed_shipping;
        document.getElementById("edit-quantity").value = this.dataset.quantity;
        document.getElementById("edit-item_price").value =
          this.dataset.item_price;
        document.getElementById("edit-total-item").value = this.dataset.total;

        document.getElementById("edit-store").value = this.dataset.storeName;
        document.getElementById("edit-sucursal").value =
          this.dataset.sucursalCode;

        document.getElementById("edit-store-id").value = this.dataset.storeId;
        document.getElementById("edit-sucursal-id").value =
          this.dataset.sucursalId;

        const storeSelect = document.getElementById("edit-store");
        storeSelect.value = this.dataset.storeId;

        editModal.show();
      });
    });
  }

  // Obtener el formulario de edición
  // Obtener el botón "Guardar cambios"
  const saveEditButton = document.getElementById("saveEdit");

  // Añadir el evento de clic al botón
  saveEditButton.addEventListener("click", function () {
    // Obtener los datos del formulario
    const idSell = document.getElementById("edit-id-sell").value;
    const sellOrder = document.getElementById("edit-sell-order").value;
    const date = document.getElementById("edit-date").value;
    const upc = document.getElementById("edit-upc").value;
    const comision = document.getElementById("edit-comision").value;
    const receivedShipping = document.getElementById("edit-rec-shipping").value;
    const payedShipping = document.getElementById("edit-pay-shipping").value;
    const storeID = document.getElementById("edit-store-id").value;
    const sucursalID = document.getElementById("edit-sucursal-id").value;
    const quantity = document.getElementById("edit-quantity").value;
    const item_price = document.getElementById("edit-item_price").value;
    const totalItem = document.getElementById("edit-total-item").value;

    // Validar que los campos requeridos estén llenos (puedes agregar más validaciones si lo deseas)
    if (
      !sellOrder ||
      !date ||
      !upc ||
      !storeID ||
      !sucursalID ||
      !quantity ||
      !item_price ||
      !comision ||
      !totalItem
    ) {
      alert("Por favor, completa todos los campos.");
      return;
    }

    // Crear un objeto con los datos a enviar
    const formData = {
      id_sell: idSell,
      sell_order: sellOrder,
      date: date,
      upc: upc,
      comision: comision,
      received_shipping: receivedShipping,
      payed_shipping: payedShipping,
      storeID: storeID,
      sucursalID: sucursalID,
      quantity: quantity,
      item_price: item_price,
      total_item: totalItem,
    };

    // Enviar los datos al servidor en formato JSON
    fetch("updateSell.php", {
      method: "PATCH",
      headers: {
        "Content-Type": "application/json", // Especifica que el contenido es JSON
      },
      body: JSON.stringify(formData), // Convierte el objeto JavaScript en JSON
    })
      .then((response) => response.json()) // Suponiendo que el servidor responde con un JSON
      .then((data) => {
        if (data.success) {
          alert("Venta actualizada con éxito.");
          window.location.reload(); // Recargar la página para ver los cambios
          // Aquí puedes cerrar el modal si todo fue exitoso
          $("#editModal").modal("hide");
          // Puedes actualizar la tabla de ventas con los nuevos datos si es necesario
          // location.reload(); // Para recargar la página y mostrar los datos actualizados
        } else {
          alert("Error al actualizar la venta: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Hubo un problema con la actualización.");
      });
  });

  function cargarSucursalesEdit(storeId, selectedSucursalId = null) {
    const sucursalSelect = document.getElementById("edit-sucursal");

    if (!storeId) {
      sucursalSelect.innerHTML =
        '<option value="">Selecciona una sucursal</option>';
      return;
    }

    const formData = new FormData();
    formData.append("id_store", storeId);

    fetch("getSucursales.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.text())
      .then((html) => {
        sucursalSelect.innerHTML = html;

        // Seleccionar la sucursal que viene cargada desde la tabla
        if (selectedSucursalId) {
          sucursalSelect.value = selectedSucursalId;
        }
      })
      .catch((error) => {
        console.error("Error al cargar sucursales:", error);
      });
  }

  document
    .getElementById("editModal")
    .addEventListener("show.bs.modal", function () {
      const storeId = document.getElementById("edit-store").value;
      const sucursalId = document.getElementById("edit-sucursal-id").value; // este hidden viene con la info de la tabla

      cargarSucursalesEdit(storeId, sucursalId);
    });

  // También actualiza las sucursales si cambian la tienda manualmente en el modal
  document.getElementById("edit-store").addEventListener("change", function () {
    const nuevoStoreId = this.value;
    cargarSucursalesEdit(nuevoStoreId);
  });

  document.getElementById("edit-sucursal")
    .addEventListener("change", function () {
      const selectedOption = this.options[this.selectedIndex];
      const comision = parseFloat(selectedOption.dataset.comision) || 0;
      const cargoFijo = parseFloat(selectedOption.dataset.cargo) || 0;

      // Rellenar el input invisible o hacer lo que necesites con la comisión
      document.getElementById("edit-comision").value = comision;

      // Opcional: mostrar en consola o hacer el siguiente paso

      // Aquí podrías llamar una función para actualizar el total automáticamente
      actualizarTotal();
    });

  function actualizarTotal() {
    const cantidad =
      parseFloat(document.getElementById("edit-quantity").value) || 0;
    const precio =
      parseFloat(document.getElementById("edit-item_price").value) || 0;
    const sucursalSelect = document.getElementById("edit-sucursal");
    const comision =
      parseFloat(document.getElementById("edit-comision").value) || 0;

    //document.getElementById("edit-comision").value = comision.toFixed(2);
    const total = cantidad * precio * comision;
    document.getElementById("edit-total-item").value = total.toFixed(2);
  }

  // Listeners
  document.getElementById("edit-quantity")
    .addEventListener("input", actualizarTotal);
  document.getElementById("edit-item_price")
    .addEventListener("input", actualizarTotal);
  document.getElementById("edit-item_price")
    .addEventListener("change", function () {
      console.log("Cambio en el precio");
      actualizarTotal();
    });

  document.getElementById("edit-comision")
    .addEventListener("input", actualizarTotal);

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
              <td><input type="checkbox" class="select-sell" value="${venta.id_sell}"></td>
              <td>${venta.sell_order}</td>
              <td>${venta.date}</td>
              <td>${venta.upc_item}</td>
              <td>${venta.received_shipping}</td>
              <td>${venta.payed_shipping}</td>
              <td>${venta.store_name}</td>
              <td>${venta.code_sucursal}</td>
              <td>${venta.comision_item}</td>
              <td>${venta.quantity}</td>
              <td>${venta.item_price}</td>
              <td>${venta.total_item}</td>
              <td>
                <button class="btn-edit" data-id="${venta.id_sell}" data-sell_order="${venta.sell_order}" data-date="${venta.date}" data-upc="${venta.upc_item}" data-received_shipping="${venta.received_shipping}" data-payed_shipping="${venta.payed_shipping}" data-store-name="${venta.store_name}" data-store-id="${venta.id_store}" data-sucursal-code="${venta.code_sucursal}" data-sucursal-id="${venta.id_sucursal}" data-comision="${venta.comision_item}" data-quantity="${venta.quantity}" data-item_price="${venta.item_price}" data-total="${venta.total_item}">
                  <img src='../../img/editar.png' width='28' height='28' alt='Editar'>
                </button>
              </td>
              <td>
                <button class="delete-btn" data-id="${venta.id_sell}">
                  🗑️
                </button>
              </td>
            `;
            });
            conectarBotones();
            agregarEventosEliminar();
            agregarEventosEditar();
          } else {
            alert("No se encontraron ventas con esos filtros.");
          }
        })
        .catch((error) => {
          console.error("Error al filtrar ventas:", error);
        });
    });

  function conectarBotones() {
    document.querySelectorAll(".btn-edit").forEach((btn) => {
      btn.addEventListener("click", function () {
        const id = this.getAttribute("data-id");
        // Aquí pones tu función para abrir el modal de editar con ese ID
        console.log("Editar venta con ID:", id);
        // abrirModalEditar(id); <-- Tu función real
      });
    });

    document.querySelectorAll(".delete-btn").forEach((btn) => {
      btn.addEventListener("click", function () {
        const id = this.getAttribute("data-id");
        // Aquí pones tu función para eliminar
        console.log("Eliminar venta con ID:", id);
        // eliminarVenta(id); <-- Tu función real
      });
    });
  }
});
