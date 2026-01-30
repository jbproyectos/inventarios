<?php
session_start();
// require_once '../errores/error_handler.php';
ini_set('display_errors', 1);
    ini_set('error_reporting', E_ALL);
include "../../includes/conexionbd.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $usuarioId = $data['usuario'];
    $departamentos = $data['departamentos'];

    error_log("Usuario ID: " . $usuarioId); // Log para depuración
    error_log("Departamentos: " . print_r($departamentos, true)); // Log para depuración

    if (empty($usuarioId) || empty($departamentos)) {
        echo json_encode([
            'success' => false,
            'message' => 'Por favor, seleccione un usuario y al menos un departamento.',
        ]);
        exit();
    }

    try {
        $conexion->beginTransaction();

        // Eliminar asignaciones anteriores
        $queryEliminar = "DELETE FROM usuarios_departamentos WHERE Id_usuario = :usuarioId";
        $stmtEliminar = $conexion->prepare($queryEliminar);
        $stmtEliminar->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmtEliminar->execute();

        // Insertar nuevas asignaciones
        $queryInsertar = "INSERT INTO usuarios_departamentos (Id_usuario, Id_departamento) VALUES (:usuarioId, :departamentoId)";
        $stmtInsertar = $conexion->prepare($queryInsertar);

        foreach ($departamentos as $departamentoId) {
            $stmtInsertar->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
            $stmtInsertar->bindParam(':departamentoId', $departamentoId, PDO::PARAM_INT);
            $stmtInsertar->execute();
        }

        $conexion->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Departamentos asignados correctamente.',
        ]);
    } catch (PDOException $e) {
        $conexion->rollBack();
        error_log("Error en la base de datos: " . $e->getMessage()); // Log para depuración
        echo json_encode([
            'success' => false,
            'message' => 'Error al asignar departamentos: ' . $e->getMessage(),
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.',
    ]);
}