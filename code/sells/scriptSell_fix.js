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
