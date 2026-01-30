<?php
header('Content-Type: application/json');

include '../../includes/conexionbd.php'; // Asegúrate de tener $conexion como una instancia válida de PDO.

try {
    // Consulta SQL para filtrar registros válidos y agrupar por tipo y año
    $sql = "
    SELECT 
        tipo, 
        COUNT(*) AS valor, 
        SUM(
            CASE 
                WHEN costoEquipoActual REGEXP '^[\\$]?[0-9,]+(\\.[0-9]{2})?$' 
                THEN CAST(
                    REPLACE(REPLACE(costoEquipoActual, '$', ''), ',', '') AS DECIMAL(10,2)
                )
                ELSE 0 
            END
        ) AS precio_total
    FROM 
        computadora
    GROUP BY 
        tipo
    ORDER BY 
        tipo
";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    // Obtener datos como arreglo asociativo
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retornar datos como JSON
    echo json_encode($data);

} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>
