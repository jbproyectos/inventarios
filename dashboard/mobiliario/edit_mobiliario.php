<?php
include '../../includes/conexionbd.php';

// Habilitar modo de error de PDO
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Obtener el ID del mobiliario a editar
$mobiliario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener información del mobiliario
try {
    $sql = "SELECT * 
    FROM mobiliario AS m
    JOIN domicilios AS d ON m.id_domicilio = d.id
    WHERE m.id_mobiliario = :id
    ";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':id', $mobiliario_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Retornar datos como JSON
        header('Content-Type: application/json');
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'No se encontró mobiliario con el ID proporcionado.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>
