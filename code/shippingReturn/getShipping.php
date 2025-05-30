<?php
include("../../conexion.php");

// Inicializamos las condiciones de búsqueda
$where = [];

// Verifica si se ha enviado el valor de sell_order
if (isset($_GET['sell_order']) && $_GET['sell_order'] !== '') {
    $where[] = "d.sell_order = '" . $mysqli->real_escape_string($_GET['sell_order']) . "'";
}

// Verifica si se ha enviado el valor de date_devolution
if (isset($_GET['date_devolution']) && $_GET['date_devolution'] !== '') {
    $where[] = "d.date = '" . $mysqli->real_escape_string($_GET['date_devolution']) . "'";
}

// Construye la consulta base
$query = "
SELECT DISTINCT 
    s.sell_order, 
    s.date, 
    sr.billing_return
FROM sell AS s
LEFT JOIN shipping_return AS sr ON s.sell_order = sr.sell_order;

";

// Si existen filtros, agregamos la cláusula WHERE
if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

// Ejecutamos la consulta
$result = $mysqli->query($query);
$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['sell_order'] . "</td>";
        echo "<td>" . $row['date'] . "</td>";
        echo "<td>" . $row['billing_return'] . "</td>";
        echo "<td>";
        echo "<button class='btn btn-action-icon btn-edit btn-sm open-modal' data-sellorder='" . $row['sell_order'] . "' data-bs-toggle='modal' data-bs-target='#ventasModal'><i class='fas fa-edit'></i></button>";
        echo "</td>";
    }
} else {
    echo "<tr><td colspan='9'>No se encontraron registros.</td></tr>";
}


$mysqli->close();
