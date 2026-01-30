<?php
include "../includes/conexionbd.php";

// Verifica si el usuario está autenticado
if (!isset($_SESSION["user_id"])) {
    die("Usuario no autenticado");
}

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

?>

<style>
/* Diseño moderno para el sidebar */
.sidebar-container {
    position: relative;
}

.sidebar-mobile-toggle {
    display: none;
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 1001;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 0.75rem;
    cursor: pointer;
    font-size: 1.25rem;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.sidebar-mobile-toggle:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
}

.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    z-index: 999;
}

.z-20 {
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(180deg, #1a202c 0%, #2d3748 100%);
}

@media (max-width: 768px) {
    .sidebar-mobile-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .z-20 {
        transform: translateX(-100%);
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        z-index: 1000;
        box-shadow: 4px 0 25px rgba(0, 0, 0, 0.4);
    }
    
    .z-20.sidebar-open {
        transform: translateX(0);
    }
    
    .sidebar-overlay.active {
        display: block;
    }
}

/* Diseño moderno para elementos del sidebar */
.sidebar-modern {
    background: linear-gradient(180deg, #1a202c 0%, #2d3748 100%);
    border-right: 1px solid rgba(255, 255, 255, 0.1);
}

.profile-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.profile-role {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
    border: none;
}

.menu-item {
    position: relative;
    border-radius: 12px;
    margin: 4px 12px;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.menu-item:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateX(4px);
}

.menu-item.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.submenu {
    background: rgba(0, 0, 0, 0.3);
    border-radius: 10px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
}

.submenu-item {
    border-radius: 8px;
    margin: 2px 4px;
    transition: all 0.2s ease;
}

.submenu-item:hover {
    background: rgba(255, 255, 255, 0.1);
}

.footer-icons {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.2);
}

.icon-button {
    position: relative;
    border-radius: 10px;
    padding: 0.5rem;
    transition: all 0.3s ease;
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid transparent;
}

.icon-button:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.2);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.version-badge {
    background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
    color: white;
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 0.75rem;
    font-weight: 600;
    margin: 0 auto;
    display: inline-block;
}

/* Animaciones mejoradas */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.menu-item {
    animation: slideIn 0.3s ease-out;
}

/* Scrollbar personalizado */
.sidebar-scroll::-webkit-scrollbar {
    width: 4px;
}

.sidebar-scroll::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.sidebar-scroll::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

.sidebar-scroll::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
}
</style>

<div class="sidebar-container">
    <!-- Botón para móviles mejorado -->
    <button id="sidebarMobileToggle"
            class="sidebar-mobile-toggle">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Overlay para móviles -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <aside class="z-20 w-64 h-screen overflow-y-auto sidebar-modern md:block flex-shrink-0 sticky top-[3rem] sidebar-scroll" id="sidebar">
        <div class="text-gray-300 flex-grow">
            <!-- Tarjeta de perfil mejorada -->
            <div class="flex justify-center item-center p-4">
                <div class="w-full profile-card">
                    <div class="flex flex-col items-center pb-6 pt-6">
                        <div class="relative mb-4">
                            <div class="w-20 h-20 rounded-full bg-gradient-to-r from-purple-400 to-blue-500 flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                                <?php
                                try {
                                    $user_id = $_SESSION["user_id"];
                                    $consulta = $conexion->prepare("
                                        SELECT 
                                            usuarios.nombre AS nombre_usuario, 
                                            usuarios.apellido AS apellido_usuario, 
                                            roless.nombre AS nombre_rol
                                        FROM 
                                            usuarios
                                        JOIN 
                                            roless ON usuarios.rolActual = roless.id
                                        WHERE 
                                            usuarios.Id_Usuario = :user_id
                                    ");
                                    $consulta->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                                    $consulta->execute();

                                    $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                                    if ($resultado && isset($resultado['nombre_usuario'])) {
                                        $nombres = explode(' ', $resultado['nombre_usuario']);
                                        $inicial = strtoupper(substr($nombres[0], 0, 1));
                                        echo $inicial;
                                    } else {
                                        echo 'U'; // Inicial por defecto
                                    }
                                } catch (PDOException $e) {
                                    echo 'U'; // Inicial por defecto en caso de error
                                }
                                ?>
                            </div>
                            <div class="absolute bottom-0 right-0 w-4 h-4 bg-green-400 rounded-full border-2 border-gray-800"></div>
                        </div>
                        <?php
                        try {
                            $user_id = $_SESSION["user_id"];
                            $consulta = $conexion->prepare("
                                SELECT 
                                    usuarios.nombre AS nombre_usuario, 
                                    usuarios.apellido AS apellido_usuario, 
                                    roless.nombre AS nombre_rol
                                FROM 
                                    usuarios
                                JOIN 
                                    roless ON usuarios.rolActual = roless.id
                                WHERE 
                                    usuarios.Id_Usuario = :user_id
                            ");
                            $consulta->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                            $consulta->execute();

                            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                            if ($resultado && isset($resultado['nombre_usuario'])) {
                                $nombre_usuario = $resultado['nombre_usuario'];
                                $apellido_usuario = $resultado['apellido_usuario'];
                                $nombre_rol = $resultado['nombre_rol'];

                                echo '<div class="flex flex-col items-center">';
                                echo '<span class="text-center font-bold text-white mb-2 text-lg">' . htmlspecialchars($nombre_usuario . ' ' . $apellido_usuario) . '</span>';
                                echo '<span class="profile-role text-xs font-medium px-3 py-1 rounded-full">' . htmlspecialchars($nombre_rol) . '</span>';
                                echo '</div>';
                            } else {
                                echo '<div class="flex flex-col items-center">';
                                echo '<span class="text-center font-bold text-white mb-2 text-lg">Usuario</span>';
                                echo '<span class="profile-role text-xs font-medium px-3 py-1 rounded-full">Usuario</span>';
                                echo '</div>';
                            }
                        } catch (PDOException $e) {
                            echo '<div class="flex flex-col items-center">';
                            echo '<span class="text-center font-bold text-white mb-2 text-lg">Usuario</span>';
                            echo '<span class="profile-role text-xs font-medium px-3 py-1 rounded-full">Usuario</span>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <ul class="mt-4 flex flex-col h-full px-2">
                <!-- Home -->
                <li class="menu-item">
                    <a class="inline-flex items-center w-full text-sm font-semibold text-white p-3 transition-all duration-300" href="./">
                        <svg class="w-5 h-5 mr-3" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="ml-1">Dashboard</span>
                    </a>
                </li>

                <!-- Inventarios -->
                <li class="menu-item">
                    <button class="inline-flex items-center justify-between w-full text-sm font-semibold p-3 transition-all duration-300" onclick="toggleMenu('inventoryMenu')">
                        <span class="inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5"></path>
                            </svg>
                            <span class="ml-1">Mis Inventarios</span>
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-300" id="inventoryMenuIcon" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <ul id="inventoryMenu" class="submenu hidden p-2 mt-2 space-y-1" aria-label="submenu">
                        <?php if (in_array('mobiliarios', $permitidos)) { ?>
                            <li class="submenu-item">
                                <a href="../dashboard/inv_mobiliario.php" class="w-full block p-2 text-sm">Mobiliario</a>
                            </li>
                        <?php } ?>
                        <?php if (in_array('domicilios', $permitidos)) { ?>
                            <li class="submenu-item">
                                <a class="w-full block p-2 text-sm" href="../dashboard/Inventario_Domicilios.php">Domicilio</a>
                            </li>
                        <?php } ?>
                        <?php if (in_array('celulares', $permitidos)) { ?>
                            <li class="submenu-item">
                                <a class="w-full block p-2 text-sm" href="../dashboard/Inventario_Celulares.php">Celulares</a>
                            </li>
                        <?php } ?>
                        <?php if (in_array('computadoras', $permitidos)) { ?>
                            <li class="submenu-item">
                                <a class="w-full block p-2 text-sm" href="../dashboard/Inventario_Computadora.php">Computadoras</a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>

                <!-- Catálogos -->
                <li class="menu-item">
                    <button class="inline-flex items-center justify-between w-full text-sm font-semibold p-3 transition-all duration-300" onclick="toggleMenu('CatalogosMenu')">
                        <span class="inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 01-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0112 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5"></path>
                            </svg>
                            <span class="ml-1">Mis Catálogos</span>
                        </span>
                        <svg class="w-4 h-4 transition-transform duration-300" id="CatalogosMenuIcon" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                    <ul id="CatalogosMenu" class="submenu hidden p-2 mt-2 space-y-1" aria-label="submenu">
                        <?php if (in_array('empleados', $permitidos)) { ?>
                            <li class="submenu-item">
                                <a href="../dashboard/empleados.php" class="w-full block p-2 text-sm">Empleados</a>
                            </li>
                        <?php } ?>
                        <?php if (in_array('departamentos', $permitidos)) { ?>
                            <li class="submenu-item">
                                <a class="w-full block p-2 text-sm" href="../dashboard/departamentos.php">Departamentos</a>
                            </li>
                        <?php } ?>
                        <?php if (in_array('oficinas', $permitidos)) { ?>
                            <li class="submenu-item">
                                <a class="w-full block p-2 text-sm" href="../dashboard/oficinas.php">Oficinas</a>
                            </li>
                        <?php } ?>
                        <?php if (in_array('puestos', $permitidos)) { ?>
                            <li class="submenu-item">
                                <a class="w-full block p-2 text-sm" href="../dashboard/puestos.php">Puestos</a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>

                <!-- Menú de administración -->
                <li class="menu-item mt-4">
                    <?php if (in_array('usuarios', $permitidos)) { ?>
                        <a href="../dashboard/Users.php" class="inline-flex items-center w-full text-sm font-semibold p-3 transition-all duration-300 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            <span class="ml-1">Usuarios</span>
                        </a>
                    <?php } ?>

                    <?php if (in_array('seguridad', $permitidos)) { ?>
                        <a href="../dashboard/Permisos.php" class="inline-flex items-center w-full text-sm font-semibold p-3 transition-all duration-300 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                            <span class="ml-1">Seguridad</span>
                        </a>
                        <a href="../dashboard/RolesPermisos.php" class="inline-flex items-center w-full text-sm font-semibold p-3 transition-all duration-300 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5zm6-10.125a1.875 1.875 0 11-3.75 0 1.875 1.875 0 013.75 0zm1.294 6.336a6.721 6.721 0 01-3.17.789 6.721 6.721 0 01-3.168-.789 3.376 3.376 0 016.338 0z" />
                            </svg>
                            <span class="ml-1">Roles y Permisos</span>
                        </a>
                    <?php } ?>

                    <!-- Avisados -->
                    <?php if (in_array('usuarios', $permitidos)) { ?>
                        <a href="../dashboard/mailsend.php" class="inline-flex items-center w-full text-sm font-semibold p-3 transition-all duration-300 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                            <span class="ml-1">Avisados (beta)</span>
                        </a>
                    <?php } ?>
                </li>

                <div class="flex-grow"></div>

                <!-- Versión -->
                <?php
                $sql = "SELECT version FROM versiones WHERE version LIKE '%prod' ORDER BY id DESC LIMIT 1";
                try {
                    $stmt = $conexion->prepare($sql);
                    $stmt->execute();
                    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

                    if ($resultado) {
                        $ultimaVersion = $resultado['version'];
                        echo "<div class='text-center mt-6 mb-4'><span class='version-badge'>" . htmlspecialchars($ultimaVersion) . "</span></div>";
                    } else {
                        echo "<div class='text-center mt-6 mb-4'><span class='version-badge'>v1.0.0</span></div>";
                    }
                } catch (PDOException $e) {
                    echo "<div class='text-center mt-6 mb-4'><span class='version-badge'>v1.0.0</span></div>";
                }
                ?>
            </ul>
        </div>

        <!-- Footer con iconos mejorados -->
        <div class="footer-icons flex justify-around p-4">
            <a href="#" data-modal-target="crypto-modal" data-modal-toggle="crypto-modal" class="icon-button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 5.25h.008v.008H12v-.008Z" />
                </svg>
            </a>
            <a href="#" class="icon-button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </a>
            <a href="../dashboard/perfil.php" class="icon-button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
            </a>
            <a href="../dashboard/auditor.php" class="icon-button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
                </svg>
            </a>
            <a href="../dashboard/login/logout.php" class="icon-button text-red-400 hover:bg-red-500 hover:text-white">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                </svg>
            </a>
        </div>
    </aside>
</div>


<!-- Modal de información de la aplicación -->
<div id="crypto-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-sm dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600 bg-gradient-to-r from-blue-600 to-purple-700">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-white">
                        Sistema de Inventarios - GRUPO KABZO
                    </h3>
                </div>
                <button type="button" class="text-gray-200 bg-transparent hover:bg-white hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="crypto-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Cerrar modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 md:p-5 space-y-4">
                <!-- Información de la aplicación -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg border border-blue-200 dark:border-blue-800">
                        <h4 class="text-lg font-semibold text-blue-800 dark:text-blue-300 mb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            Información del Sistema
                        </h4>
                        <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                            <?php
                            try {
                                $sql = "SELECT version FROM versiones WHERE version LIKE '%prod' ORDER BY id DESC LIMIT 1";
                                $stmt = $conexion->prepare($sql);
                                $stmt->execute();
                                $version_result = $stmt->fetch(PDO::FETCH_ASSOC);
                                $version = $version_result ? $version_result['version'] : 'v1.0.0';
                            } catch (PDOException $e) {
                                $version = 'v1.0.0';
                            }
                            ?>
                            <li><strong>Versión:</strong> <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300"><?php echo htmlspecialchars($version); ?></span></li>
                            <li><strong>Nombre:</strong> Sistema de Inventarios</li>
                            <li><strong>Empresa:</strong> GRUPO KABZO</li>
                            <li><strong>Área:</strong> Desarrollo de Sistemas</li>
                            <li><strong>Última actualización:</strong> <?php echo date('d/m/Y'); ?></li>
                        </ul>
                    </div>

                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                        <h4 class="text-lg font-semibold text-green-800 dark:text-green-300 mb-2 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Soporte Técnico
                        </h4>
                        <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-1">
                            <li><strong>Desarrollador:</strong> PROYECTOS</li>
                            <li><strong>Email:</strong> 
                                <a href="mailto:juan.hernandez@kabzo.com" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    desarrollo@kabzo.org
                                </a>
                            </li>
                            <li><strong>Teléfono:</strong> 
                                <a href="tel:+525512345678" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                    +52 55 1234 5678
                                </a>
                            </li>
                            <li><strong>Horario:</strong> 9:00 AM - 6:00 PM</li>
                            <li><strong>Emergencias:</strong> Disponible 24/7</li>
                        </ul>
                    </div>
                </div>

                <!-- Características del sistema -->
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                    <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                        </svg>
                        Características del Sistema
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Gestión de inventarios múltiples
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Control de usuarios y permisos
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Auditoría de movimientos
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Reportes en tiempo real
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Interfaz responsive
                        </div>
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Seguridad avanzada
                        </div>
                    </div>
                </div>

                <!-- Estado del sistema -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg border border-yellow-200 dark:border-yellow-800">
                    <h4 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        Estado del Sistema
                    </h4>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Sistema operativo correctamente</span>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">En línea</span>
                    </div>
                </div>

                <!-- Información de contacto adicional -->
                <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg border border-purple-200 dark:border-purple-800">
                    <h4 class="text-lg font-semibold text-purple-800 dark:text-purple-300 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Contacto Adicional
                    </h4>
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        <p class="mb-2">Para reportar problemas técnicos, sugerencias o solicitar nuevas funcionalidades, contacte al área de desarrollo.</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            <a href="mailto:soporte@kabzo.com" class="inline-flex items-center px-3 py-1 text-sm font-medium text-center text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Email de Soporte
                            </a>
                            <a href="tel:+525512345678" class="inline-flex items-center px-3 py-1 text-sm font-medium text-center text-white bg-green-600 rounded-lg hover:bg-green-700 focus:ring-4 focus:outline-none focus:ring-green-300 dark:bg-green-500 dark:hover:bg-green-600 dark:focus:ring-green-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                Llamar Soporte
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Última verificación: <?php echo date('d/m/Y H:i:s'); ?>
                </div>
                <button data-modal-hide="crypto-modal" type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Inicializar el modal (si usas Flowbite)
document.addEventListener('DOMContentLoaded', function() {
    // Si estás usando Flowbite, el modal se inicializa automáticamente
    // Si no, aquí está el código para manejar el modal manualmente
    
    const modal = document.getElementById('crypto-modal');
    const showButtons = document.querySelectorAll('[data-modal-toggle="crypto-modal"]');
    const hideButtons = document.querySelectorAll('[data-modal-hide="crypto-modal"]');
    
    showButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        });
    });
    
    hideButtons.forEach(button => {
        button.addEventListener('click', function() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        });
    });
    
    // Cerrar modal al hacer click fuera
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    });
    
    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
    });
});
</script>

<script>
// Función para toggle del menú (sin cambios en la lógica)
function toggleMenu(menuId) {
    const menu = document.getElementById(menuId);
    const icon = document.getElementById(menuId + 'Icon');
    
    if (menu && icon) {
        menu.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }
}

// Funcionalidad para móviles (sin cambios en la lógica)
document.addEventListener('DOMContentLoaded', function() {
    const mobileToggle = document.getElementById('sidebarMobileToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (mobileToggle && sidebar && overlay) {
        mobileToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-open');
            overlay.classList.toggle('active');
        });
        
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('sidebar-open');
            overlay.classList.remove('active');
        });
    }
    
    const sidebarLinks = document.querySelectorAll('#sidebar a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('sidebar-open');
                overlay.classList.remove('active');
            }
        });
    });
});

window.addEventListener('resize', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (window.innerWidth > 768 && sidebar && overlay) {
        sidebar.classList.remove('sidebar-open');
        overlay.classList.remove('active');
    }
});
</script>