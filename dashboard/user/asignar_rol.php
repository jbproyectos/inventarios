<?php
include '../../includes/conexionbd.php';
header('Content-Type: application/json');

$userId = $_POST['id_usuario'] ?? null;
$selectedRole = $_POST['id_rol'] ?? null;

if ($userId && $selectedRole) {
    try {
        $query = $conexion->prepare("UPDATE usuarios SET rolActual = :rol_id WHERE Id_Usuario = :user_id");
        $query->bindParam(':rol_id', $selectedRole, PDO::PARAM_INT);
        $query->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $query->execute();

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Datos insuficientes']);
}