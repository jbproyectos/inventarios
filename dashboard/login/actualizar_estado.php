<?php
include '../../includes/conexionbd.php';

// Incluir PHPMailer
require '../../PHPMailer/src/PHPMailer.php';
require '../../PHPMailer/src/Exception.php';
require '../../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json");

try {
    // Leer los datos enviados por AJAX
    $data = json_decode(file_get_contents("php://input"), true);

    $userId = $data['userId'];
    $nuevoEstado = $data['estatu'];

    // Validar los datos
    if (!isset($userId, $nuevoEstado)) {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos']);
        exit();
    }

    // Obtener información del usuario antes de actualizar
    $queryUser = "SELECT nombre, apellido, email FROM usuarios WHERE Id_Usuario = :userId";
    $stmtUser = $conexion->prepare($queryUser);
    $stmtUser->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmtUser->execute();
    $usuario = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode(['status' => 'error', 'message' => 'Usuario no encontrado']);
        exit();
    }

    // Actualizar el estado en la base de datos
    $query = "UPDATE usuarios SET estatu = :estatu WHERE Id_Usuario = :userId";
    $stmt = $conexion->prepare($query);
    $stmt->bindParam(':estatu', $nuevoEstado, PDO::PARAM_INT);
    $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        
        // Enviar correo solo si se está activando la cuenta (estatu = 1)
        if ($nuevoEstado == 1) {
            $emailEnviado = enviarCorreoActivacion($usuario);
            
            if ($emailEnviado) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Estado actualizado y correo de activación enviado correctamente'
                ]);
            } else {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Estado actualizado pero hubo un error al enviar el correo de activación'
                ]);
            }
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Estado actualizado correctamente']);
        }
        
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No se pudo actualizar el estado']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}

// Función para enviar correo de activación
function enviarCorreoActivacion($usuario) {
    // Configuración SMTP
    $SMTP_HOST = 'smtp.gmail.com';
    $SMTP_USER = 'desarrollo@kabzo.org';
    $SMTP_PASS = 'ydwt bjxd eagx jezu';
    $SMTP_PORT = 587;
    $SMTP_SECURE = 'tls';
    
    $nombreCompleto = $usuario['nombre'] . ' ' . $usuario['apellido'];
    $email = $usuario['email'];
    
    // Plantilla de correo similar a la plantilla "event"
    $htmlContent = generarPlantillaActivacion($nombreCompleto, $email);
    
    try {
        $mail = new PHPMailer(true);
        
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = $SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $SMTP_USER;
        $mail->Password = $SMTP_PASS;
        $mail->SMTPSecure = $SMTP_SECURE === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $SMTP_PORT;
        
        // Destinatarios
        $mail->setFrom($SMTP_USER, 'GRUPO KABZO - Sistema');
        $mail->addAddress($email, $nombreCompleto);
        
        // Contenido
        $mail->isHTML(true);
        $mail->Subject = '¡Cuenta Activada - GRUPO KABZO!';
        $mail->Body = $htmlContent;
        $mail->AltBody = strip_tags($htmlContent);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Error al enviar correo de activación: " . $mail->ErrorInfo);
        return false;
    }
}

// Función para generar la plantilla HTML del correo
function generarPlantillaActivacion($nombre, $email) {
    return '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cuenta Activada - GRUPO KABZO</title>
    </head>
    <body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px;">
        <div style="background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">¡CUENTA ACTIVADA EXITOSAMENTE!</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">GRUPO KABZO - Sistema de Gestión</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: #333; margin-top: 0;">Estimado/a ' . htmlspecialchars($nombre) . ',</h2>
            
            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                Nos complace informarle que su cuenta en el <strong>Sistema de Gestión GRUPO KABZO</strong> 
                ha sido activada exitosamente.
            </p>
            
            <div style="background: #f0f8ff; border-left: 4px solid #D4AF37; padding: 15px; margin: 20px 0;">
                <h3 style="color: #B8860B; margin: 0 0 10px 0; font-size: 18px;">Información de su cuenta:</h3>
                <p style="margin: 8px 0; color: #555;">
                    • <strong>Nombre:</strong> ' . htmlspecialchars($nombre) . '<br>
                    • <strong>Email:</strong> ' . htmlspecialchars($email) . '<br>
                    • <strong>Estado:</strong> <span style="color: #16a34a; font-weight: bold;">ACTIVA</span><br>
                    • <strong>Fecha de activación:</strong> ' . date('d/m/Y') . '
                </p>
            </div>
            
            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                Ahora puede acceder a todas las funcionalidades del sistema con sus credenciales. 
                Si tiene alguna pregunta o necesita asistencia, no dude en contactar al área de Desarrollo.
            </p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="https://kabzo.ddns.net/sistemas" style="background: #D4AF37; color: black; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 0 10px; border: 2px solid #B8860B;">
                    Acceder al Sistema
                </a>
            </div>
            
            <div style="background: #fff9e6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h4 style="color: #B8860B; margin: 0 0 10px 0;">¿Necesita ayuda?</h4>
                <p style="margin: 5px 0; color: #555; font-size: 14px;">
                    • Contacte al área de Desarrollo: desarrollo@kabzo.org<br>
                    • Visite nuestro portal: https://kabzo.ddns.net/sistemas<br>
                    • Horario de atención: Lunes a Viernes 9:00 - 18:00
                </p>
            </div>
            
            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                Bienvenido/a al sistema y gracias por ser parte de <strong>GRUPO KABZO</strong>.
            </p>
            
            <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
                <p style="color: #777; margin: 5px 0;">Atentamente,</p>
                <p style="color: #D4AF37; font-weight: bold; margin: 5px 0;">Área de Desarrollo - GRUPO KABZO</p>
            </div>
        </div>
        
        <div style="text-align: center; padding: 20px; color: #777; font-size: 14px; background: #000; color: #D4AF37; border-radius: 0 0 10px 10px;">
            <p style="margin: 0;">© ' . date('Y') . ' GRUPO KABZO. Todos los derechos reservados.</p>
            <p style="margin: 5px 0 0; font-size: 12px; opacity: 0.8;">Este es un mensaje automático, por favor no responda a este correo.</p>
        </div>
    </body>
    </html>';
}
?>