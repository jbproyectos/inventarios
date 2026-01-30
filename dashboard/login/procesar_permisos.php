<?php
// Conexión a la base de datos
include '../../includes/conexionbd.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Desactivar todos los permisos antes de asignar los nuevos
    $conexion->beginTransaction();
    
    try {
        // Primero, limpiamos los permisos existentes (esto es opcional, puedes comentarlo si no lo necesitas)
        $conexion->query("DELETE FROM permisos_modelos");
        
        // Procesar los permisos enviados
        foreach ($_POST['permisos'] as $rol_id => $modelos_permisos) {
            foreach ($modelos_permisos as $modelo_id => $permisos) {
                foreach ($permisos as $permiso_id => $valor) {
                    if ($valor == 1) {
                        // Insertar el nuevo permiso en la base de datos
                        $consulta = $conexion->prepare("
                            INSERT INTO permisos_modelos (rol_id, modelo_id, permiso_id) 
                            VALUES (?, ?, ?)
                        ");
                        $consulta->execute([$rol_id, $modelo_id, $permiso_id]);
                    }
                }
            }
        }

        // Si todo es correcto, confirmamos la transacción
        $conexion->commit();
        
        // Respuesta JSON para indicar éxito
        echo json_encode([
            'success' => true,
            'message' => 'Permisos asignados correctamente.'
        ]);
    } catch (PDOException $e) {
        // Si hay un error, hacemos rollback y enviamos el mensaje de error
        $conexion->rollBack();
        
        // Respuesta JSON para indicar error
        echo json_encode([
            'success' => false,
            'message' => 'Error al asignar permisos: ' . htmlspecialchars($e->getMessage())
        ]);
    }
}
?>
