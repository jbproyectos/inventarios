<?php
include 'conexionbd.php'; // Conexión a la base de datos
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id']; // ID del empleado enviado desde JavaScript

    try {
        // Consulta para obtener el ID del puesto
        $query = "
            SELECT Empleados.id, Empleados.nombre, puestos.Id_puesto, puestos.nombre AS puesto
            FROM Empleados
            JOIN puestos ON Empleados.puesto = puestos.Id_puesto
            WHERE Empleados.id = :id
        ";

        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                echo json_encode([
                    'status' => 'success',
                    'data' => [
                        'id' => $result['id'],
                        'nombre' => $result['nombre'],
                        'puesto' => $result['puesto'],
                        'puesto_id' => $result['Id_puesto'] // Agregamos el ID del puesto
                    ]
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Empleado no encontrado.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al ejecutar la consulta.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la conexión: ' . $e->getMessage()]);
    }
}
?>
