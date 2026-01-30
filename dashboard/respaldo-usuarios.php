<?php
include '../includes/middleware.php';
verificarSesion();
include "../includes/conexionbd.php";
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
    <title>Usuarios</title>
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
    <!--  -->

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

                        <h4 class="mb-4 font-semibold text-gray-800 light:text-gray-300">Gestion de Usuarios </h4>

                        <div class=" sm:rounded-lg">
                            <div class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white light:bg-gray-800">
                                <?php if ($canAdd) { ?>

                                    <!-- Modal toggle -->
                                    <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="inline-flex text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800" type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                                        </svg>
                                        Registrar usuario
                                    </button>
                                <?php } ?>
                                <!-- Main modal -->
                                <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                    <div class="relative p-4 w-full max-w-md max-h-full">
                                        <!-- Modal content -->
                                        <div class="relative bg-white rounded-lg shadow light:bg-gray-700">
                                            <!-- Modal header -->
                                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t light:border-gray-600">
                                                <h3 class="text-lg font-semibold text-gray-900 light:text-white">
                                                    Crear nuevo usuario
                                                </h3>
                                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center light:hover:bg-gray-600 light:hover:text-white" data-modal-toggle="crud-modal">
                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                    </svg>
                                                    <span class="sr-only">Cerrar ventana</span>
                                                </button>
                                            </div>
                                            <!-- Modal body -->
                                            <form id="userForm" action="#" class="p-4 md:p-5">
                                                <div class="grid gap-4 mb-4 grid-cols-2">
                                                    <div class="col-span-2 ">
                                                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Nombre<span class="text-red-500">*</span></label>
                                                        <input type="text" name="name" id="name" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="Juan" required="">
                                                    </div>
                                                    <div class="col-span-2 ">
                                                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Apellidos<span class="text-red-500">*</span></label>
                                                        <input type="tezt" name="apellido" id="apellido" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="Bautista" required="">
                                                    </div>
                                                    <div class="col-span-2 ">
                                                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">E-mail<span class="text-red-500">*</span></label>
                                                        <input type="email" name="email" id="email" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="dev@kabzo.org" required="">
                                                    </div>
                                                    <div class="col-span-2 sm:col-span-1 relative">
                                                        <label for="contrasena" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">
                                                            Contraseña<span class="text-red-500">*</span>
                                                            <!-- Ícono de información para el tooltip -->
                                                            <span class="text-blue-500 cursor-pointer" onclick="toggleTooltip()">ℹ️</span>
                                                        </label>
                                                        <input type="password" name="contrasena" id="contrasena" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="********" required>

                                                        <!-- Tooltip con los requisitos de la contraseña -->
                                                        <div id="password-tooltip" class="absolute z-10 hidden bg-white border border-gray-200 rounded-lg shadow-lg p-4 w-64 text-sm text-gray-600 light:bg-gray-800 light:border-gray-700 light:text-gray-300">
                                                            La contraseña debe tener al menos:
                                                            <ul class="list-disc list-inside mt-2">
                                                                <li id="length" class="text-red-500">8 caracteres</li>
                                                                <li id="uppercase" class="text-red-500">1 letra mayúscula</li>
                                                                <li id="number" class="text-red-500">1 número</li>
                                                                <li id="special" class="text-red-500">1 carácter especial</li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <div class="col-span-2 sm:col-span-1">
                                                        <label for="verificar" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Verificar contraseña<span class="text-red-500">*</span></label>
                                                        <input type="password" name="verificar" id="verificar" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="********" required>
                                                        <div id="password-match" class="text-sm text-red-500 light:text-red-400 mt-2"></div>

                                                    </div>
                                                    <!-- Formulario con el select -->
                                                    <?php
                                                    try {
                                                        $consulta = $conexion->query("SELECT * FROM puestos");
                                                        $puestos = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                                    } catch (PDOException $e) {
                                                        die('Error en la consulta: ' . $e->getMessage());
                                                    }
                                                    ?>

                                                    <div class="col-span-2 sm:col-span-1">
                                                        <label for="category" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Puesto<span class="text-red-500">*</span></label>
                                                        <select id="puestos" name="puestos" onchange="mostrarListaSiguiente()" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500">
                                                            <option class="light:text-white" selected="">Selecciona puesto</option>

                                                            <?php foreach ($puestos as $puesto) : ?>
                                                                <option class="light:text-white" value="<?= $puesto['Id_puesto'] ?>"><?= htmlspecialchars($puesto['nombre']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div id="nivel-container" class="col-span-2 sm:col-span-1 hidden">
                                                        <label for="nivel" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">
                                                            Selecciona personal <span class="text-red-500">*</span>
                                                        </label>
                                                        <select id="nivel" name="nivel[]" multiple class="w-full p-2 border rounded-md focus:ring focus:ring-blue-300 light:bg-gray-800 light:border-gray-700 light:text-white">
                                                            <!-- <option value="" disabled>Selecciona</option> -->
                                                        </select>
                                                    </div>






                                                    <?php
                                                    try {
                                                        $consulta = $conexion->query("SELECT * FROM oficina");
                                                        $oficinas = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                                    } catch (PDOException $e) {
                                                        die('Error en la consulta: ' . $e->getMessage());
                                                    }
                                                    ?>
                                                    <div class="col-span-2 sm:col-span-1">
                                                        <label for="category" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Oficina<span class="text-red-500">*</span></label>
                                                        <select id="oficina" name="oficina" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500">
                                                            <option class="light:text-white" selected="">Selecciona Oficina</option>

                                                            <?php foreach ($oficinas as $oficina) : ?>
                                                                <option class="light:text-white" value="<?= $oficina['Id_Oficina'] ?>"><?= htmlspecialchars($oficina['nombre']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <?php
                                                    try {
                                                        $consulta = $conexion->query("SELECT * FROM departamentos");
                                                        $depas = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                                    } catch (PDOException $e) {
                                                        die('Error en la consulta: ' . $e->getMessage());
                                                    }
                                                    ?>
                                                    <div class="col-span-2 sm:col-span-1">
                                                        <label for="category" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Departamento<span class="text-red-500">*</span></label>
                                                        <select
                                                            id="Id_departamentos"
                                                            name="Id_departamentos"
                                                            class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500">
                                                            <option value="" selected>Selecciona Departamento</option>
                                                            <?php foreach ($depas as $depa) : ?>
                                                                <option value="<?= htmlspecialchars($depa['Id_departamento']) ?>"><?= htmlspecialchars($depa['nombre']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>

                                                    </div>
                                                    <?php
                                                    // Obtener roles desde la base de datos
                                                    try {
                                                        $consulta = $conexion->query("SELECT * FROM roless");
                                                        $roles = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                                    } catch (PDOException $e) {
                                                        die('Error en la consulta de roles: ' . $e->getMessage());
                                                    }
                                                    ?>

                                                    <div class="col-span-2 sm:col-span-1">
                                                        <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                            Rol <span class="text-red-500">*</span>
                                                        </label>
                                                        <select id="role" name="role"
                                                            class="block w-full p-2 pl-3 pr-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 light:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                            <option value="" selected disabled>Selecciona un rol</option>
                                                            <?php foreach ($roles as $rol) : ?>
                                                                <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                </div>

                                                <button type="submit" id="saveenterprise" class="text-white inline-flex items-center w-full mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800">
                                                    <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Agregar usuarios
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>



                                <label for="table-search" class="sr-only">Buscar</label>
                                <div class="relative">
                                    <input type="text" id="table-search-users" class="w-full block p-2  text-sm text-gray-900 border border-gray-300 rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="Buscar usuario">
                                </div>

                            </div>
                            <div class="w-full text-sm text-left rtl:text-right text-gray-500 light:text-gray-400" style="overflow-x: auto;">

                                <table id="user-table" class="w-full text-sm text-left rtl:text-right text-gray-500 light:text-gray-400">
                                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 light:bg-gray-700 light:text-gray-400">
                                        <tr>

                                            <th scope="col" class="px-6 py-3">
                                                Nombre
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Fecha de registro
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Status
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Puesto
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Rol
                                            </th>
                                            <th scope="col" class="">
                                                Departamento
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Oficina
                                            </th>
                                            <!-- <th scope="col" class="">
                        Personal
                    </th> -->
                                            <th scope="col" class="">
                                                Departamentos asignados
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Establecer el número de registros por página
                                        $registrosPorPagina = 10;

                                        // Obtener la página actual desde la URL (por defecto será la página 1)
                                        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

                                        // Calcular el índice del primer registro de la página actual
                                        $offset = ($paginaActual - 1) * $registrosPorPagina;

                                        $idUsuario = $_SESSION["user_id"];

                                        // Verificar si el usuario tiene departamentos asignados
                                        $queryDepartamentosAsignados = "SELECT COUNT(*) AS total FROM usuarios_departamentos WHERE Id_usuario = :idUsuario";
                                        $stmtDepartamentosAsignados = $conexion->prepare($queryDepartamentosAsignados);
                                        $stmtDepartamentosAsignados->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
                                        $stmtDepartamentosAsignados->execute();
                                        $resultadoDepartamentos = $stmtDepartamentosAsignados->fetch(PDO::FETCH_ASSOC);

                                        if ($resultadoDepartamentos['total'] == 0) {
                                            // Si no tiene departamentos asignados, mostrar una alerta y detener la ejecución
                                            echo '
                                                <div class="bg-red-100 border border-red-400 text-red-700 px-8 py-4 rounded-lg text-center relative">
                                                    <button class="absolute top-0 right-0 p-2" onclick="this.parentElement.style.display=\'none\'">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                    <strong class="font-bold">¡Atención!</strong>
                                                    <span class="block sm:inline">No tienes departamentos asignados. Por favor, contacta al administrador.</span>
                                                </div>';
                                            exit();
                                        }

                                        // Obtener el total de registros que el usuario puede ver
                                        $queryTotal = $conexion->prepare("
                                                    SELECT COUNT(*) FROM usuarios
                                                    WHERE Id_departamento IN (
                                                        SELECT Id_departamento FROM usuarios_departamentos WHERE Id_usuario = :idUsuario
                                                    )
                                                ");
                                        $queryTotal->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
                                        $queryTotal->execute();
                                        $totalRegistros = $queryTotal->fetchColumn();

                                        // Calcular el número total de páginas con los registros visibles para el usuario
                                        $totalPaginas = $totalRegistros > 0 ? ceil($totalRegistros / $registrosPorPagina) : 1;

                                        // Consulta para obtener los registros de usuarios solo de los departamentos asignados al usuario
                                        $consulta = $conexion->prepare("
                                                SELECT usuarios.*, oficina.nombre AS nombre_oficina, departamentos.nombre AS nombre_departamento, 
                                                    puestos.nombre AS nombre_puesto, roless.nombre AS nombre_rol
                                                FROM usuarios
                                                LEFT JOIN oficina ON usuarios.Id_oficina = oficina.Id_oficina
                                                LEFT JOIN departamentos ON usuarios.Id_departamento = departamentos.Id_departamento
                                                LEFT JOIN puestos ON usuarios.Id_puesto = puestos.Id_puesto
                                                LEFT JOIN roless ON usuarios.rolActual = roless.id
                                                WHERE usuarios.Id_departamento IN (
                                                    SELECT Id_departamento FROM usuarios_departamentos WHERE Id_usuario = :idUsuario
                                                )
                                                LIMIT :limit OFFSET :offset
                                            ");
                                        $consulta->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
                                        $consulta->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
                                        $consulta->bindParam(':offset', $offset, PDO::PARAM_INT);
                                        $consulta->execute();
                                        $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);


                                        ?>

                                        <?php
                                        // Función para generar un color único basado en el valor
                                        function generarColor($valor)
                                        {
                                            // Usar un hash del valor para generar un color único
                                            $hash = md5($valor ?? ''); // Si $valor es null, usa una cadena vacía

                                            // Generar un color basado en el hash
                                            $r = hexdec(substr($hash, 0, 2));
                                            $g = hexdec(substr($hash, 2, 2));
                                            $b = hexdec(substr($hash, 4, 2));

                                            // Limitar los valores del color para evitar que sean demasiado oscuros o claros
                                            $r = max(min($r, 200), 50);
                                            $g = max(min($g, 200), 50);
                                            $b = max(min($b, 200), 50);

                                            // Convertir a formato rgb
                                            return "rgb($r, $g, $b)";
                                        }

                                        try {

                                            foreach ($usuarios as $usuario) {
                                                echo '<tr class="bg-white border-b light:bg-gray-800 light:border-gray-700 hover:bg-gray-50 light:hover:bg-gray-900">';
                                                echo '<th scope="row" class="flex items-center font-medium text-gray-900 light:text-white">';
                                                echo '<div class="ps-3">';
                                                echo '<div class="text-base font-semibold">' . htmlspecialchars($usuario['nombre']) . " " . htmlspecialchars($usuario['apellido']) . '</div>';
                                                echo '<div class="font-normal text-gray-500">' . htmlspecialchars($usuario['email']) . '</div>';
                                                echo '</div>';
                                                echo '</th>';
                                                echo '<td class="px-6 py-4">' . htmlspecialchars($usuario['fechaRegistro']) . '</td>';
                                                $idUsuario = $_SESSION["user_id"];

                                                // Primero, obtenemos el puesto del usuario logueado
                                                $consultaPuestoLogueado = $conexion->prepare("SELECT Id_puesto FROM usuarios WHERE Id_Usuario = :idUsuario");
                                                $consultaPuestoLogueado->bindParam(':idUsuario', $idUsuario);
                                                $consultaPuestoLogueado->execute();
                                                $puestoLogueado = $consultaPuestoLogueado->fetch(PDO::FETCH_ASSOC);

                                                // Ahora, obtenemos el puesto del usuario actual
                                                $consultaPuestoUsuario = $conexion->prepare("SELECT Id_puesto FROM puestos WHERE Id_puesto = :idPuesto");
                                                $consultaPuestoUsuario->bindParam(':idPuesto', $usuario['Id_puesto']);
                                                $consultaPuestoUsuario->execute();
                                                $puestoUsuario = $consultaPuestoUsuario->fetch(PDO::FETCH_ASSOC);

                                                // Comprobar si el puesto del usuario logueado es mayor que el del usuario actual
                                                // Si el puesto del usuario logueado tiene un valor menor que el puesto del usuario actual,
                                                // entonces no puede hacer nada al usuario (se oculta el checkbox o se muestra el mensaje).
                                                $puedeModificar = $puestoLogueado['Id_puesto'] <= $puestoUsuario['Id_puesto']; // Si el puesto logueado es menor o igual

                                                echo '<td class="px-6 py-4 text-center"> 
                                                                            <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">';

                                                                                                                if ($usuario['Id_Usuario'] != $idUsuario && $puedeModificar) {
                                                                                                                    echo '<input 
                                                                                type="checkbox" 
                                                                                id="toggle-' . $usuario['Id_Usuario'] . '" 
                                                                                class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                                                                ' . ($usuario['estatu'] == 1 ? 'checked' : '') . ' 
                                                                                onclick="cambiarEstado(' . $usuario['Id_Usuario'] . ', this.checked)" 
                                                                            />
                                                                            <label for="toggle-' . $usuario['Id_Usuario'] . '" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>';
                                                                                                                } else {
                                                                                                                    echo "<span class='text-gray-500'>
                                                                            <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' 
                                                                                stroke='currentColor' class='w-6 h-6'>
                                                                                <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' 
                                                                                    d='M6 18L18 6M6 6l12 12'/>
                                                                            </svg>
                                                                        </span>";
                                                }

                                                echo '</div></td>';




                                                // Generar color único para puesto
                                                $color_puesto = generarColor($usuario['nombre_puesto']);
                                                echo '<td class="px-6 py-4 text-center">';
                                                echo '<span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded light:bg-blue-900 light:text-blue-300" style="background-color: ' . $color_puesto . '; color: white;">'
                                                    . htmlspecialchars($usuario['nombre_puesto']) . '</span>';
                                                echo '</td>';

                                                // Generar color único para rol
                                                $color_rol = generarColor($usuario['nombre_rol']);
                                                echo '<td class="px-6 py-4 text-center">';
                                                if (empty($usuario['nombre_rol'])) {
                                                    echo '<a href="#" class="asignar-rol text-blue-500 hover:text-blue-700" data-id-usuario="' . $usuario['Id_Usuario'] . '">Asignar Rol</a>';
                                                } else {
                                                    echo '<span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded light:bg-blue-900 light:text-blue-300" style="background-color: ' . $color_rol . '; color: white;">'
                                                        . htmlspecialchars($usuario['nombre_rol']) . '</span>';
                                                }
                                                echo '</td>';



                                                // Generar color único para departamento
                                                $color_departamento = generarColor($usuario['nombre_departamento']);
                                                echo '<td class="px-6 py-4 text-center">';
                                                echo '<span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded light:bg-blue-900 light:text-blue-300" style="background-color: ' . $color_departamento . '; color: white;">'
                                                    . htmlspecialchars($usuario['nombre_departamento']) . '</span>';
                                                echo '</td>';





                                                // Determinar la tabla a consultar según el puesto
                                                $tabla = '';
                                                switch ($usuario['nombre_puesto']) {
                                                    case 'Empresa':
                                                        $tabla = 'CEO';
                                                        break;
                                                    case 'CEO':
                                                        $tabla = 'director';
                                                        break;
                                                    case 'Director':
                                                        $tabla = 'supervisor';
                                                        break;
                                                    case 'Supervisor':
                                                        $tabla = 'lider';
                                                        break;
                                                    case 'lider':
                                                        $tabla = 'staff';
                                                        break;
                                                    default:
                                                        $tabla = ''; // No hacer nada si no se cumple ningún caso
                                                }

                                                // Inicializar la variable para almacenar los nombres
                                                $nombres = '';

                                                if ($tabla && !empty($usuario['administra'])) {
                                                    try {
                                                        // Obtener los IDs separados por comas del campo `administra`
                                                        $ids = explode(',', $usuario['administra']); // Convierte la cadena en un array
                                                        $ids = array_map('trim', $ids); // Elimina espacios en blanco adicionales

                                                        // Crear la consulta para obtener los nombres
                                                        $placeholders = implode(',', array_fill(0, count($ids), '?')); // Placeholder dinámico para IN
                                                        $consulta = $conexion->prepare("SELECT nombre FROM $tabla WHERE nombre IN ($placeholders)");
                                                        $consulta->execute($ids); // Ejecuta con los IDs como parámetros

                                                        // Obtener los nombres concatenados
                                                        $resultados = $consulta->fetchAll(PDO::FETCH_COLUMN);
                                                        $nombres = implode(', ', $resultados); // Une los nombres con comas
                                                    } catch (PDOException $e) {
                                                        $nombres = 'Error al consultar nombres'; // Mensaje de error
                                                    }
                                                }

                                                // Imprimir el resultado en la tabla
                                                // Imprimir el botón "Ver personal" con modal

                                                // Generar color único para oficina
                                                // Generar color único para oficina
                                                $color_oficina = generarColor($usuario['nombre_oficina']);
                                                echo '<td class="px-6 py-4 text-center">';
                                                echo '<span class="bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded light:bg-blue-900 light:text-blue-300" style="background-color: ' . $color_oficina . '; color: white;">'
                                                    . htmlspecialchars($usuario['nombre_oficina']) . '</span>';
                                                echo '</td>';

                                                // echo '<td class="px-6 py-4">
                                                //         <button onclick="openPersonalModal(\'' . htmlspecialchars($nombres) . '\')" 
                                                //             class="text-blue-500 hover:text-blue-700 underline">
                                                //             Ver personal
                                                //         </button>
                                                //     </td>';
                                                echo '<td class="px-6 py-4">
                                <button onclick="opendepasModal(this)" 
                                    data-id="' . $usuario['Id_Usuario'] . '" 
                                    class="text-blue-500 hover:text-blue-700 underline">
                                    Departamentos asignados
                                </button>
                            </td>';
                                                echo '<td class="px-6 py-4">
                                <div class="flex space-x-4">
                                    <!-- Botón de editar -->
                                    <button class="hidden edit-button text-blue-500 hover:text-blue-700" 
                                        data-id-user="' . $usuario['Id_Usuario'] . '" title="Editar">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                            stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M17 3l4 4m0 0l-4 4m4-4H7a4 4 0 01-4-4V7a4 4 0 014-4h10a4 4 0 014 4v10a4 4 0 01-4 4H7a4 4 0 01-4-4v-4m14 0V3m0 0h-4"/>
                                        </svg>
                                    </button>

                                    <!-- Botón de ver más información -->
                                    <button class="hidden info-button text-green-500 hover:text-green-700" 
                                        data-id-user="' . $usuario['Id_Usuario'] . '" title="Ver más información">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
                                            stroke="currentColor" class="w-6 h-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M12 4v16m8-8H4"/>
                                        </svg>
                                    </button>';
                                                if ($canDelete) {

                                                    echo '<!-- Botón de eliminar -->
<button class="delete-button text-red-500 hover:text-red-700" 
    data-id-user="' . htmlspecialchars($usuario['Id_Usuario']) . '" title="Eliminar">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
        stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
            d="M6 18L18 6M6 6l12 12"/>
    </svg>
</button>';

echo '<!-- Botón de cambiar rol -->
<button class="change-role-button text-blue-500 hover:text-blue-700 ml-2" 
    data-id-user="' . htmlspecialchars($usuario['Id_Usuario']) . '" 
    data-rol-actual="' . htmlspecialchars($usuario['rolActual']) . '" 
    title="Cambiar rol">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
        stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
            d="M15.232 5.232l3.536 3.536M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4 12.5-12.5z"/>
    </svg>
</button>';
                                                } else {
                                                    echo "<span class='text-gray-500'>
                                        <svg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' 
                                            stroke='currentColor' class='w-6 h-6'>
                                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' 
                                                d='M6 18L18 6M6 6l12 12'/>
                                        </svg>
                                    </span>";
                                                }
                                                echo '</div></td>';


                                                echo '</tr>';
                                            }
                                        } catch (PDOException $e) {
                                            die('Error en la consulta: ' . $e->getMessage());
                                        }
                                        ?>
                                    </tbody>

                                </table>



                            </div>

                        </div>
                        <div class="paginacion mt-4"> 
    <ul class="flex justify-left space-x-2">
        <!-- Enlace a la página anterior -->
        <?php if ($paginaActual > 1): ?>
            <li>
                <a href="?pagina=<?= $paginaActual - 1 ?>" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                    &laquo; <!-- Esto es « -->
                </a>
            </li>
        <?php endif; ?>

        <!-- Páginas -->
        <?php
        $rangoPaginacion = 2;  // Muestra dos páginas antes y dos después de la página actual
        $startPage = max(1, $paginaActual - $rangoPaginacion);
        $endPage = min($totalPaginas, $paginaActual + $rangoPaginacion);

        if ($startPage > 1) {
            echo '<li><a href="?pagina=1" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">1</a></li>';
            if ($startPage > 2) {
                echo '<li class="px-4 py-2">...</li>';
            }
        }

        for ($i = $startPage; $i <= $endPage; $i++) {
            echo '<li>';
            echo '<a href="?pagina=' . $i . '" class="px-4 py-2 ' . ($i == $paginaActual ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300') . ' rounded-md">' . $i . '</a>';
            echo '</li>';
        }

        if ($endPage < $totalPaginas) {
            if ($endPage < $totalPaginas - 1) {
                echo '<li class="px-4 py-2">...</li>';
            }
            echo '<li><a href="?pagina=' . $totalPaginas . '" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">' . $totalPaginas . '</a></li>';
        }
        ?>

        <!-- Enlace a la página siguiente -->
        <?php if ($paginaActual < $totalPaginas): ?>
            <li>
                <a href="?pagina=<?= $paginaActual + 1 ?>" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                    &raquo; <!-- Esto es >> -->
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>
                    </div>
                </div>


            </div>
        </main>

    </div>


    <!-- Modal -->
    <div id="personalModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold">Personal Administrado</h3>
                <button onclick="closePersonalModal()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-4">
                <table class="min-w-full text-left text-sm text-gray-500">
                    <thead class="border-b text-gray-700 font-medium">
                        <tr>
                            <th class="py-2 px-4">#</th>
                            <th class="py-2 px-4">Nombre</th>
                        </tr>
                    </thead>
                    <tbody id="personalTableBody" class="divide-y">
                        <!-- Aquí se generarán dinámicamente las filas -->
                    </tbody>
                </table>
            </div>
            <div class="mt-4 text-right">
                <button onclick="closePersonalModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <div id="departamentosModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white rounded-lg shadow-lg w-1/2 p-6">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-semibold">Departamentos Asignados</h3>
                <button onclick="closeDepartamentosModal()" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="mt-4">
                <h4 class="text-lg font-medium">Departamentos Asignados</h4>
                <div id="departamentosAsignados" class="flex flex-wrap gap-2 mt-2">
                    <!-- Aquí se generarán dinámicamente los badges de departamentos asignados -->
                </div>
            </div>
            <div class="mt-4">
                <h4 class="text-lg font-medium">Departamentos Disponibles</h4>
                <div id="departamentosDisponibles" class="flex flex-wrap gap-2 mt-2">
                    <!-- Aquí se generarán dinámicamente los badges de departamentos disponibles -->
                </div>
            </div>
            <div class="mt-4 text-right">
                <button onclick="guardarDepartamentosAsignados()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Guardar
                </button>
                <button onclick="closeDepartamentosModal()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Cerrar
                </button>
            </div>
        </div>
    </div>


    <!-- Contenedor donde se cargará el modal -->




    <script>
        let usuarioActualId = null; // Variable para almacenar el ID del usuario actual
        let departamentosSeleccionados = new Set(); // Conjunto para almacenar los IDs de los departamentos seleccionados

        // Función para abrir el modal y cargar los departamentos
        function opendepasModal(button) {
            usuarioActualId = button.getAttribute('data-id'); // Establecer el ID del usuario actual
            console.log("Usuario ID:", usuarioActualId);

            const modal = document.getElementById('departamentosModal');
            modal.classList.remove('hidden');

            // Cargar los departamentos asignados y disponibles
            cargarDepartamentosAsignados(usuarioActualId);
            cargarDepartamentosDisponibles(usuarioActualId);
        }

        // Función para cargar los departamentos asignados
        function cargarDepartamentosAsignados(usuarioId) {
            Swal.fire({
                title: 'Cargando...',
                text: 'Estamos obteniendo los departamentos asignados.',
                icon: 'info',
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`user/obtener_departamentos.php?usuarioId=${usuarioId}`)
                .then(response => response.json())
                .then(data => {
                    Swal.close(); // Cierra la alerta de carga
                    const departamentosAsignadosDiv = document.getElementById('departamentosAsignados');
                    departamentosAsignadosDiv.innerHTML = '';

                    if (data.success) {
                        data.departamentos.forEach(departamento => {
                            const badge = document.createElement('div');
                            badge.className = 'bg-green-100 text-green-800 text-sm font-medium px-2.5 py-0.5 rounded cursor-pointer';
                            badge.textContent = departamento.nombre;
                            badge.setAttribute('data-id', departamento.Id_departamento);
                            badge.onclick = () => quitarDepartamento(departamento.Id_departamento);
                            departamentosAsignadosDiv.appendChild(badge);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#f44336', // Rojo para error
                        });
                    }
                })
                .catch(error => {
                    Swal.close(); // Cierra la alerta de carga
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al cargar los departamentos asignados.',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#f44336', // Rojo para error
                    });
                });
        }

        // Función para cargar los departamentos disponibles
        function cargarDepartamentosDisponibles(usuarioId) {
            Swal.fire({
                title: 'Cargando...',
                text: 'Estamos obteniendo los departamentos disponibles.',
                icon: 'info',
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`user/obtener_departamentos_disponibles.php?usuarioId=${usuarioId}`)
                .then(response => response.json())
                .then(data => {
                    Swal.close(); // Cierra la alerta de carga
                    const departamentosDisponiblesDiv = document.getElementById('departamentosDisponibles');
                    departamentosDisponiblesDiv.innerHTML = '';

                    if (data.success) {
                        data.departamentos.forEach(departamento => {
                            const badge = document.createElement('div');
                            badge.className = 'bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded cursor-pointer';
                            badge.textContent = departamento.nombre;
                            badge.setAttribute('data-id', departamento.Id_departamento);
                            badge.onclick = () => toggleSeleccionDepartamento(departamento.Id_departamento);
                            departamentosDisponiblesDiv.appendChild(badge);
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#f44336', // Rojo para error
                        });
                    }
                })
                .catch(error => {
                    Swal.close(); // Cierra la alerta de carga
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error al cargar los departamentos disponibles.',
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#f44336', // Rojo para error
                    });
                });
        }

        // Función para alternar la selección de un departamento
        function toggleSeleccionDepartamento(departamentoId) {
            const badge = document.querySelector(`#departamentosDisponibles div[data-id="${departamentoId}"]`);
            if (departamentosSeleccionados.has(departamentoId)) {
                departamentosSeleccionados.delete(departamentoId);
                badge.classList.remove('bg-purple-100', 'text-purple-800');
                badge.classList.add('bg-blue-100', 'text-blue-800');
            } else {
                departamentosSeleccionados.add(departamentoId);
                badge.classList.remove('bg-blue-100', 'text-blue-800');
                badge.classList.add('bg-purple-100', 'text-purple-800');
            }
        }

        // Función para quitar un departamento asignado
        function quitarDepartamento(departamentoId) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: '¿Quieres quitar este departamento?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, quitar',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#f44336', // Rojo para confirmación
                cancelButtonColor: '#008CBA', // Azul para cancelar
            }).then(result => {
                if (result.isConfirmed) {
                    Swal.showLoading(); // Muestra el icono de carga mientras se procesa la información
                    fetch('user/quitar_departamento.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                usuarioId: usuarioActualId,
                                departamentoId: departamentoId,
                            }),
                        })
                        .then(response => response.json())
                        .then(data => {
                            Swal.close(); // Cierra la alerta de carga
                            if (data.success) {
                                Swal.fire('Éxito', data.message, 'success');
                                cargarDepartamentosAsignados(usuarioActualId); // Recargar los departamentos asignados
                                cargarDepartamentosDisponibles(usuarioActualId); // Recargar los departamentos disponibles
                            } else {
                                Swal.fire('Error', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.close(); // Cierra la alerta de carga
                            console.error('Error:', error);
                            Swal.fire('Error', 'Ocurrió un error al quitar el departamento.', 'error');
                        });
                }
            });
        }

        // Función para guardar los departamentos seleccionados
        function guardarDepartamentosAsignados() {
            if (departamentosSeleccionados.size === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: 'Seleccione al menos un departamento.',
                    confirmButtonText: 'Aceptar',
                    confirmButtonColor: '#ff9800', // Naranja para advertencia
                });
                return;
            }

            Swal.showLoading(); // Muestra el icono de carga mientras se procesa la información
            fetch('user/asignar_departamentos.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        usuarioId: usuarioActualId,
                        departamentos: Array.from(departamentosSeleccionados),
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close(); // Cierra la alerta de carga
                    if (data.success) {
                        Swal.fire('Éxito', data.message, 'success');
                        cargarDepartamentosAsignados(usuarioActualId); // Recargar los departamentos asignados
                        cargarDepartamentosDisponibles(usuarioActualId); // Recargar los departamentos disponibles
                        departamentosSeleccionados.clear(); // Limpiar la selección
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.close(); // Cierra la alerta de carga
                    console.error('Error:', error);
                    Swal.fire('Error', 'Ocurrió un error al asignar los departamentos.', 'error');
                });
        }

        // Función para cerrar el modal
        function closeDepartamentosModal() {
            const modal = document.getElementById('departamentosModal');
            modal.classList.add('hidden');
            departamentosSeleccionados.clear(); // Limpiar la selección al cerrar el modal
        }
    </script>




    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- <script src="login/doc.js"></script> -->


    <!-- Incluir CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Incluir jQuery (Select2 depende de jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Incluir JS de Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


    <script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@2.0.1/dist/js/multi-select-tag.js"></script>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function cambiarEstado(userId, isChecked) {
            const nuevoEstado = isChecked ? 1 : 0;

            Swal.fire({
                title: "¿Estás seguro?",
                text: "El estado del usuario será cambiado.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, cambiar",
                cancelButtonText: "Cancelar",
                customClass: {
                    confirmButton: "bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded",
                    cancelButton: "bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("login/actualizar_estado.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                userId,
                                estatu: nuevoEstado
                            }),
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.status === "success") {
                                Swal.fire({
                                    title: "¡Estado cambiado!",
                                    text: "El estado ha sido actualizado correctamente.",
                                    icon: "success",
                                    customClass: {
                                        confirmButton: "bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded",
                                    },
                                    buttonsStyling: false,
                                });
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: "Hubo un problema al cambiar el estado.",
                                    icon: "error",
                                    customClass: {
                                        confirmButton: "bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded",
                                    },
                                    buttonsStyling: false,
                                });
                            }
                        })
                        .catch((error) => {
                            Swal.fire({
                                title: "Error",
                                text: "No se pudo conectar con el servidor.",
                                icon: "error",
                                customClass: {
                                    confirmButton: "bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded",
                                },
                                buttonsStyling: false,
                            });
                        });
                } else {
                    document.getElementById(`toggle-${userId}`).checked = !isChecked;
                }
            });
        }
    </script>

    <script>
        document.getElementById('userForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            // Validación del lado del cliente
            if (!formData.get('puestos') || !formData.get('oficina') || !formData.get('name') ||
                !formData.get('apellido') || !formData.get('email') ||
                !formData.get('contrasena') || !formData.get('verificar')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Todos los campos son obligatorios',
                });
                return;
            }
            console.log('Departamento seleccionado:', formData.get('Id_departamentos')); // Muestra el departamento

            console.log([...formData.entries()]); // Esto imprime todos los datos enviados

            // Validación de contraseñas
            if (formData.get('contrasena') !== formData.get('verificar')) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Las contraseñas no coinciden',
                });
                return;
            }

            // Validación de correo electrónico
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(formData.get('email'))) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El correo electrónico no es válido',
                });
                return;
            }

            // Mostrar indicador de carga
            Swal.fire({
                title: 'Cargando...',
                text: 'Por favor, espera mientras se procesa la solicitud',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            // Enviar datos al servidor
            fetch('login/addUser.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error HTTP: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                            confirmButtonColor: '#4CAF50', // Color verde para éxito
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonColor: '#F44336', // Color rojo para error
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la solicitud',
                        text: error.message,
                        confirmButtonColor: '#F44336', // Color rojo para error
                    });
                });

        });
    </script>

    <script>
        function mostrarListaSiguiente() {
            const puestoSeleccionado = document.getElementById('puestos').value;

            if (puestoSeleccionado) {
                // Mostrar el contenedor del siguiente nivel
                document.getElementById('nivel-container').style.display = 'block';

                // Cargar las opciones correspondientes al puesto seleccionado
                cargarNivel(puestoSeleccionado);
            } else {
                // Ocultar y limpiar si no hay selección
                document.getElementById('nivel-container').style.display = 'none';
                limpiarSelect('nivel');
            }
        }

        function cargarNivel(puesto) {
            // Realizar una solicitud AJAX para obtener los datos del siguiente nivel
            $.ajax({
                url: 'login/get_nivel.php', // Este archivo PHP debe manejar la lógica según el puesto
                type: 'GET',
                data: {
                    puesto
                },
                success: function(data) {
                    console.log('Respuesta de get_nivel.php:', data);

                    // Convertir en objeto si es una cadena
                    if (typeof data === 'string') {
                        data = JSON.parse(data);
                    }

                    // Limpiar el select de nivel
                    limpiarSelect('nivel');

                    // Comprobar si se han encontrado datos
                    if (Array.isArray(data) && data.length > 0) {
                        // Agregar las opciones al select
                        data.forEach(function(item) {
                            $('#nivel').append('<option value="' + item.nombre + '">' + item.nombre + '</option>');
                        });

                        // Aplicar Select2 si es necesario
                        $('#nivel').select2({
                            placeholder: "Selecciona una opción",
                            allowClear: true
                        });
                    } else {
                        // Si no hay datos, mostrar un mensaje
                        $('#nivel').append('<option value="">No hay opciones disponibles</option>');
                    }
                },
                error: function() {
                    alert('Error al cargar los datos');
                }
            });
        }

        function limpiarSelect(id) {
            const select = document.getElementById(id);
            if (select) {
                select.innerHTML = '<option value="">Selecciona</option>';
            }
        }


        // Mapeo de puestos a los títulos y el próximo nivel jerárquico
        const puestoMap = {
            "empresa": {
                label: "CEOs",
                next: "ceos"
            },
            "ceos": {
                label: "Directores",
                next: "directores"
            },
            "director": {
                label: "Supervisores",
                next: "supervisores"
            },
            "supervisor": {
                label: "Líderes",
                next: "lideres"
            },
            "lider": {
                label: "Staff",
                next: "staff"
            }
        };

        // Cuando cambie el puesto, actualizar la etiqueta y cargar los datos
        document.getElementById('puestos').addEventListener('change', function() {
            const puestoId = this.value;

            // Obtener los datos del mapa según el puesto seleccionado
            const puestoData = Object.keys(puestoMap).find(key => key === puestoId.toLowerCase());
            if (puestoData) {
                // Actualizar el texto dinámico
                const label = puestoMap[puestoData].label;
                document.getElementById('select-label').textContent = `Selecciona ${label}`;

                // Mostrar el contenedor
                document.getElementById('select-container').style.display = 'block';

                // Cargar el personal dinámicamente
                cargarPersonal(puestoId);
            } else {
                // Ocultar el select si no hay puesto seleccionado
                document.getElementById('select-container').style.display = 'none';
            }
        });



        function openPersonalModal(nombres) {
            const modal = document.getElementById('personalModal');
            const tableBody = document.getElementById('personalTableBody');

            // Dividir los nombres en un array y crear las filas
            const nombresArray = nombres.split(','); // Convertir la lista de nombres a un array
            tableBody.innerHTML = ''; // Limpiar el contenido previo
            nombresArray.forEach((nombre, index) => {
                tableBody.innerHTML += `
            <tr>
                <td class="py-2 px-4">${index + 1}</td>
                <td class="py-2 px-4">${nombre.trim()}</td>
            </tr>
        `;
            });

            // Mostrar el modal
            modal.classList.remove('hidden');
        }

        function closePersonalModal() {
            const modal = document.getElementById('personalModal');
            modal.classList.add('hidden');
        }
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Botón de eliminar
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id-user');

                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: '¡No podrás revertir esta acción!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Lógica para eliminar el elemento
                            fetch(`user/delete.php?id=${id}`, {
                                    method: 'POST'
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: '¡Eliminado!',
                                            text: data.message,
                                            icon: 'success',
                                            confirmButtonColor: '#4CAF50', // Color verde para éxito
                                        }).then(() => location.reload());
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: data.message,
                                            icon: 'error',
                                            confirmButtonColor: '#F44336', // Color rojo para error
                                        });
                                    }
                                })
                                .catch(error => {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Hubo un problema en el servidor',
                                        icon: 'error',
                                        confirmButtonColor: '#F44336', // Color rojo para error
                                    });
                                });

                        }
                    });
                });
            });
            
            
            document.querySelectorAll('.change-role-button').forEach(button => {
    button.addEventListener('click', function() {
        const id = this.getAttribute('data-id-user');
        const rolActual = this.getAttribute('data-rol-actual');

        // Roles desde PHP
        const ROLES_OPTIONS = <?php echo json_encode(array_column($roles,'nombre','id'), JSON_UNESCAPED_UNICODE); ?>;

        // Crear select estilizado
        let optionsHtml = '<select id="swal-rol-select" class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-400">';
        optionsHtml += '<option value="">-- Selecciona un rol --</option>';
        for (const [idRol, nombreRol] of Object.entries(ROLES_OPTIONS)) {
            const selected = (idRol == rolActual) ? 'selected' : '';
            optionsHtml += `<option value="${idRol}" ${selected}>${nombreRol}</option>`;
        }
        optionsHtml += '</select>';

        // Mostrar SweetAlert2
        Swal.fire({
            title: 'Cambiar rol',
            html: optionsHtml,
            showCancelButton: true,
            confirmButtonText: 'Guardar',
            cancelButtonText: 'Cancelar',
            customClass: {
                confirmButton: 'bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded ml-2',
                cancelButton: 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-4 py-2 rounded'
            },
            buttonsStyling: false,
            preConfirm: () => {
                const selectedRol = document.getElementById('swal-rol-select').value;
                if (!selectedRol) Swal.showValidationMessage('Debes seleccionar un rol');
                return selectedRol;
            },
            didOpen: () => {
                document.getElementById('swal-rol-select').focus();
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedRol = result.value;

                Swal.fire({
                    title: 'Guardando...',
                    didOpen: () => Swal.showLoading(),
                    allowOutsideClick: false,
                    showConfirmButton: false
                });

                // Petición POST
                fetch('user/asignar_rol.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `id_usuario=${encodeURIComponent(id)}&id_rol=${encodeURIComponent(selectedRol)}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar badge
                        let span = document.querySelector('.rol-label[data-id-usuario="'+id+'"]');
                        if (!span) {
                            span = document.createElement('span');
                            span.className = 'rol-label bg-blue-500 text-white text-xs font-medium me-2 px-2.5 py-0.5 rounded';
                            span.setAttribute('data-id-usuario', id);
                            button.parentNode.insertBefore(span, button);
                        }
                        span.textContent = ROLES_OPTIONS[selectedRol]; // nombre del rol
                        span.style.backgroundColor = '#007BFF';
                        span.style.color = 'white';

                        // actualizar atributo del botón
                        button.setAttribute('data-rol-actual', selectedRol);

                        Swal.fire({
                            title: 'Rol actualizado',
                            icon: 'success',
                            timer: 1200,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.error || 'No se pudo actualizar el rol',
                            icon: 'error'
                        });
                    }
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire({
                        title: 'Error de red',
                        text: 'Intenta de nuevo',
                        icon: 'error'
                    });
                });
            }
        });
    });
});

            // Botón de editar
            document.querySelectorAll('.edit-button').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'Editar información',
                        html: `
                    <form id="editForm">
                        <input type="text" id="editField1" class="swal2-input" placeholder="Campo 1">
                        <input type="text" id="editField2" class="swal2-input" placeholder="Campo 2">
                    </form>
                `,
                        focusConfirm: false,
                        showCancelButton: true,
                        confirmButtonText: 'Guardar',
                        cancelButtonText: 'Cancelar',
                        preConfirm: () => {
                            const field1 = document.getElementById('editField1').value;
                            const field2 = document.getElementById('editField2').value;

                            if (!field1 || !field2) {
                                Swal.showValidationMessage('Todos los campos son obligatorios');
                                return false;
                            }

                            return {
                                field1,
                                field2
                            };
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const {
                                field1,
                                field2
                            } = result.value;

                            fetch(`edit.php?id=${id}`, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify({
                                        field1,
                                        field2
                                    }),
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire(
                                            '¡Editado!',
                                            data.message,
                                            'success'
                                        ).then(() => location.reload());
                                    } else {
                                        Swal.fire('Error', data.message, 'error');
                                    }
                                })
                                .catch(error => {
                                    Swal.fire('Error', 'Hubo un problema en el servidor', 'error');
                                });
                        }
                    });
                });
            });

            // Botón de más información
            document.querySelectorAll('.info-button').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');

                    fetch(`info.php?id=${id}`)
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire({
                                title: 'Más información',
                                html: `
                            <p><b>Campo 1:</b> ${data.field1}</p>
                            <p><b>Campo 2:</b> ${data.field2}</p>
                        `,
                                confirmButtonText: 'Cerrar',
                            });
                        })
                        .catch(error => {
                            Swal.fire('Error', 'Hubo un problema en el servidor', 'error');
                        });
                });
            });
        });
    </script>


    <!-- asigna rol -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> -->
    <script>
        const asignarRolLinks = document.querySelectorAll('.asignar-rol');

        asignarRolLinks.forEach(link => {
            link.addEventListener('click', async (event) => {
                event.preventDefault();

                const userId = link.getAttribute('data-id-usuario'); // Obtener el ID del usuario

                // Mostrar spinner de carga inicial
                Swal.fire({
                    title: 'Cargando...',
                    text: 'Obteniendo roles disponibles',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });

                try {
                    // Consultar roles desde la base de datos
                    const response = await fetch('user/obtener_roles.php');
                    const data = await response.json();

                    if (data.success) {
                        // Construir las opciones del dropdown
                        const rolesOptions = data.roles
                            .map(role => `<option value="${role.id}">${role.nombre}</option>`)
                            .join('');

                        // Mostrar SweetAlert con botones personalizados
                        Swal.fire({
                            title: 'Asignar Rol',
                            html: `
                            <div class="flex flex-col items-center space-y-4">
                                <select id="select-rol" class="block w-full px-4 py-2 text-sm border rounded-lg bg-gray-100 focus:ring-2 focus:ring-blue-500 focus:outline-none">
                                    <option value="">Seleccione un rol</option>
                                    ${rolesOptions}
                                </select>
                            </div>
                        `,
                            confirmButtonText: 'Asignar',
                            cancelButtonText: 'Cancelar',
                            showCancelButton: true,
                            focusConfirm: false,
                            customClass: {
                                confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500',
                                cancelButton: 'bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500'
                            },
                            preConfirm: () => {
                                const selectedRole = document.getElementById('select-rol').value;
                                if (!selectedRole) {
                                    Swal.showValidationMessage('Debe seleccionar un rol');
                                    return false;
                                }
                                return selectedRole;
                            }
                        }).then(async (result) => {
                            if (result.isConfirmed) {
                                const selectedRole = result.value;

                                // Mostrar un spinner mientras se realiza la asignación
                                Swal.fire({
                                    title: 'Asignando...',
                                    text: 'Procesando solicitud',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    },
                                });

                                // Enviar el rol seleccionado al servidor
                                try {
                                    const assignResponse = await fetch('user/asignar_rol.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify({
                                            userId,
                                            selectedRole
                                        }),
                                    });

                                    const assignData = await assignResponse.json();

                                    if (assignData.success) {
                                        Swal.fire({
                                            title: '¡Éxito!',
                                            text: 'Rol asignado correctamente',
                                            icon: 'success',
                                            customClass: {
                                                container: 'bg-green-500 text-white', // Personaliza el color de fondo y texto
                                                confirmButton: 'bg-green-700 hover:bg-green-800' // Personaliza el color del botón
                                            }
                                        }).then(() => location.reload());
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'No se pudo asignar el rol',
                                            icon: 'error',
                                            customClass: {
                                                container: 'bg-red-500 text-white', // Personaliza el color de fondo y texto
                                                confirmButton: 'bg-red-700 hover:bg-red-800' // Personaliza el color del botón
                                            }
                                        });
                                    }
                                } catch (error) {
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Hubo un problema con el servidor',
                                        icon: 'error',
                                        customClass: {
                                            container: 'bg-red-500 text-white', // Personaliza el color de fondo y texto
                                            confirmButton: 'bg-red-700 hover:bg-red-800' // Personaliza el color del botón
                                        }
                                    });
                                }
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudieron obtener los roles',
                            icon: 'error',
                            customClass: {
                                container: 'bg-red-500 text-white', // Personaliza el color de fondo y texto
                                confirmButton: 'bg-red-700 hover:bg-red-800' // Personaliza el color del botón
                            }
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un problema al consultar los roles',
                        icon: 'error',
                        customClass: {
                            container: 'bg-red-500 text-white', // Personaliza el color de fondo y texto
                            confirmButton: 'bg-red-700 hover:bg-red-800' // Personaliza el color del botón
                        }
                    });
                }
            });
        });
    </script>
    <script>
        // Función para mostrar/ocultar el tooltip
        function toggleTooltip() {
            const tooltip = document.getElementById('password-tooltip');
            tooltip.classList.toggle('hidden');
        }

        // Validación en tiempo real de la contraseña
        const contrasena = document.getElementById('contrasena');
        const verificar = document.getElementById('verificar');
        const passwordMatch = document.getElementById('password-match');
        const requirements = {
            length: document.getElementById('length'),
            uppercase: document.getElementById('uppercase'),
            number: document.getElementById('number'),
            special: document.getElementById('special')
        };

        contrasena.addEventListener('input', () => {
            const value = contrasena.value;

            // Validar longitud
            if (value.length >= 8) {
                requirements.length.classList.remove('text-red-500');
                requirements.length.classList.add('text-green-500');
            } else {
                requirements.length.classList.remove('text-green-500');
                requirements.length.classList.add('text-red-500');
            }

            // Validar mayúsculas
            if (/[A-Z]/.test(value)) {
                requirements.uppercase.classList.remove('text-red-500');
                requirements.uppercase.classList.add('text-green-500');
            } else {
                requirements.uppercase.classList.remove('text-green-500');
                requirements.uppercase.classList.add('text-red-500');
            }

            // Validar números
            if (/\d/.test(value)) {
                requirements.number.classList.remove('text-red-500');
                requirements.number.classList.add('text-green-500');
            } else {
                requirements.number.classList.remove('text-green-500');
                requirements.number.classList.add('text-red-500');
            }

            // Validar caracteres especiales
            if (/[!@#$%^&*(),.?":{}|<>]/.test(value)) {
                requirements.special.classList.remove('text-red-500');
                requirements.special.classList.add('text-green-500');
            } else {
                requirements.special.classList.remove('text-green-500');
                requirements.special.classList.add('text-red-500');
            }
        });

        verificar.addEventListener('input', () => {
            if (verificar.value === contrasena.value) {
                passwordMatch.textContent = 'Las contraseñas coinciden.';
                passwordMatch.classList.remove('text-red-500');
                passwordMatch.classList.add('text-green-500');
            } else {
                passwordMatch.textContent = 'Las contraseñas no coinciden.';
                passwordMatch.classList.remove('text-green-500');
                passwordMatch.classList.add('text-red-500');
            }
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Selecciona todas las entradas de filtro
            const filterInputs = document.querySelectorAll('.filter-input');
            const table = document.getElementById('user-table');
            const rows = table.querySelectorAll('tbody tr');

            filterInputs.forEach((input, colIndex) => {
                input.addEventListener('input', () => {
                    const filterValue = input.value.toLowerCase();

                    rows.forEach(row => {
                        const cell = row.cells[colIndex + 1]; // Columna correspondiente
                        const cellText = cell ? cell.textContent.toLowerCase() : '';
                        row.style.display = cellText.includes(filterValue) ? '' : 'none';
                    });
                });
            });
        });
    </script>
    <script>
        document.getElementById("table-search-compu").addEventListener("keyup", function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll("#compu-table tbody tr");

            rows.forEach(row => {
                let text = row.innerText.toLowerCase();
                row.style.display = text.includes(filter) ? "" : "none";
            });
        });
    </script>

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