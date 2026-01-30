<?php
// Marcar como revisados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos enviados desde el cliente
    $data = json_decode(file_get_contents('php://input'), true);
    $ids = $data['ids'] ?? [];
    $comment = $data['comment'] ?? ''; // Comentario opcional
    $status = $data['status'] ?? 1; // Por defecto, revisado sin errores (1)

    // Validar que los IDs no estén vacíos
    if (count($ids) > 0) {
        try {
            // Conexión a la base de datos
            include "../../includes/conexionbd.php";
            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Marcar los registros como revisados con el estado proporcionado
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "UPDATE computadora 
                    SET revisado = ?, comment = ? 
                    WHERE Id_computadora IN ($placeholders)";
            $stmt = $conexion->prepare($sql);

            // Construir los parámetros: estado, comentario y los IDs
            $params = array_merge([$status, $comment], array_map('intval', $ids));

            // Ejecutar la consulta
            $stmt->execute($params);

            echo json_encode(['success' => true]);
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No se seleccionaron registros']);
    }
}
