<?php
include '../../includes/conexionbd.php'; // Asegúrate de incluir tu archivo de conexión

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'eliminar_todos') {
    try {
        // Ejecuta la consulta para eliminar todos los registros de mobiliario
        $sql = "DELETE FROM mobiliario";
        $stmt = $conexion->prepare($sql);
        $stmt->execute();

        // Respuesta exitosa
        echo json_encode([
            'success' => true,
            'message' => 'Todos los registros de mobiliario han sido eliminados.'
        ]);
    } catch (PDOException $e) {
        // Manejo de errores
        echo json_encode([
            'success' => false,
            'message' => 'Hubo un error al eliminar los registros: ' . $e->getMessage()
        ]);
    }
}
?>
