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
  const receivedShipping = document.getElementById("receivedShipping");
  const quantitySell = document.getElementById("quantitySell");
  const today = new Date().toISOString().split("T")[0];
  const taxInput = document.getElementById("taxItem");
  const withheldTaxInput = document.getElementById("withheld_tax");
  const incentivesInput = document.getElementById("incentives");
  const international_fee = document.getElementById("international_fee");
  const ad_fee = document.getElementById("ad_fee");
  const other_fee = document.getElementById("other_fee");
  const itemProfit = document.getElementById("itemProfit");
  const markup = document.getElementById("markup");
  const profitMargin = document.getElementById("profitMargin");
  const itemCost = document.getElementById("itemCost");
  const skuItem = document.getElementById("sku");
  
  // Nuevos elementos para la sección de orden
  const orderConfigSection = document.getElementById("orderConfigSection");
  const orderMarketplace = document.getElementById("orderMarketplace");
  const orderStoreCod = document.getElementById("orderStoreCod");
  const orderFinalFeeInput = document.getElementById("orderFinalFee");
  const orderFixedChargeInput = document.getElementById("orderFixedCharge");
  
  // Debug: verificar si los elementos existen
  console.log('Order elements check:', {
    orderConfigSection: !!orderConfigSection,
    orderMarketplace: !!orderMarketplace,
    orderStoreCod: !!orderStoreCod,
    orderFinalFeeInput: !!orderFinalFeeInput,
    orderFixedChargeInput: !!orderFixedChargeInput
  });
  
  // Debug: verificar elementos de cálculo
  console.log('Calculation elements check:', {
    markup: !!markup,
    profitMargin: !!profitMargin,
    itemProfit: !!itemProfit,
    itemCost: !!itemCost
  });
  
  sellDateInput.value = today;

  // Variable global para el costo del item
  let itemProfitValue = 0;
  
  // Variables globales para configuraciones de fees
  let currentFeeConfigs = [];
  let currentStoreId = null;
  let currentSucursalId = null;

// Cargar sucursales al cambiar la tienda
  if (tiendaSelect) {
    tiendaSelect.addEventListener("change", function () {
      if (sucursalSelect) {
        loadStoreOptions(this.value, sucursalSelect);
      }
    });
  } else {
    console.warn('⚠️ El elemento #tienda no existe en el DOM.');
  }

  // Cargar sucursales para la sección de orden
  if (orderMarketplace) {
    orderMarketplace.addEventListener("change", function () {
      if (orderStoreCod) {
        loadStoreOptions(this.value, orderStoreCod);
      }
    });
  } else {
    console.warn('⚠️ El elemento #orderMarketplace no existe en el DOM al cargar el script.');
  }

  // Función para cargar sucursales (reutilizable)
  function loadStoreOptions(id_store, targetSelect) {
    targetSelect.innerHTML = '<option value="">Cargando...</option>';
    
    fetch("getSucursales.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `id_store=${encodeURIComponent(id_store)}`,
    })
      .then((response) => response.text())
      .then((data) => {
        targetSelect.innerHTML = data;
      })
      .catch((error) => {
        console.error("Error cargando sucursales:", error);
      });
  }  // Actualizar comisión al cambiar sucursal (simplificado)
  if (sucursalSelect) {
    sucursalSelect.addEventListener("change", function () {
      // Solo actualizar cálculos básicos, sin fees
      setTimeout(() => {
        if (quantityInput.value && priceInput.value) {
          calcularTotalBasico();
        }
      }, 50);
    });
  } else {
    console.warn('⚠️ El elemento #sucursal no existe en el DOM.');
  }

  function calcularTotalBasico() {
    const quantity = parseFloat(quantityInput.value) || 0;
    const price = parseFloat(priceInput.value.replace("$", "")) || 0;
    const receivedShippingValue = parseFloat(receivedShipping.value) || 0;
    const taxInputValue = parseFloat(taxInput.value) || 0;
    const withheldTaxValue = parseFloat(withheldTaxInput.value) || 0;
    const incentives_value = parseFloat(incentivesInput.value) || 0;
    const international_fee_value = parseFloat(international_fee.value) || 0;
    const ad_fee_value = parseFloat(ad_fee.value) || 0;
    const other_fee_value = parseFloat(other_fee.value) || 0;

    if (quantity > 0 && price > 0) {
      const total = (price * quantity) + receivedShippingValue + taxInputValue - withheldTaxValue + incentives_value - international_fee_value - ad_fee_value - other_fee_value;
      
      if (refInput) {
        refInput.value = `$${total.toFixed(2)}`;
      }

      // Calcular profit si tenemos el costo
      const itemCostValue = itemProfitValue || 0;
      if (itemCostValue > 0) {
        const profit = total - (itemCostValue * quantity);
        if (itemProfit) {
          itemProfit.value = `$${profit.toFixed(2)}`;
        }

        // Calcular markup: (Item Profit / (Price Item + Shipping Received + Incentives Offered)) * 100
        if (markup) {
          const baseForMarkup = (price * quantity) + receivedShippingValue + incentives_value;
          if (baseForMarkup > 0) {
            const markupValue = ((profit / baseForMarkup) * 100);
            markup.value = `${markupValue.toFixed(2)}%`;
          } else {
            markup.value = "0.00%";
          }
        }

        // Calcular profit margin: (Item Profit / Item Cost) * 100
        if (profitMargin) {
          const totalItemCost = itemCostValue * quantity;
          if (totalItemCost > 0) {
            const profitMarginValue = ((profit / totalItemCost) * 100);
            profitMargin.value = `${profitMarginValue.toFixed(2)}%`;
          } else {
            profitMargin.value = "0.00%";
          }
        }
      }
    }
  }

  // Buscar item por UPC al perder foco
  if (upcInput) {
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
  } else {
    console.warn('⚠️ El elemento #upc no existe en el DOM.');
  }

  function limpiarCamposProducto() {
    itemNameInput.value = "";
    priceInput.value = "";
    brandItem.value = "";
    upcInput.value = "";
    quantityInput.value = "";
    refInput.value = "";
    receivedShipping.value = "";
    taxInput.value = "";
    withheldTaxInput.value = "";
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
    sucursalSelect.innerHTML = '<option value="">-- First select store --</option>';
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
    }    itemNameInput.value = data.item;
    itemCost.value = "$" + parseFloat(data.cost).toFixed(2);
    priceInput.value = "$";
    brandItem.value = data.brand || "";
    skuItem.value = data.sku || "";

    const quantityInput1 = document.getElementById("quantitySell");
    
    // DEBUG: verificar el costo del item
    console.log("Raw data.cost:", data.cost, typeof data.cost);
    const parsedCost = parseFloat(data.cost);
    console.log("Parsed cost:", parsedCost, typeof parsedCost);
    
    itemProfitValue = parsedCost || 0; // Asegurar que se establezca como número
    
    console.log("Item loaded - Cost set to:", itemProfitValue);
    console.log("Global itemProfitValue after setting:", window.itemProfitValue || itemProfitValue);

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
  // Escuchar cambios en cantidad y precio
  if (quantityInput) quantityInput.addEventListener("input", calcularTotalBasico);
  if (priceInput) priceInput.addEventListener("input", calcularTotalBasico);
  if (withheldTaxInput) withheldTaxInput.addEventListener("input", calcularTotalBasico);
  if (receivedShipping) receivedShipping.addEventListener("input", calcularTotalBasico);
  if (taxInput) taxInput.addEventListener("input", calcularTotalBasico);
  if (incentivesInput) incentivesInput.addEventListener("input", calcularTotalBasico);
  if (international_fee) international_fee.addEventListener("input", calcularTotalBasico);
  if (ad_fee) ad_fee.addEventListener("input", calcularTotalBasico);
  if (other_fee) other_fee.addEventListener("input", calcularTotalBasico);

  // Evitar envío del formulario con Enter
  if (form) {
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
      parseFloat(document.getElementById("receivedShipping").value) || 0;    // const payedShipping =
    //   parseFloat(document.getElementById("payedShipping").value) || 0;
    const tax = parseFloat(
      document.getElementById("taxItem").value.replace("$", "") || 0
    );
    const withheldTax = parseFloat(
      document.getElementById("withheld_tax").value.replace("$", "") || 0
    );

    const nombre = itemNameInput.value.trim();
    const upc = upcInput.value.trim();
    const sku = skuItem.value.trim();
    const quantity = parseFloat(quantityInput.value) || 0;
    const tienda = tiendaSelect?.options[tiendaSelect.selectedIndex]?.text || "";
    const sucursal = sucursalSelect?.options[sucursalSelect.selectedIndex]?.text || "";    const precioUnitario = parseFloat(priceInput.value.replace("$", "")) || 0;
    const brand = brandItem.value.trim();
    // Los fees de comisión y cargo fijo ahora se manejan a nivel de orden, no por item
    const comision = 0; // Se calculará a nivel de orden
    const incentives_value = parseFloat(incentivesInput.value) || 0;
    const international_fee_value = parseFloat(international_fee.value) || 0;
    const ad_fee_value = parseFloat(ad_fee.value) || 0;
    const other_fee_value = parseFloat(other_fee.value) || 0;
    const comisionFijo = 0; // Se calculará a nivel de orden
    const date = sellDateInput.value;    const total =
      precioUnitario * quantity +
      receivedShipping +
      tax -
      withheldTax -
      comision -
      comisionFijo +
      incentives_value -
      international_fee_value -
      ad_fee_value -
      other_fee_value;
    const itemProfitDisplayValue = parseFloat(itemProfit.value.replace("$", "")) || 0;
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
      <td class="quantity">${quantity}</td>
      <td class="received_shipping">${receivedShipping.toFixed(2)}</td>
      <td class="tax">${tax.toFixed(2)}</td>
      <td class="withheld_tax">${withheldTax.toFixed(2)}</td>
      <td class="precio_unitario">$${precioUnitario.toFixed(2)}</td>
      <td class="total_item">${total.toFixed(2)}</td>
      <td><button type="button" class="btn-action btn-delete"><i class="fas fa-trash-alt"></i></button></td>
      
      <!-- Campos ocultos para datos adicionales -->
      <td class="sku_item d-none">${sku}</td>
      <td class="item_name d-none">${nombre}</td>
      <td class="brand d-none">${brand}</td>
      <td class="fecha d-none">${date}</td>
      <td class="incentives_value d-none">${incentives_value}</td> 
      <td class="international_fee d-none">${international_fee_value}</td>
      <td class="ad_fee d-none">${ad_fee_value}</td>
      <td class="other_fee d-none">${other_fee_value}</td>
      <td class="item_profit d-none">${itemProfitDisplayValue}</td>
      <td class="markup d-none">${markupValue}</td>
      <td class="profit_margin d-none">${profitMarginValue}</td>
      <td class="comision d-none">0</td>
      <td class="cargo_fijo d-none">0</td>
    `;
      // Agregar evento al botón de eliminar
      fila.querySelector(".btn-delete").addEventListener("click", function () {
        fila.remove();

        // Si ya no hay filas en la tabla, habilitar el campo de fecha
        if (bodyTable.querySelectorAll("tr").length === 0) {
          sellDateInput.disabled = false;
          // Ocultar sección de orden si no hay items
          orderConfigSection.style.display = 'none';
        }
        
        // Actualizar totales después de eliminar fila
        updateOrderTotals();
      });
      
      // Agregar datos de store e sucursal como atributos para el resumen
      fila.setAttribute('data-store', tiendaSelect?.value || '');
      fila.setAttribute('data-sucursal', sucursalSelect?.value || '');
      
      bodyTable.appendChild(fila);

      // Mostrar sección de orden después del primer item
      orderConfigSection.style.display = 'block';

      // Actualizar totales después de agregar fila
      updateOrderTotals();

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
      upcInput.focus();
    } else {
      Swal.fire({
        icon: "error",
        title: "Error validating",
        text: "Verify that all data is complete and correct.",
      });
    }
  });
  } else {
    console.warn('⚠️ El elemento .form no existe en el DOM.');
  }

  // Primera implementación removida - se usa la versión mejorada al final del script

// Función para recrear campos completamente editables
  function recreateEditableFields() {
    console.log("Recreating fields to ensure editability...");
    
    // En la nueva estructura, los campos de comisión y cargo fijo no existen a nivel de item
    // Solo verificamos withheld tax que sí existe
    const withheldTaxField = document.getElementById("withheld_tax");
    
    // También agregar event listeners a withheld tax si existe
    if (withheldTaxField) {
      withheldTaxField.addEventListener('input', function() {
        console.log(`Withheld Tax value changed to: ${this.value} - triggering recalculation`);
        setTimeout(() => recalcularTotalesConValoresActuales(), 10);
      });
      
      withheldTaxField.addEventListener('change', function() {
        console.log(`Withheld Tax change event - final value: ${this.value}`);
        setTimeout(() => recalcularTotalesConValoresActuales(), 10);
      });
    }
  }
    // Función específica para recalcular cuando cambien Final Fee o Fixed Charge manualmente
  function recalcularTotalesConValoresActuales() {
    console.log("=== STARTING RECALCULATION ===");
    
    // Verificar que tenemos los elementos necesarios
    if (!quantityInput || !priceInput) {
      console.log("Missing required inputs for calculation");
      return;
    }
    
    // Obtener todos los valores actuales de los campos
    const quantity = parseFloat(quantityInput.value) || 0;
    const priceValue = priceInput.value.replace("$", "").trim();
    const price = parseFloat(priceValue) || 0;
    const ventaTotal = quantity * price;
    
    console.log("Basic values - Quantity:", quantity, "Price:", price, "Venta Total:", ventaTotal);
    
    if (quantity === 0 || price === 0) {
      console.log("Quantity or price is 0, skipping calculation");
      return;
    }
    
    // En la nueva estructura, no hay campos de comisión y cargo fijo a nivel de item
    // Estos se manejan a nivel de orden en el footer de la tabla
    const finalComision = 0; // Se maneja a nivel de orden
    const finalCargoFijo = 0; // Se maneja a nivel de orden
    
    // Otros valores
    const incentives_value = parseFloat(incentivesInput.value) || 0;
    const international_fee_value = parseFloat(international_fee.value) || 0;
    const ad_fee_value = parseFloat(ad_fee.value) || 0;
    const other_fee_value = parseFloat(other_fee.value) || 0;
    const receivedShippingValue = parseFloat(receivedShipping.value) || 0;
    const taxInputValue = parseFloat(taxInput.value) || 0;
    const withheldTaxValue = parseFloat(withheldTaxInput.value) || 0;
    
    console.log("Fee values - Final Fee:", finalComision, "Fixed Charge:", finalCargoFijo);
    console.log("Other values - Shipping:", receivedShippingValue, "Tax:", taxInputValue, "Withheld Tax:", withheldTaxValue);
    console.log("Item cost (itemProfitValue):", itemProfitValue);
    
    // Calcular el total
    const total = ventaTotal + receivedShippingValue + taxInputValue - withheldTaxValue - finalComision - finalCargoFijo + incentives_value - international_fee_value - ad_fee_value - other_fee_value;
    
    // Calcular profit usando el costo del item
    const itemCostValue = itemProfitValue || 0;
    console.log("Using item cost value:", itemCostValue);
    
    if (itemCostValue === 0) {
      console.log("⚠️ WARNING: Item cost is 0 - this may indicate an issue with item loading");
    }
    
    const profitTotal = total - (itemCostValue * quantity);
    
    console.log("Calculations - Total:", total, "Profit:", profitTotal);
    
    // Actualizar los campos de resultado
    if (refInput) {
      refInput.value = `$${total.toFixed(2)}`;
      console.log("✅ Total Order updated:", total.toFixed(2));
    } else {
      console.log("❌ refInput not found");
    }
    
    if (itemProfit) {
      itemProfit.value = `$${profitTotal.toFixed(2)}`;
      console.log("✅ Item Profit updated:", profitTotal.toFixed(2));
    } else {
      console.log("❌ itemProfit not found");
    }
      // FÓRMULA CORREGIDA PARA MARKUP: (Item Profit / (Price Item + Shipping Received + Incentives Offered)) * 100
    if (markup && quantity > 0 && price > 0) {
      const baseForMarkup = (price * quantity) + receivedShippingValue + incentives_value;
      console.log("Markup calculation - Base:", baseForMarkup, "Profit:", profitTotal);
      
      if (baseForMarkup > 0) {
        const markupValue = ((profitTotal / baseForMarkup) * 100);
        markup.value = `${markupValue.toFixed(2)}%`;
        console.log("✅ Markup updated:", markupValue.toFixed(2), "% (Profit/Revenue)");
      } else {
        markup.value = "0.00%";
        console.log("❌ Base for markup calculation is 0");
      }
    } else {
      console.log("❌ markup not found or invalid values");
    }
    
    // FÓRMULA CORREGIDA PARA PROFIT MARGIN: (Item Profit / Item Cost) * 100
    if (profitMargin && itemCostValue > 0 && quantity > 0) {
      const totalItemCost = itemCostValue * quantity;
      console.log("Profit Margin calculation - Total Item Cost:", totalItemCost, "Profit:", profitTotal);
      
      const profitMarginValue = ((profitTotal / totalItemCost) * 100);
      profitMargin.value = `${profitMarginValue.toFixed(2)}%`;
      console.log("✅ Profit Margin updated:", profitMarginValue.toFixed(2), "% (Profit/Cost)");
    } else {
      console.log("❌ profitMargin not found or invalid values (itemCost:", itemCostValue, "quantity:", quantity, ")");
      if (profitMargin) {
        profitMargin.value = "0.00%";
      }
    }
    
    console.log("=== RECALCULATION COMPLETED ===");
  }
  
  // EVENT DELEGATION para Withheld Tax (los otros campos no existen en la nueva estructura)
  document.addEventListener('input', function(e) {
    if (e.target.id === 'withheld_tax') {
      console.log(`🔥 FIELD CHANGED: ${e.target.id} = ${e.target.value}`);
      setTimeout(() => {
        console.log("→ Triggering recalculation...");
        recalcularTotalesConValoresActuales();
      }, 50);
    }
  });
  
  document.addEventListener('change', function(e) {
    if (e.target.id === 'withheld_tax') {
      console.log(`🔥 FIELD CHANGED (change): ${e.target.id} = ${e.target.value}`);
      setTimeout(() => {
        console.log("→ Triggering recalculation from change event...");
        recalcularTotalesConValoresActuales();
      }, 50);
    }
  });
  
  // DEBUG BUTTON
  setTimeout(() => {
    const debugButton = document.getElementById('debug-calc');
    if (debugButton) {
      debugButton.addEventListener('click', function() {
        console.log('🔧 DEBUG BUTTON CLICKED - Manual recalculation');
        recalcularTotalesConValoresActuales();
      });
      console.log('✅ Debug button listener added');
    }
  }, 100);
  
  console.log('✅ Event delegation setup completed');

  // === NUEVAS FUNCIONALIDADES PARA MANEJO DE TOTALES DE ORDEN ===

  // Función para cargar configuraciones de fees
  async function loadFeeConfigs(sucursalId) {
    try {
      const response = await fetch(`getFeeConfigs.php?id_sucursal=${sucursalId}`);
      const data = await response.json();
      
      if (data.success) {
        currentFeeConfigs = data.configs;
        console.log('Fee configs loaded:', currentFeeConfigs);
        return currentFeeConfigs;
      } else {
        console.error('Error loading fee configs:', data.message);
        return [];
      }
    } catch (error) {
      console.error('Error fetching fee configs:', error);
      return [];
    }
  }

  // Función para cargar sucursales basada en la tienda seleccionada
  function loadSucursales(storeId, selectElement) {
    const formData = new FormData();
    formData.append('id_store', storeId);
    
    fetch(`getSucursales.php`, {
      method: 'POST',
      body: formData
    })
      .then(response => response.text())
      .then(data => {
        try {
          const jsonData = JSON.parse(data);
          selectElement.innerHTML = '<option value="">--Select a store code--</option>';
          
          // Si getSucursales.php devuelve el formato actual, adaptamos
          if (typeof jsonData === 'object') {
            Object.keys(jsonData).forEach(id => {
              const sucursal = jsonData[id];
              const option = document.createElement('option');
              option.value = id;
              option.textContent = sucursal.code_sucursal;
              selectElement.appendChild(option);
            });
          }
        } catch (e) {
          // Si no es JSON válido, parseamos el HTML response
          console.log('Response is not JSON, parsing as HTML options');
          const tempDiv = document.createElement('div');
          tempDiv.innerHTML = data;
          const options = tempDiv.querySelectorAll('option');
          
          selectElement.innerHTML = '<option value="">--Select a store code--</option>';
          options.forEach(option => {
            selectElement.appendChild(option.cloneNode(true));
          });
        }
      })
      .catch(error => {
        console.error('Error fetching sucursales:', error);
        selectElement.innerHTML = '<option value="">Error loading store codes</option>';
      });
  }

  // Función para calcular fees basado en el total de items
  function calculateOrderFees(totalItems) {
    if (currentFeeConfigs.length === 0) {
      return { finalFee: 0, fixedCharge: 0 };
    }

    // Buscar la configuración apropiada basada en sales_less_than
    const config = currentFeeConfigs.find(c => totalItems < c.sales_less_than) || 
                   currentFeeConfigs[currentFeeConfigs.length - 1];

    return {
      finalFee: config.comision,
      fixedCharge: config.cargo_fijo
    };
  }

  // Función para calcular y actualizar totales de la orden
  function updateOrderTotals() {
    const bodyTable = document.getElementById('bodyTable');
    const tableTotals = document.getElementById('tableTotals');
    const totalAllItemsSpan = document.getElementById('totalAllItems');
    const footerFinalFeeInput = document.getElementById('footerFinalFee');
    const footerFixedChargeInput = document.getElementById('footerFixedCharge');
    const finalOrderTotalSpan = document.getElementById('finalOrderTotal');
    const orderConfigSection = document.getElementById('orderConfigSection');

    console.log('Updating order totals...');

    // Calcular total de todos los items
    let totalItems = 0;
    const rows = bodyTable.querySelectorAll('tr');
    
    console.log('Found rows:', rows.length);
    
    rows.forEach(row => {
      const totalItemCell = row.querySelector('.total_item');
      if (totalItemCell) {
        const totalItemValue = parseFloat(totalItemCell.textContent) || 0;
        totalItems += totalItemValue;
        console.log('Row total:', totalItemValue);
      }
    });

    console.log('Total items calculated:', totalItems);

    // Mostrar/ocultar la sección de totales y configuración de orden
    if (rows.length > 0) {
      console.log('Showing totals section and order config');
      
      // Mostrar sección de configuración de orden
      if (orderConfigSection) {
        orderConfigSection.style.display = 'block';
      }
      
      // Mostrar tfoot de totales
      tableTotals.style.display = 'table-footer-group';
      
      // Actualizar total de items
      if (totalAllItemsSpan) {
        totalAllItemsSpan.textContent = `$${totalItems.toFixed(2)}`;
        console.log('Updated total items display');
      }

      // Calcular fees automáticamente si hay configuraciones
      const calculatedFees = calculateOrderFees(totalItems);
      
      // Solo actualizar los campos si están vacíos
      if (footerFinalFeeInput && (!footerFinalFeeInput.value || parseFloat(footerFinalFeeInput.value) === 0)) {
        footerFinalFeeInput.value = calculatedFees.finalFee.toFixed(2);
      }
      
      if (footerFixedChargeInput && (!footerFixedChargeInput.value || parseFloat(footerFixedChargeInput.value) === 0)) {
        footerFixedChargeInput.value = calculatedFees.fixedCharge.toFixed(2);
      }

      // Calcular total final
      updateFinalOrderTotal();
    } else {
      console.log('Hiding totals section and order config');
      tableTotals.style.display = 'none';
      
      // Ocultar sección de configuración de orden
      if (orderConfigSection) {
        orderConfigSection.style.display = 'none';
      }
    }
  }

  // Función para actualizar el total final de la orden
  function updateFinalOrderTotal() {
    const totalAllItemsSpan = document.getElementById('totalAllItems');
    const footerFinalFeeInput = document.getElementById('footerFinalFee');
    const footerFixedChargeInput = document.getElementById('footerFixedCharge');
    const finalOrderTotalSpan = document.getElementById('finalOrderTotal');

    if (totalAllItemsSpan && footerFinalFeeInput && footerFixedChargeInput && finalOrderTotalSpan) {
      const totalItems = parseFloat(totalAllItemsSpan.textContent.replace('$', '')) || 0;
      const finalFee = parseFloat(footerFinalFeeInput.value) || 0;
      const fixedCharge = parseFloat(footerFixedChargeInput.value) || 0;

      const finalTotal = totalItems - finalFee - fixedCharge;
      finalOrderTotalSpan.textContent = `$${finalTotal.toFixed(2)}`;
      
      console.log('Updated final total:', finalTotal);
    }
  }

  // Event listeners para los campos de fees de la orden
  document.addEventListener('input', function(e) {
    if (e.target.id === 'footerFinalFee' || e.target.id === 'footerFixedCharge' || 
        e.target.id === 'orderFinalFee' || e.target.id === 'orderFixedCharge') {
      updateFinalOrderTotal();
    }
  });

  // Listener para la sección de orden (usando delegación de eventos)
  document.addEventListener('change', function(e) {
    if (e.target.id === 'orderStoreCod') {
      currentSucursalId = e.target.value;
      const orderMarketplace = document.getElementById('orderMarketplace');
      currentStoreId = orderMarketplace ? orderMarketplace.value : '';

      if (currentSucursalId) {
        loadFeeConfigs(currentSucursalId).then(() => {
          updateOrderTotals();
        });
      }
    }
    
    if (e.target.id === 'orderMarketplace') {
      const storeId = e.target.value;
      const orderStoreCod = document.getElementById('orderStoreCod');
      
      if (orderStoreCod) {
        // Limpiar opciones anteriores
        orderStoreCod.innerHTML = '<option value="">--First select a store--</option>';
        
        if (storeId) {
          // Cargar sucursales para la tienda seleccionada
          loadSucursales(storeId, orderStoreCod);
        }
      }
    }
  });

  // Función para recrear campos editables (agregar aquí si no existe)
  function recreateEditableFields() {
    forceFieldsEditable();
  }

  // Observer para detectar cambios en la tabla y actualizar totales
  const tableObserver = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      if (mutation.type === 'childList') {
        updateOrderTotals();
      }
    });
  });

  // Observar cambios en el tbody
  const bodyTableForObserver = document.getElementById('bodyTable');
  if (bodyTableForObserver) {
    tableObserver.observe(bodyTableForObserver, { childList: true, subtree: true });
  }

  // Función saveSellButton con modal para sell order manual y guardado completo
  const saveSellButtonSecond = document.getElementById('saveSellButton');
  if (saveSellButtonSecond && !saveSellButtonSecond.hasAttribute('data-listener-added')) {
    saveSellButtonSecond.setAttribute('data-listener-added', 'true');
    
    saveSellButtonSecond.addEventListener('click', function(e) {
      e.preventDefault();
      
      const tableRows = document.querySelectorAll('#bodyTable tr');
      
      if (tableRows.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Sin datos',
          text: 'No hay ventas para guardar.',
        });
        return;
      }

      // Mostrar modal para ingresar sell order manualmente
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

          // Obtener datos centralizados de la orden
          const orderMarketplace = document.getElementById('orderMarketplace');
          const orderStoreCod = document.getElementById('orderStoreCod');
          
          const orderStoreId = parseInt(orderMarketplace?.value || '0');
          const orderSucursalId = parseInt(orderStoreCod?.value || '0');
          
          if (orderStoreId === 0 || orderSucursalId === 0) {
            Swal.fire({
              icon: 'warning',
              title: 'Order Configuration Required',
              text: 'Please select marketplace and store code before saving.',
            });
            return;
          }

          // Recopilar datos de ventas desde la tabla
          const ventas = [];
          tableRows.forEach((row) => {
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
            const withheld_tax = parseFloat(
              row.querySelector(".withheld_tax")?.textContent.replace("$", "").trim() ||
                "0"
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
            const date = row.querySelector(".fecha")?.textContent || sellDateInput.value;
            const sku = row.querySelector(".sku_item")?.textContent || "";

            ventas.push({
              upc_item,
              quantity,
              received_shipping,
              tax,
              withheld_tax,
              id_store: orderStoreId,  // Usar el valor centralizado
              id_sucursal: orderSucursalId,  // Usar el valor centralizado
              comision: 0,  // Se calculará desde el summary
              cargo_fijo: 0,  // Se calculará desde el summary
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
            });
          });

          // Recopilar datos del resumen de la orden
          const totalAllItemsSpan = document.getElementById('totalAllItems');
          const footerFinalFeeInput = document.getElementById('footerFinalFee');
          const footerFixedChargeInput = document.getElementById('footerFixedCharge');
          const finalOrderTotalSpan = document.getElementById('finalOrderTotal');

          const summary = {
            final_fee: parseFloat(footerFinalFeeInput?.value || '0') || 0,
            fixed_charge: parseFloat(footerFixedChargeInput?.value || '0') || 0,
            total_items: parseFloat(totalAllItemsSpan?.textContent.replace('$', '') || '0') || 0,
            final_total: parseFloat(finalOrderTotalSpan?.textContent.replace('$', '') || '0') || 0
          };

          // Datos completos para enviar
          const dataToSend = {
            ventas: ventas,
            sell_order: manualSellOrder,
            summary: summary
          };

          try {
            const response = await fetch("saveSell.php", {
              method: "POST",
              headers: {
                "Content-Type": "application/json",
              },
              body: JSON.stringify(dataToSend),
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
                  text: `${ventas.length} sales registered with order ${manualSellOrder}.`,
                });
              }

              // Limpiar tabla
              const bodyTable = document.querySelector("#bodyTable");
              if (bodyTable) {
                bodyTable.innerHTML = "";
              }

              // Actualizar totales después de limpiar
              updateOrderTotals();

              // Habilitar el campo de fecha
              const sellDateInput = document.getElementById("sellDate");
              if (sellDateInput) {
                sellDateInput.disabled = false;
              }

              // Limpiar formulario principal
              const form = document.querySelector('.form');
              if (form) {
                form.reset();
              }
              
              // Restaurar fecha actual
              if (sellDateInput) {
                sellDateInput.value = new Date().toISOString().split('T')[0];
              }
              
              // Limpiar sección de configuración de orden
              const orderConfigSection = document.getElementById('orderConfigSection');
              if (orderConfigSection) {
                orderConfigSection.style.display = 'none';
              }
              
            } else {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: result.message || "Error saving sale.",
              });
            }
          } catch (error) {
            console.error("Error saving sale:", error);
            Swal.fire({
              icon: "error",
              title: "Error",
              text: "Error sending sales data.",
            });
          }
        }
      });
    });
  }

});
