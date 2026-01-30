<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit', '1024M');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/middleware.php';
verificarSesion();
include "../includes/conexionbd.php";

// Obtener información del usuario para permisos
$user_id = $_SESSION["user_id"];
$stmt = $conexion->prepare("SELECT rolActual FROM usuarios WHERE Id_Usuario = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Usuario no encontrado");
}

$rolActual = $user['rolActual'];

// Verificar permisos para esta sección
$stmtPermissions = $conexion->prepare("
    SELECT DISTINCT p.nombre 
    FROM permisos p
    JOIN permisos_modelos pm ON p.id = pm.permiso_id
    WHERE pm.rol_id = :rol_id
");
$stmtPermissions->bindParam(':rol_id', $rolActual, PDO::PARAM_INT);
$stmtPermissions->execute();
$permissions = $stmtPermissions->fetchAll(PDO::FETCH_ASSOC);

$canView = in_array('ver', array_column($permissions, 'nombre'));
$canEdit = in_array('editar', array_column($permissions, 'nombre'));

if (!$canView) {
    die("No tienes permisos para acceder a esta sección");
}

// Configuración PHPMailer
ini_set('display_errors', 1);
error_reporting(E_ALL);
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Config SMTP
$SMTP_HOST = 'smtp.gmail.com';
$SMTP_USER = 'desarrollo@kabzo.org';
$SMTP_PASS = 'ydwt bjxd eagx jezu';
$SMTP_PORT = 587;
$SMTP_SECURE = 'tls';

// Defaults
$default_batch_size = 30;
$default_sleep_seconds = 2;

// Clase para manejar el envío de correos con filtros de base de datos
class EmailSenderDB {
    private $db;
    private $report = [];
    
    public function __construct($db) {
        $this->db = $db;
        $this->report = [
            'total' => 0,
            'sent' => 0,
            'failed' => 0,
            'details' => []
        ];
    }
    
    // Obtener usuarios registrados pero sin verificar (revisado != 2)
    public function getUsersPendingVerification() {
        $query = "SELECT u.Id_Usuario as id, u.email, u.nombre, c.revisado 
                  FROM usuarios u 
                  INNER JOIN computadora c ON u.email = c.correo_asociado 
                  WHERE u.email IS NOT NULL AND u.email != '' 
                  AND (c.revisado IS NULL OR c.revisado != 2)
                  AND u.estatu = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ✅ CORREGIDO: Mapear nombre correctamente
        $formatted = [];
        foreach ($results as $row) {
            $formatted[] = [
                'id' => $row['id'],
                'email' => $row['email'],
                'nombre' => $row['nombre'] ?: 'Usuario' // ✅ Usar 'nombre' que es el campo real
            ];
        }
        return $formatted;
    }
    
    // Obtener usuarios ya verificados (revisado = 2)
    public function getVerifiedUsers() {
        $query = "SELECT DISTINCT u.Id_Usuario AS id, u.email, u.nombre 
                  FROM usuarios u
                  INNER JOIN computadora c ON u.email = c.correo_asociado
                  WHERE c.revisado = 2 
                  AND u.email IS NOT NULL 
                  AND u.email != ''
                  AND u.estatu = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ✅ CORREGIDO: Mapear nombre correctamente
        $formatted = [];
        foreach ($results as $row) {
            $formatted[] = [
                'id' => $row['id'],
                'email' => $row['email'],
                'nombre' => $row['nombre'] ?: 'Usuario' // ✅ Usar 'nombre' que es el campo real
            ];
        }
        return $formatted;
    }

    // Obtener todos los usuarios con email
    public function getAllUsersWithEmail() {
        $query = "SELECT u.Id_Usuario as id, u.email, u.nombre 
                  FROM usuarios u 
                  WHERE u.email IS NOT NULL AND u.email != ''
                  AND u.estatu = 1";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ✅ CORREGIDO: Mapear nombre correctamente
        $formatted = [];
        foreach ($results as $row) {
            $formatted[] = [
                'id' => $row['id'],
                'email' => $row['email'],
                'nombre' => $row['nombre'] ?: 'Usuario' // ✅ Usar 'nombre' que es el campo real
            ];
        }
        return $formatted;
    }
    
    // Obtener correos asociados de computadora que no están registrados como usuarios
    public function getUnregisteredEmails() {
        $query = "SELECT c.correo_asociado as email, c.asignado_a, c.id_computadora as id
                  FROM computadora c 
                  WHERE c.correo_asociado IS NOT NULL 
                  AND c.correo_asociado != '' 
                  AND NOT EXISTS (
                      SELECT 1 FROM usuarios u 
                      WHERE u.email = c.correo_asociado 
                      AND u.estatu = 1
                  )";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ✅ CORREGIDO: Usar 'asignado_a' que es el campo real en computadora
        $formatted = [];
        foreach ($results as $row) {
            $formatted[] = [
                'id' => $row['id'],
                'email' => $row['email'],
                'nombre' => $row['asignado_a'] ?: 'Usuario' // ✅ Usar 'asignado_a' que es el campo real
            ];
        }
        
        return $formatted;
    }
    
    // Registrar envío en la base de datos
    public function logEmailSent($userId, $subject, $status, $error = '', $body = '', $attempts = 1) {
        try {
            $query = "INSERT INTO email_logs (user_id, subject, body, sent_at, status, error_message, attempts)
                      VALUES (:user_id, :subject, :body, NOW(), :status, :error, :attempts)";
            
            $stmt = $this->db->prepare($query);
            $ok = $stmt->execute([
                ':user_id' => $userId,
                ':subject' => $subject,
                ':body' => $body,
                ':status' => $status,
                ':error' => $error,
                ':attempts' => $attempts
            ]);

        } catch (PDOException $e) {
            error_log("Error al registrar email: " . $e->getMessage());
        }
    }

    // Obtener estadísticas de envíos
    public function getEmailStats() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'enviado' THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as failed
                  FROM email_logs 
                  WHERE DATE(sent_at) = CURDATE()";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Obtener conteo de usuarios por tipo
    public function getUserCounts() {
        $pending = count($this->getUsersPendingVerification());
        $verified = count($this->getVerifiedUsers());
        $all = count($this->getAllUsersWithEmail());
        $unregistered = count($this->getUnregisteredEmails());
        
        return [
            'pending' => $pending,
            'verified' => $verified,
            'all' => $all,
            'unregistered' => $unregistered
        ];
    }
}

// Helpers
function parse_emails($text){
    $text = str_replace(["\r",";"], ["",","], $text);
    $parts = preg_split('/[\n,]+/', $text);
    $emails = [];
    foreach($parts as $p){
        $p = trim($p);
        if(!$p) continue;
        if(preg_match('/(.*)<([^>]+)>/', $p, $m)){
            $emails[] = [
                'email' => trim($m[2]), 
                'nombre' => trim($m[1]), // ✅ CORREGIDO: usar 'nombre'
                'id' => 0
            ];
        }else{
            $emails[] = [
                'email' => $p,
                'nombre' => '', // ✅ CORREGIDO: usar 'nombre'
                'id' => 0
            ];
        }
    }
    return $emails;
}

function parse_csv($tmp){
    $rows=[];
    if(($handle=fopen($tmp,"r"))!==FALSE){
        while(($data=fgetcsv($handle,1000,","))!==FALSE){
            if(count($data)>=1){
                $email=trim($data[0]);
                $name=isset($data[1])?trim($data[1]):'';
                if($email)$rows[]= [
                    'email' => $email,
                    'nombre' => $name, // ✅ CORREGIDO: usar 'nombre'
                    'id' => 0
                ];
            }
        }
        fclose($handle);
    }
    return $rows;
}

// Inicializar el sistema de correos con base de datos
$emailSenderDB = new EmailSenderDB($conexion);
$userCounts = $emailSenderDB->getUserCounts();
$emailStats = $emailSenderDB->getEmailStats();

$report = [];
$report = [
    'total' => 0,
    'sent' => 0,
    'failed' => 0,
    'error_message' => '',
    'details' => []
];

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='send'){
    if (!$canEdit) {
        die("No tienes permisos para enviar correos");
    }
    
    $from_email = $_POST['from_email']??$SMTP_USER;
    $from_name  = $_POST['from_name']??'GRUPO KABZO';
    $subject    = $_POST['subject']??'(sin asunto)';
    $body_raw   = $_POST['body']??'';
    $recipient_type = $_POST['recipient_type']??'manual';
    $batch_size = max(1,(int)($_POST['batch_size']??$default_batch_size));
    $sleep_seconds = max(0,(int)($_POST['sleep_seconds']??$default_sleep_seconds));
    $max_retries = max(0,(int)($_POST['max_retries']??1));

    // Obtener destinatarios según el tipo seleccionado
    $recipients = [];
    if ($recipient_type === 'pending_verification') {
        $recipients = $emailSenderDB->getUsersPendingVerification();
    } elseif ($recipient_type === 'verified') {
        $recipients = $emailSenderDB->getVerifiedUsers();
    } elseif ($recipient_type === 'all_users') {
        $recipients = $emailSenderDB->getAllUsersWithEmail();
    } elseif ($recipient_type === 'unregistered') {
        $recipients = $emailSenderDB->getUnregisteredEmails();
    } else {
        // Lista manual de emails
        $recipients_text = $_POST['recipients']??'';
        $recipients = parse_emails($recipients_text);

        if(!empty($_FILES['csv_file']['tmp_name']) && $_FILES['csv_file']['error']===UPLOAD_ERR_OK){
            $csv_recip = parse_csv($_FILES['csv_file']['tmp_name']);
            $recipients = array_merge($recipients, $csv_recip);
        }
    }

    // Filtrar emails duplicados y válidos
    $final_recipients=[];
    $seen=[];
    foreach($recipients as $r){
        // ✅ CORREGIDO: Usar 'nombre' en lugar de 'name'
        $email = isset($r['email']) ? filter_var($r['email'], FILTER_SANITIZE_EMAIL) : '';
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)) continue;
        if(isset($seen[strtolower($email)])) continue;
        $seen[strtolower($email)]=true;
        
        // ✅ CORREGIDO: Usar 'nombre' en lugar de 'name'
        $final_recipients[]=[
            'id' => $r['id'] ?? null,
            'email' => $email,
            'nombre' => $r['nombre'] ?? '' // ✅ CORREGIDO: usar 'nombre'
        ];
    }

    $report['total']=count($final_recipients);
    $counter=0;

    foreach($final_recipients as $r){
        $counter++;
        $attempt=0; $sent_ok=false; $last_error='';
        
        // ✅ CORREGIDO: Inicializar con 'nombre'
        $recipientName = 'Estimado Usuario';
        
        while($attempt<=$max_retries && !$sent_ok){
            $attempt++;
            $mail = new PHPMailer(true);
            try{
                $mail->isSMTP();
                $mail->Host=$SMTP_HOST;
                $mail->SMTPAuth=true;
                $mail->Username=$SMTP_USER;
                $mail->Password=$SMTP_PASS;
                $mail->SMTPSecure=$SMTP_SECURE==='ssl'?PHPMailer::ENCRYPTION_SMTPS:PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port=$SMTP_PORT;
                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';
                
                $mail->setFrom($from_email,$from_name);
                
                // ✅ CORREGIDO: Usar 'nombre' en lugar de 'name'
                $recipientName = isset($r['nombre']) ? $r['nombre'] : '';
                $recipientName = $recipientName ?: 'Estimado Usuario';
                
                // Limpiar el nombre de caracteres problemáticos
                $recipientName = preg_replace('/[^\p{L}\p{N}\s\.\-]/u', '', $recipientName);
                $recipientName = trim($recipientName);
                
                if(empty($recipientName)) {
                    $recipientName = 'Estimado Usuario';
                }
                
                $mail->addAddress($r['email'], $recipientName);
                $mail->isHTML(true);
                $mail->Subject=$subject;
                
                $body=str_replace(
                    ['{{email}}','{{name}}'],
                    [$r['email'], $recipientName],
                    $body_raw
                );
                
                $mail->Body=$body;
                $mail->AltBody=strip_tags($body);
                $mail->send();
                $sent_ok=true;
                $report['sent']++;
                $report['details'][]=[
                    'email'=>$r['email'],
                    'name'=>$recipientName,
                    'status'=>'enviado',
                    'attempts'=>$attempt,
                    'error'=>''
                ];
                
                // Registrar envío exitoso
                $emailSenderDB->logEmailSent($r['id'] ?? null, $subject, 'enviado', '', $body, $attempt);

            }catch(Exception $e){
                $last_error = $mail->ErrorInfo?:$e->getMessage();
                if($attempt<=$max_retries) sleep(1);
            }
        }
        if(!$sent_ok){
            $report['failed']++;
            $report['details'][]=[
                'email'=>$r['email'],
                'name'=>$recipientName,
                'status'=>'error',
                'attempts'=>$attempt,
                'error'=>$last_error
            ];
            
            // Registrar error
            $emailSenderDB->logEmailSent($r['id'] ?? null, $subject, 'error', $last_error, $body, $attempt);
        }
        if($batch_size>0 && $counter%$batch_size===0) sleep($sleep_seconds);
    }
}

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envio Correos - Sistema KABZO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-lite.min.js"></script>
    
    <style>
        .email-card {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            border: 1px solid #e5e7eb;
            transition: all 0.3s ease;
        }
        .email-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }
        .tab-email {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }
        .tab-email.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
        }
        .tab-email:hover:not(.active) {
            color: #1d4ed8;
        }
        .tab-content-email {
            display: none;
        }
        .tab-content-email.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .template-card {
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .template-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
            border-color: #3b82f6;
        }
        .template-card.active {
            border-color: #3b82f6;
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.2);
        }
        .badge-email {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
        }
        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }
        .stat-email {
            text-align: center;
            padding: 1rem;
            border-radius: 0.75rem;
            background: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }
        .note-editor.note-frame {
            border: 1px solid #d1d5db !important;
            border-radius: 0.5rem !important;
        }
        .preview-container {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1.5rem;
            background: white;
            max-height: 400px;
            overflow-y: auto;
        }
        .recipient-option {
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .recipient-option:hover, .recipient-option.active {
            border-color: #3b82f6;
            background-color: #eff6ff;
        }
        .recipient-option.active {
            border-width: 3px;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <?php include 'includes/nav.php' ?>

    <div class="flex">
        <!-- Sidebar -->
        <?php include 'includes/aside.php' ?>
        
        <!-- Main Content -->
        <main class="flex-1 p-6">
            <!-- Header -->

            <?php if (!$canEdit): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <span class="text-yellow-800">Tienes permisos de solo lectura. No puedes enviar correos.</span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Estadísticas rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-paper-plane text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Envíos Hoy</p>
                            <p class="text-xl font-bold text-gray-900"><?php echo $emailStats['total'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-clock text-orange-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Pendientes Verificación</p>
                            <p class="text-xl font-bold text-gray-900"><?php echo $userCounts['pending']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Verificados</p>
                            <p class="text-xl font-bold text-gray-900"><?php echo $userCounts['verified']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-user-plus text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">No Registrados</p>
                            <p class="text-xl font-bold text-gray-900"><?php echo $userCounts['unregistered']; ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-users text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Usuarios</p>
                            <p class="text-xl font-bold text-gray-900"><?php echo $userCounts['all']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sistema de pestañas -->
            <div class="email-card mb-6">
                <div class="border-b border-gray-200">
                    <div class="flex overflow-x-auto">
                        <div class="tab-email active" data-tab="recipients">
                            <i class="fas fa-users mr-2"></i>Destinatarios
                        </div>
                        <div class="tab-email" data-tab="content">
                            <i class="fas fa-edit mr-2"></i>Contenido
                        </div>
                        <div class="tab-email" data-tab="preview">
                            <i class="fas fa-eye mr-2"></i>Vista Previa
                        </div>
                        <div class="tab-email" data-tab="send">
                            <i class="fas fa-paper-plane mr-2"></i>Enviar
                        </div>
                    </div>
                </div>

                <form method="post" enctype="multipart/form-data" id="emailForm" class="p-6">
                    <input type="hidden" name="action" value="send">

                    <!-- Pestaña Destinatarios -->
                    <div class="tab-content-email active" id="recipients-tab">
                        <h3 class="text-lg font-semibold mb-4">Seleccionar Destinatarios</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <!-- Opción 1: Usuarios pendientes de verificación -->
                            <div class="recipient-option" data-type="pending_verification">
                                <input type="radio" name="recipient_type" value="pending_verification" id="pending_verification" class="hidden">
                                <label for="pending_verification" class="cursor-pointer block">
                                    <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-exclamation-triangle text-orange-600"></i>
                                        </div>
                                        <h4 class="font-semibold text-gray-800">Pendientes de Verificación</h4>
                                    </div>
                                    <p class="text-sm text-gray-600 ml-11">
                                        Usuarios registrados que no han verificado su equipo (revisado ≠ 2)
                                    </p>
                                    <div class="mt-2 text-xs text-blue-600 ml-11">
                                        <i class="fas fa-database mr-1"></i>
                                        <?php echo $userCounts['pending']; ?> usuarios encontrados
                                    </div>
                                </label>
                            </div>

                            <!-- Opción 2: Usuarios verificados -->
                            <div class="recipient-option" data-type="verified">
                                <input type="radio" name="recipient_type" value="verified" id="verified" class="hidden">
                                <label for="verified" class="cursor-pointer block">
                                    <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-check-circle text-green-600"></i>
                                        </div>
                                        <h4 class="font-semibold text-gray-800">Usuarios Verificados</h4>
                                    </div>
                                    <p class="text-sm text-gray-600 ml-11">
                                        Usuarios que ya completaron la verificación (revisado = 2)
                                    </p>
                                    <div class="mt-2 text-xs text-blue-600 ml-11">
                                        <i class="fas fa-database mr-1"></i>
                                        <?php echo $userCounts['verified']; ?> usuarios encontrados
                                    </div>
                                </label>
                            </div>

                            <!-- Opción 3: Correos no registrados -->
                            <div class="recipient-option" data-type="unregistered">
                                <input type="radio" name="recipient_type" value="unregistered" id="unregistered" class="hidden">
                                <label for="unregistered" class="cursor-pointer block">
                                    <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user-plus text-red-600"></i>
                                        </div>
                                        <h4 class="font-semibold text-gray-800">Correos No Registrados</h4>
                                    </div>
                                    <p class="text-sm text-gray-600 ml-11">
                                        Correos en tabla computadora (correo_asociado) sin usuario registrado
                                    </p>
                                    <div class="mt-2 text-xs text-blue-600 ml-11">
                                        <i class="fas fa-database mr-1"></i>
                                        <?php echo $userCounts['unregistered']; ?> correos encontrados
                                    </div>
                                </label>
                            </div>

                            <!-- Opción 4: Todos los usuarios -->
                            <div class="recipient-option" data-type="all_users">
                                <input type="radio" name="recipient_type" value="all_users" id="all_users" class="hidden">
                                <label for="all_users" class="cursor-pointer block">
                                    <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-users text-blue-600"></i>
                                        </div>
                                        <h4 class="font-semibold text-gray-800">Todos los Usuarios</h4>
                                    </div>
                                    <p class="text-sm text-gray-600 ml-11">
                                        Todos los usuarios activos con correo electrónico
                                    </p>
                                    <div class="mt-2 text-xs text-blue-600 ml-11">
                                        <i class="fas fa-database mr-1"></i>
                                        <?php echo $userCounts['all']; ?> usuarios encontrados
                                    </div>
                                </label>
                            </div>

                            <!-- Opción 5: Lista manual -->
                            <div class="recipient-option active" data-type="manual">
                                <input type="radio" name="recipient_type" value="manual" id="manual" class="hidden" checked>
                                <label for="manual" class="cursor-pointer block">
                                    <div class="flex items-center mb-2">
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-edit text-purple-600"></i>
                                        </div>
                                        <h4 class="font-semibold text-gray-800">Lista Manual</h4>
                                    </div>
                                    <p class="text-sm text-gray-600 ml-11">
                                        Ingresa manualmente los correos o sube un archivo CSV
                                    </p>
                                </label>
                            </div>
                        </div>

                        <!-- Lista manual de emails (solo se muestra para opción manual) -->
                        <div id="manual_emails_section">
                            <div class="space-y-4">
                                <div>
                                    <label for="recipients" class="block text-sm font-medium text-gray-700 mb-2">
                                        Destinatarios (uno por línea o separados por comas)
                                    </label>
                                    <textarea id="recipients" name="recipients" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                              rows="4" placeholder="ejemplo@dominio.com, Juan Pérez <juan@ejemplo.com>"></textarea>
                                    <p class="text-sm text-gray-500 mt-1">Formato: email o "Nombre" &lt;email&gt;</p>
                                </div>
                                
                                <div>
                                    <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                                        O sube un archivo CSV
                                    </label>
                                    <input type="file" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           id="csv_file" name="csv_file" accept=".csv">
                                    <p class="text-sm text-gray-500 mt-1">El CSV debe tener al menos una columna con emails, y opcionalmente nombres en la segunda columna</p>
                                </div>
                            </div>
                        </div>

                        <!-- Configuración del Remitente -->
                        <div class="mt-6 email-card p-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Configuración del Remitente</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="from_email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email del Remitente
                                    </label>
                                    <input type="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           id="from_email" name="from_email" value="<?php echo htmlspecialchars($SMTP_USER); ?>" required>
                                </div>
                                <div>
                                    <label for="from_name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre del Remitente
                                    </label>
                                    <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           id="from_name" name="from_name" value="GRUPO KABZO" required>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 next-tab-email" data-next="content">
                                Siguiente <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Pestaña Contenido -->
                    <div class="tab-content-email" id="content-tab">
                        <h3 class="text-lg font-semibold mb-4">Contenido del Correo</h3>
                        
                        <!-- Asunto -->
                        <div class="mb-4">
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Asunto del Correo
                            </label>
                            <input type="text" id="subject" name="subject" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Ingresa el asunto del correo" value="Verificación de Equipo de Cómputo - GRUPO KABZO">
                        </div>

                        <!-- Plantillas -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">Plantillas Disponibles</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Plantilla Recordatorio Verificación -->
                                <div class="template-card active" data-template="verification_reminder">
                                    <div class="flex items-center mb-2 p-4">
                                        <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-desktop text-orange-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">Recordatorio Verificación</h4>
                                            <p class="text-sm text-gray-600">Para usuarios pendientes de verificar equipo</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Plantilla Invitación Registro -->
                                <div class="template-card" data-template="registration_invitation">
                                    <div class="flex items-center mb-2 p-4">
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user-plus text-red-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">Invitación Registro</h4>
                                            <p class="text-sm text-gray-600">Para correos no registrados</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Plantilla Notificación General -->
                                <div class="template-card" data-template="general_notice">
                                    <div class="flex items-center mb-2 p-4">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-bullhorn text-blue-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-800">Aviso General</h4>
                                            <p class="text-sm text-gray-600">Para comunicación general con usuarios</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Cuerpo del correo -->
                        <div class="mb-6">
                            <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
                                Cuerpo del Mensaje
                            </label>
                            <textarea id="body" name="body" class="form-control" style="height: 300px;"></textarea>
                            <p class="text-sm text-gray-500 mt-2">
                                Usa <code class="bg-gray-100 px-1 rounded">{{name}}</code> y <code class="bg-gray-100 px-1 rounded">{{email}}</code> para personalizar cada correo
                            </p>
                        </div>

                        <!-- Configuración de Envío -->
                        <div class="email-card p-6 mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4">Configuración de Envío</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="batch_size" class="block text-sm font-medium text-gray-700 mb-2">
                                        Tamaño del Lote
                                    </label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           id="batch_size" name="batch_size" value="<?php echo $default_batch_size; ?>" min="1">
                                    <p class="text-sm text-gray-500 mt-1">Correos por lote</p>
                                </div>
                                <div>
                                    <label for="sleep_seconds" class="block text-sm font-medium text-gray-700 mb-2">
                                        Pausa (segundos)
                                    </label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           id="sleep_seconds" name="sleep_seconds" value="<?php echo $default_sleep_seconds; ?>" min="0" step="1">
                                    <p class="text-sm text-gray-500 mt-1">Tiempo entre lotes</p>
                                </div>
                                <div>
                                    <label for="max_retries" class="block text-sm font-medium text-gray-700 mb-2">
                                        Máx. Reintentos
                                    </label>
                                    <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           id="max_retries" name="max_retries" value="1" min="0">
                                    <p class="text-sm text-gray-500 mt-1">Intentos por error</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-6">
                            <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 prev-tab-email" data-prev="recipients">
                                <i class="fas fa-arrow-left mr-2"></i> Anterior
                            </button>
                            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 next-tab-email" data-next="preview">
                                Siguiente <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Pestaña Vista Previa -->
                    <div class="tab-content-email" id="preview-tab">
                        <div class="email-card p-6">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-eye text-purple-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Vista Previa del Correo</h3>
                            </div>
                            
                            <div class="preview-container mb-4">
                                <div class="flex justify-between items-center mb-4 pb-3 border-b border-gray-200">
                                    <div class="font-medium text-gray-800">Asunto: <span id="preview-subject">Comunicación importante</span></div>
                                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200" id="refreshPreview">
                                        <i class="fas fa-sync-alt mr-2"></i> Actualizar Vista Previa
                                    </button>
                                </div>
                                
                                <div id="emailPreview">
                                    <!-- Vista previa del email se cargará aquí -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-6">
                            <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 prev-tab-email" data-prev="content">
                                <i class="fas fa-arrow-left mr-2"></i> Anterior
                            </button>
                            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 next-tab-email" data-next="send">
                                Siguiente <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Pestaña Enviar -->
                    <div class="tab-content-email" id="send-tab">
                        <div class="email-card p-6">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-paper-plane text-green-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Enviar Correos</h3>
                            </div>
                            
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                                    <div>
                                        <strong>Resumen:</strong> 
                                        <span id="summary-text">Se enviarán X correos</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center">
                                <?php if ($canEdit): ?>
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-medium transition duration-200">
                                        <i class="fas fa-rocket mr-2"></i> Iniciar Envío Masivo
                                    </button>
                                <?php else: ?>
                                    <button type="button" disabled class="bg-gray-400 text-white px-8 py-3 rounded-lg font-medium cursor-not-allowed">
                                        <i class="fas fa-ban mr-2"></i> Sin permisos para enviar
                                    </button>
                                <?php endif; ?>
                                <p class="text-sm text-gray-500 mt-2">Revisa toda la configuración antes de enviar</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-start mt-6">
                            <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 prev-tab-email" data-prev="preview">
                                <i class="fas fa-arrow-left mr-2"></i> Anterior
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <?php if(!empty($report) && $_SERVER['REQUEST_METHOD']==='POST'): ?>
            <div class="email-card mt-6">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-chart-bar text-green-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Reporte de Envío</h3>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="stat-email">
                            <div class="text-2xl font-bold text-blue-600"><?php echo $report['total']; ?></div>
                            <div class="text-sm text-gray-600">Total</div>
                        </div>
                        <div class="stat-email">
                            <div class="text-2xl font-bold text-green-600"><?php echo $report['sent']; ?></div>
                            <div class="text-sm text-gray-600">Enviados</div>
                        </div>
                        <div class="stat-email">
                            <div class="text-2xl font-bold text-red-600"><?php echo $report['failed']; ?></div>
                            <div class="text-sm text-gray-600">Fallidos</div>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 font-medium text-gray-700">#</th>
                                    <th class="px-4 py-2 font-medium text-gray-700">Email</th>
                                    <th class="px-4 py-2 font-medium text-gray-700">Nombre</th>
                                    <th class="px-4 py-2 font-medium text-gray-700">Estado</th>
                                    <th class="px-4 py-2 font-medium text-gray-700">Intentos</th>
                                    <th class="px-4 py-2 font-medium text-gray-700">Error</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php $i=0; foreach($report['details'] as $d): $i++; ?>
                                <tr>
                                    <td class="px-4 py-2"><?php echo $i;?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($d['email']);?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($d['name']);?></td>
                                    <td class="px-4 py-2">
                                        <?php if($d['status']==='enviado'): ?>
                                            <span class="badge-email badge-success">enviado</span>
                                        <?php else: ?>
                                            <span class="badge-email badge-danger">error</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2"><?php echo $d['attempts'];?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($d['error']);?></td>
                                </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php
            // Traer todos los logs de emails
            $stmt = $conexion->prepare("
                SELECT 
                    e.id AS log_id,
                    e.user_id,
                    e.subject,
                    e.status,
                    e.sent_at,
                    e.attempts,
                    e.error_message,
                    u.nombre AS user_name,
                    u.email AS user_email,
                    c.correo_asociado AS comp_email,
                    c.asignado_a AS comp_name
                FROM email_logs e
                LEFT JOIN usuarios u ON e.user_id = u.Id_Usuario
                LEFT JOIN computadora c ON e.user_id = c.id_computadora
                ORDER BY e.sent_at DESC
            ");
            $stmt->execute();
            $email_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <div class="email-card mt-6">
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-laptop text-yellow-600"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Historial de Correos de Inventario</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2">#</th>
                                    <th class="px-4 py-2">Correo</th>
                                    <th class="px-4 py-2">Nombre</th>
                                    <th class="px-4 py-2">Tipo de Aviso</th>
                                    <th class="px-4 py-2">Asunto</th>
                                    <th class="px-4 py-2">Estado</th>
                                    <th class="px-4 py-2">Intentos</th>
                                    <th class="px-4 py-2">Error</th>
                                    <th class="px-4 py-2">Fecha</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <?php $i=0; foreach($email_logs as $log): $i++; 
                                    // Determinar correo y nombre
                                    if($log['user_email']){
                                        $email = $log['user_email'];
                                        $name  = $log['user_name'] ?: 'Usuario';
                                        $type  = 'Verificación de Inventario';
                                    } elseif($log['comp_email']){
                                        $email = $log['comp_email'];
                                        $name  = $log['comp_name'] ?: 'Usuario';
                                        $type  = 'Registro de Equipo';
                                    } else {
                                        $email = 'Desconocido';
                                        $name  = 'Desconocido';
                                        $type  = 'Desconocido';
                                    }
                                ?>
                                <tr>
                                    <td class="px-4 py-2"><?php echo $i;?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($email); ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($name); ?></td>
                                    <td class="px-4 py-2"><?php echo $type; ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($log['subject']); ?></td>
                                    <td class="px-4 py-2">
                                        <?php if($log['status']==='enviado'): ?>
                                            <span class="badge-email badge-success">Enviado</span>
                                        <?php else: ?>
                                            <span class="badge-email badge-danger">Error</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 py-2"><?php echo $log['attempts']; ?></td>
                                    <td class="px-4 py-2"><?php echo htmlspecialchars($log['error_message']); ?></td>
                                    <td class="px-4 py-2"><?php echo $log['sent_at']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    
    <script>
        $(document).ready(function(){
            // Inicializar Summernote
            $('#body').summernote({
                placeholder: 'Escribe tu correo aquí...',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });

            // Plantillas mejoradas con emojis
           const templates = {
    'verification_reminder': `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px;">
        <div style="background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">VERIFICACIÓN DE EQUIPO DE CÓMPUTO ASIGNADO</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">GRUPO KABZO - Área de Desarrollo</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: #333; margin-top: 0;">Estimado(a) {{name}}</h2>
            
            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                Por medio del presente, solicitamos amablemente verificar la información de su equipo de cómputo asignado, 
                incluyendo sus datos personales y configuración del sistema.
            </p>
            
            <div style="background: #fff9e6; border-left: 4px solid #D4AF37; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #B8860B; margin: 0 0 10px 0; font-size: 18px;">Instrucciones importantes:</h3>
                <div style="color: #555; line-height: 1.6;">
                    <p style="margin: 8px 0;">• <strong>Registro obligatorio:</strong> Debe registrarse en la plataforma con el correo: <strong>{{email}}</strong></p>
                    <p style="margin: 8px 0;">• Verifique cuidadosamente toda la información personal</p>
                    <p style="margin: 8px 0;">• Consulte el video tutorial para el proceso paso a paso</p>
                    <p style="margin: 8px 0;">• Una vez registrado, espere máximo una hora para la activación de la cuenta</p>
                    <p style="margin: 8px 0;">• Espere un correo de confirmación de activación</p>
                    <p style="margin: 8px 0;">• Complete la verificación antes de la fecha establecida</p>
                </div>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="https://drive.google.com/file/d/11My-PVsGv6otglkrdxaRBRIbgZFccdNE/view?usp=drivesdk" 
                   style="background: #D4AF37; color: black; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 0 10px; border: 2px solid #B8860B; font-size: 16px;">
                    Ver Video Tutorial
                </a>
                <a href="https://kabzo.ddns.net/sistemas/" 
                   style="background: #000000; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 0 10px; border: 2px solid #333; font-size: 16px;">
                    Acceder a Plataforma
                </a>
            </div>
            
            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                Este proceso es <strong>OBLIGATORIO</strong> para todos los colaboradores de <strong>GRUPO KABZO</strong>.
            </p>
            
            <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee; text-align: center;">
                <p style="color: #777; margin: 5px 0;">Atentamente,</p>
                <p style="color: #D4AF37; font-weight: bold; margin: 5px 0; font-size: 18px;">Área de Desarrollo - GRUPO KABZO</p>
            </div>
        </div>
    </div>`,

    'registration_invitation': `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px;">
        <div style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">REGISTRO DE EQUIPO DE CÓMPUTO</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">GRUPO KABZO - Área de Desarrollo</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: #333; margin-top: 0;">Estimado(a) {{name}}</h2>
            
            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                Hemos identificado que tiene un equipo de cómputo asignado pero aún no se ha registrado en nuestra plataforma.
            </p>
            
            <div style="background: #fee2e2; border-left: 4px solid #dc2626; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #b91c1c; margin: 0 0 10px 0; font-size: 18px;">Acción requerida:</h3>
                <div style="color: #555; line-height: 1.6;">
                    <p style="margin: 8px 0;">• <strong>Registro obligatorio:</strong> Debe registrarse con el correo: <strong>{{email}}</strong></p>
                    <p style="margin: 8px 0;">• Complete su registro para acceder a todos los beneficios</p>
                    <p style="margin: 8px 0;">• El registro es necesario para el seguimiento de su equipo</p>
                    <p style="margin: 8px 0;">• Consulte el video tutorial para el proceso paso a paso</p>
                    <p style="margin: 8px 0;">• Complete el registro lo antes posible</p>
                </div>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="https://drive.google.com/file/d/11My-PVsGv6otglkrdxaRBRIbgZFccdNE/view?usp=drivesdk" 
                   style="background: #dc2626; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 0 10px; border: 2px solid #b91c1c; font-size: 16px;">
                    Ver Video Tutorial
                </a>
                <a href="https://kabzo.ddns.net/sistemas/" 
                   style="background: #000000; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 0 10px; border: 2px solid #333; font-size: 16px;">
                    Registrarse en Plataforma
                </a>
            </div>
            
            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                Este proceso es <strong>OBLIGATORIO</strong> para todos los colaboradores de <strong>GRUPO KABZO</strong>.
            </p>
            
            <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee; text-align: center;">
                <p style="color: #777; margin: 5px 0;">Atentamente,</p>
                <p style="color: #dc2626; font-weight: bold; margin: 5px 0; font-size: 18px;">Área de Desarrollo - GRUPO KABZO</p>
            </div>
        </div>
    </div>`,

    'general_notice': `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px;">
        <div style="background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0;">
            <h1 style="margin: 0; font-size: 24px;">COMUNICADO IMPORTANTE</h1>
            <p style="margin: 5px 0 0; opacity: 0.9;">GRUPO KABZO - Área de Desarrollo</p>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h2 style="color: #333; margin-top: 0;">Estimado(a) {{name}}</h2>
            
            <p style="color: #555; line-height: 1.6; font-size: 16px;">
                Tenemos información importante que compartir con usted sobre el uso de equipos y sistemas en GRUPO KABZO.
            </p>
            
            <div style="background: #fffbeb; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 5px;">
                <h3 style="color: #b45309; margin: 0 0 10px 0; font-size: 18px;">Información relevante:</h3>
                <div style="color: #555; line-height: 1.6;">
                    <p style="margin: 8px 0;">• Mantenga su información actualizada en el sistema</p>
                    <p style="margin: 8px 0;">• Verifique que su correo {{email}} esté correcto</p>
                    <p style="margin: 8px 0;">• Reporte cualquier cambio en su equipo asignado</p>
                    <p style="margin: 8px 0;">• Contacte a soporte ante cualquier duda</p>
                </div>
            </div>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="https://kabzo.ddns.net/sistemas/" 
                   style="background: #000000; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 0 10px; border: 2px solid #333; font-size: 16px;">
                    Acceder al Sistema
                </a>
            </div>
            
            <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee; text-align: center;">
                <p style="color: #777; margin: 5px 0;">Atentamente,</p>
                <p style="color: #f59e0b; font-weight: bold; margin: 5px 0; font-size: 18px;">Área de Desarrollo - GRUPO KABZO</p>
            </div>
        </div>
    </div>`
};

            // Sistema de pestañas
            $('.tab-email').click(function() {
                const tabId = $(this).data('tab');
                
                $('.tab-email').removeClass('active');
                $(this).addClass('active');
                
                $('.tab-content-email').removeClass('active');
                $(`#${tabId}-tab`).addClass('active');
            });

            // Navegación entre pestañas
            $('.next-tab-email').click(function() {
                const nextTab = $(this).data('next');
                
                $('.tab-email').removeClass('active');
                $(`.tab-email[data-tab="${nextTab}"]`).addClass('active');
                
                $('.tab-content-email').removeClass('active');
                $(`#${nextTab}-tab`).addClass('active');
                
                if(nextTab === 'preview') {
                    updatePreview();
                }
                
                if(nextTab === 'send') {
                    updateSummary();
                }
            });

            $('.prev-tab-email').click(function() {
                const prevTab = $(this).data('prev');
                
                $('.tab-email').removeClass('active');
                $(`.tab-email[data-tab="${prevTab}"]`).addClass('active');
                
                $('.tab-content-email').removeClass('active');
                $(`#${prevTab}-tab`).addClass('active');
            });

            // Selección de destinatarios
            $('.recipient-option').click(function() {
                $('.recipient-option').removeClass('active');
                $(this).addClass('active');
                $(this).find('input[type="radio"]').prop('checked', true);
                
                // Mostrar/ocultar sección de emails manuales
                if ($(this).data('type') === 'manual') {
                    $('#manual_emails_section').show();
                } else {
                    $('#manual_emails_section').hide();
                }
            });

            // Selección de plantillas
            $('.template-card').click(function() {
                $('.template-card').removeClass('active');
                $(this).addClass('active');
                
                const templateId = $(this).data('template');
                $('#body').summernote('code', templates[templateId]);
                
                if($('#preview-tab').hasClass('active')) {
                    updatePreview();
                }
            });

            // Actualizar vista previa
            function updatePreview() {
                const subject = $('#subject').val() || 'Sin asunto';
                const body = $('#body').summernote('code') || '<p>No hay contenido para mostrar</p>';
                
                $('#preview-subject').text(subject);
                $('#emailPreview').html(body);
            }

            // Actualizar resumen
            function updateSummary() {
                const recipientType = $('input[name="recipient_type"]:checked').val();
                let count = 0;
                let description = '';
                
                if (recipientType === 'pending_verification') {
                    count = <?php echo $userCounts['pending']; ?>;
                    description = 'usuarios pendientes de verificación';
                } else if (recipientType === 'verified') {
                    count = <?php echo $userCounts['verified']; ?>;
                    description = 'usuarios verificados';
                } else if (recipientType === 'unregistered') {
                    count = <?php echo $userCounts['unregistered']; ?>;
                    description = 'correos no registrados';
                } else if (recipientType === 'all_users') {
                    count = <?php echo $userCounts['all']; ?>;
                    description = 'todos los usuarios';
                } else {
                    const manualEmails = $('#recipients').val().split(/[\n,]+/).filter(email => email.trim());
                    count = manualEmails.length;
                    description = 'emails manuales';
                }
                
                const selectedTemplate = $('.template-card.active').find('h4').text() || 'Personalizada';
                $('#summary-text').text(`Se enviarán ${count} correos a ${description} usando la plantilla "${selectedTemplate}"`);
            }

            $('#refreshPreview').click(updatePreview);

            // Establecer plantilla por defecto
            $('.template-card[data-template="verification_reminder"]').click();
        });
    </script>
</body>
</html>
