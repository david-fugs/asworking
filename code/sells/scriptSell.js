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
  //const payedShipping = document.getElementById("payedShipping");
  const quantitySell = document.getElementById("quantitySell");
  const today = new Date().toISOString().split("T")[0];
  const taxInput = document.getElementById("taxItem");
  const cargoFijoInput = document.getElementById("cargo_fijo");
  const incentivesInput = document.getElementById("incentives");
  const international_fee = document.getElementById("international_fee");
  const ad_fee = document.getElementById("ad_fee");
  const other_fee = document.getElementById("other_fee");
  const itemProfit = document.getElementById("itemProfit");
  const markup = document.getElementById("markup");
  const profitMargin = document.getElementById("profitMargin");
  const itemCost = document.getElementById("itemCost");
  const skuItem = document.getElementById("sku");
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
    const configsJson = selectedOption.getAttribute("data-configs");

    if (configsJson) {
      const configs = JSON.parse(configsJson);
      if (configs.length > 0) {
        const primeraConfig = configs[0];
        document.getElementById("comisionItem").value = primeraConfig.comision;
        document.getElementById("cargo_fijo").value = primeraConfig.cargo_fijo;
        console.log("sales less than:", primeraConfig.sales_less_than);
        console.log("Primera configuración aplicada:");
        console.log("Comisión:", primeraConfig.comision);
        console.log("Cargo fijo:", primeraConfig.cargo_fijo);
      } else {
        // Por si acaso está vacío el array
        document.getElementById("comisionItem").value = 0;
        document.getElementById("cargo_fijo").value = 0;
      }
    }
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
    // payedShipping.value = "";
    taxInput.value = "";
    cargoFijoInput.value = "";
    incentivesInput.value = "";
    international_fee.value = "";
    ad_fee.value = "";
    other_fee.value = "";
    itemProfit.value = "";
    markup.value = "";
    profitMargin.value = "";
    itemCost.value = "";
    skuItem.value = "";

    // Reiniciar selects a su opción por defecto
    tiendaSelect.selectedIndex = 0;
    sucursalSelect.innerHTML =
      '<option value="">-- First select store --</option>';
  }
  function seleccionarProducto(index) {
    const item = window._opcionesProducto[index];
    Swal.close(); // Cerrar modal
    llenarCamposProducto(item); // Llenar los campos como si fuera solo 1 resultado
  }
  window.seleccionarProducto = seleccionarProducto; // Hacer la función accesible globalmente
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
        if (data.success && data.items.length > 0) {
          if (data.items.length === 1) {
            llenarCamposProducto(data.items[0]);
          } else {
            // Mostrar opciones para elegir
            mostrarOpcionesProducto(data.items);
          }
        } else {
          Swal.fire({
            icon: "error",
            title: "UPC Not Found",
            text: "This UPC does not exist in the database.",
          });
          limpiarCamposProducto();
        }
      })
      .catch((error) => {
        console.error("Error al buscar item por UPC:", error);
      });
  }  function mostrarOpcionesProducto(items) {
    // Los items ya vienen filtrados y con stock acumulado por SKU desde el backend
    // Solo necesitamos mostrarlos sin filtrado adicional
    const unicos = items;

    let html = `
  <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
`;    unicos.forEach((item, index) => {
      html += `
    <div onclick="seleccionarProducto(${index})"
      style="
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 15px;
        width: 240px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        background-color: #f9f9f9;
        transition: transform 0.2s, box-shadow 0.2s;
      "
      class="producto-opcion"
      data-index="${index}"
      onmouseover="this.style.transform='scale(1.03)'; this.style.boxShadow='0 4px 12px rgba(0, 0, 0, 0.2)'"
      onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 2px 6px rgba(0, 0, 0, 0.1)'"
    >      <strong style="font-size: 16px; color: #333;">${item.item}</strong><br>
      <span style="color: #777;">Brand:</span> ${item.brand || "N/A"}<br>
      <span style="color: #777;">SKU:</span> ${item.sku || "N/A"}<br>
      <span style="color: #777;">Cost:</span> <strong>$${parseFloat(item.cost).toFixed(2)}</strong><br>
      <span style="color: #777;">Ref Item:</span> <strong>${item.ref_item || "N/A"}</strong><br>
    </div>
  `;
    });

    html += `</div>`;

    Swal.fire({
      title: "Choose the correct product",
      html: html,
      showConfirmButton: false,
      width: "800px",
    });

    // Guardamos sólo los únicos productos
    window._opcionesProducto = unicos;
  }

  function llenarCamposProducto(data) {
    const maxCantidadInventory = parseInt(data.quantity);
    if (maxCantidadInventory <= 0) {
      Swal.fire({
        icon: "warning",
        title: "Out of stock",
        text: "This product has no available inventory.",
      });
      limpiarCamposProducto();
      return;
    }

    itemNameInput.value = data.item;
    itemCost.value = "$" + parseFloat(data.cost).toFixed(2);
    priceInput.value = "$";
    brandItem.value = data.brand || "";
    skuItem.value = data.sku || "";

    const quantityInput1 = document.getElementById("quantitySell");
    itemProfitValue = data.cost;

    quantityInput1.max = maxCantidadInventory;
    quantityInput1.value = "";

    quantityInput1.addEventListener("input", function () {
      if (parseInt(this.value) > maxCantidadInventory) {
        alert(`Only ${maxCantidadInventory} units are available.`);
        this.value = "";
        refInput.value = "";
      }
    });
  }

  function actualizarComisionYCargo() {
    console.log("itemProfitValue" + itemProfitValue);
    //dependiendo de la sucursal seleccionada, se actualiza la comisión y el cargo fijo
    const quantity = parseFloat(quantityInput.value) || 0;
    const price = parseFloat(priceInput.value.replace("$", "")) || 0;
    const ventaTotal = quantity * price;
    console.log("Venta total: ", ventaTotal);
    console.log("price: ", price);
    console.log("quantity: ", quantity);

    const selectedOption = sucursalSelect.options[sucursalSelect.selectedIndex];
    const configsJson = selectedOption.getAttribute("data-configs");

    if (configsJson) {
      const configs = JSON.parse(configsJson);

      // Buscar la primera configuración donde ventaTotal < sales_less_than
      const configAplicada =
        configs.find((c) => ventaTotal < c.sales_less_than) ||
        configs[configs.length - 1];

      const comision = parseFloat(configAplicada.comision) || 0;
      const cargo_fijo = parseFloat(configAplicada.cargo_fijo) || 0;
      const incentives_value = parseFloat(incentivesInput.value) || 0;
      const international_fee_value = parseFloat(international_fee.value) || 0;
      const ad_fee_value = parseFloat(ad_fee.value) || 0;
      const other_fee_value = parseFloat(other_fee.value) || 0;
      const receivedShippingValue =
        parseFloat(receivedShipping.value.replace("$", "")) || 0;
      const taxInputValue = parseFloat(taxInput.value.replace("$", "")) || 0;
      document.getElementById("cargo_fijo").value = cargo_fijo.toFixed(2);
      // Actualizar los inputs visuales
      comisionInput.value = comision.toFixed(2);
      // Calcular el total y mostrarlo
      const total =
        ventaTotal +
        receivedShippingValue +
        taxInputValue -
        comision -
        cargo_fijo +
        incentives_value -
        international_fee_value -
        ad_fee_value -
        other_fee_value;
      //costo es itemprofitValue
      profitTotal = total - itemProfitValue * quantity;
      itemProfit.value = `$${profitTotal.toFixed(2)}`;
      refInput.value = `$${total.toFixed(2)}`;
      markup.value = `${((profitTotal / (price * quantity)) * 100).toFixed(
        2
      )}%`;

      profitMargin.value = `${(
        (profitTotal / (price * quantity + receivedShippingValue)) *
        100
      ).toFixed(2)}%`;
      sku.value = skuItem.value;
    }
  }

  // Escuchar cambios en cantidad y precio
  quantityInput.addEventListener("input", actualizarComisionYCargo);
  priceInput.addEventListener("input", actualizarComisionYCargo);
  sucursalSelect.addEventListener("change", actualizarComisionYCargo);
  receivedShipping.addEventListener("input", actualizarComisionYCargo);
  taxInput.addEventListener("input", actualizarComisionYCargo);
  comisionInput.addEventListener("input", actualizarComisionYCargo);
  cargoFijoInput.addEventListener("input", actualizarComisionYCargo);
  incentivesInput.addEventListener("input", actualizarComisionYCargo);
  international_fee.addEventListener("input", actualizarComisionYCargo);
  ad_fee.addEventListener("input", actualizarComisionYCargo);
  other_fee.addEventListener("input", actualizarComisionYCargo);

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
    // const payedShipping =
    //   parseFloat(document.getElementById("payedShipping").value) || 0;
    const tax = parseFloat(
      document.getElementById("taxItem").value.replace("$", "") || 0
    );

    const nombre = itemNameInput.value.trim();
    const upc = upcInput.value.trim();
    const sku = skuItem.value.trim();
    const quantity = parseFloat(quantityInput.value) || 0;
    const tienda = tiendaSelect.options[tiendaSelect.selectedIndex]?.text || "";
    const sucursal =
      sucursalSelect.options[sucursalSelect.selectedIndex]?.text || "";
    const precioUnitario = parseFloat(priceInput.value.replace("$", "")) || 0;
    const brand = brandItem.value.trim();
    const comision =
      parseFloat(document.getElementById("comisionItem").value) || 0;
    const incentives_value = parseFloat(incentivesInput.value) || 0;
    const international_fee_value = parseFloat(international_fee.value) || 0;
    const ad_fee_value = parseFloat(ad_fee.value) || 0;
    const other_fee_value = parseFloat(other_fee.value) || 0;
    const comisionFijo =
      parseFloat(document.getElementById("cargo_fijo").value) || 0;
    const date = sellDateInput.value;
    const total =
      precioUnitario * quantity +
      receivedShipping +
      tax -
      comision -
      comisionFijo +
      incentives_value -
      international_fee_value -
      ad_fee_value -
      other_fee_value;
    const itemProfitValue = parseFloat(itemProfit.value.replace("$", "")) || 0;
    const markupValue = parseFloat(markup.value.replace("%", "")) || 0;
    const profitMarginValue =
      parseFloat(profitMargin.value.replace("%", "")) || 0;

    if (!date) {
      alert("⚠️ Debes ingresar una fecha para poder continuar.");
      return; // No hace nada más si no hay fecha
    }
    // ESTO LO SAQUE DE LA PARTE DE ABAJO POR SI SE NECESITA MAS ADELANTE <td class="payed_shipping">${payedShipping.toFixed(2)}</td>
    //  <td class="item_name">${nombre}</td>
    //  <td class="fecha">${date}</td>
    // <td class="brand">${brand}</td>
    if (nombre !== "" && upc && quantity > 0 && tienda && sucursal) {
      const fila = document.createElement("tr");
      fila.innerHTML = `
      <td class="upc_item">${upc}</td>
      <td class="sku_item d-none">${sku}</td>
      <td class="quantity">${quantity}</td>
      <td class="id_store" data-id="${tiendaSelect.value}">${tienda}</td>
      <td class="id_sucursal" data-id="${sucursalSelect.value}">${sucursal}</td>
      <td class="comision">$${comision.toFixed(2)}</td>
      <td class="cargo_fijo">$${comisionFijo.toFixed(2)}</td>
      <td class="received_shipping">${receivedShipping.toFixed(2)}</td>
      <td class="">${tax.toFixed(2)}</td>
      <td class="precio_unitario">$${precioUnitario.toFixed(2)}</td>
      <td class="total_item">${total.toFixed(2)}</td>
      <td class="incentives_value d-none">${incentives_value}</td> 
      <td class="international_fee d-none">${international_fee_value}</td>
      <td class="ad_fee d-none">${ad_fee_value}</td>
      <td class="other_fee d-none">${other_fee_value}</td>
      <td class="item_profit d-none">${itemProfitValue}</td>
      <td class="markup d-none">${markupValue}</td>
      <td class="profit_margin d-none">${profitMarginValue}</td>
    
      <td><button type="button" class="btn-action btn-delete"><i class="fas fa-trash-alt"></i></button></td>
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
      Swal.fire({
        icon: "error",
        title: "Error validating",
        text: "Verify that all data is complete and correct.",
      });
    }
  });

  // guardar campos en tabla
  document
    .getElementById("saveSellButton")
    .addEventListener("click", async (e) => {
      e.preventDefault();

      Swal.fire({
        title: "Enter Sell Order",
        input: "text",
        inputLabel: "Manual Sell Order",
        inputPlaceholder: "Enter the sell order number",
        showCancelButton: true,
        confirmButtonText: "Submit",
        inputValidator: (value) => {
          if (!value) {
            return "You need to enter a number!";
          }
        },
      }).then(async (result) => {
        if (result.isConfirmed) {
          const manualSellOrder = result.value;

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
            const tax = parseFloat(
              row.querySelector(".tax")?.textContent.replace("$", "").trim() ||
                "0"
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
              row
                .querySelector(".comision")
                ?.textContent.replace("$", "")
                .trim() || "0"
            );
            const cargo_fijo = parseFloat(
              row
                .querySelector(".cargo_fijo")
                ?.textContent.replace("$", "")
                .trim() || "0"
            );
            const total_item = parseFloat(
              row.querySelector(".total_item")?.textContent || "0"
            );
            const item_price = parseFloat(
              row
                .querySelector(".precio_unitario")
                ?.textContent.replace("$", "")
                .trim() || "0"
            );
            const incentives_value = parseFloat(
              row.querySelector(".incentives_value")?.textContent || "0"
            );
            const international_fee_value = parseFloat(
              row.querySelector(".international_fee")?.textContent || "0"
            );
            const ad_fee_value = parseFloat(
              row.querySelector(".ad_fee")?.textContent || "0"
            );
            const other_fee_value = parseFloat(
              row.querySelector(".other_fee")?.textContent || "0"
            );
            const item_profit = parseFloat(
              row.querySelector(".item_profit")?.textContent || "0"
            );
            const markup = parseFloat(
              row.querySelector(".markup")?.textContent.replace("%", "") || "0"
            );
            const profit_margin = parseFloat(
              row
                .querySelector(".profit_margin")
                ?.textContent.replace("%", "") || "0"
            );
            const date = row.querySelector(".fecha")?.textContent || "";
            const sku = row.querySelector(".sku_item")?.textContent || "";

            ventas.push({
              upc_item,
              quantity,
              received_shipping,
              tax,
              id_store,
              id_sucursal,
              comision,
              cargo_fijo,
              item_price,
              incentives_value,
              international_fee_value,
              ad_fee_value,
              other_fee_value,
              total_item,
              date,
              item_profit,
              markup,
              profit_margin,
              sku
              // payed_shipping, // Descomenta si necesitas enviarlo
            });
          });

          if (ventas.length === 0) {
            Swal.fire({
              icon: "error",
              title: "Error creating sale",
              text: "There are no products to create a sale.",
            });
            return;
          }

          try {
            const response = await fetch("saveSell.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify({ ventas, sell_order: manualSellOrder }),
            });

            const result = await response.json();
            if (result.success) {
              if (ventas.length === 1) {
                Swal.fire({
                  icon: "success",
                  title: "Sale saved successfully",
                  text: `The sale was created with order number ${manualSellOrder}.`,
                });
              } else {
                Swal.fire({
                  icon: "success",
                  title: "Sales saved successfully",
                  text: `They have registered ${ventas.length} ventas.`,
                });
              }

              // Limpiar tabla
              bodyTable.innerHTML = "";

              // Habilitar el campo de fecha
              sellDateInput.disabled = false;
            }
          } catch (error) {
            Swal.fire({
              icon: "error",
              title: "Error",
              text: `Error sending sales.`,
            });
          }
        }
      });
    });
});
