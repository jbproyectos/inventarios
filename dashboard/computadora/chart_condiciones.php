<?php
header('Content-Type: application/json');

include '../../includes/conexionbd.php'; // $conexion debe ser una instancia de PDO.

try {
    // Consulta SQL para obtener las oficinas y el conteo de computadoras únicas por oficina
    $sql = "SELECT o.nombre AS OFICINAS, COUNT(DISTINCT c.Id_computadora) as valor 
            FROM computadora c 
            JOIN oficina o ON c.Id_oficina = o.Id_Oficina 
            GROUP BY o.nombre";
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
