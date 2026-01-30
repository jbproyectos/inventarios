<?php
header('Content-Type: application/json');

include '../../includes/conexionbd.php'; // $conexion debe ser una instancia de PDO.


try {


    // Consulta para obtener la cantidad de computadoras por condición
    $query = "SELECT status, COUNT(*) as cantidad FROM computadora GROUP BY status";
    $stmt = $conexion->query($query);

    $resultados = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $resultados[] = [
            'status' => $row['status'],
            'cantidad' => $row['cantidad']
        ];
    }

    echo json_encode($resultados);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
