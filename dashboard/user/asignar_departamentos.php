<?php
session_start();
include "../../includes/conexionbd.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $usuarioId = $data['usuarioId'];
    $departamentos = $data['departamentos'];

    try {
        $conexion->beginTransaction();

        // Insertar nuevas asignaciones
        $query = "INSERT INTO usuarios_departamentos (Id_usuario, Id_departamento) VALUES (:usuarioId, :departamentoId)";
        $stmt = $conexion->prepare($query);

        foreach ($departamentos as $departamentoId) {
            $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $stmt->bindParam(':departamentoId', $departamentoId, PDO::PARAM_INT);
            $stmt->execute();
        }

        $conexion->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Departamentos asignados correctamente.',
        ]);
    } catch (PDOException $e) {
        $conexion->rollBack();
        echo json_encode([
            'success' => false,
            'message' => 'Error al asignar los departamentos: ' . $e->getMessage(),
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.',
    ]);
}