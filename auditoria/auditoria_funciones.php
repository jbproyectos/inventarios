<?php
// generar_json.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

include '../includes/conexionbd.php'; // Archivo de conexión a la base de datos

try {
    // Consulta para obtener los datos de auditoría
    $sql = "SELECT id, usuario, tabla_afectada, operacion, registro_id, datos_anteriores, datos_nuevos, fecha FROM auditoria ORDER BY fecha DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $auditoria = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los datos en formato JSON
    echo json_encode($auditoria, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>
