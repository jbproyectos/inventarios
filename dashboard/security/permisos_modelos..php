<?php
// Incluir conexión a la base de datos
include '../includes/conexionbd.php';

function getUserRole($userId) {
    global $conexion; // Asegúrate de que la conexión esté disponible

    // Consulta para obtener el rol del usuario
    $stmt = $conexion->prepare("SELECT rolActual FROM usuarios WHERE Id_Usuario = :user_id");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return $result['rolActual'];
    } else {
        return null; // Si no se encuentra el rol, puedes devolver null o lanzar un error
    }
}

?>
