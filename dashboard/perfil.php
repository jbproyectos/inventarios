<?php
include '../includes/middleware.php';
verificarSesion();
include "../includes/conexionbd.php";
require_once '../errores/error_handler.php';

// Obtén el user_id de la sesión
$user_id = $_SESSION["user_id"];

// Obtener el rol del usuario actual
$stmt = $conexion->prepare("SELECT rolActual FROM usuarios WHERE Id_Usuario = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Usuario no encontrado");
}

// Guardamos el rol del usuario
$rolActual = $user['rolActual'];

// Obtener los permisos asociados a ese rol
$stmtPermissions = $conexion->prepare("
    SELECT DISTINCT p.nombre 
    FROM permisos p
    JOIN permisos_modelos pm ON p.id = pm.permiso_id
    WHERE pm.rol_id = :rol_id
");
$stmtPermissions->bindParam(':rol_id', $rolActual, PDO::PARAM_INT);
$stmtPermissions->execute();
$permissions = $stmtPermissions->fetchAll(PDO::FETCH_ASSOC);

// Asegúrate de que los permisos estén correctamente asignados a las variables
$canEdit = in_array('editar', array_column($permissions, 'nombre'));
$canDelete = in_array('eliminar', array_column($permissions, 'nombre'));
$canView = in_array('ver', array_column($permissions, 'nombre'));
$canAdd = in_array('crear', array_column($permissions, 'nombre'));
$canViewPw = in_array('ver_contrasenas', array_column($permissions, 'nombre'));

// Obtener los modelos (secciones) que el usuario tiene permitido ver
$stmtModelosPermitidos = $conexion->prepare("
    SELECT DISTINCT m.nombre
    FROM modelos m
    JOIN permisos_modelos pm ON m.id = pm.modelo_id
    JOIN permisos p ON p.id = pm.permiso_id
    WHERE pm.rol_id = :rol_id AND p.nombre = 'ver'
");
$stmtModelosPermitidos->bindParam(':rol_id', $rolActual, PDO::PARAM_INT);
$stmtModelosPermitidos->execute();
$modelosPermitidos = $stmtModelosPermitidos->fetchAll(PDO::FETCH_ASSOC);

// Extraer solo los nombres de los modelos permitidos
$permitidos = array_column($modelosPermitidos, 'nombre');

// Obtener información del usuario para el perfil
$usuario_id = $_SESSION['user_id'];
$sql = "
    SELECT u.*, o.nombre AS nombre_oficina, d.nombre AS nombre_departamento, p.nombre AS nombre_puesto
    FROM usuarios u
    LEFT JOIN oficina o ON u.Id_oficina = o.Id_Oficina
    LEFT JOIN departamentos d ON u.Id_departamento = d.Id_departamento
    LEFT JOIN puestos p ON u.Id_puesto = p.Id_puesto
    WHERE u.Id_Usuario = :usuario_id
";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Obtener equipos asignados
$id_usuario = $_SESSION["user_id"];
$stmt_usuario = $conexion->prepare("SELECT nombre, apellido, email FROM usuarios WHERE Id_Usuario = :id");
$stmt_usuario->bindParam(':id', $id_usuario, PDO::PARAM_INT);
$stmt_usuario->execute();
$usuario_info = $stmt_usuario->fetch(PDO::FETCH_ASSOC);

$computadoras = [];
$mobiliario = [];
$celulares = [];

if ($usuario_info) {
    $correo_usuario = $usuario_info['email'];
    $nombre_completo = $usuario_info['nombre'] . ' ' . $usuario_info['apellido'];
    $nombre_depa = $usuario['nombre_departamento'];
    $nombre_oficina = $usuario['nombre_oficina'];
    $nombre_puesto = $usuario['nombre_puesto'];
    $email = $usuario['email'];
    $fechaRegistro = $usuario['fechaRegistro'];
    $fechaUltimoIngreso = $usuario['fechaUltimoIngreso'];
    
    // Computadoras asignadas
    $stmt_compu = $conexion->prepare("SELECT * FROM computadora WHERE correo_asociado = :correo");
    $stmt_compu->bindParam(':correo', $correo_usuario, PDO::PARAM_STR);
    $stmt_compu->execute();
    $computadoras = $stmt_compu->fetchAll(PDO::FETCH_ASSOC);
    
    // Mobiliario asignado

    // Celulares asignados
    $stmt_cel = $conexion->prepare("SELECT * FROM celulares WHERE asignado_a = :nombre_completo");
    $stmt_cel->bindParam(':nombre_completo', $nombre_completo, PDO::PARAM_STR);
    $stmt_cel->execute();
    $celulares = $stmt_cel->fetchAll(PDO::FETCH_ASSOC);
}

// Obtener iniciales para avatar
$sql = "SELECT nombre FROM usuarios WHERE Id_Usuario = :user_id";
$stmt = $conexion->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);
$username = $userData ? $userData['nombre'] : "Usuario Desconocido";
$initials = strtoupper(substr($username, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($nombre_completo ?? 'Perfil de Usuario'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .avatar-color {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .status-active {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-inactive {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .equipment-card {
            border-left: 4px solid;
        }
        .computer-card {
            border-left-color: #3b82f6;
        }
        .mobile-card {
            border-left-color: #10b981;
        }
        .furniture-card {
            border-left-color: #f59e0b;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-700">
    <!-- Navbar -->
    <?php include 'includes/nav.php' ?>

    <div class="flex">
        <!-- Sidebar -->
        <?php include 'includes/aside.php' ?>
        
        <main class="flex-1 p-6">
            <!-- Header Section -->
<!--             <div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Mi Perfil</h1>
    <p class="text-gray-600 mt-2">Información personal y equipos asignados</p>
</div> -->
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Columna izquierda: Información del usuario -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Tarjeta de perfil -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col items-center text-center">
                                <!-- Avatar -->
                                <div class="relative mb-4">
                                    <div class="w-24 h-24 rounded-full avatar-color flex items-center justify-center text-white text-2xl font-bold">
                                        <?php echo $initials; ?>
                                    </div>
                                    <div class="absolute bottom-0 right-0 w-6 h-6 bg-green-500 rounded-full border-2 border-white"></div>
                                </div>
                                
                                <!-- Nombre y rol -->
                                <h2 class="text-xl font-bold text-gray-800"><?php echo htmlspecialchars($nombre_completo); ?></h2>
                                <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($nombre_puesto ?? 'Sin puesto asignado'); ?></p>
                                
                                <!-- Departamento y oficina -->
                                <div class="mt-4 flex flex-col space-y-2 w-full">
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Departamento:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($nombre_depa ?? 'No asignado'); ?></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Oficina:</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($nombre_oficina ?? 'No asignada'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Información de contacto -->
                        <div class="border-t border-gray-200 p-6">
                            <h3 class="font-semibold text-gray-700 mb-4">Información de Contacto</h3>
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-gray-600"><?php echo htmlspecialchars($email ?? 'No disponible'); ?></span>
                                </div>
                                
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-gray-600">Fecha de registro:</p>
                                        <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($fechaRegistro ?? 'No disponible'); ?></p>
                                    </div>
                                </div>
                                
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-gray-600">Último acceso:</p>
                                        <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($fechaUltimoIngreso ?? 'No disponible'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Acciones -->
                        <div class="border-t border-gray-200 p-4 bg-gray-50">
                            <button class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition duration-200 flex items-center justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Solicitar edición de datos
                            </button>
                        </div>
                    </div>
                    
                    <!-- Tarjeta de estadísticas -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="font-semibold text-gray-700 mb-4">Mis Equipos</h3>
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div class="p-3 bg-blue-50 rounded-lg">
                                <p class="text-2xl font-bold text-blue-600"><?php echo count($computadoras); ?></p>
                                <p class="text-sm text-gray-600 mt-1">Computadoras</p>
                            </div>
                            <div class="p-3 bg-green-50 rounded-lg">
                                <p class="text-2xl font-bold text-green-600"><?php echo count($celulares); ?></p>
                                <p class="text-sm text-gray-600 mt-1">Celulares</p>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-lg">
                                <p class="text-2xl font-bold text-amber-600"><?php echo count($mobiliario); ?></p>
                                <p class="text-sm text-gray-600 mt-1">Mobiliario</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Columna derecha: Equipos asignados -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="border-b border-gray-200 px-6 py-4">
                            <h3 class="text-lg font-semibold text-gray-800">Equipos Asignados</h3>
                            <p class="text-gray-600 text-sm mt-1">Lista de equipos y recursos asignados a tu cuenta</p>
                        </div>
                        
                        <div class="p-6">
                            <!-- Computadoras -->
                            <div class="mb-8">
                                <h4 class="font-medium text-gray-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    Computadoras (<?php echo count($computadoras); ?>)
                                </h4>
                                
                                <?php if (!empty($computadoras)): ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <?php foreach ($computadoras as $c): ?>
                                            <div class="equipment-card computer-card bg-white border border-gray-200 rounded-lg p-4 card-hover">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h5 class="font-semibold text-gray-800"><?php echo htmlspecialchars($c['marca'] . ' ' . $c['modelo']); ?></h5>
                                                    <span class="status-badge <?php echo $c['condicion'] === 'Activo' ? 'status-active' : 'status-inactive'; ?>">
                                                        <?php echo htmlspecialchars($c['condicion']); ?>
                                                    </span>
                                                </div>
                                                <div class="space-y-2 text-sm text-gray-600">
                                                    <div class="flex justify-between">
                                                        <span>Procesador:</span>
                                                        <span class="font-medium"><?php echo htmlspecialchars($c['procesador']); ?></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>RAM:</span>
                                                        <span class="font-medium"><?php echo htmlspecialchars($c['ram']); ?></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>Almacenamiento:</span>
                                                        <span class="font-medium"><?php echo htmlspecialchars($c['tipoDeDisco']); ?></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>Sistema:</span>
                                                        <span class="font-medium"><?php echo htmlspecialchars($c['tipo'] ?? 'Windows'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-gray-500">No tienes computadoras asignadas</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Celulares -->
                            <div class="mb-8">
                                <h4 class="font-medium text-gray-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Celulares (<?php echo count($celulares); ?>)
                                </h4>
                                
                                <?php if (!empty($celulares)): ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <?php foreach ($celulares as $cel): ?>
                                            <div class="equipment-card mobile-card bg-white border border-gray-200 rounded-lg p-4 card-hover">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h5 class="font-semibold text-gray-800"><?php echo htmlspecialchars($cel['marca'] . ' ' . $cel['modelo']); ?></h5>
                                                    <span class="status-badge <?php echo $cel['status'] === 'Activo' ? 'status-active' : 'status-inactive'; ?>">
                                                        <?php echo htmlspecialchars($cel['status']); ?>
                                                    </span>
                                                </div>
                                                <div class="space-y-2 text-sm text-gray-600">
                                                    <div class="flex justify-between">
                                                        <span>Número:</span>
                                                        <span class="font-medium"><?php echo htmlspecialchars($cel['numero'] ?? 'No disponible'); ?></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>IMEI:</span>
                                                        <span class="font-medium"><?php echo htmlspecialchars($cel['imei'] ?? 'No disponible'); ?></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>Marca | Modelo:</span>
                                                            <span class="font-medium">
                                                                <?php 
                                                                    echo htmlspecialchars(($cel['marca'] ?? 'No especificada') . ' ' . ($cel['modelo'] ?? '')); 
                                                                ?>
                                                            </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-gray-500">No tienes celulares asignados</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Mobiliario -->
                            <div>
                                <h4 class="font-medium text-gray-700 mb-4 flex items-center">
                                    <svg class="w-5 h-5 text-amber-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    Mobiliario (<?php echo count($mobiliario); ?>)
                                </h4>
                                
                                <?php if (!empty($mobiliario)): ?>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <?php foreach ($mobiliario as $m): ?>
                                            <div class="equipment-card furniture-card bg-white border border-gray-200 rounded-lg p-4 card-hover">
                                                <div class="flex justify-between items-start mb-2">
                                                    <h5 class="font-semibold text-gray-800"><?php echo htmlspecialchars($m['tipo']); ?></h5>
                                                    <span class="status-badge <?php echo $m['estado'] === 'Bueno' ? 'status-active' : 'status-inactive'; ?>">
                                                        <?php echo htmlspecialchars($m['estado']); ?>
                                                    </span>
                                                </div>
                                                <div class="space-y-2 text-sm text-gray-600">
                                                    <div class="flex justify-between">
                                                        <span>Descripción:</span>
                                                        <span class="font-medium text-right"><?php echo htmlspecialchars($m['descripcion']); ?></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>Material:</span>
                                                        <span class="font-medium"><?php echo htmlspecialchars($m['material'] ?? 'No especificado'); ?></span>
                                                    </div>
                                                    <div class="flex justify-between">
                                                        <span>Color:</span>
                                                        <span class="font-medium"><?php echo htmlspecialchars($m['color'] ?? 'No especificado'); ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-8 bg-gray-50 rounded-lg">
                                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                        <p class="text-gray-500">No tienes mobiliario asignado</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Función para generar color de avatar
        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        // Aplicar color al avatar
        document.addEventListener('DOMContentLoaded', function() {
            var avatar = document.querySelector('.avatar-color');
            if (avatar) {
                // Usamos un gradiente fijo para consistencia, pero podrías usar getRandomColor() si prefieres
                // avatar.style.backgroundColor = getRandomColor();
            }
        });

        // Toggle mobile menu
        var mobileMenuButton = document.getElementById('mobile-menu-button');
        var sideMenu = document.querySelector('.z-20');

        function toggleMobileMenu() {
            sideMenu.classList.toggle('hidden');
        }

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', toggleMobileMenu);
        }

        // Fullscreen toggle
        function toggleFullScreen() {
            const fullscreenIcon = document.getElementById('fullscreen-icon');
            const exitFullscreenIcon = document.getElementById('exit-fullscreen-icon');

            if (!document.fullscreenElement) {
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen();
                } else if (document.documentElement.msRequestFullscreen) {
                    document.documentElement.msRequestFullscreen();
                }
                if (fullscreenIcon) fullscreenIcon.classList.add('hidden');
                if (exitFullscreenIcon) exitFullscreenIcon.classList.remove('hidden');
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                if (fullscreenIcon) fullscreenIcon.classList.remove('hidden');
                if (exitFullscreenIcon) exitFullscreenIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>