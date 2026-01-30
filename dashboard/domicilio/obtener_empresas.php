<?php
// Incluye tu conexión PDO.
include '../../includes/conexionbd.php';

// Habilita la visualización de errores para depuración (puedes quitar esto en producción).
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // 1. Preparar y ejecutar la consulta SQL usando la sintaxis de PDO.
    $sql = "SELECT nombre FROM empresas ORDER BY nombre ASC";
    $stmt = $conexion->query($sql); // 'query' es un método válido en PDO para consultas simples.

    // 2. Obtener todos los resultados en un array asociativo.
    // fetchAll(PDO::FETCH_ASSOC) es la forma más directa en PDO de hacer lo que intentabas con el bucle while.
    $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Devolver los datos en formato JSON.
    header('Content-Type: application/json');
    echo json_encode($empresas);

} catch (PDOException $e) {
    // Captura cualquier error de la base de datos y lo devuelve como JSON.
    header('Content-Type: application/json');
    http_response_code(500); // Código de error del servidor.
    echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
}

// 4. Cerrar la conexión (en PDO, se hace asignando null).
$conexion = null;

?>
