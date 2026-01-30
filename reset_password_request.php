<?php
include 'includes/conexionbd.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST["token"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($password != $confirmPassword) {
        echo "Las contraseñas no coinciden. Vuelve a intentarlo.";
        exit;
    }

    $userId = verificarToken($token);

    if ($userId !== false) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "UPDATE usuarios SET contrasena = :contrasena WHERE Id_Usuario = :user_id";
        $stmt = $conexion->prepare($sql);
        $stmt->bindParam(':contrasena', $hashedPassword);
        $stmt->bindParam(':user_id', $userId);

        try {
            $stmt->execute();
            echo "Contraseña restablecida con éxito.";
        } catch (PDOException $e) {
            echo "Error al restablecer la contraseña: " . $e->getMessage();
        }
    } else {
        echo "Token no válido o ha expirado.";
    }
} else {
    // header("Location: index.php");
    exit;
}

function verificarToken($token) {
    include 'includes/conexionbd.php';
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT id_usuario FROM tokens_reset WHERE token = :token AND expiracion > NOW()";
    $stmt = $conexion->prepare($sql);
    $stmt->bindParam(':token', $token);

    try {
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row['id_usuario'] : false;
    } catch (PDOException $e) {
        echo "Error al verificar el token: " . $e->getMessage();
        return false;
    }
}
?>
