<?php
require_once 'conexion.php'; // Incluye tu archivo de conexión a la base de datos

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitiza el ID recibido
    $data = json_decode(file_get_contents('php://input'), true); // Obtiene los datos enviados en el cuerpo de la solicitud

    if (!empty($data['field1']) && !empty($data['field2'])) {
        try {
            $query = $conexion->prepare('UPDATE tu_tabla SET campo1 = :field1, campo2 = :field2 WHERE id = :id');
            $query->bindParam(':field1', $data['field1']);
            $query->bindParam(':field2', $data['field2']);
            $query->bindParam(':id', $id, PDO::PARAM_INT);

            if ($query->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'El registro fue actualizado con éxito.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo actualizar el registro.'
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en el servidor: ' . $e->getMessage()
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Todos los campos son obligatorios.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Solicitud inválida.'
    ]);
}
?>
