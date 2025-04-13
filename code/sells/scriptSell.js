document.addEventListener("DOMContentLoaded", function () {
  const tiendaSelect = document.getElementById("tienda");
  const sucursalSelect = document.getElementById("sucursal");
  const upcInput = document.getElementById("upc");
  const form = document.querySelector(".form");
  const quantityInput = document.querySelector('input[name="quantitySell"]');
  const priceInput = document.getElementById("priceItem");
  const refInput = document.querySelector("#UnitTotal");
  const itemNameInput = document.getElementById("item_name");
  const bodyTable = document.querySelector("#tableItems tbody");
  const brandItem = document.getElementById("brandItemInput");
  const sellDateInput = document.getElementById("sellDate");
  const comisionInput = document.getElementById("comisionItem");
  const receivedShipping = document.getElementById("receivedShipping");
  const payedShipping = document.getElementById("payedShipping");

  const today = new Date().toISOString().split("T")[0];
  sellDateInput.value = today;

  // Cargar sucursales al cambiar la tienda
  tiendaSelect.addEventListener("change", function () {
    const id_store = this.value;
    sucursalSelect.innerHTML = '<option value="">Cargando...</option>';
    document.getElementById("comisionItem").value = ""; // Reiniciar comisión

    fetch("getSucursales.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id_store=${encodeURIComponent(id_store)}`,
    })
      .then((response) => response.text())
      .then((data) => {
        sucursalSelect.innerHTML = data;
      })
      .catch((error) => {
        console.error("Error cargando sucursales:", error);
      });
  });

  // Actualizar comisión al cambiar sucursal
  sucursalSelect.addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];
    const comision = selectedOption.getAttribute("data-comision") || "0";
    const cargo_fijo = selectedOption.getAttribute("data-cargo") || "0";
    //aca se hara la operacion para la comision
    //console.log("Comisión:", comision, "Cargo fijo:", cargo_fijo);
    document.getElementById("comisionItem").value = comision;
  });

  // Buscar item por UPC al perder foco
  upcInput.addEventListener("blur", function () {
    const upc = this.value.trim();
    if (upc !== "") {
      buscarItemPorUPC(upc);
    } else {
      limpiarCamposProducto();
    }
  });

  // Buscar item por UPC al presionar Enter
  upcInput.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
      const upc = this.value.trim();
      if (upc !== "") {
        buscarItemPorUPC(upc);
      } else {
        limpiarCamposProducto();
      }
    }
  });

  function limpiarCamposProducto() {
    itemNameInput.value = "";
    priceInput.value = "";
    brandItem.value = "";
    upcInput.value = "";
    quantityInput.value = "";
    refInput.value = "";
    comisionInput.value = "";
    receivedShipping.value = "";
    payedShipping.value = "";

    // Reiniciar selects a su opción por defecto
    tiendaSelect.selectedIndex = 0;
    sucursalSelect.innerHTML =
      '<option value="">-- Primero selecciona una tienda --</option>';
  }

  function buscarItemPorUPC(upc) {
    if (!upc.trim()) {
      limpiarCamposProducto();
      return;
    }

    fetch("getItemName.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `upc=${encodeURIComponent(upc)}`,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success && data.item && data.cost) {
          itemNameInput.value = data.item;
          priceInput.value = "$" + parseFloat(data.cost).toFixed(2);
          brandItem.value = data.brand || "";
        } else {
          limpiarCamposProducto();
        }
      })
      .catch((error) => {
        console.error("Error al buscar item por UPC:", error);
      });
  }

  // Calcular precio total en tiempo real
  quantityInput.addEventListener("input", () => {
    const quantity = parseFloat(quantityInput.value) || 0;
    const price = parseFloat(priceInput.value.replace("$", "")) || 0;
    const comision = parseFloat(comisionInput.value) || 0;
    const total = quantity * price + comision;
    refInput.value = `$${total.toFixed(2)}`;
  });

  comisionInput.addEventListener("input", () => {
    const quantity = parseFloat(quantityInput.value) || 0;
    const price = parseFloat(priceInput.value.replace("$", "")) || 0;
    const comision = parseFloat(comisionInput.value) || 0;
    const total = quantity * price + comision;
    refInput.value = `$${total.toFixed(2)}`;
  });

  // Evitar envío del formulario con Enter
  form.addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
      e.preventDefault();
    }
  });

  // Validar y agregar a la tabla al hacer submit
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    // Obtener shipping actualizado al momento del envío
    const receivedShipping =
      parseFloat(document.getElementById("receivedShipping").value) || 0;
    const payedShipping =
      parseFloat(document.getElementById("payedShipping").value) || 0;

    const nombre = itemNameInput.value.trim();
    const upc = upcInput.value.trim();
    const quantity = parseFloat(quantityInput.value) || 0;
    const tienda = tiendaSelect.options[tiendaSelect.selectedIndex]?.text || "";
    const sucursal =
      sucursalSelect.options[sucursalSelect.selectedIndex]?.text || "";
    const precioUnitario = parseFloat(priceInput.value.replace("$", "")) || 0;
    const total = precioUnitario * quantity;
    const brand = brandItem.value.trim();
    const comision =
      parseFloat(document.getElementById("comisionItem").value) || 0;
    const date = sellDateInput.value;

    if (!date) {
      alert("⚠️ Debes ingresar una fecha para poder continuar.");
      return; // No hace nada más si no hay fecha
    }

    if (nombre !== "" && upc && quantity > 0 && tienda && sucursal) {
      const fila = document.createElement("tr");
      fila.innerHTML = `
      <td class="item_name">${nombre}</td>
      <td class="upc_item">${upc}</td>
      <td class="quantity">${quantity}</td>
      <td class="id_store" data-id="${tiendaSelect.value}">${tienda}</td>
      <td class="id_sucursal" data-id="${sucursalSelect.value}">${sucursal}</td>
      <td class="brand">${brand}</td>
      <td class="comision">$${comision.toFixed(2)}</td>
      <td class="fecha">${date}</td>
      <td class="received_shipping">${receivedShipping.toFixed(2)}</td>
      <td class="payed_shipping">${payedShipping.toFixed(2)}</td>
      <td class="precio_unitario">$${precioUnitario.toFixed(2)}</td>
      <td class="total_item">${total.toFixed(2)}</td>
      <td><button type="button" class="btn-delete">🗑️</button></td>
    `;

      // Agregar evento al botón de eliminar
      fila.querySelector(".btn-delete").addEventListener("click", function () {
        fila.remove();

        // Si ya no hay filas en la tabla, habilitar el campo de fecha
        if (bodyTable.querySelectorAll("tr").length === 0) {
          sellDateInput.disabled = false;
        }
      });
      bodyTable.appendChild(fila);

      // Desactivar fecha luego del primer ingreso
      if (!sellDateInput.disabled) {
        sellDateInput.disabled = true;
      }

      // Limpiar campos
      limpiarCamposProducto();
      upcInput.value = "";
      quantityInput.value = "";
      priceInput.value = "";
      refInput.value = "";
      document.getElementById("comisionItem").value = "";
      upcInput.focus();
    } else {
      alert("Verifica que todos aatos estén completos y correctos.");
    }
  });

  // guardar campos en tabla
  document
    .getElementById("saveSellButton")
    .addEventListener("click", async (e) => {
      e.preventDefault();

      const rows = document.querySelectorAll("#tableItems tbody tr");
      const ventas = [];

      rows.forEach((row) => {
        const upc_item = row.querySelector(".upc_item")?.textContent || "";
        const quantity = parseInt(
          row.querySelector(".quantity")?.textContent || "0"
        );
        const received_shipping = parseFloat(
          row.querySelector(".received_shipping")?.textContent || "0"
        );
        const payed_shipping = parseFloat(
          row.querySelector(".payed_shipping")?.textContent || "0"
        );
        const id_store = parseInt(
          row.querySelector(".id_store")?.dataset.id || "0"
        );
        const id_sucursal = parseInt(
          row.querySelector(".id_sucursal")?.dataset.id || "0"
        );
        const comision = parseFloat(
          row.querySelector(".comision")?.textContent.replace("$", "").trim() ||
            "0"
        );
        const total_item = parseFloat(
          row.querySelector(".total_item")?.textContent || "0"
        );

        const date = row.querySelector(".fecha")?.textContent || "";
        ventas.push({
          upc_item,
          quantity,
          received_shipping,
          payed_shipping,
          id_store,
          id_sucursal,
          comision,
          total_item,
          date,
        });
      });

      if (ventas.length === 0) {
        alert("No hay productos para vender.");
        return;
      }

      console.log("Ventas a guardar:", ventas);
      try {
        const response = await fetch("saveSell.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({ ventas }),
        });

        const result = await response.json();
        if (result.success) {
          alert(
            "Ventas guardadas correctamente. Sell Order: " + result.sell_order
          );

          // Limpiar tabla
          bodyTable.innerHTML = "";

          // Habilitar el campo de fecha
          sellDateInput.disabled = false;
        } else {
          alert("Error: " + result.message);
        }
      } catch (error) {
        alert("Error al enviar las ventas: " + error.message);
      }
    });
    
});
