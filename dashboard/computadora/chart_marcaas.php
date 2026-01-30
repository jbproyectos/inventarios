<?php
header('Content-Type: application/json');

include '../../includes/conexionbd.php'; // Asegúrate de tener la conexión correcta.

try {
    // Consulta para obtener el precio total por marca y cantidad de equipos por marca
    $sql = "
    SELECT 
        c.marca AS marca, 
        COUNT(c.Id_departamento) AS cantidad_equipos,  -- Número de equipos por marca
        COUNT(DISTINCT c.marca) AS marcas_distintas,  -- Número de marcas distintas
        ROUND(SUM(
            CASE 
                WHEN c.costoEquipoActual REGEXP '^[\\$0-9,.]+$'  -- Verifica si el valor es un formato válido de precio
                THEN CAST(REPLACE(REPLACE(c.costoEquipoActual, '$', ''), ',', '') AS DECIMAL(10,2))
                ELSE 0 
            END
        ), 2) AS precio_total  -- Precio total por marca, redondeado a 2 decimales
    FROM 
        computadora c 
    GROUP BY 
        c.marca";  // Agrupar por marca

    $stmt = $conexion->prepare($sql);
    $stmt->execute();

    // Obtener los datos
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Enviar los datos como JSON
    echo json_encode($data);

} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}

?>
