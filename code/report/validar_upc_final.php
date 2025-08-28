<?php
include("../../conexion.php");
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['upc_final'])) {
    $upc_final = trim($_POST['upc_final']);

    // Prepared statement to safely query the items table
    $stmt = $mysqli->prepare("SELECT folder_item, item_item FROM items WHERE upc_item = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('s', $upc_final);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            echo json_encode([
                'exists' => true,
                'folder_item' => $row['folder_item'] ?? '',
                'item_item' => $row['item_item'] ?? ''
            ]);
        } else {
            echo json_encode([
                'exists' => false
            ]);
        }

        $stmt->close();
    } else {
        echo json_encode([
            'exists' => false,
            'message' => 'Database error preparing statement',
            'error' => $mysqli->error
        ]);
    }

} else {
    echo json_encode([
        'exists' => false,
        'message' => 'Invalid request'
    ]);
}

$mysqli->close();
?>
