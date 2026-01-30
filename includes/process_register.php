<?php
include 'conexionbd.php';
// Incluir PHPMailer
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $contrasena = $_POST['password'];
    $verificacionContrasena = $_POST['verificacionContrasena'];
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $departamento = $_POST['departamento'];
    $oficina = $_POST['oficina'];
    $puesto = $_POST['puesto'];
    $fechaRegistro = date('Y-m-d H:i:s');
    $estatu = 0;
    $rol = 10;

    if ($contrasena !== $verificacionContrasena) {
        echo json_encode(['status' => 'error', 'message' => 'Las contraseñas no coinciden.']);
        exit();
    }

    // Verificar si el correo ya existe
    $stmtCheck = $conexion->prepare("SELECT Id_Usuario FROM usuarios WHERE email = ?");
    $stmtCheck->execute([$email]);
    if ($stmtCheck->fetch(PDO::FETCH_ASSOC)) {
        echo json_encode(['status' => 'error', 'message' => 'Este correo ya está registrado.']);
        exit();
    }

    $hashedPassword = password_hash($contrasena, PASSWORD_DEFAULT);

    try {
        // Obtener nombres de departamento, oficina y puesto para el correo
        $stmtDept = $conexion->prepare("SELECT nombre FROM departamentos WHERE id_departamento = ?");
        $stmtDept->execute([$departamento]);
        $departamentoNombre = $stmtDept->fetch(PDO::FETCH_ASSOC)['nombre'];

        $stmtOficina = $conexion->prepare("SELECT nombre FROM oficina WHERE id_oficina = ?");
        $stmtOficina->execute([$oficina]);
        $oficinaNombre = $stmtOficina->fetch(PDO::FETCH_ASSOC)['nombre'];

        $stmtPuesto = $conexion->prepare("SELECT nombre FROM puestos WHERE id_puesto = ?");
        $stmtPuesto->execute([$puesto]);
        $puestoNombre = $stmtPuesto->fetch(PDO::FETCH_ASSOC)['nombre'];

        // Insertar en tabla usuarios
        $query = "INSERT INTO usuarios (email, contrasena, nombre, apellido, fechaRegistro, Id_puesto, Id_departamento, Id_oficina, estatu, rolActual) 
                  VALUES (:email, :password, :nombre, :apellido, :fechaRegistro, :Id_puesto, :Id_departamento, :Id_oficina, :estatu, :rol)";
        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':fechaRegistro', $fechaRegistro);
        $stmt->bindParam(':Id_puesto', $puesto);
        $stmt->bindParam(':Id_departamento', $departamento);
        $stmt->bindParam(':Id_oficina', $oficina);
        $stmt->bindParam(':estatu', $estatu);
        $stmt->bindParam(':rol', $rol);

        if ($stmt->execute()) {
            $idUsuario = $conexion->lastInsertId();

            // Insertar en tabla usuarios_departamentos
            $queryDept = "INSERT INTO usuarios_departamentos (Id_usuario, Id_departamento) VALUES (:idUsuario, :idDepartamento)";
            $stmtDept = $conexion->prepare($queryDept);
            $stmtDept->bindParam(':idUsuario', $idUsuario);
            $stmtDept->bindParam(':idDepartamento', $departamento);
            $stmtDept->execute();

            // Enviar correos
            $correoUsuario = enviarCorreoUsuario($nombre, $apellido, $email, $puestoNombre, $departamentoNombre, $oficinaNombre);
            $correoAdmin = enviarCorreoAdministrador($nombre, $apellido, $email, $puestoNombre, $departamentoNombre, $oficinaNombre);

            $mensaje = 'Usuario registrado correctamente y asignado a su departamento.';
            
            if ($correoUsuario) {
                $mensaje .= ' Se envió correo de confirmación al usuario.';
            } else {
                $mensaje .= ' Error al enviar correo al usuario.';
            }
            
            if ($correoAdmin) {
                $mensaje .= ' Se notificó al administrador.';
            } else {
                $mensaje .= ' Error al notificar al administrador.';
            }

            echo json_encode(['status' => 'success', 'message' => $mensaje]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al registrar el usuario.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
}

// Función para enviar correo al usuario que se registra
function enviarCorreoUsuario($nombre, $apellido, $email, $puesto, $departamento, $oficina) {
    $SMTP_HOST = 'smtp.gmail.com';
    $SMTP_USER = 'desarrollo@kabzo.org';
    $SMTP_PASS = 'ydwt bjxd eagx jezu';
    $SMTP_PORT = 587;
    $SMTP_SECURE = 'tls';
    
    $nombreCompleto = $nombre . ' ' . $apellido;
    
    $htmlContent = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro Exitoso - GRUPO KABZO</title>
    </head>
    <body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px;">
        <div style="background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">REGISTRO EXITOSO</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">GRUPO KABZO - Sistema de Gestión</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: #333; margin-top: 0;">¡Bienvenido/a ' . htmlspecialchars($nombre) . '!</h2>
            
            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                Su registro en el <strong>Sistema de Gestión GRUPO KABZO</strong> ha sido exitoso. 
                Su cuenta está pendiente de activación por el administrador.
            </p>
            
            <div style="background: #f0f8ff; border-left: 4px solid #D4AF37; padding: 15px; margin: 20px 0;">
                <h3 style="color: #B8860B; margin: 0 0 10px 0; font-size: 18px;">Detalles de su registro:</h3>
                <p style="margin: 8px 0; color: #555;">
                    • <strong>Nombre completo:</strong> ' . htmlspecialchars($nombreCompleto) . '<br>
                    • <strong>Email:</strong> ' . htmlspecialchars($email) . '<br>
                    • <strong>Puesto:</strong> ' . htmlspecialchars($puesto) . '<br>
                    • <strong>Departamento:</strong> ' . htmlspecialchars($departamento) . '<br>
                    • <strong>Oficina:</strong> ' . htmlspecialchars($oficina) . '<br>
                    • <strong>Fecha de registro:</strong> ' . date('d/m/Y H:i') . '<br>
                    • <strong>Estado:</strong> <span style="color: #f59e0b; font-weight: bold;">PENDIENTE DE ACTIVACIÓN</span>
                </p>
            </div>
            
            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                <strong>Próximos pasos:</strong><br>
                1. El administrador revisará su solicitud de registro<br>
                2. Recibirá un correo cuando su cuenta sea activada<br>
                3. Podrá acceder al sistema con sus credenciales
            </p>
            
            <div style="background: #fff9e6; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <h4 style="color: #B8860B; margin: 0 0 10px 0;">Información importante:</h4>
                <p style="margin: 5px 0; color: #555; font-size: 14px;">
                    • Este proceso puede tomar de 2 a 6 horas<br>
                    • Mantenga sus credenciales en un lugar seguro<br>
                    • Contacte a desarrollo@kabzo.org si tiene dudas
                </p>
            </div>
            
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

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $SMTP_USER;
        $mail->Password = $SMTP_PASS;
        $mail->SMTPSecure = $SMTP_SECURE === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $SMTP_PORT;
        
        $mail->setFrom($SMTP_USER, 'GRUPO KABZO - Sistema');
        $mail->addAddress($email, $nombreCompleto);
        
        $mail->isHTML(true);
        $mail->Subject = 'Registro Exitoso - GRUPO KABZO';
        $mail->Body = $htmlContent;
        $mail->AltBody = strip_tags($htmlContent);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Error al enviar correo al usuario: " . $mail->ErrorInfo);
        return false;
    }
}

// Función para enviar correo al administrador
function enviarCorreoAdministrador($nombre, $apellido, $email, $puesto, $departamento, $oficina) {
    $SMTP_HOST = 'smtp.gmail.com';
    $SMTP_USER = 'desarrollo@kabzo.org';
    $SMTP_PASS = 'ydwt bjxd eagx jezu';
    $SMTP_PORT = 587;
    $SMTP_SECURE = 'tls';
    
    $nombreCompleto = $nombre . ' ' . $apellido;
    
    $htmlContent = '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nuevo Registro de Usuario - GRUPO KABZO</title>
    </head>
    <body style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px;">
        <div style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">NUEVO REGISTRO DE USUARIO</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">GRUPO KABZO - Sistema de Gestión</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: #333; margin-top: 0;">Se ha registrado un nuevo usuario</h2>
            
            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                Un nuevo usuario se ha registrado en el sistema y requiere activación de cuenta.
            </p>
            
            <div style="background: #fef2f2; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0;">
                <h3 style="color: #dc2626; margin: 0 0 10px 0; font-size: 18px;">Información del nuevo usuario:</h3>
                <p style="margin: 8px 0; color: #555;">
                    • <strong>Nombre completo:</strong> ' . htmlspecialchars($nombreCompleto) . '<br>
                    • <strong>Email:</strong> ' . htmlspecialchars($email) . '<br>
                    • <strong>Puesto:</strong> ' . htmlspecialchars($puesto) . '<br>
                    • <strong>Departamento:</strong> ' . htmlspecialchars($departamento) . '<br>
                    • <strong>Oficina:</strong> ' . htmlspecialchars($oficina) . '<br>
                    • <strong>Fecha de registro:</strong> ' . date('d/m/Y H:i') . '<br>
                    • <strong>Estado actual:</strong> <span style="color: #f59e0b; font-weight: bold;">PENDIENTE DE ACTIVACIÓN</span>
                </p>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="http://38.65.143.27/sistemas/dashboard/Users.php" style="background: #dc2626; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 0 10px; border: 2px solid #b91c1c;">
                    Gestionar Usuarios
                </a>
            </div>
            
            <p style="color: #555; line-height: 1.6; font-size: 14px;">
                <strong>Acción requerida:</strong> Por favor, revise la información del usuario y active su cuenta en el panel de administración.
            </p>
            
            <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
                <p style="color: #777; margin: 5px 0;">Sistema Automático de Notificaciones</p>
                <p style="color: #dc2626; font-weight: bold; margin: 5px 0;">GRUPO KABZO - Área de Desarrollo</p>
            </div>
        </div>
        
        <div style="text-align: center; padding: 20px; color: #777; font-size: 14px; background: #000; color: #D4AF37; border-radius: 0 0 10px 10px;">
            <p style="margin: 0;">© ' . date('Y') . ' GRUPO KABZO. Todos los derechos reservados.</p>
            <p style="margin: 5px 0 0; font-size: 12px; opacity: 0.8;">Este es un mensaje automático del sistema.</p>
        </div>
    </body>
    </html>';

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $SMTP_USER;
        $mail->Password = $SMTP_PASS;
        $mail->SMTPSecure = $SMTP_SECURE === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $SMTP_PORT;
        
        $mail->setFrom($SMTP_USER, 'GRUPO KABZO - Sistema');
        $mail->addAddress('desarrollo@kabzo.org', 'Administrador KABZO');
        // Puedes agregar más administradores si es necesario
        // $mail->addAddress('otro-admin@kabzo.org', 'Otro Administrador');
        
        $mail->isHTML(true);
        $mail->Subject = 'Nuevo Registro de Usuario - Requiere Activación';
        $mail->Body = $htmlContent;
        $mail->AltBody = strip_tags($htmlContent);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Error al enviar correo al administrador: " . $mail->ErrorInfo);
        return false;
    }
}
?>