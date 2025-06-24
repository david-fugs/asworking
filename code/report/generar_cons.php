<?php
include("../../conexion.php");
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['folder'])) {
    $folder = $mysqli->real_escape_string(trim($_POST['folder']));
    
    // Buscar el último consecutivo para este folder
    $query = "SELECT cons_report FROM daily_report 
              WHERE cons_report LIKE '$folder cons %' 
              ORDER BY cons_report DESC 
              LIMIT 1";
    
    $result = $mysqli->query($query);
    
    $next_cons = 1; // Por defecto empezar en 1
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $last_cons = $row['cons_report'];
        
        // Extraer el número del consecutivo
        // Formato esperado: "100 cons 5"
        if (preg_match('/cons (\d+)$/', $last_cons, $matches)) {
            $next_cons = intval($matches[1]) + 1;
        }
    }
    
    echo json_encode([
        'success' => true,
        'next_cons' => $next_cons,
        'full_cons' => $folder . ' cons ' . $next_cons
    ]);
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}

$mysqli->close();
?>
