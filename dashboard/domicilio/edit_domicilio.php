<?php
include '../../includes/conexionbd.php';

// Enable PDO error mode
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Get the ID of the equipment to edit
$equipo_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch equipment information
try {
    $sql = "SELECT * FROM domicilios WHERE id = :id";
    $stmt = $conexion->prepare($sql);
    $stmt->bindValue(':id', $equipo_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Output data of each row
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Return data as JSON
        header('Content-Type: application/json');
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'No equipment found with the given ID.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>