<?php
include '../../includes/conexionbd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nombre_rol = $_POST['nombre_rol'];
    $subname = $_POST['subname'];
    
  


    if (empty($nombre_rol) || empty($subname)) {
        $response = array('success' => false, 'message' => 'Todos los campos son obligatorios');
    } else {
        try {
            $sql = "INSERT INTO roles (nombre_rol, subname) 
                    VALUES (?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$nombre_rol, $subname]);

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