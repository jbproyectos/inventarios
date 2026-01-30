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
        <?php

        $user_id = $_SESSION["user_id"];

        try {
            $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE Id_Usuario = :user_id");
            $consulta->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $consulta->execute();

            $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

            if ($resultado) {
                $nombre_usuario = $resultado['nombre'];
                $apellidos = $resultado['apellido'];
                $rol = $resultado['rolActual'];

                echo  $nombre_usuario . " " . $apellidos;
            } else {
                echo 'Usuario no encontrado';
            }
        } catch (PDOException $e) {
            echo 'Error en la consulta: ' . $e->getMessage();
        }
        ?>
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-1 gap-2 mb-2">
                <div class="flex h-auto flex-col gap-6 mt-8 mb-8 md:flex-row">
                    <!-- Primer div que abarca dos columnas -->
                    <div class="w-full max-w-sm bg-white  rounded-lg  light:bg-gray-800 light:border-gray-700">
                        <div class="flex justify-end px-4 pt-4">
                            <button id="dropdownButton" data-dropdown-toggle="dropdown" class="inline-block text-gray-500 light:text-gray-400 hover:bg-gray-100 light:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-200 light:focus:ring-gray-700 rounded-lg text-sm p-1.5" type="button">
                                <span class="sr-only">Open dropdown</span>
                                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                                    <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
                                </svg>
                            </button>
                            <!-- Dropdown menu -->
                            <div id="dropdown" class="z-10 hidden text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow w-44 light:bg-gray-700">
                                <ul class="py-2" aria-labelledby="dropdownButton">
                                    <li>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 light:hover:bg-gray-600 light:text-gray-200 light:hover:text-white">Solicitar edición</a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                        <div class="flex flex-col items-center pb-10">

                            <div class="relative inline-flex items-center justify-center w-12 h-12 overflow-hidden bg-gray-100 rounded-full light:bg-gray-600 ring-4 ring-gray-300 light:ring-slate-900">
                                <?php
                                include '../includes/conexionbd.php';

                                $sql = "SELECT nombre FROM usuarios WHERE Id_Usuario = :user_id";
                                $stmt = $conexion->prepare($sql);
                                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                                $stmt->execute();

                                $userData = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($userData) {
                                    $username = $userData['nombre'];
                                } else {
                                    $username = "Usuario Desconocido";
                                }
                                ?>


                                <div class="aquivainicial relative inline-flex items-center justify-center w-10 h-10 overflow-hidden bg-gray-100 rounded-full light:bg-gray-600">
                                    <span class="font-medium text-gray-600 light:text-gray-300"></span>
                                </div>

                            </div>
                            <h5 class="mb-1 text-xl font-medium text-gray-900 light:text-white mt-4">
                                <?php

                                $user_id = $_SESSION["user_id"];

                                try {
                                    $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE Id_Usuario = :user_id");
                                    $consulta->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                                    $consulta->execute();

                                    $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                                    if ($resultado) {
                                        $nombre_usuario = $resultado['nombre'];
                                        $apellidos = $resultado['apellido'];
                                        $rol = $resultado['rolActual'];

                                        echo '<div class="flex flex-col">';
                                        echo '<span class="text-right">' . $nombre_usuario . " " . $apellidos . '</span>';
                                        echo '</div>';
                                        echo '</h5>';
                                        
                                    } else {
                                        echo 'Usuario no encontrado';
                                    }
                                } catch (PDOException $e) {
                                    echo 'Error en la consulta: ' . $e->getMessage();
                                }
                                ?>
                                <!-- <div class="flex mt-4 md:mt-6">
                                        <a href="#" class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800">Add friend</a>
                                        <a href="#" class="inline-flex items-center px-4 py-2 text-sm font-medium text-center text-gray-900 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 light:bg-gray-800 light:text-white light:border-gray-600 light:hover:bg-gray-700 light:hover:border-gray-700 light:focus:ring-gray-700 ms-3">Message</a>
                                    </div> -->
                                <?php

                                $usuario_id = $_SESSION['user_id'];

                                $sql = "
                                    SELECT u.*, o.nombre AS nombre_oficina, d.nombre AS nombre_departamento, p.nombre AS nombre_puesto
                                    FROM usuarios u
                                    LEFT JOIN oficina o ON u.id_oficina = o.id_oficina
                                    LEFT JOIN departamentos d ON u.id_departamento = d.id_departamento
                                    LEFT JOIN puestos p ON u.id_puesto = p.id_puesto
                                    WHERE u.Id_Usuario = :usuario_id
                                ";
                                $stmt = $conexion->prepare($sql);
                                $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
                                $stmt->execute();
                                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                                if ($usuario) {
                                ?>
                                    <div class="border-t light:border-gray-700 mt-4 w-full p-4 text-center">
                                        <div class="mb-2">
                                            <span class="font-medium text-gray-600 light:text-gray-400">Puesto:</span>
                                            <p class="font-medium text-gray-600 light:text-gray-300"><?= htmlspecialchars($usuario['nombre_puesto']) ?></p>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-medium text-gray-600 light:text-gray-400">Oficina:</span>
                                            <p class="font-medium text-gray-600 light:text-gray-300"><?= htmlspecialchars($usuario['nombre_oficina']) ?></p>
                                        </div>
                                        <div class="mb-2">
                                            <span class="font-medium text-gray-600 light:text-gray-400">Departamento:</span>
                                            <p class="font-medium text-gray-600 light:text-gray-300"><?= htmlspecialchars($usuario['nombre_departamento']) ?></p>

                                        </div>
                                        <div class="mb-2">
                                            <span class="font-medium text-gray-600 light:text-gray-400">Email:</span>
                                            <p class="font-medium text-gray-600 light:text-gray-300"><?= htmlspecialchars($usuario['email']) ?></p>

                                        </div>
                                        <div class="mb-2">
                                            <span class="font-medium text-gray-600 light:text-gray-400">Fecha de registro:</span>
                                            <p class="font-medium text-gray-600 light:text-gray-300"><?= htmlspecialchars($usuario['fechaRegistro']) ?></p>

                                        </div>
                                        <div class="mb-2">
                                            <span class="font-medium text-gray-600 light:text-gray-400">Fecha ultimo ingreso:</span>
                                            <p class="font-medium text-gray-600 light:text-gray-300"><?= htmlspecialchars($usuario['fechaUltimoIngreso']) ?></p>

                                        </div>
                                        <!-- <div class="mb-2">
                                                <span class="font-medium text-gray-600 light:text-gray-400">Ultimo rol:</span>
                                                <p class="font-medium text-gray-600 light:text-gray-300"><?= htmlspecialchars($usuario['ultimoRol']) ?></p>
                                            </div> -->
                                    </div>
                                <?php
                                } else {
                                    echo "Usuario no encontrado";
                                }
                                ?>


                        </div>
                    </div>

                    <div class="w-full p-4 bg-white rounded-xl shadow-xs light:bg-gray-800 h-auto">
                        <div class="min-w-0 p-4 bg-white rounded-xl light:bg-gray-800">
                            <h4 class="mb-4 font-semibold text-gray-800 light:text-gray-300">
                                Equipos asignados
                            </h4>
                            <div class="">

                                <div style="overflow-x: auto;">
                                    <?php

                                    // Obtén el nombre completo del usuario desde la sesión
                                    $nombre_completo = $_SESSION["user_id"]; // Suponiendo que guardas el nombre completo en la sesión

                                    try {
                                        // Consultar los datos del usuario por nombre completo
                                        $consulta_usuario = $conexion->prepare("SELECT * FROM usuarios WHERE Id_Usuario = :user_id");
                                        $consulta_usuario->bindParam(':user_id', $nombre_completo, PDO::PARAM_STR);
                                        $consulta_usuario->execute();
                                        $resultado_usuario = $consulta_usuario->fetch(PDO::FETCH_ASSOC);

                                        if ($resultado_usuario) {
                                            $nombre_usuario = $resultado_usuario['nombre'];
                                            $apellidos = $resultado_usuario['apellido'];
                                            $rol = $resultado_usuario['rolActual'];
                                            $nameComplete = $nombre_usuario . ' ' . $apellidos;



                                            // Consultar los equipos asignados al usuario por nombre completo
                                            $consulta_computadoras = $conexion->prepare("SELECT * FROM computadora WHERE asignado_a = :nameComplete");
                                            $consulta_computadoras->bindParam(':nameComplete', $nameComplete, PDO::PARAM_STR);
                                            $consulta_computadoras->execute();
                                            $computadoras = $consulta_computadoras->fetchAll(PDO::FETCH_ASSOC);

                                            $consulta_mobiliario = $conexion->prepare("SELECT * FROM mobiliario WHERE asignado_a = :nombre_completo");
                                            $consulta_mobiliario->bindParam(':nombre_completo', $nombre_completo, PDO::PARAM_STR);
                                            $consulta_mobiliario->execute();
                                            $mobiliario = $consulta_mobiliario->fetchAll(PDO::FETCH_ASSOC);

                                            $consulta_celulares = $conexion->prepare("SELECT * FROM celular WHERE asignado_a = :nombre_completo");
                                            $consulta_celulares->bindParam(':nombre_completo', $nombre_completo, PDO::PARAM_STR);
                                            $consulta_celulares->execute();
                                            $celulares = $consulta_celulares->fetchAll(PDO::FETCH_ASSOC);

                                            // Mostrar tarjetas de equipos asignados
                                            echo '<div class="flex flex-wrap gap-4">';

                                            // Computadoras
                                            if (count($computadoras) > 0) {
                                                foreach ($computadoras as $computadora) {
                                                    echo '<div class="w-full max-w-sm bg-white border border-gray-200 rounded-lg  light:bg-gray-800 light:border-gray-700">';

                                                    // Botón de opciones
                                                    echo '<div class="flex justify-end px-4 pt-4">';
                                                    echo '<button id="dropdownButton" data-dropdown-toggle="dropdown" class="inline-block text-gray-500 light:text-gray-400 hover:bg-gray-100 light:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-200 light:focus:ring-gray-700 rounded-lg text-sm p-1.5" type="button">';
                                                    echo '<span class="sr-only">Open dropdown</span>';
                                                    echo '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">';
                                                    echo '<path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>';
                                                    echo '</svg>';
                                                    echo '</button>';
                                                    echo '</div>';

                                                    // Imagen de la computadora (imagen de stock)
                                                    echo '<div class="flex flex-col items-center pb-10">';
                                                    echo '<img class="w-24 h-24 mb-3 rounded-full shadow-sm" src="https://picsum.photos/200" alt="Imagen aleatoria" />'; // Imagen de stock
                                                    echo '<h5 class="mb-1 text-xl font-medium text-gray-900 light:text-white">' . $computadora['marca'] . '</h5>';
                                                    echo '<span class="text-sm text-gray-500 light:text-gray-400">Modelo: ' . $computadora['modelo'] . '</span>';
                                                    echo '<p class="text-sm text-gray-500 light:text-gray-400">Estado: ' . $computadora['condicion'] . '</p>';
                                                    echo '<span class="text-sm text-gray-500 light:text-gray-400">Tipo de disco: ' . $computadora['tipoDeDisco'] . '</span>';
                                                    echo '<p class="text-sm text-gray-500 light:text-gray-400">Procesador: ' . $computadora['procesador'] . '</p>';
                                                    echo '<p class="text-sm text-gray-500 light:text-gray-400">Ram: ' . $computadora['ram'] . '</p>';
                                                    echo '<p class="text-sm text-gray-500 light:text-gray-400">Fecha de asignacion: ' . $computadora['fechaDeAsignacion'] . '</p>';


                                                    echo '</div>';

                                                    echo '</div>'; // Cierre del div de la tarjeta
                                                }
                                            } else {
                                                echo '<div class="w-full p-4 bg-white rounded-xl shadow-lg light:bg-gray-800">No tienes computadoras asignadas.</div>';
                                            }


                                            // Mobiliario
                                            if (count($mobiliario) > 0) {
                                                foreach ($mobiliario as $item) {
                                                    echo '<div class="w-full md:w-1/3 p-4 bg-white rounded-xl shadow-lg light:bg-gray-800">';
                                                    echo '<div class="flex justify-end px-4 pt-4">';
                                                    echo '<button id="dropdownButton" data-dropdown-toggle="dropdown" class="inline-block text-gray-500 light:text-gray-400 hover:bg-gray-100 light:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-200 light:focus:ring-gray-700 rounded-lg text-sm p-1.5" type="button">';
                                                    echo '<span class="sr-only">Open dropdown</span>';
                                                    echo '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">';
                                                    echo '<path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>';
                                                    echo '</svg>';
                                                    echo '</button>';
                                                    echo '</div>';
                                                    echo '<div class="flex flex-col items-center pb-10">';
                                                    // Imagen de stock (puedes cambiar la URL de la imagen según sea necesario)
                                                    echo '<img class="w-24 h-24 mb-3 rounded-full shadow-sm" src="https://placekitten.com/200/200" alt="Imagen de mobiliario"/>';
                                                    echo '<h5 class="mb-1 text-xl font-medium text-gray-900 light:text-white">Mobiliario: ' . $item['tipo'] . '</h5>';
                                                    echo '<p class="text-sm text-gray-500 light:text-gray-400">Descripción: ' . $item['descripcion'] . '</p>';
                                                    echo '<p class="text-sm text-gray-500 light:text-gray-400">Estado: ' . $item['estado'] . '</p>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                            } else {
                                                echo '<div class="w-full p-4 bg-white rounded-xl shadow-lg light:bg-gray-800">No tienes mobiliario asignado.</div>';
                                            }

                                            // Celulares
                                            if (count($celulares) > 0) {
                                                foreach ($celulares as $celular) {
                                                    echo '<div class="w-full md:w-1/3 p-4 bg-white rounded-xl shadow-sm light:bg-gray-800">';
                                                    echo '<div class="flex justify-end px-4 pt-4">';
                                                    echo '<button id="dropdownButton" data-dropdown-toggle="dropdown" class="inline-block text-gray-500 light:text-gray-400 hover:bg-gray-100 light:hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-200 light:focus:ring-gray-700 rounded-lg text-sm p-1.5" type="button">';
                                                    echo '<span class="sr-only">Open dropdown</span>';
                                                    echo '<svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">';
                                                    echo '<path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z"/>';
                                                    echo '</svg>';
                                                    echo '</button>';
                                                    echo '</div>';
                                                    echo '<div class="flex flex-col items-center pb-10">';
                                                    // Imagen de stock (puedes cambiar la URL de la imagen según sea necesario)
                                                    echo '<img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="https://placekitten.com/200/200" alt="Imagen de celular"/>';
                                                    echo '<h5 class="mb-1 text-xl font-medium text-gray-900 light:text-white">Celular: ' . $celular['marca'] . ' ' . $celular['modelo'] . '</h5>';
                                                    echo '<p class="text-sm text-gray-500 light:text-gray-400">Estado: ' . $celular['estado'] . '</p>';
                                                    echo '</div>';
                                                    echo '</div>';
                                                }
                                            } else {
                                                echo '<div class="w-full p-4 bg-white rounded-xl shadow-lg light:bg-gray-800">No tienes celulares asignados.</div>';
                                            }

                                            echo '</div>';
                                        } else {
                                            echo 'Usuario no encontrado';
                                        }
                                    } catch (PDOException $e) {
                                        echo 'Error en la consulta: ' . $e->getMessage();
                                    }
                                    ?>

                                </div>
                            </div>
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
</body>

</html>