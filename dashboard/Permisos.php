<?php
include '../includes/middleware.php';
verificarSesion();
include "../includes/conexionbd.php";
require_once '../errores/error_handler.php';

// Verificar autenticación
if (!isset($_SESSION["user_id"])) {
    die("Usuario no autenticado");
}

$user_id = $_SESSION["user_id"];

// Optimizar consultas - una sola consulta para usuario
try {
    $stmt = $conexion->prepare("SELECT rolActual FROM usuarios WHERE Id_Usuario = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception("Usuario no encontrado");
    }

    $rolActual = $user['rolActual'];

    // Consulta unificada para permisos
    $stmtPermissions = $conexion->prepare("
        SELECT DISTINCT p.nombre 
        FROM permisos p
        JOIN permisos_modelos pm ON p.id = pm.permiso_id
        WHERE pm.rol_id = :rol_id
    ");
    $stmtPermissions->bindParam(':rol_id', $rolActual, PDO::PARAM_INT);
    $stmtPermissions->execute();
    $permissions = $stmtPermissions->fetchAll(PDO::FETCH_COLUMN, 0); // Solo obtener los nombres

    // Asignar permisos de manera más eficiente
    $canEdit = in_array('editar', $permissions);
    $canDelete = in_array('eliminar', $permissions);
    $canView = in_array('ver', $permissions);
    $canAdd = in_array('crear', $permissions);
    $canViewPw = in_array('ver_contrasenas', $permissions);

    // Consulta unificada para modelos permitidos
    $stmtModelosPermitidos = $conexion->prepare("
        SELECT DISTINCT m.nombre
        FROM modelos m
        JOIN permisos_modelos pm ON m.id = pm.modelo_id
        JOIN permisos p ON p.id = pm.permiso_id
        WHERE pm.rol_id = :rol_id AND p.nombre = 'ver'
    ");
    $stmtModelosPermitidos->bindParam(':rol_id', $rolActual, PDO::PARAM_INT);
    $stmtModelosPermitidos->execute();
    $permitidos = $stmtModelosPermitidos->fetchAll(PDO::FETCH_COLUMN, 0);

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Obtener datos para la gestión de roles (solo si el usuario tiene permisos)
$roles = $modelos = $permisos = $permisosAsignados = [];
if ($canView) { // Solo cargar si tiene permiso de ver
    try {
        // Usar consultas preparadas para mayor seguridad
        $roles = $conexion->query("SELECT * FROM roless")->fetchAll(PDO::FETCH_ASSOC);
        $modelos = $conexion->query("SELECT * FROM modelos")->fetchAll(PDO::FETCH_ASSOC);
        $permisos = $conexion->query("SELECT * FROM permisos")->fetchAll(PDO::FETCH_ASSOC);
        $permisosAsignados = $conexion->query("SELECT * FROM permisos_modelos")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al cargar datos de roles: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles y Permisos</title>
    
    <!-- Consolidar todos los CSS en el head -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="../assets/css/profile.css">
    
    <style>
        .role-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }
        .role-table th, .role-table td {
            border: 1px solid #e5e7eb;
            padding: 0.75rem;
            text-align: left;
        }
        .role-table th {
            background-color: #f9fafb;
            font-weight: 600;
        }
        .role-header {
            background-color: #e5e7eb;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .role-header:hover {
            background-color: #d1d5db;
        }
        .role-sections {
            background-color: #f8fafc;
        }
        .checkbox-cell {
            text-align: center;
        }
        .permission-checkbox {
            transform: scale(1.2);
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
                <h1 class="text-3xl font-bold text-gray-800">Gestión de Roles y Permisos</h1>
                <p class="text-gray-600 mt-2">Administra roles, permisos y asignaciones de usuarios en tu sistema.</p>
            </div>

            <!-- Verificación de permisos -->
            <?php if (!$canView): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <p>No tienes permisos para ver esta sección.</p>
                </div>
            <?php else: ?>
            
            <!-- Contenido Principal -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <!-- Estadísticas rápidas -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-2xl font-bold text-blue-600"><?= count($roles) ?></p>
                            <p class="text-sm text-gray-600">Roles</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-2xl font-bold text-green-600"><?= count($modelos) ?></p>
                            <p class="text-sm text-gray-600">Secciones</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-2xl font-bold text-purple-600"><?= count($permisos) ?></p>
                            <p class="text-sm text-gray-600">Permisos</p>
                        </div>
                        <div class="bg-amber-50 p-4 rounded-lg">
                            <p class="text-2xl font-bold text-amber-600"><?= count($permisosAsignados) ?></p>
                            <p class="text-sm text-gray-600">Asignaciones</p>
                        </div>
                    </div>

                    <!-- Tabla de permisos -->
                    <form id="asignarPermisosRol">
                        <div class="overflow-x-auto">
                            <table class="role-table">
                                <thead>
                                    <tr>
                                        <th class="w-1/6">Rol</th>
                                        <th class="w-1/6">Sección</th>
                                        <?php foreach ($permisos as $permiso): ?>
                                            <th class="w-1/12 text-center" title="<?= htmlspecialchars($permiso['descripcion'] ?? '') ?>">
                                                <?= htmlspecialchars($permiso['nombre']) ?>
                                            </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roles as $rol): ?>
                                        <!-- Fila del rol -->
                                        <tr class="role-header" onclick="toggleRole(<?= $rol['id'] ?>)">
                                            <td class="font-semibold">
                                                <div class="flex items-center">
                                                    <span><?= htmlspecialchars($rol['nombre']) ?></span>
                                                    <svg class="w-4 h-4 ml-2 transition-transform" id="icon-<?= $rol['id'] ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </td>
                                            <td colspan="<?= count($permisos) + 1 ?>" class="text-gray-500">
                                                Click para expandir/contraer
                                            </td>
                                        </tr>
                                        
                                        <!-- Secciones del rol -->
                                        <?php foreach ($modelos as $modelo): ?>
                                            <tr class="role-sections role-sections-<?= $rol['id'] ?>" style="display: none;">
                                                <td></td>
                                                <td class="font-medium"><?= htmlspecialchars($modelo['nombre']) ?></td>
                                                <?php foreach ($permisos as $permiso): ?>
                                                    <td class="checkbox-cell">
                                                        <?php
                                                        $checked = '';
                                                        foreach ($permisosAsignados as $permisoAsignado) {
                                                            if ($permisoAsignado['rol_id'] == $rol['id'] && 
                                                                $permisoAsignado['modelo_id'] == $modelo['id'] && 
                                                                $permisoAsignado['permiso_id'] == $permiso['id']) {
                                                                $checked = 'checked';
                                                                break;
                                                            }
                                                        }
                                                        ?>
                                                        <input type="checkbox" 
                                                               class="permission-checkbox"
                                                               name="permisos[<?= $rol['id'] ?>][<?= $modelo['id'] ?>][<?= $permiso['id'] ?>]" 
                                                               value="1" 
                                                               <?= $checked ?>
                                                               <?= !$canEdit ? 'disabled' : '' ?>>
                                                    </td>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Botón de guardar -->
                        <?php if ($canEdit): ?>
                            <div class="mt-6 flex justify-end">
                                <button type="submit" 
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Guardar Cambios
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="mt-4 text-center text-gray-500">
                                <p>No tienes permisos para editar los roles.</p>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Consolidar todos los scripts al final -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Funcionalidad para expandir/contraer roles
        function toggleRole(roleId) {
            const rows = document.querySelectorAll(`.role-sections-${roleId}`);
            const icon = document.getElementById(`icon-${roleId}`);
            
            rows.forEach(row => {
                row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
            });
            
            // Rotar ícono
            if (icon) {
                icon.style.transform = icon.style.transform === 'rotate(180deg)' ? 'rotate(0deg)' : 'rotate(180deg)';
            }
        }

        // Manejo del formulario
        document.addEventListener("DOMContentLoaded", () => {
            const form = document.querySelector("#asignarPermisosRol");
            if (!form) return;

            form.addEventListener("submit", async (e) => {
                e.preventDefault();

                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                // Mostrar loading
                submitBtn.innerHTML = '<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Guardando...';
                submitBtn.disabled = true;

                try {
                    const formData = new FormData(form);
                    const response = await fetch("login/procesar_permisos.php", {
                        method: "POST",
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        throw new Error(data.message);
                    }

                } catch (error) {
                    console.error('Error:', error);
                    await Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Ocurrió un error al procesar la solicitud.'
                    });
                } finally {
                    // Restaurar botón
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        });

        // Mobile menu functionality
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const sideMenu = document.querySelector('.z-20');

        if (mobileMenuButton && sideMenu) {
            mobileMenuButton.addEventListener('click', () => {
                sideMenu.classList.toggle('hidden');
            });
        }
    </script>
</body>
</html>