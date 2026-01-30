<?php
include "conexionbd.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $departamento = $_POST['departamento'];

    try {
        // Consulta para buscar empleados que coincidan con el departamento por ID
        $stmt = $conexion->prepare("
            SELECT e.id, e.nombre 
            FROM Empleados e
            JOIN departamentos d ON e.departamento = d.Id_departamento
            WHERE d.nombre LIKE :departamento
        ");
        $stmt->execute(['departamento' => '%' . $departamento . '%']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode([
                'status' => 'success',
                'data' => $result // Devuelve un array con los empleados (id y nombre)
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron empleados para el departamento seleccionado.'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error en la consulta: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Solicitud no válida.'
    ]);
}
?>