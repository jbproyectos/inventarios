<?php
require_once 'conexion.php'; // Incluye tu archivo de conexión a la base de datos

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitiza el ID recibido

    try {
        $query = $conexion->prepare('SELECT campo1, campo2 FROM tu_tabla WHERE id = :id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        $result = $query->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode([
                'success' => true,
                'data' => $result
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se encontró el registro.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en el servidor: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Solicitud inválida.'
    ]);
}
?>
