<?php
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

// Si no tiene permisos, mostrar mensaje
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

// Helpers
function parse_emails($text){
    $text = str_replace(["\r",";"], ["",","], $text);
    $parts = preg_split('/[\n,]+/', $text);
    $emails = [];
    foreach($parts as $p){
        $p = trim($p);
        if(!$p) continue;
        if(preg_match('/(.*)<([^>]+)>/', $p, $m)){
            $emails[] = ['email'=>trim($m[2]), 'name'=>trim($m[1])];
        }else{
            $emails[] = ['email'=>$p,'name'=>''];
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
                if($email)$rows[]= ['email'=>$email,'name'=>$name];
            }
        }
        fclose($handle);
    }
    return $rows;
}

$report=['total'=>0,'sent'=>0,'failed'=>0,'details'=>[]];

if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['action']) && $_POST['action']==='send'){
    if (!$canEdit) {
        die("No tienes permisos para enviar correos");
    }
    
    $from_email = $_POST['from_email']??$SMTP_USER;
    $from_name  = $_POST['from_name']??'Remitente';
    $subject    = $_POST['subject']??'(sin asunto)';
    $body_raw   = $_POST['body']??'';
    $recipients_text = $_POST['recipients']??'';
    $batch_size = max(1,(int)($_POST['batch_size']??$default_batch_size));
    $sleep_seconds = max(0,(int)($_POST['sleep_seconds']??$default_sleep_seconds));
    $max_retries = max(0,(int)($_POST['max_retries']??1));

    $recipients = parse_emails($recipients_text);

    if(!empty($_FILES['csv_file']['tmp_name']) && $_FILES['csv_file']['error']===UPLOAD_ERR_OK){
        $csv_recip = parse_csv($_FILES['csv_file']['tmp_name']);
        $recipients = array_merge($recipients, $csv_recip);
    }

    $final_recipients=[];
    $seen=[];
    foreach($recipients as $r){
        $email = filter_var($r['email'], FILTER_SANITIZE_EMAIL);
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)) continue;
        if(isset($seen[strtolower($email)])) continue;
        $seen[strtolower($email)]=true;
        $final_recipients[]=$r;
    }

    $report['total']=count($final_recipients);
    $counter=0;

    foreach($final_recipients as $r){
        $counter++;
        $attempt=0; $sent_ok=false; $last_error='';
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
                $mail->setFrom($from_email,$from_name);
                $mail->addAddress($r['email'],$r['name']?:null);
                $mail->isHTML(true);
                $mail->Subject=$subject;
                $body=str_replace(['{{email}}','{{name}}'],[$r['email'],$r['name']],$body_raw);
                $mail->Body=$body;
                $mail->AltBody=strip_tags($body);
                $mail->send();
                $sent_ok=true;
                $report['sent']++;
                $report['details'][]=['email'=>$r['email'],'name'=>$r['name'],'status'=>'enviado','attempts'=>$attempt,'error'=>''];
            }catch(Exception $e){
                $last_error = $mail->ErrorInfo?:$e->getMessage();
                if($attempt<=$max_retries) sleep(1);
            }
        }
        if(!$sent_ok){
            $report['failed']++;
            $report['details'][]=['email'=>$r['email'],'name'=>$r['name'],'status'=>'error','attempts'=>$attempt,'error'=>$last_error];
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
    <title>Email Marketing - Sistema KABZO</title>
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
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Sistema de Email Marketing</h1>
                <p class="text-gray-600 mt-2">Envío masivo de correos electrónicos para GRUPO KABZO</p>
            </div>

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

            <!-- Sistema de pestañas -->
            <div class="email-card mb-6">
                <div class="border-b border-gray-200">
                    <div class="flex overflow-x-auto">
                        <div class="tab-email active" data-tab="config">
                            <i class="fas fa-cog mr-2"></i>Configuración
                        </div>
                        <div class="tab-email" data-tab="templates">
                            <i class="fas fa-palette mr-2"></i>Plantillas
                        </div>
                        <div class="tab-email" data-tab="recipients">
                            <i class="fas fa-users mr-2"></i>Destinatarios
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

                    <!-- Pestaña Configuración -->
                    <div class="tab-content-email active" id="config-tab">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Configuración del Remitente -->
                            <div class="email-card p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-cog text-blue-600"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800">Configuración del Remitente</h3>
                                </div>
                                
                                <div class="space-y-4">
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
                                    <div>
                                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                            Asunto del Correo
                                        </label>
                                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               id="subject" name="subject" value="Verifica tu inventario" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Configuración de Envío -->
                            <div class="email-card p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-sliders-h text-green-600"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-800">Configuración de Envío</h3>
                                </div>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="batch_size" class="block text-sm font-medium text-gray-700 mb-2">
                                            Tamaño del Lote
                                        </label>
                                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               id="batch_size" name="batch_size" value="<?php echo $default_batch_size; ?>" min="1">
                                        <p class="text-sm text-gray-500 mt-1">Número de correos a enviar antes de pausar</p>
                                    </div>
                                    <div>
                                        <label for="sleep_seconds" class="block text-sm font-medium text-gray-700 mb-2">
                                            Pausa (segundos)
                                        </label>
                                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               id="sleep_seconds" name="sleep_seconds" value="<?php echo $default_sleep_seconds; ?>" min="0" step="1">
                                        <p class="text-sm text-gray-500 mt-1">Tiempo de pausa entre lotes</p>
                                    </div>
                                    <div>
                                        <label for="max_retries" class="block text-sm font-medium text-gray-700 mb-2">
                                            Máx. Reintentos
                                        </label>
                                        <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                               id="max_retries" name="max_retries" value="1" min="0">
                                        <p class="text-sm text-gray-500 mt-1">Intentos por destinatario en caso de error</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 next-tab-email" data-next="templates">
                                Siguiente <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Pestaña Plantillas -->
                    <div class="tab-content-email" id="templates-tab">
                        <div class="email-card p-6 mb-6">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-palette text-purple-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Selecciona una Plantilla</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <?php
                                $templates = [
                                    'business' => ['name' => 'Corporativo', 'color' => 'from-blue-500 to-blue-700', 'desc' => 'Profesional para empresas'],
                                    'newsletter' => ['name' => 'Newsletter', 'color' => 'from-pink-500 to-pink-700', 'desc' => 'Para boletines informativos'],
                                    'promotion' => ['name' => 'Promocional', 'color' => 'from-orange-500 to-orange-600', 'desc' => 'Ofertas y descuentos'],
                                    'event' => ['name' => 'Aviso KABZO', 'color' => 'from-yellow-500 to-yellow-600', 'desc' => 'Invitaciones a eventos'],
                                    'holiday' => ['name' => 'Festivo', 'color' => 'from-green-500 to-green-600', 'desc' => 'Saludos de temporada'],
                                    'minimal' => ['name' => 'Minimalista', 'color' => 'from-indigo-500 to-indigo-700', 'desc' => 'Diseño limpio y simple'],
                                    'elegant' => ['name' => 'Elegante', 'color' => 'from-gray-700 to-gray-900', 'desc' => 'Para ocasiones especiales'],
                                    'custom' => ['name' => 'Personalizado', 'color' => 'from-red-500 to-red-600', 'desc' => 'Comienza desde cero']
                                ];
                                
                                foreach ($templates as $key => $template): ?>
                                    <div class="template-card" data-template="<?= $key ?>">
                                        <div class="h-24 bg-gradient-to-r <?= $template['color'] ?>"></div>
                                        <div class="p-3">
                                            <div class="template-name font-medium text-gray-800"><?= $template['name'] ?></div>
                                            <div class="template-desc text-sm text-gray-600"><?= $template['desc'] ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="email-card p-6">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-edit text-blue-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Editor de Contenido</h3>
                            </div>
                            
                            <div>
                                <label for="body" class="block text-sm font-medium text-gray-700 mb-2">
                                    Cuerpo del Correo
                                </label>
                                <textarea id="body" name="body" class="form-control" style="height: 300px;"></textarea>
                                <p class="text-sm text-gray-500 mt-2">
                                    Usa <code class="bg-gray-100 px-1 rounded">{{name}}</code> y <code class="bg-gray-100 px-1 rounded">{{email}}</code> para personalizar cada correo
                                </p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-6">
                            <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 prev-tab-email" data-prev="config">
                                <i class="fas fa-arrow-left mr-2"></i> Anterior
                            </button>
                            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 next-tab-email" data-next="recipients">
                                Siguiente <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Pestaña Destinatarios -->
                    <div class="tab-content-email" id="recipients-tab">
                        <div class="email-card p-6">
                            <div class="flex items-center mb-6">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-green-600"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Lista de Destinatarios</h3>
                            </div>
                            
                            <div class="space-y-6">
                                <div>
                                    <label for="recipients" class="block text-sm font-medium text-gray-700 mb-2">
                                        Destinatarios (uno por línea o separados por comas)
                                    </label>
                                    <textarea id="recipients" name="recipients" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                              rows="6" placeholder="ejemplo@dominio.com, Juan Pérez <juan@ejemplo.com>"></textarea>
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
                                
                                <div class="flex items-center">
                                    <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition duration-200" id="validateEmails">
                                        <i class="fas fa-check-circle mr-2"></i> Validar Emails
                                    </button>
                                    <span id="emailCount" class="badge-email ml-3" style="display: none;">0 emails válidos</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between mt-6">
                            <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 prev-tab-email" data-prev="templates">
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
                            <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 prev-tab-email" data-prev="recipients">
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
                                        <span id="summary-text">Se enviarán X correos con la plantilla Y</span>
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

            // Plantillas para diferentes ocasiones
            const templates = {
                'business': `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px;">
                    <div style="background: #4361ee; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0;">
                        <h1 style="margin: 0;">KABZO</h1>
                        <p style="margin: 5px 0 0; opacity: 0.9;">Soluciones profesionales</p>
                    </div>
                    <div style="background: white; padding: 30px; border-radius: 0 0 10px 10px;">
                        <h2 style="color: #333; margin-top: 0;">Hola {{name}},</h2>
                        <p style="color: #555; line-height: 1.6;">Nos complace contactarte para informarte sobre nuestras soluciones profesionales diseñadas para impulsar tu negocio.</p>
                        <p style="color: #555; line-height: 1.6;">Hemos notado que tu dirección de correo es <strong>{{email}}</strong>, y creemos que nuestros servicios pueden ser de gran valor para ti.</p>
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="#" style="background: #4361ee; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block;">Conocer más</a>
                        </div>
                        <p style="color: #555; line-height: 1.6;">Si tienes alguna pregunta, no dudes en contactarnos.</p>
                        <p style="color: #555; line-height: 1.6;">Atentamente,<br>El equipo de KABZO</p>
                    </div>
                    <div style="text-align: center; padding: 20px; color: #777; font-size: 14px;">
                        <p>© 2023 KABZO. Todos los derechos reservados.</p>
                    </div>
                </div>`,
                
                'event': `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px;">
                    <div style="background: linear-gradient(135deg, #D4AF37 0%, #B8860B 100%); color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0;">
                        <h1 style="margin: 0; font-size: 24px;">VERIFICA TU EQUIPO DE COMPUTO ASIGNADO</h1>
                        <p style="margin: 5px 0 0; opacity: 0.9;">GRUPO KABZO - Área de Desarrollo</p>
                    </div>
                    
                    <div style="background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <h2 style="color: #333; margin-top: 0;">Estimado/a {{name}},</h2>
                        
                        <p style="color: #555; line-height: 1.6; font-size: 16px;">
                            Por medio del presente, solicitamos amablemente verificar la información de su equipo de cómputo, 
                            incluyendo sus datos personales y configuración del sistema.
                        </p>
                        
                        <div style="background: #fff9e6; border-left: 4px solid #D4AF37; padding: 15px; margin: 20px 0;">
                            <h3 style="color: #B8860B; margin: 0 0 10px 0; font-size: 18px;">Instrucciones importantes:</h3>
                            <p style="margin: 8px 0; color: #555;">
                                • <strong>REGISTRO OBLIGATORIO:</strong> Debe registrarse en la plataforma con el correo: <strong>{{email}}</strong><br>
                                • Verifique cuidadosamente toda la información personal<br>
                                • Consulte el video tutorial para el proceso paso a paso<br>
                                • Una vez registrado favor de esperar maximo una hora para la activacion de la cuenta caso contrario ponerse en contacto con el area de PROYECTOS.<br>
                                • Espere un correo de activación<br>
                                • Complete la verificación antes de la fecha establecida
                            </p>
                        </div>
                        
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="https://drive.google.com/file/d/11My-PVsGv6otglkrdxaRBRIbgZFccdNE/view?usp=drivesdk" style="background: #D4AF37; color: black; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 0 10px; border: 2px solid #B8860B;">
                                Ver Video Tutorial
                            </a>
                            <a href="http://38.65.143.27/sistemas/" style="background: #000000; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold; margin: 0 10px; border: 2px solid #333;">
                                Acceder a Plataforma
                            </a>
                        </div>
                        
                        <p style="color: #555; line-height: 1.6; font-size: 16px;">
                            Este proceso es <strong>OBLIGATORIO</strong> para todos los colaboradores de <strong>GRUPO KABZO</strong> y forma parte de nuestras 
                            actualizaciones automáticas desarrolladas por el Área de Desarrollo.
                        </p>
                        
                        <p style="color: #555; line-height: 1.6; font-size: 16px;">
                            Agradecemos su atención y pronta respuesta a esta solicitud.
                        </p>
                        
                        <div style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #eee;">
                            <p style="color: #777; margin: 5px 0;">Atentamente,</p>
                            <p style="color: #D4AF37; font-weight: bold; margin: 5px 0;">Área de Desarrollo - GRUPO KABZO</p>
                        </div>
                    </div>
                    
                    <div style="text-align: center; padding: 20px; color: #777; font-size: 14px; background: #000; color: #D4AF37; border-radius: 0 0 10px 10px;">
                        <p style="margin: 0;">© 2025 GRUPO KABZO. Todos los derechos reservados.</p>
                        <p style="margin: 5px 0 0; font-size: 12px; opacity: 0.8;">Mensaje automático generado por el sistema</p>
                    </div>
                </div>`,
                
                'custom': `<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <h2 style="color: #333;">Hola {{name}},</h2>
                    <p>Este es un correo personalizado. Tu dirección de correo es: {{email}}</p>
                    <p>Puedes editar este contenido como desees usando el editor.</p>
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
                
                // Actualizar vista previa si vamos a esa pestaña
                if(nextTab === 'preview') {
                    updatePreview();
                }
                
                // Actualizar resumen si vamos a la pestaña de envío
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

            // Selección de plantillas
            $('.template-card').click(function() {
                $('.template-card').removeClass('active');
                $(this).addClass('active');
                
                const templateId = $(this).data('template');
                $('#body').summernote('code', templates[templateId]);
                
                // Actualizar vista previa si estamos en esa pestaña
                if($('#preview-tab').hasClass('active')) {
                    updatePreview();
                }
            });

            // Validar emails
            $('#validateEmails').click(function() {
                const emailsText = $('#recipients').val();
                const emails = emailsText.split(/[\n,]+/).map(email => email.trim()).filter(email => email);
                
                let validCount = 0;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                emails.forEach(email => {
                    // Extraer solo el email si está en formato "Nombre <email>"
                    const match = email.match(/<(.+)>/);
                    const actualEmail = match ? match[1] : email;
                    
                    if(emailRegex.test(actualEmail)) {
                        validCount++;
                    }
                });
                
                $('#emailCount').text(`${validCount} emails válidos`).show();
                
                if(validCount === emails.length) {
                    $('#emailCount').addClass('badge-success').removeClass('badge-danger');
                } else {
                    $('#emailCount').addClass('badge-danger').removeClass('badge-success');
                }
            });

            // Actualizar vista previa
            function updatePreview() {
                const subject = $('#subject').val();
                const body = $('#body').summernote('code');
                
                $('#preview-subject').text(subject);
                $('#emailPreview').html(body);
            }

            // Actualizar resumen
            function updateSummary() {
                const emailsText = $('#recipients').val();
                const emails = emailsText.split(/[\n,]+/).map(email => email.trim()).filter(email => email);
                
                let validCount = 0;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                emails.forEach(email => {
                    const match = email.match(/<(.+)>/);
                    const actualEmail = match ? match[1] : email;
                    
                    if(emailRegex.test(actualEmail)) {
                        validCount++;
                    }
                });
                
                const selectedTemplate = $('.template-card.active').find('.template-name').text() || 'Personalizada';
                
                $('#summary-text').text(`Se enviarán ${validCount} correos con la plantilla "${selectedTemplate}"`);
            }

            // Actualizar vista previa cuando cambie el asunto o el cuerpo
            $('#subject').on('input', function() {
                if($('#preview-tab').hasClass('active')) {
                    updatePreview();
                }
            });

            $('#refreshPreview').click(updatePreview);

            // Establecer plantilla por defecto
            $('.template-card[data-template="business"]').click();
        });
    </script>
</body>
</html>