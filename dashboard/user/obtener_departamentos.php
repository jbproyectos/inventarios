<?php
session_start();
include "../../includes/conexionbd.php";

header('Content-Type: application/json');

if (isset($_GET['usuarioId'])) {
    $usuarioId = $_GET['usuarioId'];

    try {
        // Obtener los departamentos asignados al usuario
        $query = "
            SELECT d.Id_departamento, d.nombre
            FROM departamentos d
            JOIN usuarios_departamentos ud ON d.Id_departamento = ud.Id_departamento
            WHERE ud.Id_usuario = :usuarioId
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
            'message' => 'Error al obtener los departamentos asignados: ' . $e->getMessage(),
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'ID de usuario no proporcionado.',
    ]);
}