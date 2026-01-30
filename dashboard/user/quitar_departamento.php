<?php
session_start();
include "../../includes/conexionbd.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $usuarioId = $data['usuarioId'];
    $departamentoId = $data['departamentoId'];

    try {
        // Eliminar la asignación del departamento
        $query = "DELETE FROM usuarios_departamentos WHERE Id_usuario = :usuarioId AND Id_departamento = :departamentoId";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->bindParam(':departamentoId', $departamentoId, PDO::PARAM_INT);
        $stmt->execute();

        echo json_encode([
            'success' => true,
            'message' => 'Departamento quitado correctamente.',
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al quitar el departamento: ' . $e->getMessage(),
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.',
    ]);
}