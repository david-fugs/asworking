document.addEventListener("DOMContentLoaded", function () {
  const deleteButtons = document.querySelectorAll(".delete-btn"); // Selecciona todos los botones de eliminar

  document.getElementById("bulkReturnBtn").addEventListener("click", function () {
    const selectedCheckboxes = document.querySelectorAll(".select-sell:checked");
    const ids = Array.from(selectedCheckboxes).map(cb => cb.value);
  
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
        .then(response => response.text())
        .then(data => {
          if (data.trim() === "success") {
            Swal.fire("¡Sent!", "Records are now on Devolutions.", "success");
            selectedCheckboxes.forEach(cb => cb.closest("tr").remove());
          } else {
            Swal.fire("Error", data);
          }
        })
        .catch(error => {
          console.error("Error:", error);
          Swal.fire("Error", "error.");
        });
      }
    });
  });

  
  deleteButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const id_sell = this.getAttribute("data-id"); // Obtén el ID del sell_order
      const row = this.closest("tr"); // Encuentra la fila (tr) correspondiente

      // Mostrar mensaje de confirmación con SweetAlert2
      Swal.fire({
        title: "¿Are you sure?",
        text: "¡This action can not be undone!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Delete it", 
        cancelButtonText: "Cancel",
      }).then((result) => {
        if (result.isConfirmed) {
          // Si se confirma, realiza una solicitud para eliminar el registro
          fetch("deleteSell.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: "id_sell=" + encodeURIComponent(id_sell), // Envía el sell_order como parámetro
          })
            .then((response) => response.text())
            .then((data) => {
              if (data.trim() === "success") {
                Swal.fire(
                  "¡Eliminated!",
                  "The record has been deleted.",
                  "success"
                );
                row.remove(); // Elimina la fila de la tabla sin recargar la página
              } else {
                Swal.fire("Error", "deleting the record.", "error");
              }
            })
            .catch((error) => {
              console.error("Error:", error);
              Swal.fire(
                "Error",
                "Error conecting to the server.",
                "error"
              );
            });
        }
      });
    });
  });

  const editButtons = document.querySelectorAll(".edit-btn");
  const editModal = new bootstrap.Modal(document.getElementById("editModal"));

  editButtons.forEach((button) => {
    button.addEventListener("click", function () {
      // Llenar los campos del modal con los valores de la fila
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

      // Mostrar nombre de tienda y sucursal en los campos correspondientes
      document.getElementById("edit-store").value = this.dataset.storeName; // Nombre de la tienda
      document.getElementById("edit-sucursal").value =
        this.dataset.sucursalCode; // Código de sucursal

      // Guardar los IDs de tienda y sucursal en inputs invisibles
      document.getElementById("edit-store-id").value = this.dataset.storeId; // ID de la tienda
      document.getElementById("edit-sucursal-id").value =
        this.dataset.sucursalId; // ID de la sucursal

      const storeSelect = document.getElementById("edit-store");
      storeSelect.value = this.dataset.storeId;

      // Mostrar el modal
      editModal.show();
    });
  });

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

  document
    .getElementById("edit-sucursal")
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
  document
    .getElementById("edit-quantity")
    .addEventListener("input", actualizarTotal);
  document
    .getElementById("edit-item_price")
    .addEventListener("input", actualizarTotal);
  document
    .getElementById("edit-item_price")
    .addEventListener("change", function () {
      console.log("Cambio en el precio");
      actualizarTotal();
    });

  document
    .getElementById("edit-comision")
    .addEventListener("input", actualizarTotal);
});
