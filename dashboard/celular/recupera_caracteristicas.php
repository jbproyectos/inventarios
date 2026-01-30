<?php
include '../includes/middleware.php';
verificarSesion();
include "../includes/conexionbd.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        $stmt = $conexion->prepare("
            SELECT c.*, d.nombre as nombre_departamento, o.nombre as nombre_oficina
            FROM celulares c
            JOIN departamentos d ON c.Id_departamento = d.Id_departamento
            JOIN oficina o ON c.Id_Oficina = o.Id_Oficina
            WHERE c.Id_celular = ?
        ");
        $stmt->execute([$id]);
        $celular = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($celular) {
            echo json_encode($celular);
        } else {
            echo json_encode(['error' => 'Celular no encontrado']);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]);
    }
}
?>