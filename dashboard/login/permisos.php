<?php
// Conexión a la base de datos
include '../../includes/conexionbd.php';



// Procesar solicitudes AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    try {
        switch ($action) {
            case 'crear_rol':
                $nombre_rol = $_POST['nombre_rol'];
                $consulta = $conexion->prepare("INSERT INTO roless (nombre) VALUES (?)");
                $consulta->execute([$nombre_rol]);
                echo json_encode(['success' => true, 'message' => 'Rol creado exitosamente']);
                break;

            case 'crear_permiso':
                $nombre_permiso = $_POST['nombre_permiso'];
                $consulta = $conexion->prepare("INSERT INTO permisos (nombre) VALUES (?)");
                $consulta->execute([$nombre_permiso]);
                echo json_encode(['success' => true, 'message' => 'Permiso creado exitosamente']);
                break;

            case 'asignar_rol_usuario':
                $id_usuario = $_POST['id_usuario'];
                $id_rol = $_POST['id_rol'];
                $consulta = $conexion->prepare("INSERT INTO roles_usuarios (usuario_id, rol_id) VALUES (?, ?)");
                $consulta->execute([$id_usuario, $id_rol]);
                echo json_encode(['success' => true, 'message' => 'Rol asignado al usuario exitosamente']);
                break;

            case 'asignar_permiso_rol':
                $id_rol = $_POST['id_rol'];
                $id_permiso = $_POST['id_permiso'];
                $consulta = $conexion->prepare("INSERT INTO permisos_modelos (rol_id, permiso_id) VALUES (?, ?)");
                $consulta->execute([$id_rol, $id_permiso]);
                echo json_encode(['success' => true, 'message' => 'Permiso asignado al rol exitosamente']);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}


