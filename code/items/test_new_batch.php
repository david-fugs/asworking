<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test New Batch Functionality</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            padding: 20px;
        }
        .test-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #0dcaf0; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">🧪 New Batch Functionality Tests</h1>
        
        <div class="test-card">
            <h3>✅ Database Configuration Check</h3>
            <?php
            include("../../conexion.php");
            
            // Check if UNIQUE constraint is removed
            $result = $mysqli->query("SHOW INDEXES FROM items WHERE Key_name = 'idx_sku_item'");
            if ($result) {
                $index = $result->fetch_assoc();
                if ($index && $index['Non_unique'] == 1) {
                    echo '<p class="success">✓ UNIQUE constraint successfully removed from sku_item</p>';
                    echo '<p class="info">→ Multiple items can now have the same SKU with different batches</p>';
                } else if ($index && $index['Non_unique'] == 0) {
                    echo '<p class="error">✗ UNIQUE constraint still exists on sku_item</p>';
                    echo '<p>Run: <code>remove_unique_constraint.php</code> to fix this</p>';
                } else {
                    echo '<p class="error">✗ No index found on sku_item</p>';
                }
            }
            ?>
        </div>

        <div class="test-card">
            <h3>📊 Sample Data Check</h3>
            <?php
            // Check if there are any items with duplicate SKUs (after removing constraint)
            $sql = "SELECT sku_item, COUNT(*) as count, GROUP_CONCAT(batch_item SEPARATOR ', ') as batches 
                    FROM items 
                    GROUP BY sku_item 
                    HAVING count > 1 
                    LIMIT 5";
            $result = $mysqli->query($sql);
            
            if ($result && $result->num_rows > 0) {
                echo '<p class="success">✓ Found items with multiple batches:</p>';
                echo '<table class="table table-sm">';
                echo '<thead><tr><th>SKU</th><th>Batch Count</th><th>Batches</th></tr></thead><tbody>';
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['sku_item']) . '</td>';
                    echo '<td>' . $row['count'] . '</td>';
                    echo '<td>' . htmlspecialchars($row['batches']) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<p class="info">ℹ No duplicate SKUs found yet (this is normal for a fresh installation)</p>';
                echo '<p>→ Use the "Add New Batch" feature to create items with the same UPC/SKU but different batches</p>';
            }
            ?>
        </div>

        <div class="test-card">
            <h3>🔗 Quick Links</h3>
            <div class="btn-group" role="group">
                <a href="additems.php" class="btn btn-primary">Add Items (Test Here)</a>
                <a href="../report/addReport.php" class="btn btn-success">Add Report (Test Here)</a>
                <a href="remove_unique_constraint.php" class="btn btn-warning">Run Constraint Removal</a>
                <a href="NEW_BATCH_FUNCTIONALITY.md" class="btn btn-info" target="_blank">View Documentation</a>
            </div>
        </div>

        <div class="test-card">
            <h3>📝 Test Instructions</h3>
            <ol>
                <li><strong>First Item:</strong> Go to <a href="additems.php">Add Items</a> and create an item with:
                    <ul>
                        <li>UPC: TEST-123</li>
                        <li>SKU: TEST-SKU-001</li>
                        <li>Batch: BATCH-A</li>
                        <li>Cost: $50.00</li>
                        <li>Quantity: 100</li>
                    </ul>
                </li>
                <li><strong>Second Batch:</strong> Enter the SAME UPC (TEST-123):
                    <ul>
                        <li>Modal will show "UPC already exists!"</li>
                        <li>Click <strong>"Add New Batch (Same UPC/SKU)"</strong></li>
                        <li>Change Batch to: BATCH-B</li>
                        <li>Change Cost to: $48.00</li>
                        <li>Change Quantity to: 150</li>
                        <li>Submit</li>
                    </ul>
                </li>
                <li><strong>Verify:</strong> Both batches should now exist in the database with the same UPC and SKU but different batch numbers and costs</li>
            </ol>
        </div>

        <div class="test-card">
            <h3>🔍 Recent Items Query</h3>
            <?php
            $sql = "SELECT upc_item, sku_item, batch_item, brand_item, item_item, cost_item, quantity_item, 
                    DATE_FORMAT(fecha_alta_item, '%Y-%m-%d %H:%i') as created 
                    FROM items 
                    ORDER BY fecha_alta_item DESC 
                    LIMIT 10";
            $result = $mysqli->query($sql);
            
            if ($result && $result->num_rows > 0) {
                echo '<p class="info">Last 10 items added:</p>';
                echo '<div style="overflow-x: auto;">';
                echo '<table class="table table-sm table-striped">';
                echo '<thead><tr><th>UPC</th><th>SKU</th><th>Batch</th><th>Brand</th><th>Item</th><th>Cost</th><th>Qty</th><th>Created</th></tr></thead><tbody>';
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['upc_item']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['sku_item']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['batch_item']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['brand_item']) . '</td>';
                    echo '<td>' . htmlspecialchars(substr($row['item_item'], 0, 30)) . '...</td>';
                    echo '<td>$' . number_format($row['cost_item'], 2) . '</td>';
                    echo '<td>' . $row['quantity_item'] . '</td>';
                    echo '<td>' . $row['created'] . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
                echo '</div>';
            }
            
            $mysqli->close();
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
