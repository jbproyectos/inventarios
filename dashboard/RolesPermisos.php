<?php
include '../includes/middleware.php';
verificarSesion();
include "../includes/conexionbd.php";
require_once '../errores/error_handler.php';

// Verificar autenticación y obtener datos del usuario en una sola consulta
if (!isset($_SESSION["user_id"])) {
    die("Usuario no autenticado");
}

$user_id = $_SESSION["user_id"];

try {
    // Consulta unificada para usuario y permisos
    $stmt = $conexion->prepare("
        SELECT u.rolActual, GROUP_CONCAT(DISTINCT p.nombre) as permisos 
        FROM usuarios u 
        LEFT JOIN permisos_modelos pm ON u.rolActual = pm.rol_id 
        LEFT JOIN permisos p ON pm.permiso_id = p.id 
        WHERE u.Id_Usuario = :user_id 
        GROUP BY u.Id_Usuario
    ");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        throw new Exception("Usuario no encontrado");
    }

    $rolActual = $userData['rolActual'];
    $userPermissions = $userData['permisos'] ? explode(',', $userData['permisos']) : [];

    // Asignar permisos
    $canEdit = in_array('editar', $userPermissions);
    $canDelete = in_array('eliminar', $userPermissions);
    $canView = in_array('ver', $userPermissions);
    $canAdd = in_array('crear', $userPermissions);
    $canViewPw = in_array('ver_contrasenas', $userPermissions);

    // Obtener datos para la gestión de roles
    $roles = $conexion->query("SELECT * FROM roless")->fetchAll(PDO::FETCH_ASSOC);
    $permisos = $conexion->query("SELECT * FROM permisos")->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles y Permisos</title>
    
    <!-- CDNs consolidados -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .form-input {
            transition: all 0.3s ease;
        }
        .form-input:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <?php include 'includes/nav.php' ?>

    <div class="flex">
        <!-- Sidebar -->
        <?php include 'includes/aside.php' ?>
        
        <main class="flex-1 p-6">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-800">Gestión de Roles y Permisos</h1>
                <p class="text-gray-600 mt-2">Administra roles, permisos y asignaciones de usuarios en tu sistema.</p>
            </div>

            <!-- Estadísticas -->
            <!-- <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="stat-card rounded-xl p-6 text-center">
                    <div class="text-4xl font-bold mb-2"><?= count($roles) ?></div>
                    <div class="text-blue-100">Roles Activos</div>
                </div>
                <div class="stat-card rounded-xl p-6 text-center">
                    <div class="text-4xl font-bold mb-2"><?= count($permisos) ?></div>
                    <div class="text-blue-100">Permisos Disponibles</div>
                </div>
            </div> -->

            <!-- Contenido Principal -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                
                <!-- Gestión de Roles -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 card-hover">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-800">Gestión de Roles</h2>
                            <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                                <?= count($roles) ?> roles
                            </span>
                        </div>

                        <!-- Formulario Crear Rol -->
                        <form id="formCrearRol" class="mb-6">
                            <div class="space-y-4">
                                <div>
                                    <label for="nombreRol" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nuevo Rol
                                    </label>
                                    <input type="text" 
                                           id="nombreRol" 
                                           placeholder="Ingresa el nombre del rol" 
                                           required
                                           class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                </div>
                                <button type="submit" 
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Crear Nuevo Rol
                                </button>
                            </div>
                        </form>

                        <!-- Lista de Roles -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Roles Existentes</h3>
                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                <?php if (!empty($roles)): ?>
                                    <?php foreach ($roles as $rol): ?>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <div>
                                                <span class="font-medium text-gray-800"><?= htmlspecialchars($rol['nombre']) ?></span>
                                                <span class="text-sm text-gray-500 ml-2">ID: <?= $rol['id'] ?></span>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="editarRol(<?= $rol['id'] ?>, '<?= htmlspecialchars($rol['nombre']) ?>')" 
                                                        class="text-blue-600 hover:text-blue-800 transition duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button onclick="eliminarRol(<?= $rol['id'] ?>)" 
                                                        class="text-red-600 hover:text-red-800 transition duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        No hay roles creados
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gestión de Permisos -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 card-hover">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-800">Gestión de Permisos</h2>
                            <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                                <?= count($permisos) ?> permisos
                            </span>
                        </div>

                        <!-- Formulario Crear Permiso -->
                        <form id="formCrearPermiso" class="mb-6">
                            <div class="space-y-4">
                                <div>
                                    <label for="nombrePermiso" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nuevo Permiso
                                    </label>
                                    <input type="text" 
                                           id="nombrePermiso" 
                                           placeholder="Ingresa el nombre del permiso" 
                                           required
                                           class="form-input w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                                </div>
                                <button type="submit" 
                                        class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Crear Nuevo Permiso
                                </button>
                            </div>
                        </form>

                        <!-- Lista de Permisos -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 mb-4">Permisos Existentes</h3>
                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                <?php if (!empty($permisos)): ?>
                                    <?php foreach ($permisos as $permiso): ?>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                            <div>
                                                <span class="font-medium text-gray-800"><?= htmlspecialchars($permiso['nombre']) ?></span>
                                                <span class="text-sm text-gray-500 ml-2">ID: <?= $permiso['id'] ?></span>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button onclick="editarPermiso(<?= $permiso['id'] ?>, '<?= htmlspecialchars($permiso['nombre']) ?>')" 
                                                        class="text-blue-600 hover:text-blue-800 transition duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                                <button onclick="eliminarPermiso(<?= $permiso['id'] ?>)" 
                                                        class="text-red-600 hover:text-red-800 transition duration-200">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="text-center py-4 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                        </svg>
                                        No hay permisos creados
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts consolidados -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    
    <script>
        // Funciones de utilidad
        const showAlert = (icon, title, text) => {
            return Swal.fire({ icon, title, text, timer: 3000, showConfirmButton: false });
        };

        const showLoading = () => Swal.fire({ title: 'Procesando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        // Manejo de formularios
        document.addEventListener("DOMContentLoaded", () => {
            // Crear Rol
            document.querySelector("#formCrearRol").addEventListener("submit", async (e) => {
                e.preventDefault();
                const form = e.target;
                const button = form.querySelector('button[type="submit"]');
                const originalText = button.innerHTML;
                
                button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creando...';
                button.disabled = true;

                try {
                    const formData = new FormData();
                    formData.append('action', 'crear_rol');
                    formData.append('nombre_rol', document.querySelector("#nombreRol").value);

                    const response = await fetch("login/permisos.php", { method: "POST", body: formData });
                    const data = await response.json();

                    if (data.success) {
                        await showAlert('success', '¡Éxito!', data.message);
                        form.reset();
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    await showAlert('error', 'Error', error.message);
                } finally {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            });

            // Crear Permiso
            document.querySelector("#formCrearPermiso").addEventListener("submit", async (e) => {
                e.preventDefault();
                const form = e.target;
                const button = form.querySelector('button[type="submit"]');
                const originalText = button.innerHTML;
                
                button.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Creando...';
                button.disabled = true;

                try {
                    const formData = new FormData();
                    formData.append('action', 'crear_permiso');
                    formData.append('nombre_permiso', document.querySelector("#nombrePermiso").value);

                    const response = await fetch("login/permisos.php", { method: "POST", body: formData });
                    const data = await response.json();

                    if (data.success) {
                        await showAlert('success', '¡Éxito!', data.message);
                        form.reset();
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    await showAlert('error', 'Error', error.message);
                } finally {
                    button.innerHTML = originalText;
                    button.disabled = false;
                }
            });
        });

        // Funciones para editar y eliminar
        function editarRol(id, nombre) {
            Swal.fire({
                title: 'Editar Rol',
                input: 'text',
                inputValue: nombre,
                showCancelButton: true,
                confirmButtonText: 'Actualizar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Lógica para actualizar rol
                    console.log('Actualizar rol:', id, result.value);
                }
            });
        }

        function eliminarRol(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esta acción",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Lógica para eliminar rol
                    console.log('Eliminar rol:', id);
                }
            });
        }

        // Funciones similares para permisos
        function editarPermiso(id, nombre) {
            // Implementar similar a editarRol
        }

        function eliminarPermiso(id) {
            // Implementar similar a eliminarRol
        }

        // Mobile menu
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sideMenu = document.querySelector('.z-20');
        if (mobileMenuButton && sideMenu) {
            mobileMenuButton.addEventListener('click', () => sideMenu.classList.toggle('hidden'));
        }
    </script>
</body>
</html>