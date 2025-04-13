document.addEventListener("DOMContentLoaded", function () {
  const deleteButtons = document.querySelectorAll(".delete-btn"); // Selecciona todos los botones de eliminar

  deleteButtons.forEach((button) => {
    button.addEventListener("click", function () {
      const id_sell = this.getAttribute("data-id"); // Obtén el ID del sell_order
      const row = this.closest("tr"); // Encuentra la fila (tr) correspondiente

      // Mostrar mensaje de confirmación con SweetAlert2
      Swal.fire({
        title: "¿Estás seguro?",
        text: "¡Esta acción no se puede deshacer!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
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
                  "¡Eliminado!",
                  "El registro ha sido eliminado.",
                  "success"
                );
                row.remove(); // Elimina la fila de la tabla sin recargar la página
              } else {
                Swal.fire("Error", "No se pudo eliminar el registro.", "error");
              }
            })
            .catch((error) => {
              console.error("Error:", error);
              Swal.fire(
                "Error",
                "Hubo un problema al conectar con el servidor.",
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
      document.getElementById("edit-store").value = this.dataset.store;
      document.getElementById("edit-sucursal").value = this.dataset.sucursal;
      document.getElementById("edit-quantity").value = this.dataset.quantity;
      document.getElementById("edit-total-item").value = this.dataset.total;

      // Mostrar el modal
      editModal.show();
    });
  });
});
