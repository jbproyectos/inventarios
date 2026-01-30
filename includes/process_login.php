<?php
session_start();
include 'conexionbd.php';

header('Content-Type: application/json'); // Retornaremos JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, completa todos los campos']);
        exit();
    }

    try {
        // Obtener usuario
        $stmt = $conexion->prepare("SELECT Id_Usuario, contrasena, estatu FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['contrasena'])) {
            if ($user['estatu'] == 1) {
                // Usuario aprobado, crear sesión
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['Id_Usuario'];

                // Actualizar último ingreso
                $usuario_id = $_SESSION['user_id'];
                $fecha_actual = date('Y-m-d H:i:s');
                $consulta_actualizar = $conexion->prepare('UPDATE usuarios SET fechaUltimoIngreso = :fecha_actual WHERE Id_Usuario = :usuario_id');
                $consulta_actualizar->bindParam(':fecha_actual', $fecha_actual);
                $consulta_actualizar->bindParam(':usuario_id', $usuario_id);
                $consulta_actualizar->execute();

                // Enviar respuesta de éxito
                $redirectUrl = 'dashboard/';
                echo json_encode(['success' => true, 'redirect' => $redirectUrl]);
                exit();
            } else {
                // Usuario no aprobado
                echo json_encode([
                    'success' => false,
                    'message' => 'Tu cuenta aún no ha sido aprobada. Contacta al administrador.'
                ]);
                exit();
            }
        } else {
            // Credenciales incorrectas
            echo json_encode(['success' => false, 'message' => 'Credenciales incorrectas']);
            exit();
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
        exit();
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No se recibieron datos por POST']);
    exit();
}
?>
