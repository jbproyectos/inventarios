<?php
header('Content-Type: application/json');

include '../../includes/conexionbd.php'; // $conexion debe ser una instancia de PDO.

try {
    // Consulta para obtener la cantidad de computadoras por año del campo fechaDeLanzamiento
    $query = "
        SELECT 
            STR_TO_DATE(fechaDeLanzamiento, '%d/%m/%Y') AS fechaConvertida,
            COUNT(*) AS cantidad,
            YEAR(STR_TO_DATE(fechaDeLanzamiento, '%d/%m/%Y')) AS year
        FROM computadora
        GROUP BY YEAR(STR_TO_DATE(fechaDeLanzamiento, '%d/%m/%Y'))
        ORDER BY year ASC
    ";
    $stmt = $conexion->query($query);

    $resultados = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $resultados[] = [
            'year' => $row['year'],
            'cantidad' => $row['cantidad']
        ];
    }

    echo json_encode($resultados);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
