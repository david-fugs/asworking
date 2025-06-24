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
  const quantitySell = document.getElementById("quantitySell");  const today = new Date().toISOString().split("T")[0];
  const taxInput = document.getElementById("taxItem");
  const withheldTaxInput = document.getElementById("withheld_tax");
  const cargoFijoInput = document.getElementById("cargo_fijo");
  const incentivesInput = document.getElementById("incentives");
  const international_fee = document.getElementById("international_fee");
  const ad_fee = document.getElementById("ad_fee");
  const other_fee = document.getElementById("other_fee");
  const itemProfit = document.getElementById("itemProfit");  const markup = document.getElementById("markup");
  const profitMargin = document.getElementById("profitMargin");
  const itemCost = document.getElementById("itemCost");  const skuItem = document.getElementById("sku");
  sellDateInput.value = today;

  // Variable global para el costo del item
  let itemProfitValue = 0;
  // Función más agresiva para forzar la editabilidad
  function forceFieldsEditable() {
    const comisionField = document.getElementById("comisionItem");
    const cargoFijoField = document.getElementById("cargo_fijo");
    
    console.log("Forcing fields to be editable...");
    
    [comisionField, cargoFijoField].forEach((field, index) => {
      if (field) {
        const fieldName = index === 0 ? "Final Fee" : "Fixed Charge";
        
        // Remover TODOS los atributos que puedan bloquear
        field.readOnly = false;
        field.disabled = false;
        field.removeAttribute('readonly');
        field.removeAttribute('disabled');
        field.removeAttribute('data-readonly');
        field.removeAttribute('tabindex');
        
        // FORZAR estilos más agresivamente
        field.style.setProperty('background-color', '', 'important');
        field.style.setProperty('cursor', 'text', 'important');
        field.style.setProperty('pointer-events', 'auto', 'important');
        field.style.setProperty('user-select', 'text', 'important');
        field.style.setProperty('opacity', '1', 'important');
        field.style.setProperty('filter', 'none', 'important');
        field.style.setProperty('color', '#495057', 'important');
        field.style.setProperty('border', '1px solid #ced4da', 'important');
        
        // Remover clases que puedan interferir
        field.classList.remove('disabled', 'readonly', 'form-control-plaintext');
        field.classList.add('form-control');
        
        // Remover todos los event listeners que puedan estar bloqueando
        const newField = field.cloneNode(true);
        newField.value = field.value; // Mantener el valor
        field.parentNode.replaceChild(newField, field);
        
        // Re-obtener la referencia después del reemplazo
        const updatedField = document.getElementById(field.id);
        
        // Verificar si realmente es editable
        const isEditable = !updatedField.readOnly && !updatedField.disabled;
        console.log(`${fieldName} is editable: ${isEditable}`);
        console.log(`${fieldName} readOnly: ${updatedField.readOnly}`);
        console.log(`${fieldName} disabled: ${updatedField.disabled}`);
        
        // Agregar event listeners solo una vez
        if (!updatedField.hasAttribute('data-listeners-added')) {
          updatedField.setAttribute('data-listeners-added', 'true');
          
          updatedField.addEventListener('focus', function() {
            console.log(`${fieldName} focused - editable: ${!this.readOnly && !this.disabled}`);
            this.style.setProperty('background-color', '#fff', 'important');
          });
          
          updatedField.addEventListener('click', function() {
            console.log(`${fieldName} clicked - editable: ${!this.readOnly && !this.disabled}`);
            this.readOnly = false;
            this.disabled = false;
            this.focus();
          });
            updatedField.addEventListener('input', function() {
            console.log(`${fieldName} input changed - value: ${this.value}`);
            setTimeout(() => recalcularTotalesConValoresActuales(), 10);
          });
          
          updatedField.addEventListener('change', function() {
            console.log(`${fieldName} change event - value: ${this.value}`);
            setTimeout(() => recalcularTotalesConValoresActuales(), 10);
          });
          
          updatedField.addEventListener('blur', function() {
            console.log(`${fieldName} blur event - value: ${this.value}`);
            setTimeout(() => recalcularTotalesConValoresActuales(), 10);
          });
          
          updatedField.addEventListener('keydown', function(e) {
            console.log(`${fieldName} keydown: ${e.key}`);
            // Asegurar que las teclas funcionen
            if (e.key === 'Backspace' || e.key === 'Delete' || /\d/.test(e.key) || e.key === '.') {
              e.stopPropagation();
            }
          });
        }
      }
    });
  }// Asegurar que los campos Final Fee y Fixed Charge siempre sean editables
  function ensureFieldsEditable() {
    const comisionField = document.getElementById("comisionItem");
    const cargoFijoField = document.getElementById("cargo_fijo");
    
    if (comisionField) {
      // Forzar la eliminación de todos los atributos que bloqueen la edición
      comisionField.readOnly = false;
      comisionField.disabled = false;
      comisionField.removeAttribute('readonly');
      comisionField.removeAttribute('disabled');
      comisionField.removeAttribute('data-readonly');
      
      // Forzar estilos editables
      comisionField.style.backgroundColor = '';
      comisionField.style.cursor = 'text';
      comisionField.style.pointerEvents = 'auto';
      comisionField.style.userSelect = 'text';
      
      // Asegurar que tenga la clase correcta
      if (!comisionField.classList.contains('form-control')) {
        comisionField.classList.add('form-control');
      }
      
      console.log("Final Fee field forced to be editable");
    }
    
    if (cargoFijoField) {
      // Forzar la eliminación de todos los atributos que bloqueen la edición
      cargoFijoField.readOnly = false;
      cargoFijoField.disabled = false;
      cargoFijoField.removeAttribute('readonly');
      cargoFijoField.removeAttribute('disabled');
      cargoFijoField.removeAttribute('data-readonly');
      
      // Forzar estilos editables
      cargoFijoField.style.backgroundColor = '';
      cargoFijoField.style.cursor = 'text';
      cargoFijoField.style.pointerEvents = 'auto';
      cargoFijoField.style.userSelect = 'text';
      
      // Asegurar que tenga la clase correcta
      if (!cargoFijoField.classList.contains('form-control')) {
        cargoFijoField.classList.add('form-control');
      }
      
      console.log("Fixed Charge field forced to be editable");
    }
  }  // Ejecutar al cargar la página
  ensureFieldsEditable();
  forceFieldsEditable();
  
  // Verificación periódica MENOS frecuente - solo cuando sea necesario
  let forceCount = 0;
  const maxForces = 5; // Limitar a 5 intentos
  
  const intervalId = setInterval(() => {
    if (forceCount < maxForces) {
      const comisionField = document.getElementById("comisionItem");
      const cargoFijoField = document.getElementById("cargo_fijo");
      
      // Solo forzar si realmente están bloqueados
      if ((comisionField && (comisionField.readOnly || comisionField.disabled)) ||
          (cargoFijoField && (cargoFijoField.readOnly || cargoFijoField.disabled))) {
        forceFieldsEditable();
        forceCount++;
      }
    } else {
      // Después de 5 intentos, parar el intervalo
      clearInterval(intervalId);
      console.log("Stopped forcing fields - maximum attempts reached");
    }  }, 3000); // Cada 3 segundos en lugar de 2
  
  // Agregar event listeners adicionales para asegurar que los campos permanezcan editables
  const comisionField = document.getElementById("comisionItem");
  const cargoFijoField = document.getElementById("cargo_fijo");
  
  if (comisionField) {
    comisionField.addEventListener('focus', forceFieldsEditable);
    comisionField.addEventListener('click', forceFieldsEditable);
  }
  
  if (cargoFijoField) {
    cargoFijoField.addEventListener('focus', forceFieldsEditable);
    cargoFijoField.addEventListener('click', forceFieldsEditable);
  }

// Cargar sucursales al cambiar la tienda
  tiendaSelect.addEventListener("change", function () {
    const id_store = this.value;
    sucursalSelect.innerHTML = '<option value="">Cargando...</option>';
    
    // Reiniciar y asegurar que los campos sean editables
    const comisionField = document.getElementById("comisionItem");
    const cargoFijoField = document.getElementById("cargo_fijo");
    
    comisionField.value = "";    cargoFijoField.value = "";
    
    // Asegurar que los campos siempre sean editables
    comisionField.readOnly = false;
    comisionField.disabled = false;
    cargoFijoField.readOnly = false;
    cargoFijoField.disabled = false;
    
    // Llamar función adicional para asegurar editabilidad
    ensureFieldsEditable();

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
  });  // Actualizar comisión al cambiar sucursal
  sucursalSelect.addEventListener("change", function () {
    const selectedOption = this.options[this.selectedIndex];
    const configsJson = selectedOption.getAttribute("data-configs");

    console.log("Store Cod changed - Starting field update process");

    if (configsJson) {
      const configs = JSON.parse(configsJson);
      if (configs.length > 0) {
        const primeraConfig = configs[0];
        
        const comisionField = document.getElementById("comisionItem");
        const cargoFijoField = document.getElementById("cargo_fijo");
        
        console.log("Updating field values...");
        console.log("Final Fee value:", primeraConfig.comision);
        console.log("Fixed Charge value:", primeraConfig.cargo_fijo);
        
        // Actualizar valores
        comisionField.value = primeraConfig.comision;
        cargoFijoField.value = primeraConfig.cargo_fijo;
        
        // Hacer campos editables de forma simple y directa
        console.log("Making fields editable immediately...");
        
        comisionField.readOnly = false;
        comisionField.disabled = false;
        comisionField.removeAttribute('readonly');
        comisionField.removeAttribute('disabled');
        
        cargoFijoField.readOnly = false;
        cargoFijoField.disabled = false;
        cargoFijoField.removeAttribute('readonly');
        cargoFijoField.removeAttribute('disabled');
        
        // Verificar inmediatamente
        console.log("Final Fee editable:", !comisionField.readOnly && !comisionField.disabled);
        console.log("Fixed Charge editable:", !cargoFijoField.readOnly && !cargoFijoField.disabled);
          // Una sola llamada a forceFieldsEditable con un pequeño delay
        setTimeout(() => {
          console.log("Applying force fields editable after Store Cod change");
          recreateEditableFields(); // Usar la nueva función que recrea los campos
        }, 100);
        
        console.log("Config applied - sales less than:", primeraConfig.sales_less_than);
      } else {
        console.log("No configs found, setting defaults");
        const comisionField = document.getElementById("comisionItem");
        const cargoFijoField = document.getElementById("cargo_fijo");
        
        comisionField.value = 0;
        cargoFijoField.value = 0;
        
        comisionField.readOnly = false;
        comisionField.disabled = false;
        cargoFijoField.readOnly = false;
        cargoFijoField.disabled = false;
        
        setTimeout(() => recreateEditableFields(), 100);
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
    receivedShipping.value = "";    // payedShipping.value = "";
    taxInput.value = "";
    withheldTaxInput.value = "";
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
  function actualizarComisionYCargo() {
    console.log("=== STARTING actualizarComisionYCargo ===");
    console.log("itemProfitValue:", itemProfitValue);
    
    const quantity = parseFloat(quantityInput.value) || 0;
    const price = parseFloat(priceInput.value.replace("$", "")) || 0;
    const ventaTotal = quantity * price;
    
    console.log("Basic calculations - Quantity:", quantity, "Price:", price, "Venta total:", ventaTotal);

    if (quantity === 0 || price === 0) {
      console.log("Quantity or price is 0, skipping calculation");
      return;
    }

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
      
      console.log("Config values - Comision:", comision, "Cargo fijo:", cargo_fijo);
      
      // Obtener valores actuales de los campos (incluso si fueron recreados)
      const currentComisionField = document.getElementById("comisionItem");
      const currentCargoFijoField = document.getElementById("cargo_fijo");
      
      // Solo aplicar valores de configuración si los campos están vacíos o no existen
      if (!currentComisionField || currentComisionField.value === "" || currentComisionField.value === "0") {
        if (currentComisionField) currentComisionField.value = comision.toFixed(2);
        if (comisionInput) comisionInput.value = comision.toFixed(2);
      }
      
      if (!currentCargoFijoField || currentCargoFijoField.value === "" || currentCargoFijoField.value === "0") {
        if (currentCargoFijoField) currentCargoFijoField.value = cargo_fijo.toFixed(2);
      }
      
      console.log("Fields updated with config values");
      
      // FORZAR que los campos permanezcan editables después de actualizar valores
      setTimeout(() => {
        console.log("Forcing fields editable after config update");
        forceFieldsEditable();
      }, 0);

      // Llamar a la función de recálculo específica para obtener los valores más actuales
      setTimeout(() => {
        console.log("Calling specific recalculation function");
        recalcularTotalesConValoresActuales();
      }, 50);
      
    }
    console.log("=== COMPLETED actualizarComisionYCargo ===");
  }// Escuchar cambios en cantidad y precio
  quantityInput.addEventListener("input", actualizarComisionYCargo);
  priceInput.addEventListener("input", actualizarComisionYCargo);
  sucursalSelect.addEventListener("change", actualizarComisionYCargo);
  withheldTaxInput.addEventListener("input", actualizarComisionYCargo);
  receivedShipping.addEventListener("input", actualizarComisionYCargo);
  taxInput.addEventListener("input", actualizarComisionYCargo);
  incentivesInput.addEventListener("input", actualizarComisionYCargo);
  international_fee.addEventListener("input", actualizarComisionYCargo);
  ad_fee.addEventListener("input", actualizarComisionYCargo);
  other_fee.addEventListener("input", actualizarComisionYCargo);
  // Agregar event listeners a los campos de comisión y cargo fijo originales
  // (estos se mantienen hasta que los campos sean recreados)
  comisionInput.addEventListener("input", function() {
    console.log("Original Final Fee input changed:", this.value);
    recalcularTotalesConValoresActuales();
  });
  
  cargoFijoInput.addEventListener("input", function() {
    console.log("Original Fixed Charge input changed:", this.value);
    recalcularTotalesConValoresActuales();
  });
  
  cargoFijoInput.addEventListener("change", function() {
    console.log("Original Fixed Charge change event:", this.value);
    recalcularTotalesConValoresActuales();
  });
  
  comisionInput.addEventListener("change", function() {
    console.log("Original Final Fee change event:", this.value);
    recalcularTotalesConValoresActuales();
  });
  
  // Agregar también keyup para los campos originales
  comisionInput.addEventListener("keyup", function() {
    console.log("Original Final Fee keyup event:", this.value);
    recalcularTotalesConValoresActuales();
  });
  
  cargoFijoInput.addEventListener("keyup", function() {
    console.log("Original Fixed Charge keyup event:", this.value);
    recalcularTotalesConValoresActuales();
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
    const tienda = tiendaSelect.options[tiendaSelect.selectedIndex]?.text || "";
    const sucursal =
      sucursalSelect.options[sucursalSelect.selectedIndex]?.text || "";    const precioUnitario = parseFloat(priceInput.value.replace("$", "")) || 0;
    const brand = brandItem.value.trim();
    const comision =
      parseFloat(document.getElementById("comisionItem").value) || 0;
    const incentives_value = parseFloat(incentivesInput.value) || 0;
    const international_fee_value = parseFloat(international_fee.value) || 0;
    const ad_fee_value = parseFloat(ad_fee.value) || 0;
    const other_fee_value = parseFloat(other_fee.value) || 0;
    const comisionFijo =
      parseFloat(document.getElementById("cargo_fijo").value) || 0;
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
      <td class="sku_item d-none">${sku}</td>
      <td class="quantity">${quantity}</td>
      <td class="id_store" data-id="${tiendaSelect.value}">${tienda}</td>
      <td class="id_sucursal" data-id="${sucursalSelect.value}">${sucursal}</td>
      <td class="comision">$${comision.toFixed(2)}</td>
      <td class="cargo_fijo">$${comisionFijo.toFixed(2)}</td>      <td class="received_shipping">${receivedShipping.toFixed(2)}</td>
      <td class="">${tax.toFixed(2)}</td>
      <td class="withheld_tax">${withheldTax.toFixed(2)}</td>
      <td class="precio_unitario">$${precioUnitario.toFixed(2)}</td>
      <td class="total_item">${total.toFixed(2)}</td>
      <td class="incentives_value d-none">${incentives_value}</td> 
      <td class="international_fee d-none">${international_fee_value}</td>
      <td class="ad_fee d-none">${ad_fee_value}</td>
      <td class="other_fee d-none">${other_fee_value}</td>
      <td class="item_profit d-none">${itemProfitDisplayValue}</td>
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
            );            const tax = parseFloat(
              row.querySelector(".tax")?.textContent.replace("$", "").trim() ||
                "0"
            );
            const withheld_tax = parseFloat(
              row.querySelector(".withheld_tax")?.textContent.replace("$", "").trim() ||
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
            const sku = row.querySelector(".sku_item")?.textContent || "";            ventas.push({
              upc_item,
              quantity,
              received_shipping,
              tax,
              withheld_tax,
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
          }        }
      });
    });

// Función para recrear campos completamente editables
  function recreateEditableFields() {
    console.log("Recreating fields to ensure editability...");
    
    const comisionField = document.getElementById("comisionItem");
    const cargoFijoField = document.getElementById("cargo_fijo");
    const withheldTaxField = document.getElementById("withheld_tax");
    
    // Procesar Final Fee y Fixed Charge
    [comisionField, cargoFijoField].forEach((field, index) => {
      if (field) {
        const fieldName = index === 0 ? "Final Fee" : "Fixed Charge";
        const currentValue = field.value;
        
        // Crear un nuevo input completamente
        const newInput = document.createElement('input');
        newInput.type = 'number';
        newInput.name = field.name;
        newInput.id = field.id;
        newInput.className = 'form-control';
        newInput.step = '0.01';
        newInput.min = '0';
        newInput.value = currentValue;
        
        // Estilos para asegurar que sea editable
        newInput.style.backgroundColor = '#fff';
        newInput.style.border = '1px solid #ced4da';
        newInput.style.color = '#495057';
        newInput.style.cursor = 'text';
        
        // Reemplazar el campo existente
        field.parentNode.replaceChild(newInput, field);
        console.log(`${fieldName} recreated with value: ${currentValue}`);
        
        // Agregar event listeners para recálculos automáticos
        newInput.addEventListener('input', function() {
          console.log(`${fieldName} value changed to: ${this.value} - triggering recalculation`);
          setTimeout(() => recalcularTotalesConValoresActuales(), 10);
        });
        
        newInput.addEventListener('change', function() {
          console.log(`${fieldName} change event - final value: ${this.value}`);
          setTimeout(() => recalcularTotalesConValoresActuales(), 10);
        });
        
        newInput.addEventListener('keyup', function() {
          console.log(`${fieldName} keyup event - value: ${this.value}`);
          setTimeout(() => recalcularTotalesConValoresActuales(), 10);
        });
        
        newInput.addEventListener('blur', function() {
          console.log(`${fieldName} lost focus - final value: ${this.value}`);
          setTimeout(() => recalcularTotalesConValoresActuales(), 10);
        });
        
        // Event listeners para debugging
        newInput.addEventListener('focus', function() {
          console.log(`${fieldName} focused - can edit: true`);
        });
      }
    });
    
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
    
    // Obtener valores actuales de los campos (incluso recreados)
    const currentComisionField = document.getElementById("comisionItem");
    const currentCargoFijoField = document.getElementById("cargo_fijo");
    
    const finalComision = currentComisionField ? (parseFloat(currentComisionField.value) || 0) : 0;
    const finalCargoFijo = currentCargoFijoField ? (parseFloat(currentCargoFijoField.value) || 0) : 0;
    
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
    
    if (markup && quantity > 0 && price > 0) {
      const markupValue = ((profitTotal / (price * quantity)) * 100);
      markup.value = `${markupValue.toFixed(2)}%`;
      console.log("✅ Markup updated:", markupValue.toFixed(2), "%");
    } else {
      console.log("❌ markup not found or invalid values");
    }
    
    if (profitMargin && (price * quantity + receivedShippingValue) > 0) {
      const profitMarginValue = ((profitTotal / (price * quantity + receivedShippingValue)) * 100);
      profitMargin.value = `${profitMarginValue.toFixed(2)}%`;
      console.log("✅ Profit Margin updated:", profitMarginValue.toFixed(2), "%");
    } else {
      console.log("❌ profitMargin not found or invalid values");
    }
    
    console.log("=== RECALCULATION COMPLETED ===");
  }
  
  // SIMPLE EVENT DELEGATION para Final Fee, Fixed Charge y Withheld Tax
  document.addEventListener('input', function(e) {
    if (e.target.id === 'comisionItem' || e.target.id === 'cargo_fijo' || e.target.id === 'withheld_tax') {
      console.log(`🔥 FIELD CHANGED: ${e.target.id} = ${e.target.value}`);
      setTimeout(() => {
        console.log("→ Triggering recalculation...");
        recalcularTotalesConValoresActuales();
      }, 50);
    }
  });
  
  document.addEventListener('change', function(e) {
    if (e.target.id === 'comisionItem' || e.target.id === 'cargo_fijo' || e.target.id === 'withheld_tax') {
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

});
