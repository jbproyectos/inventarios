<?php
include '../../includes/conexionbd.php';

header('Content-Type: application/json');

try {
    $query = $conexion->prepare("SELECT id, nombre FROM roless");
    $query->execute();
    $roles = $query->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'roles' => $roles]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
