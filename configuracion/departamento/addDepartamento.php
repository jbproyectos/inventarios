<?php
include '../../includes/conexionbd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nombre = $_POST['nombre'];
    
  


    if (empty($nombre)) {
        $response = array('success' => false, 'message' => 'Todos los campos son obligatorios');
    } else {
        try {
            $sql = "INSERT INTO departamentos (nombre) 
                    VALUES (?)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre]);

            $conexion = null;
            $response = array('success' => true, 'message' => 'Registro exitoso');
        } catch (PDOException $e) {
            $response = array('success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage());
        }
    }


    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    header('Content-Type: application/json');
    echo json_encode(array('success' => false, 'message' => 'No se recibieron datos por POST'));
}

?>