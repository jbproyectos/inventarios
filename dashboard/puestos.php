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
$canViewMoney = in_array('ver_dinero', array_column($permissions, 'nombre'));

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
    <title>Puestos | Kabzo</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="../assets/css/profile.css">
    <!-- <script src="../assets/js/dash.js"></script> -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" /> -->
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script> -->
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" /> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <!-- Asegúrate de cargar jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Luego, carga Toastr -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<style>
    #map { height: 100%; width: 100%; }
</style>

    <style>
        #file-compu {
            overflow: hidden;
            /* Oculta el contenido que sobresalga */
            position: relative;
            /* Controla el contenido en relación al padre */
        }

        #preview-content pre {
            max-height: 100%;
            /* Asegura que el contenido no exceda el alto del contenedor */
            overflow-y: auto;
            /* Habilita el scroll si el contenido es largo */
            white-space: pre-wrap;
            /* Ajusta el texto para respetar saltos de línea */
            word-wrap: break-word;
            /* Ajusta palabras largas */
            text-align: left;
            /* Alinea el texto a la izquierda */
            padding: 1rem;
        }
    </style>

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

                        <h4 class="mb-4 font-semibold text-gray-600 light:text-gray-300">
                            Mis Puestos </h4>
                        <div class="">
                            <div class=" sm:rounded-lg">
                                <div class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white ">
                                    <div class="">
                                        <?php if ($canAdd): ?>

                                            <!-- Modal toggle -->
                                            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="inline-flex text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800" type="button">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                                </svg>
                                                Registrar Puesto
                                            </button>

                                        <?php else: ?>
                                        <?php endif; ?>
                                        <?php if ($canDelete) { ?>

                                            <button data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="border-l border-gray-200 inline-flex text-gray-500 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium ml-4 text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800" type="button">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>

                                        <?php     } else {
                                            echo "<span class='text-gray-500'></span>";
                                        }
                                        ?>
                                        <button class="border-l border-gray-200 inline-flex text-gray-500 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-gray-700 light:focus:ring-blue-800" type="button" id="openModalButton">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                        </button>
                                        <?php if ($canAdd) { ?>
                                            <button data-modal-target="progress-modal" data-modal-toggle="progress-modal" class="hover:bg-gray-100 border-l border-gray-200 inline-flex text-gray-500  focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800" type="button">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                                </svg>
                                            </button>
                                        <?php } ?>


                                        


                                        <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                            <div class="relative p-4 w-full max-w-md max-h-full">
                                                <div class="relative bg-white rounded-lg shadow light:bg-gray-700">
                                                    <div class="p-4 md:p-5 text-center">
                                                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 light:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>
                                                        <h3 class="mb-5 text-lg font-normal text-gray-500 light:text-gray-400">Está seguro que desea vaciar la tabla?</h3>
                                                        <button id="elimina_domicilios" data-modal-hide="popup-modal" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 light:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                                                            Si, seguro
                                                        </button>
                                                        <button data-modal-hide="popup-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 light:bg-gray-700 light:text-gray-300 light:border-gray-500 light:hover:text-white light:hover:bg-gray-600 light:focus:ring-gray-600">No, cancelar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>



                                        <!-- Main modal -->
                                        <div id="progress-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                            <div class="relative p-4 w-full max-w-md max-h-full">
                                                <!-- Modal content -->
                                                <div class="relative bg-white rounded-lg shadow light:bg-gray-700">

                                                    <div class="p-4 md:p-5">
                                                        <svg class="w-10 h-10 text-gray-400 light:text-gray-500 mb-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                                                            <path d="M8 5.625c4.418 0 8-1.063 8-2.375S12.418.875 8 .875 0 1.938 0 3.25s3.582 2.375 8 2.375Zm0 13.5c4.963 0 8-1.538 8-2.375v-4.019c-.052.029-.112.054-.165.082a8.08 8.08 0 0 1-.745.353c-.193.081-.394.158-.6.231l-.189.067c-2.04.628-4.165.936-6.3.911a20.601 20.601 0 0 1-6.3-.911l-.189-.067a10.719 10.719 0 0 1-.852-.34 8.08 8.08 0 0 1-.493-.244c-.053-.028-.113-.053-.165-.082v4.019C0 17.587 3.037 19.125 8 19.125Zm7.09-12.709c-.193.081-.394.158-.6.231l-.189.067a20.6 20.6 0 0 1-6.3.911 20.6 20.6 0 0 1-6.3-.911l-.189-.067a10.719 10.719 0 0 1-.852-.34 8.08 8.08 0 0 1-.493-.244C.112 6.035.052 6.01 0 5.981V10c0 .837 3.037 2.375 8 2.375s8-1.538 8-2.375V5.981c-.052.029-.112.054-.165.082a8.08 8.08 0 0 1-.745.353Z" />
                                                        </svg>
                                                        <h3 class="mb-1 text-xl font-bold text-gray-900 light:text-white">Carga massiva | Tabla Computadoras</h3>
                                                        <p class="text-gray-500 light:text-gray-400 mb-6">¿Está seguro cargar un archivo a la base de datos?
                                                        <p>
                                                        <form id="cargamasivapc" class="p-2" method="post">
                                                            <div class="flex justify-between mb-1 text-gray-500 light:text-gray-400">
                                                                <div class="flex items-center justify-center w-full">
                                                                    <label for="dropzone-file" id="file-compu" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 light:hover:bg-bray-800 light:bg-gray-700 hover:bg-gray-100 light:border-gray-600 light:hover:border-gray-500 light:hover:bg-gray-600">
                                                                        <div id="preview-domicilios" class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                            <svg class="w-8 h-8 mb-4 text-gray-500 light:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                                            </svg>
                                                                            <p class="mb-2 text-sm text-gray-500 light:text-gray-400"><span class="font-semibold">Click para cargar archivo</span></p>
                                                                            <p class="text-xs text-gray-500 light:text-gray-400">Soporta CSV o TXT</p>
                                                                        </div>


                                                                        <input id="dropzone-file" type="file" name="subeDomicilios" class="hidden" accept=".csv,.txt" />
                                                                    </label>
                                                                </div>
                                                            </div>


                                                            <!-- Modal footer -->
                                                            <div class="flex items-center mt-6 space-x-2 rtl:space-x-reverse">
                                                                <button data-modal-hide="progress-modal" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800">Subir</button>
                                                                <button data-modal-hide="progress-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 light:bg-gray-700 light:text-gray-300 light:border-gray-500 light:hover:text-white light:hover:bg-gray-600 light:focus:ring-gray-600">Cancelar</button>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Main modal -->
                                        <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                            <div class="relative p-4 w-full max-h-full">
                                                <!-- Modal content -->
                                                <div class="relative bg-white rounded-lg shadow-lg max-w-2xl mx-auto light:bg-gray-700"> <!-- Cambié max-w-lg a max-w-xl -->
                                                    <!-- Modal header -->
                                                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t light:border-gray-600">
                                                        <h3 id="registroCompu" class="text-lg font-semibold text-gray-900 light:text-white">
                                                            Nuevo Domicilio
                                                        </h3>
                                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center light:hover:bg-gray-600 light:hover:text-white" data-modal-toggle="crud-modal">
                                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                            </svg>
                                                            <span class="sr-only">Cerrar ventana</span>
                                                        </button>
                                                    </div>
                                                    <!-- Modal body -->
                                                   <form action="#" method="post" class="p-6 bg-white rounded-lg shadow-md space-y-6">

  <h2 class="text-xl font-bold text-gray-700">Registrar Domicilio</h2>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <label for="direccion" class="block mb-2 text-sm font-medium text-gray-900">Dirección</label>
      <input type="text" id="direccion" name="direccion" required
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>
    

<div>
  <label for="empresas-select" class="block mb-2 text-sm font-medium text-gray-900">
    Busca y selecciona dos empresas
  </label>
  <select multiple id="empresas-select" name="empresas[]" required></select>
</div>



    <div>
      <label for="municipio" class="block mb-2 text-sm font-medium text-gray-900">Municipio</label>
      <input type="text" id="municipio" name="municipio" required
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div class="">
      <label for="ubicacion" class="block mb-2 text-sm font-medium text-gray-900">Ubicación (URL o referencia)</label>
      <input type="text" id="ubicacion" name="ubicacion"
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div>
      <label for="escritorios" class="block mb-2 text-sm font-medium text-gray-900">Escritorios</label>
      <input type="text" id="escritorios" name="escritorios"
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div>
      <label for="sillas_de_escritorios" class="block mb-2 text-sm font-medium text-gray-900">Sillas de Escritorios</label>
      <input type="text" id="sillas_de_escritorios" name="sillas_de_escritorios"
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div>
      <label for="sillas" class="block mb-2 text-sm font-medium text-gray-900">Sillas</label>
      <input type="text" id="sillas" name="sillas"
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div>
      <label for="mesa_escritorio" class="block mb-2 text-sm font-medium text-gray-900">Mesa (Escritorio)</label>
      <input type="text" id="mesa_escritorio" name="mesa_escritorio"
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div>
      <label for="sillones" class="block mb-2 text-sm font-medium text-gray-900">Sillones</label>
      <input type="text" id="sillones" name="sillones"
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div>
      <label for="mesa_de_centro" class="block mb-2 text-sm font-medium text-gray-900">Mesa de Centro</label>
      <input type="text" id="mesa_de_centro" name="mesa_de_centro"
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div>
      <label for="cajoneras" class="block mb-2 text-sm font-medium text-gray-900">Cajoneras</label>
      <input type="text" id="cajoneras" name="cajoneras"
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div>
      <label for="estantes" class="block mb-2 text-sm font-medium text-gray-900">Estantes</label>
      <input type="text" id="estantes" name="estantes"
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div>
      <label for="otros" class="block mb-2 text-sm font-medium text-gray-900">Otros</label>
      <input type="text" id="otros" name="otros"
        class="block w-full p-2 border border-gray-300 rounded-lg text-sm focus:ring-blue-500 focus:border-blue-500">
    </div>
  </div>

  <button type="submit" id="agregaDomicilio" data-action="addDomicilio"
    class="text-white inline-flex items-center w-full mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800">
    <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
      xmlns="http://www.w3.org/2000/svg">
      <path fill-rule="evenodd"
        d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z"
        clip-rule="evenodd"></path>
    </svg>
    Agregar Domicilio
  </button>
</form>

                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <!-- <button id="mark-reviewed-button" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 light:border-blue-500 light:text-blue-500 light:hover:text-white light:hover:bg-blue-500 light:focus:ring-blue-800">Marcar como Revisados</button> -->

                                    
                                    
                                    <form method="GET" class="mb-4">
    <input type="text" name="search" placeholder="Buscar departamento..." 
           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
           class="p-2 border rounded w-80">
    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded ml-2">Buscar</button>
</form>

                                </div>
                                <div id="tbl_domicilios" style="overflow-x: auto;" class="h-auto">

                                    <table id="puestos-table" class="w-full text-sm text-left text-gray-500 mb-4">
    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3">ID</th>
            <th scope="col" class="px-6 py-3">Nombre</th>
            <th scope="col" class="px-6 py-3">ID Superior</th>
            <th scope="col" class="px-6 py-3">Acción</th>
        </tr>
    </thead>
    <tbody>
    <?php
    try {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $registrosPorPagina = 10;
        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $offset = ($paginaActual - 1) * $registrosPorPagina;

        // Total de registros
        $queryTotal = $conexion->query("SELECT COUNT(*) FROM puestos");
        $totalRegistros = $queryTotal->fetchColumn();
        $totalPaginas = $totalRegistros > 0 ? ceil($totalRegistros / $registrosPorPagina) : 1;

        // Búsqueda
        $busqueda = isset($_GET['search']) ? $_GET['search'] : '';

        $query = "SELECT * FROM puestos";
        if ($busqueda !== '') {
            $query .= " WHERE nombre LIKE :busqueda OR Id_superior LIKE :busqueda";
        }
        $query .= " ORDER BY Id_puesto ASC LIMIT :limit OFFSET :offset";

        $stmt = $conexion->prepare($query);
        $stmt->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

        if ($busqueda !== '') {
            $likeBusqueda = "%$busqueda%";
            $stmt->bindParam(':busqueda', $likeBusqueda, PDO::PARAM_STR);
        }

        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($resultado) {
            foreach ($resultado as $fila) {
                echo '<tr class="bg-white border-b hover:bg-gray-50">';
                echo '<td class="px-6 py-4">'.htmlspecialchars($fila['Id_puesto']).'</td>';
                echo '<td class="px-6 py-4">'.htmlspecialchars($fila['nombre']).'</td>';
                echo '<td class="px-6 py-4">'.htmlspecialchars($fila['Id_superior']).'</td>';

                echo '<td class="px-6 py-4 text-center flex justify-center space-x-2">';
                
                // Ver detalle
                echo '<button data-id="' . $fila['Id_puesto'] . '" class="p-2 text-green-600 hover:text-green-800 rounded-full bg-green-100 hover:bg-green-200" title="Ver detalles">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                </button>';

                // Editar
                echo '<button data-id="' . $fila['Id_puesto'] . '" class="p-2 text-blue-600 hover:text-blue-800 rounded-full bg-blue-100 hover:bg-blue-200" title="Editar">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/>
                </svg>
                </button>';

                // Eliminar
                echo '<button data-id="' . $fila['Id_puesto'] . '" class="delete-button p-2 text-red-600 hover:text-red-800 rounded-full bg-red-100 hover:bg-red-200" title="Eliminar">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                </button>';

                echo '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No se encontraron registros.</td></tr>';
        }
    } catch (PDOException $e) {
        echo '<tr><td colspan="4" class="px-6 py-4 text-red-600">Error al cargar registros.</td></tr>';
    }
    ?>
    </tbody>
</table>





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
                    </div>
                </div>




            </div>
        </main>

    </div>



    <!-- Contenedor donde se cargará el modal -->
    <div id="crud-edit" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative p-4 w-full max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow-lg max-w-2xl mx-auto"> <!-- Solo light -->
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                <h3 id="registroCompu" class="text-lg font-semibold text-gray-900">
                    Editar Domicilio
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-edit">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Cerrar ventana</span>
                </button>
            </div>
            <?php include 'domicilio/formEdit.php'; ?>
            <!-- Modal body -->
            <div id="modal-container">

            </div>
        </div>
    </div>
</div>




    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script> -->

    <div id="drawer-right-example" class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white  dark:bg-gray-800" tabindex="-1" aria-labelledby="drawer-right-label">
        <h5 id="drawer-right-label" class="mr-4 inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400">
            <svg class="w-4 h-4 me-2.5 mr-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>Caracteristicas
        </h5>
        <button type="button" data-drawer-hide="drawer-right-example" aria-controls="drawer-right-example" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 end-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Cerrar</span>
        </button>



        <div class="border-t border-gray-700 mt-4 w-full p-4 text-left infocaracteristicas">

        </div>
    </div>





    <script>
        document.getElementById('dropzone-file').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const previewContainer = document.getElementById('preview-domicilios');

            if (file) {
                // Verifica que sea un archivo soportado
                if (file.type === 'text/csv' || file.type === 'text/plain') {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        // Reemplaza el contenido del contenedor
                        previewContainer.innerHTML = `<pre class="text-sm text-gray-500 dark:text-gray-400 whitespace-pre-wrap overflow-auto w-full h-full">${e.target.result}</pre>`;
                    };

                    reader.onerror = function() {
                        previewContainer.innerHTML = `<p class="text-sm text-red-500">Error al leer el archivo.</p>`;
                    };

                    reader.readAsText(file);
                } else {
                    previewContainer.innerHTML = `<p class="text-sm text-red-500">Formato no compatible. Solo se admiten archivos CSV o TXT.</p>`;
                }
            } else {
                previewContainer.innerHTML = `<p class="text-sm text-gray-500 dark:text-gray-400">No se seleccionó ningún archivo.</p>`;
            }
        });
    </script>
    <script>
      
        $(document).ready(function() {
    $('[data-drawer-show="drawer-right-example"]').on('click', function() {
        var rowId = $(this).data('row-id');
        console.log(rowId);

        $.ajax({
            url: 'domicilio/recupera_caracteristicas.php',
            type: 'GET',
            data: { id: rowId },
            success: function(data) {
                var domicilioInfo = JSON.parse(data);

                $('.infocaracteristicas').html(`
                    <h3 class="text-lg font-semibold mb-4">Detalle del Domicilio</h3>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Dirección:</span>
                        <p class="text-gray-300">${domicilioInfo.direccion}</p>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Municipio:</span>
                        <p class="text-gray-300">${domicilioInfo.municipio}</p>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Ubicación:</span>
                        <p class="text-gray-300">${domicilioInfo.ubicacion}</p>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Escritorios:</span>
                        <p class="text-gray-300">${domicilioInfo.escritorios}</p>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Sillas de Escritorio:</span>
                        <p class="text-gray-300">${domicilioInfo.sillas_de_escritorios}</p>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Sillas:</span>
                        <p class="text-gray-300">${domicilioInfo.sillas}</p>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Mesa de Escritorio:</span>
                        <p class="text-gray-300">${domicilioInfo.mesa_escritorio}</p>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Sillones:</span>
                        <p class="text-gray-300">${domicilioInfo.sillones}</p>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Mesa de Centro:</span>
                        <p class="text-gray-300">${domicilioInfo.mesa_de_centro}</p>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Cajoneras:</span>
                        <p class="text-gray-300">${domicilioInfo.cajoneras}</p>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Estantes:</span>
                        <p class="text-gray-300">${domicilioInfo.estantes}</p>
                    </div>
                    <div class="mb-2">
                        <span class="font-medium text-gray-600">Otros:</span>
                        <p class="text-gray-300">${domicilioInfo.otros}</p>
                    </div>
                `);
            },
            error: function() {
                console.error('Error al obtener información del domicilio.');
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo obtener la información del domicilio.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    background: '#2c2f38',
                    color: '#fff',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });
});




        $("#cargamasivapc").submit(function(e) {
            e.preventDefault();

            // Obtener el input de archivo y verificar si hay un archivo cargado
            var fileInput = $("#dropzone-file")[0]; // Asegúrate de que el ID coincida con el input file en tu formulario
            if (!fileInput.files.length) {
                Swal.fire({
                    title: '¡Error!',
                    text: 'Debes seleccionar un archivo antes de continuar.',
                    icon: 'warning',
                    confirmButtonText: 'Aceptar'
                });
                return; // Detener la ejecución si no hay archivo
            }

            var parametros = new FormData($(this)[0]);

            // Mostrar loader mientras se procesa la solicitud
            Swal.fire({
                title: 'Procesando...',
                text: 'Por favor espera mientras se cargan los datos.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                type: "POST",
                url: "domicilio/import_masivo.php",
                cache: false,
                data: parametros,
                contentType: false,
                processData: false,
                success: function(data) {
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Datos agregados correctamente.',
                        icon: 'success',
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                    Swal.fire({
                        title: '¡Error!',
                        text: 'Hubo un problema al procesar el archivo. Inténtalo nuevamente.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            });
        });
    </script>



    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


    <script>
      $(document).ready(function () {
    $('#agregaDomicilio').on('click', function (e) {
        e.preventDefault();

        var formData = new FormData();

        formData.append('direccion', $('#direccion').val());
        formData.append('municipio', $('#municipio').val());
        formData.append('ubicacion', $('#ubicacion').val());
        formData.append('escritorios', $('#escritorios').val());
        formData.append('sillas_de_escritorios', $('#sillas_de_escritorios').val());
        formData.append('sillas', $('#sillas').val());
        formData.append('mesa_escritorio', $('#mesa_escritorio').val());
        formData.append('sillones', $('#sillones').val());
        formData.append('mesa_de_centro', $('#mesa_de_centro').val());
        formData.append('cajoneras', $('#cajoneras').val());
        formData.append('estantes', $('#estantes').val());
        formData.append('otros', $('#otros').val());

        // 🚀 Aquí obtenemos las empresas seleccionadas
        var empresas = $('#empresas-select').val(); // devuelve un array
        if (empresas) {
            empresas.forEach((empresa, index) => {
                formData.append('empresas[]', empresa);
            });
        }

        $.ajax({
            type: 'POST',
            url: 'domicilio/addDomicilio.php',
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Domicilio registrado!',
                        text: data.message,
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#16a34a'
                    }).then(() => {
                        // limpiar formulario después del registro
                        $('#miForm')[0].reset(); // limpia todos los campos del form
                        const choices = document.getElementById('empresas-select').choices;
                        if (choices) choices.clearStore(); // limpia selección de Choices.js
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        confirmButtonText: 'Cerrar',
                        confirmButtonColor: '#dc2626'
                    });
                }
            },
            error: function (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error en la solicitud',
                    text: 'Ocurrió un problema con el servidor',
                    confirmButtonText: 'Cerrar',
                    confirmButtonColor: '#dc2626'
                });
                console.error('Error en la solicitud AJAX:', error);
            }
        });
    });
});







       $(document).ready(function() {
    $('#elimina_domicilios').on('click', function(e) {
        e.preventDefault(); // Evitar comportamiento por defecto

        // Verificar si la tabla tiene filas antes de proceder
        if ($('#tbl_domicilios tbody tr').length === 0) {
            Swal.fire({
                title: 'Tabla vacía',
                text: 'No hay registros para eliminar.',
                icon: 'info',
                confirmButtonText: 'Aceptar',
                background: '#2c2f38',
                color: '#fff',
                confirmButtonColor: '#3085d6'
            });
            return; // Detener ejecución si la tabla está vacía
        }

        // Confirmación antes de eliminar
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esta acción eliminará todos los registros de Domicilios!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            background: '#2c2f38',
            color: '#fff',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                // Solicitud AJAX para eliminar todos los registros
                $.ajax({
                    type: 'POST',
                    url: 'domicilio/DeleteTable.php', // Archivo PHP que elimina registros
                    data: { action: 'eliminar_todos' }, // Enviar parámetro de acción
                    dataType: 'json',
                    success: function(data) {
                        if (data.success) {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: data.message, // Mensaje del servidor
                                icon: 'success',
                                confirmButtonText: 'Aceptar',
                                background: '#2c2f38',
                                color: '#fff',
                                confirmButtonColor: '#3085d6'
                            });

                            // Eliminar las filas de la tabla visualmente
                            $('#tbl_domicilios tbody').html('');
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: data.message,
                                icon: 'error',
                                confirmButtonText: 'Aceptar',
                                background: '#2c2f38',
                                color: '#fff',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function(error) {
                        console.error('Error en la solicitud AJAX:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un error en la solicitud.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar',
                            background: '#2c2f38',
                            color: '#fff',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
    });
});

    </script>

    <script>
    


        document.getElementById('openModalButton').addEventListener('click', function() {
            document.getElementById('filterModal').classList.remove('hidden');
        });

        document.getElementById('closeModalButton').addEventListener('click', function() {
            document.getElementById('filterModal').classList.add('hidden');
        });

        document.getElementById('closeModalButton2').addEventListener('click', function() {
            document.getElementById('filterModal').classList.add('hidden');
        });

        document.getElementById('applyFiltersButton').addEventListener('click', function() {
            const filterDate = document.getElementById('filterDate').value;
            const filterStatus = document.getElementById('filterStatus').value;

            // Aquí puedes agregar lógica para filtrar los datos de la tabla según los valores de los filtros
            console.log('Filtrar por:', filterDate, filterStatus);

            // Crear la lógica para la descarga
            const data = [
                ['Fecha', 'Estado'], // Encabezados
                [filterDate || 'N/A', filterStatus || 'N/A'] // Datos filtrados
            ];

            const csvContent = data.map(e => e.join(',')).join('\n');
            const blob = new Blob([csvContent], {
                type: 'text/csv'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'datos_filtrados.csv';
            a.click();

            // Cerrar modal
            document.getElementById('filterModal').classList.add('hidden');
        });
    </script>
   <script>
$('[data-modal-target="crud-edit"]').on('click', function() {
    var idDom = $(this).data('id-domicilio'); // ID del domicilio seleccionado
    rellenarModalDomicilio(idDom);
});

// Función para rellenar los datos del modal de domicilios
function rellenarModalDomicilio(idDom) {
    fetch('domicilio/edit_domicilio.php?id=' + idDom)
        .then(response => response.json())
        .then(data => {
            // Rellenar los campos del modal
            $('#id_edit').val(data.id);
            $('#direccion_edit').val(data.direccion);
            $('#empresa1_edit').val(data.empresa1);
            $('#empresa2_edit').val(data.empresa2);
            $('#municipio_edit').val(data.municipio);
            $('#ubicacion_edit').val(data.ubicacion);
            $('#escritorios_edit').val(data.escritorios);
            $('#sillas_de_escritorios_edit').val(data.sillas_de_escritorios);
            $('#sillas_edit').val(data.sillas);
            $('#mesa_escritorio_edit').val(data.mesa_escritorio);
            $('#sillones_edit').val(data.sillones);
            $('#mesa_de_centro_edit').val(data.mesa_de_centro);
            $('#cajoneras_edit').val(data.cajoneras);
            $('#estantes_edit').val(data.estantes);
            $('#otros_edit').val(data.otros);

            // Limpiar bordes rojos previos
            $('input, select, textarea').removeClass('border-red-500');

            if (data.comment) {
                const camposConErrores = data.comment.split('|')[0].split(',');
                camposConErrores.forEach((campo) => {
                    campo = campo.trim();
                    const inputField = $(`#${campo}_edit`);
                    if (inputField.length > 0) {
                        inputField.addClass('border-red-500');
                    }
                });
            }

            // Cambiar el texto del botón y la acción
            $('#actualizarDomicilio')
                .text('Actualizar Domicilio')
                .data('action', 'editDomicilio'); 
        })
        .catch(error => console.error('Error al cargar los datos del domicilio:', error));
}

</script>


    <script>
    document.getElementById('actualizarDomicilio').addEventListener('click', function (e) {
        e.preventDefault();

        // Capturando los valores del formulario
        const id = document.getElementById('id_edit').value;
        const direccion = document.getElementById('direccion_edit').value;
        const empresa1 = document.getElementById('empresa1_edit').value;
        const empresa2 = document.getElementById('empresa2_edit').value;
        const municipio = document.getElementById('municipio_edit').value;
        const ubicacion = document.getElementById('ubicacion_edit').value;
        const escritorios = document.getElementById('escritorios_edit').value;
        const sillasDeEscritorios = document.getElementById('sillas_de_escritorios_edit').value;
        const sillas = document.getElementById('sillas_edit').value;
        const mesaEscritorio = document.getElementById('mesa_escritorio_edit').value;
        const sillones = document.getElementById('sillones_edit').value;
        const mesaDeCentro = document.getElementById('mesa_de_centro_edit').value;
        const cajoneras = document.getElementById('cajoneras_edit').value;
        const estantes = document.getElementById('estantes_edit').value;
        const otros = document.getElementById('otros_edit').value;

        // Validar campos requeridos (ejemplo: dirección y municipio)
        if (!direccion || !municipio) {
            Swal.fire({
                icon: 'warning',
                title: 'Campos incompletos',
                text: 'Por favor completa al menos Dirección y Municipio.'
            });
            return;
        }

        // Crear un objeto con los datos
        const formData = new FormData();
        formData.append('id', id);
        formData.append('direccion', direccion);
        formData.append('empresa1', empresa1);
        formData.append('empresa2', empresa2);
        formData.append('municipio', municipio);
        formData.append('ubicacion', ubicacion);
        formData.append('escritorios', escritorios);
        formData.append('sillas_de_escritorios', sillasDeEscritorios);
        formData.append('sillas', sillas);
        formData.append('mesa_escritorio', mesaEscritorio);
        formData.append('sillones', sillones);
        formData.append('mesa_de_centro', mesaDeCentro);
        formData.append('cajoneras', cajoneras);
        formData.append('estantes', estantes);
        formData.append('otros', otros);

        // Enviar datos mediante fetch
        fetch('domicilio/update_domicilio.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Actualización exitosa',
                        text: 'Domicilio actualizado correctamente.'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al actualizar',
                        text: data.message || 'Hubo un problema con la actualización.'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema con la actualización.'
                });
            });
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
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.getElementById('verMapaBtn').addEventListener('click', function() {
    document.getElementById('mapModal').classList.remove('hidden');

    // Obtener todas las direcciones desde tu backend
    fetch('domicilio/get_all_direcciones.php')
    .then(res => res.json())
    .then(data => {

        // Inicializar mapa centrado en México (o promedio de todas)
        var map = L.map('map').setView([25.6866, -100.3161], 12); 

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        data.forEach(d => {
            let direccion = d.direccion.replace(/#/g,'').replace(/Int\.\s*\d+/gi,'').replace(/Cp\.\s*\d+/gi,'').replace(/Col\.?\s*/gi,'').trim();
            
            // Geocodificación con Nominatim
            fetch(`domicilio/geocode.php?q=${encodeURIComponent(direccion)}`)
            .then(res => res.json())
            .then(geo => {
                if(geo.length > 0){
                    let lat = parseFloat(geo[0].lat);
                    let lon = parseFloat(geo[0].lon);

                    L.marker([lat, lon])
                     .addTo(map)
                     .bindPopup(`<b>${d.empresa1} / ${d.empresa2}</b><br>${d.direccion}`);
                }
            });
        });

    });
});

// Cerrar modal
document.getElementById('cerrarMapa').addEventListener('click', function(){
    document.getElementById('mapModal').classList.add('hidden');
    document.getElementById('map').innerHTML = ""; // Limpiar mapa para reinicializar
});







document.addEventListener('DOMContentLoaded', function() {
            // Botón de eliminar
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id-domicilio');
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
                            fetch(`domicilio/deleteDomicilio.php?id=${id}`, {
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
});

          
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const selectElement = document.getElementById('empresas-select');
    
    // Inicializa Choices.js con una configuración básica mientras se cargan los datos.
    const choices = new Choices(selectElement, {
      removeItemButton: true,
      maxItemCount: 2,
      searchPlaceholderValue: 'Cargando empresas...',
    });

    // 1. Llama a tu script de backend para obtener las empresas
    fetch('domicilio/obtener_empresas.php')
      .then(response => {
        // Verifica que la respuesta sea exitosa
        if (!response.ok) {
          throw new Error('La solicitud a la red falló');
        }
        return response.json(); // Convierte la respuesta a formato JSON
      })
      .then(data => {
        // 2. Transforma los datos recibidos al formato que necesita Choices.js
        const opcionesParaChoices = data.map(empresa => {
          return {
            value: empresa.nombre,
            label: empresa.nombre,
          };
        });

        // 3. Carga las opciones en el selector
        choices.setChoices(opcionesParaChoices, 'value', 'label', false);
        
        // Opcional: Cambia el placeholder una vez que los datos están cargados
        selectElement.setAttribute('data-placeholder', 'Escribe para buscar...');

      })
      .catch(error => {
        // 4. Maneja cualquier error que ocurra durante la solicitud
        console.error('Error al cargar las empresas:', error);
        // Podrías mostrar un mensaje al usuario aquí
      });
  });
</script>

</body>

</html>