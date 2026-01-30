<?php
header('Content-Type: application/json');

include '../../includes/conexionbd.php'; // $conexion debe ser una instancia de PDO.

try {
    // Consulta SQL para obtener la inversión por oficina
    $sql = "
        SELECT 
            o.nombre AS OFICINAS, 
            COUNT(c.Id_oficina) AS valor,
            COUNT(DISTINCT c.Id_oficina) AS oficinasv,
            SUM(
                CASE 
                    WHEN c.costoEquipoActual REGEXP '^[\\$0-9,.]+$' 
                    THEN CAST(REPLACE(REPLACE(c.costoEquipoActual, '$', ''), ',', '') AS DECIMAL(10,2))
                    ELSE 0 
                END
            ) AS precio_total
        FROM 
            computadora c 
        JOIN 
            oficina o ON c.Id_oficina = o.Id_Oficina 
        GROUP BY 
            o.nombre";
    
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    
    // Obtener los datos en formato asociativo
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Enviar datos como JSON
    echo json_encode($data);

} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>
