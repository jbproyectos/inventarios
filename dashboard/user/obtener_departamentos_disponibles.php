<?php
session_start();
include "../../includes/conexionbd.php";

header('Content-Type: application/json');

if (isset($_GET['usuarioId'])) {
    $usuarioId = $_GET['usuarioId'];

    try {
        // Obtener los departamentos no asignados al usuario
        $query = "
            SELECT d.Id_departamento, d.nombre
            FROM departamentos d
            WHERE d.Id_departamento NOT IN (
                SELECT ud.Id_departamento
                FROM usuarios_departamentos ud
                WHERE ud.Id_usuario = :usuarioId
            )
        ";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':usuarioId', $usuarioId, PDO::PARAM_INT);
        $stmt->execute();
        $departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'departamentos' => $departamentos,
        ]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener los departamentos disponibles: ' . $e->getMessage(),
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID de usuario no proporcionado.',
    ]);
}