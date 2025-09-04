<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test UPC Modal Functionality</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Test UPC Modal Functionality</h2>
        <p>This page tests the UPC modal functionality without requiring authentication.</p>
        
        <div class="form-group">
            <label for="test_upc">Enter a UPC to test:</label>
            <input type="text" id="test_upc" class="form-control" placeholder="Enter UPC code" style="max-width: 300px;" value="<?php echo isset($_GET['upc']) ? htmlspecialchars($_GET['upc']) : ''; ?>">
            <button type="button" id="test_btn" class="btn btn-primary mt-2">Test UPC Check</button>
        </div>
        
        <div id="result" class="mt-4"></div>
    </div>

    <script>
        $('#test_btn').on('click', function() {
            var upc = $('#test_upc').val().toUpperCase();
            if (upc.trim() === '') {
                alert('Please enter a UPC');
                return;
            }
            
            $.ajax({
                url: 'verificar_upc.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    upc_item: upc
                },
                success: function(data) {
                    console.log('Respuesta del servidor:', data);
                    if (data.status === 'existe') {
                        // Show modal similar to the main page
                        var tableHtml = '<div style="overflow:auto;max-width:100%;"><table class="table table-bordered"><thead><tr><th>Select</th><th>Brand</th><th>Item</th><th>SKU</th><th>REF</th><th>COST</th><th>Batch</th><th>Quantity</th></tr></thead><tbody>';
                        data.items.forEach(function(item, idx) {
                            var qty = item.quantity_inventory || 0;
                            var costDisplay = (typeof item.cost_item !== 'undefined' && item.cost_item !== null && item.cost_item !== '') ? '$' + parseFloat(item.cost_item).toFixed(2) : '';
                            var refDisplay = item.ref_item || '';
                            var batchDisplay = (typeof item.batch_item !== 'undefined' && item.batch_item !== null && item.batch_item !== '') ? item.batch_item : '';
                            tableHtml += '<tr>' +
                                '<td><input type="radio" name="selected_item" value="' + idx + '" ' + (idx === 0 ? 'checked' : '') + '></td>' +
                                '<td>' + item.brand_item + '</td>' +
                                '<td>' + item.item_item + '</td>' +
                                '<td>' + item.sku_item + '</td>' +
                                '<td>' + refDisplay + '</td>' +
                                '<td>' + costDisplay + '</td>' +
                                '<td>' + batchDisplay + '</td>' +
                                '<td>' + qty + '</td>' +
                                '</tr>';
                        });
                        tableHtml += '</tbody></table></div>';
                        
                        var addQtyHtml = '<div class="form-group text-left">' +
                            '<label for="add-qty-input">Add Quantity (will redirect to edit location):</label>' +
                            '<input type="number" min="1" id="add-qty-input" class="form-control" style="width:120px;display:inline-block;" />' +
                            '</div>';

                        Swal.fire({
                            title: 'UPC already exists!',
                            html: '<div style="text-align:left">' + tableHtml + addQtyHtml + '</div>',
                            icon: 'warning',
                            width: '90%',
                            showCancelButton: true,
                            confirmButtonText: 'Add Quantity & Edit Location',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#632b8b',
                            preConfirm: () => {
                                const addQty = parseInt(document.getElementById('add-qty-input').value);
                                if (isNaN(addQty) || addQty <= 0) {
                                    Swal.showValidationMessage('Please enter a valid quantity to add.');
                                    return false;
                                }
                                const selectedRadio = document.querySelector('input[name="selected_item"]:checked');
                                if (!selectedRadio) {
                                    Swal.showValidationMessage('Please select an item to update.');
                                    return false;
                                }
                                return {
                                    addQty: addQty,
                                    selectedIdx: parseInt(selectedRadio.value)
                                };
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                var addQty = result.value.addQty;
                                var selectedIdx = result.value.selectedIdx;
                                var selectedItem = data.items[selectedIdx];
                                var currentQty = parseInt(selectedItem.quantity_inventory) || 0;
                                var newQty = currentQty + addQty;
                                
                                // Test the AJAX call
                                $.ajax({
                                    url: 'test_connection.php',
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {
                                        upc_item: upc,
                                        sku_item: (selectedItem.sku_item || ''),
                                        brand_item: selectedItem.brand_item,
                                        item_item: selectedItem.item_item,
                                        ref_item: selectedItem.ref_item || '',
                                        color_item: selectedItem.color_item || '',
                                        size_item: selectedItem.size_item || '',
                                        category_item: selectedItem.category_item || '',
                                        weight_item: selectedItem.weight_item || '',
                                        cost_item: selectedItem.cost_item || '',
                                        batch_item: selectedItem.batch_item || '',
                                        current_quantity: currentQty,
                                        new_quantity: newQty,
                                        added_quantity: addQty
                                    },
                                    success: function(resp) {
                                        console.log('Create report response:', resp);
                                        $('#result').html('<div class="alert alert-success"><h5>Success!</h5><pre>' + JSON.stringify(resp, null, 2) + '</pre></div>');
                                        if (resp.status === 'success') {
                                            Swal.fire({
                                                title: 'Success!',
                                                text: 'Item quantity updated. You can now go to edit the location.',
                                                icon: 'success',
                                                confirmButtonColor: '#632b8b',
                                                confirmButtonText: 'Go to Edit Location'
                                            }).then(() => {
                                                window.open('../report/editLocationFolder.php', '_blank');
                                            });
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.log('AJAX Error:', xhr.responseText);
                                        $('#result').html('<div class="alert alert-danger"><h5>Error!</h5><p>' + error + '</p><pre>' + xhr.responseText + '</pre></div>');
                                    }
                                });
                            }
                        });
                        
                    } else if (data.status === 'no_existe') {
                        $('#result').html('<div class="alert alert-info">UPC does not exist in database.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#result').html('<div class="alert alert-danger">Error checking UPC: ' + error + '</div>');
                }
            });
        });
    </script>
</body>
</html>
