<?php
include '../../includes/conexionbd.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    try {
        $query = $conexion->prepare('DELETE FROM mobiliario WHERE id_mobiliario = :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);

        if ($query->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'El registro fue eliminado con éxito.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo eliminar el registro.'
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
        'message' => 'Solicitud inválida.'
    ]);
}
