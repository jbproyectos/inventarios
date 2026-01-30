<?php
include '../../includes/conexionbd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_GET['id'] ?? $_POST['id'] ?? 0;
    
    if ($id == 0) {
        echo json_encode(['success' => false, 'message' => 'ID no especificado']);
        exit;
    }
    
    try {
        // Verificar si el teléfono existe
        $stmtCheck = $conexion->prepare("SELECT COUNT(*) FROM telefonos WHERE id = ?");
        $stmtCheck->execute([$id]);
        
        if ($stmtCheck->fetchColumn() == 0) {
            echo json_encode(['success' => false, 'message' => 'Teléfono no encontrado']);
            exit;
        }
        
        // Eliminar teléfono (las claves foráneas con CASCADE eliminarán automáticamente propietarios y cuentas)
        $stmt = $conexion->prepare("DELETE FROM telefonos WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Teléfono eliminado exitosamente']);
        
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>