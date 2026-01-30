<?php
include '../../includes/conexionbd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action !== 'eliminar_todos') {
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        exit;
    }
    
    try {
        // Desactivar restricciones de clave foránea
        $conexion->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Eliminar todas las tablas relacionadas
        $conexion->exec("TRUNCATE TABLE cuentas_icloud");
        $conexion->exec("TRUNCATE TABLE historial_propietarios");
        $conexion->exec("TRUNCATE TABLE telefonos");
        
        // Reactivar restricciones de clave foránea
        $conexion->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        echo json_encode(['success' => true, 'message' => 'Todos los registros han sido eliminados']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>