<?php
include '../includes/middleware.php';
verificarSesion();
include "../includes/conexionbd.php";
require_once '../errores/error_handler.php';



// session_start();
// echo $_SESSION["user_id"];

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

?>

<?php
// Incluir el manejador de errores
require_once '../errores/error_handler.php';

// Algunos ejemplos de errores para probar:
// $undefined_var = $undefined_var + 1; // Esto generará un Notice

// Error deprecated (simulando un error de deprecated)
// $html = htmlspecialchars(null); // Esto generará un warning
?>
<?php

// include "../includes/conexionbd.php";

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

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Auditor
    </title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="../assets/css/profile.css">
    <!-- <script src="../assets/js/dash.js"></script> -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" /> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <!-- Asegúrate de cargar jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Luego, carga Toastr -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


</head>

<body class="bg-gray-100 ext-gray-600">


    <!-- Navbar -->
    <?php include 'includes/nav.php' ?>


    <div class="flex">
        <!-- Sidebar -->

        <?php include 'includes/aside.php' ?>
        <main class="flex-1 p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 xl:grid-cols-1 gap-2 mb-2">
                <div class="grid grid-cols-1  gap-2 mb-2">

                    <div class="bg-white p-4 shadow rounded-lg">
                        <h4 class="mb-4 font-semibold text-gray-800">Gestión de Movimientos en la Base de Datos</h4>

                        <!-- Filtros -->
                        <form id="form-filtros" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div>
                                <label for="fecha_inicio">Fecha Inicio</label>
                                <input type="date" id="fecha_inicio" class="w-full border-gray-300 rounded-lg" />
                            </div>
                            <div>
                                <label for="fecha_fin">Fecha Fin</label>
                                <input type="date" id="fecha_fin" class="w-full border-gray-300 rounded-lg" />
                            </div>
                            <div>
                                <label for="operacion" class="block text-sm font-medium text-gray-700">Operación</label>
                                <select id="operacion" name="operacion" class="mt-1 p-2 block w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Todas</option>
                                    <option value="INSERT">Insert</option>
                                    <option value="UPDATE">Update</option>
                                    <option value="DELETE">Delete</option>
                                </select>
                            </div>
                            <div>
                                <label for="tabla_afectada">Tabla Afectada</label>
                                <select id="tabla_afectada" class="p-2 w-full border-gray-300 rounded-lg">
                                    <option value="">Todas</option>
                                </select>
                            </div>
                            <button type="submit" class="col-span-1 sm:col-span-3 bg-blue-500 text-white rounded-lg p-2">Filtrar</button>
                        </form>

                        <table class="w-full mt-4 border-collapse border border-gray-200">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th>ID</th>
                                    <th>Usuario</th>
                                    <th>Tabla Afectada</th>
                                    <th>Operación</th>
                                    <th>Registro ID</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody id="tabla-body"></tbody>
                        </table>


                        <!-- Paginación -->
                        <div class="mt-4 flex justify-between items-center">
                            <button id="anterior" class="px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg shadow hover:bg-gray-400 focus:outline-none">Anterior</button>
                            <span class="paginacion-info text-sm text-gray-500">Página 1</span>
                            <button id="siguiente" class="px-4 py-2 bg-gray-300 text-gray-700 font-semibold rounded-lg shadow hover:bg-gray-400 focus:outline-none">Siguiente</button>
                        </div>
                    </div>


                </div>
            </div>
        </main>

    </div>








    <script>
        var mobileMenuButton = document.getElementById('mobile-menu-button');
        var sideMenu = document.querySelector('.z-20');

        function toggleMobileMenu() {
            sideMenu.classList.toggle('hidden');
        }

        mobileMenuButton.addEventListener('click', toggleMobileMenu);
    </script>
    <script>
        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        var initials = '<?php echo strtoupper(substr($username, 0, 1)); ?>';

        var randomColor = getRandomColor();

        document.addEventListener('DOMContentLoaded', function() {
            var profileButton = document.querySelector('.profile-button');
            profileButton.style.backgroundColor = randomColor;
            profileButton.innerHTML = '<span class="text-white font-bold">' + initials + '</span>';
        });
    </script>
    <script>
        function toggleFullScreen() {
            const fullscreenIcon = document.getElementById('fullscreen-icon');
            const exitFullscreenIcon = document.getElementById('exit-fullscreen-icon');

            if (!document.fullscreenElement) {
                // Entrar en pantalla completa
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) { // Firefox
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) { // Chrome, Safari y Opera
                    document.documentElement.webkitRequestFullscreen();
                } else if (document.documentElement.msRequestFullscreen) { // IE/Edge
                    document.documentElement.msRequestFullscreen();
                }
                fullscreenIcon.classList.add('hidden');
                exitFullscreenIcon.classList.remove('hidden');
            } else {
                // Salir de pantalla completa
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) { // Firefox
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) { // Chrome, Safari y Opera
                    document.webkitExitFullscreen();
                } else if (document.msExitFullscreen) { // IE/Edge
                    document.msExitFullscreen();
                }
                fullscreenIcon.classList.remove('hidden');
                exitFullscreenIcon.classList.add('hidden');
            }
        }
    </script>

    <script>
        function toggleMenu(menuId) {
            const menu = document.getElementById(menuId);
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        }
    </script>
    <script>
        let paginaActual = 1; // Página inicial

        // Función para cargar registros
        async function cargarRegistros(filtros = {}) {
            filtros.pagina = paginaActual; // Agregar la página al filtro

            try {
                const response = await fetch('get_auditor.php', {
                    method: 'POST',
                    body: JSON.stringify(filtros),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });

                const {
                    data,
                    total,
                    registros_por_pagina,
                    pagina
                } = await response.json();

                // Actualizar la interfaz de los registros
                const tableBody = document.getElementById('tabla-body');
                tableBody.innerHTML = '';
                data.forEach(row => {
                    tableBody.innerHTML += `
                <tr class='text-center'>
                    <td>${row.id}</td>
                    <td>${row.usuario}</td>
                    <td>${row.tabla_afectada}</td>
                    <td>${row.operacion}</td>
                    <td>${row.registro_id}</td>
                    <td>${row.fecha}</td>
                </tr>
            `;
                });

                // Actualizar la paginación
                actualizarPaginacion(pagina, Math.ceil(total / registros_por_pagina));
            } catch (error) {
                console.error('Error al cargar registros:', error.message);
            }
        }

        // Función para actualizar la paginación
        function actualizarPaginacion(pagina, totalPaginas) {
            const paginacionInfo = document.querySelector('.paginacion-info');
            paginacionInfo.textContent = `Página ${pagina} de ${totalPaginas}`;

            const botonAnterior = document.getElementById('anterior');
            const botonSiguiente = document.getElementById('siguiente');

            // Desactivar/activar los botones de paginación
            botonAnterior.disabled = pagina === 1;
            botonSiguiente.disabled = pagina === totalPaginas;

            // Actualizar los eventos de los botones
            botonAnterior.onclick = () => cambiarPagina(pagina - 1);
            botonSiguiente.onclick = () => cambiarPagina(pagina + 1);
        }

        // Función para cambiar de página
        function cambiarPagina(nuevaPagina) {
            if (nuevaPagina < 1) return; // No permitir página menor a 1
            paginaActual = nuevaPagina;
            cargarRegistros();
        }

        // Al cargar la página, carga los registros iniciales
        document.addEventListener('DOMContentLoaded', async function() {
            try {
                // Cargar registros iniciales
                await cargarRegistros(); // Función para obtener registros
            } catch (error) {
                console.error('Error al cargar tablas afectadas:', error.message);
            }
        });
    </script>
</body>

</html>