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
    <title>Mobiliario | Inventario</title>
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
                            Mobiliario </h4>
                        <div class="">
                            <div class=" sm:rounded-lg">
                                <div class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white ">
                                    <div class="">
                                        <?php if ($canAdd): ?>

                                            <!-- Modal toggle -->
                                           <!-- Botón normal para desktop -->
<!-- Botón Desktop -->
<!-- Botón Desktop -->
<button id="btn-desktop"
    class="hidden px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg flex items-center gap-3 transition transform hover:-translate-y-1 hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-300"
    data-modal-target="crud-modal" data-modal-toggle="crud-modal"
    type="button"
>
    <!-- Ícono -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
         stroke-width="2" stroke="currentColor" class="w-6 h-6">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 4v16m8-8H4" />
    </svg>

    <!-- Texto -->
    <span>Registrar Mobiliario</span>
</button>


<!-- Botón Móvil -->
<button id="btn-mobile"
    class="hidden fixed bottom-4 right-4 w-16 h-16 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center shadow-lg z-50"
    data-modal-target="crud-modal" data-modal-toggle="crud-modal"
    type="button"
>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
         stroke-width="2" stroke="currentColor" class="w-8 h-8">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M12 4v16m8-8H4" />
    </svg>
</button>

<script>
function toggleButtons() {
    const desktop = document.getElementById('btn-desktop');
    const mobile = document.getElementById('btn-mobile');
    if (window.innerWidth >= 640) {
        desktop.classList.remove('hidden');
        mobile.classList.add('hidden');
    } else {
        desktop.classList.add('hidden');
        mobile.classList.remove('hidden');
    }
}

// Inicial
toggleButtons();

// Detectar resize
window.addEventListener('resize', toggleButtons);
</script>






                                        <?php else: ?>
                                        <?php endif; ?>
                                        <?php if ($canDelete) { ?>
<!-- 
                                            <button data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="border-l border-gray-200 inline-flex text-gray-500 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium ml-4 text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800" type="button">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button> -->

                                        <?php     } else {
                                            echo "<span class='text-gray-500'></span>";
                                        }
                                        ?>
                                       <!--  <button class="border-l border-gray-200 inline-flex text-gray-500 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-gray-700 light:focus:ring-blue-800" type="button" id="openModalButton">
                                           <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                               <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                           </svg>
                                       </button> -->
                                        <?php if ($canAdd) { ?>
                                           <!--  <button data-modal-target="progress-modal" data-modal-toggle="progress-modal" class="hover:bg-gray-100 border-l border-gray-200 inline-flex text-gray-500  focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800" type="button">
                                               <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                   <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                               </svg>
                                           </button> -->
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
        <div class="relative bg-white rounded-lg shadow-lg max-w-2xl mx-auto light:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t light:border-gray-600">
                <h3 id="registroMobiliario" class="text-lg font-semibold text-gray-900 light:text-white">
                    Registrar Mobiliario
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center light:hover:bg-gray-600 light:hover:text-white" data-modal-toggle="crud-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Cerrar ventana</span>
                </button>
            </div>
            <!-- Modal body -->
          <form action="#" method="post" class="p-4 bg-white rounded-xl space-y-6" enctype="multipart/form-data">

    <!-- Código Inventario (Hidden pero con diseño) -->
    <div class="hidden">
        <label for="codigo_inventario" class="block mb-2 text-sm font-semibold text-gray-800">Código de Inventario</label>
        <input type="text" id="codigo_inventario" name="codigo_inventario"
            class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-gray-100" readonly>
    </div>

    <!-- Foto -->
    <div>
        <label for="foto" class="block mb-3 text-sm font-semibold text-gray-800">Foto del artículo</label>
        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center bg-gray-50 hover:bg-gray-100 transition-colors">
            <input type="file" id="foto" name="foto" accept="image/*" class="w-full">
            <p class="text-sm text-gray-500 mt-2">Toca para seleccionar una imagen</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <!-- Descripción -->
        <div class="col-span-2">
            <label for="descripcion" class="block mb-2 text-sm font-semibold text-gray-800">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="2" required placeholder="Describe el artículo..."
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
        </div>

        <!-- Categoría -->
        <div class="col-span-2">
            <label for="categoria" class="block mb-2 text-sm font-semibold text-gray-800">Categoría</label>
            <select id="categoria" name="categoria" required
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">Selecciona una categoría</option>
                <option value="Silla">Silla</option>
                <option value="Mesa">Mesa</option>
                <option value="Escritorio">Escritorio</option>
                <option value="Banco">Banco</option>
                <option value="Estante">Estante</option>
                <option value="Archivador">Archivador</option>
                <option value="Librero">Librero</option>
                <option value="Gabinete">Gabinete</option>
                <option value="Locker">Locker</option>
                <option value="Pizarrón">Pizarrón</option>
                <option value="Pantalla">Pantalla</option>
                <option value="Monitor">Monitor</option>
                <option value="CPU">CPU</option>
                <option value="Laptop">Laptop</option>
                <option value="Teclado">Teclado</option>
                <option value="Mouse">Mouse</option>
                <option value="Impresora">Impresora</option>
                <option value="Escáner">Escáner</option>
                <option value="Proyector">Proyector</option>
                <option value="Teléfono">Teléfono</option>
                <option value="Router">Router</option>
                <option value="Switch">Switch</option>
                <option value="Regulador">Regulador</option>
                <option value="No-break">No-break</option>
                <option value="Cableado">Cableado</option>
                <option value="Aire acondicionado">Aire acondicionado</option>
                <option value="Ventilador">Ventilador</option>
                <option value="Lámpara">Lámpara</option>
                <option value="Cafetera">Cafetera</option>
                <option value="Refrigerador">Refrigerador</option>
                <option value="Microondas">Microondas</option>
                <option value="Dispensador de agua">Dispensador de agua</option>
                <option value="Sofá">Sofá</option>
                <option value="Sillón">Sillón</option>
                <option value="Butaca">Butaca</option>
                <option value="Cama">Cama</option>
                <option value="Colchón">Colchón</option>
                <option value="Buró">Buró</option>
                <option value="Ropero">Ropero</option>
                <option value="Otro">Otro</option>
            </select>
        </div>

        <!-- Modelo y Marca -->
        <div>
            <label for="modelo" class="block mb-2 text-sm font-semibold text-gray-800">Modelo</label>
            <input type="text" id="modelo" name="modelo" placeholder="Ej. XT-2000"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <div>
            <label for="marca" class="block mb-2 text-sm font-semibold text-gray-800">Marca</label>
            <input type="text" id="marca" name="marca" placeholder="Ej. Samsung"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Número de Serie (Hidden pero con diseño) -->
        <div class="hidden">
            <label for="numero_serie" class="block mb-2 text-sm font-semibold text-gray-800">Número de Serie</label>
            <input type="text" id="numero_serie" name="numero_serie" value="NO-APLICA"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-gray-100">
        </div>

        <!-- Condición y Estado -->
        <div>
            <label for="condicion" class="block mb-2 text-sm font-semibold text-gray-800">Condición</label>
            <select id="condicion" name="condicion"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="nuevo">Nuevo</option>
                <option value="bueno" selected>Bueno</option>
                <option value="regular">Regular</option>
                <option value="malo">Malo</option>
            </select>
        </div>

        <div>
            <label for="estado_actual" class="block mb-2 text-sm font-semibold text-gray-800">Estado Actual</label>
            <select id="estado_actual" name="estado_actual"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="activo" selected>Activo</option>
                <option value="almacenado">Almacenado</option>
                <option value="en reparación">En Reparación</option>
                <option value="baja">Baja</option>
                <option value="candidato para venta">Candidato para venta</option>
            </select>
        </div>

        <!-- Cantidad -->
        <div>
            <label for="total" class="block mb-2 text-sm font-semibold text-gray-800">Cantidad</label>
            <input type="number" id="total" name="total" min="1" value="1" placeholder="1"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Responsable (Hidden pero con diseño) -->
        <div class="hidden">
            <label for="responsable" class="block mb-2 text-sm font-semibold text-gray-800">Responsable</label>
            <input type="text" id="responsable" name="responsable" value="NO-ASIGNADO"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-gray-100">
        </div>

        <!-- Sección Extra (Hidden pero con diseño) -->
        <div id="extra-section" class="hidden">
            <label for="costo" class="block mb-2 text-sm font-semibold text-gray-800">Costo</label>
            <input type="number" step="0.01" id="costo" name="costo" value="0"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-gray-100">
        </div>

        <!-- Fechas -->
        <div>
            <label for="fecha_adquisicion" class="block mb-2 text-sm font-semibold text-gray-800">Fecha Adquisición</label>
            <input type="date" id="fecha_adquisicion" name="fecha_adquisicion"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <div>
            <label for="garantia_vencimiento" class="block mb-2 text-sm font-semibold text-gray-800">Vencimiento Garantía</label>
            <input type="date" id="garantia_vencimiento" name="garantia_vencimiento"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Depreciación Anual (Hidden pero con diseño) -->
        <div class="hidden">
            <label for="depreciacion_anual" class="block mb-2 text-sm font-semibold text-gray-800">Depreciación Anual (%)</label>
            <input type="number" step="0.01" id="depreciacion_anual" name="depreciacion_anual" value="0"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-gray-100">
        </div>

        <!-- Disponibilidad (Hidden pero con diseño) -->
        <div class="hidden">
            <label for="disponibilidad" class="block mb-2 text-sm font-semibold text-gray-800">Disponibilidad</label>
            <select id="disponibilidad" name="disponibilidad"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-gray-100">
                <option value="disponible" selected>Disponible</option>
                <option value="no disponible">No Disponible</option>
                <option value="prestado">Prestado</option>
            </select>
        </div>

        <!-- Domicilio -->
        <div class="col-span-2">
            <label for="domicilio" class="block mb-2 text-sm font-semibold text-gray-800">Domicilio</label>
            <select id="domicilio" name="domicilio"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">Selecciona un domicilio</option>
            </select>
        </div>
    </div>

    <!-- Notas -->
    <div>
        <label for="notas" class="block mb-2 text-sm font-semibold text-gray-800">Notas / Observaciones</label>
        <textarea id="notas" name="notas" rows="2" placeholder="Agregar observaciones adicionales..."
            class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
    </div>

    <!-- Botón -->
    <button type="submit" id="agregarMobiliario"
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-4 px-4 rounded-lg transition-colors flex items-center justify-center shadow-md">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
        </svg>
        Agregar Mobiliario
    </button>
</form>

                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <!-- <button id="mark-reviewed-button" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 light:border-blue-500 light:text-blue-500 light:hover:text-white light:hover:bg-blue-500 light:focus:ring-blue-800">Marcar como Revisados</button> -->

                                    
                                    
                                <form method="GET" class="mb-4 flex gap-2">
                                    <input type="text" name="search" placeholder="Buscar Mobiliario..."
                                           value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                                           class="p-2 border rounded flex-1">
                                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded flex-shrink-0">
                                        Buscar
                                    </button>
                                </form>


                                </div>
                                <div id="tbl_domicilios" style="overflow-x: auto;" class="h-auto">

                                    <table id="compu-table" class="w-full text-sm text-left rtl:text-right text-gray-500 light:text-gray-400 mb-4">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 light:bg-gray-700 light:text-gray-400">
    <tr>
        <th scope="col" class="px-6 py-3">Código</th>
        <th scope="col" class="px-6 py-3">Foto</th>
        <th scope="col" class="px-6 py-3">Descripción</th>
        <th scope="col" class="px-6 py-3">Categoría</th>
        <th scope="col" class="px-6 py-3">Modelo</th>
        <th scope="col" class="px-6 py-3">Marca</th>
        <th scope="col" class="px-6 py-3">Condición</th>
        <th scope="col" class="px-6 py-3">Estado</th>
        <th scope="col" class="px-6 py-3">Cantidad</th>
        <th scope="col" class="px-6 py-3">Costo</th>
        <th scope="col" class="px-6 py-3">Disponibilidad</th>
        <th scope="col" class="px-6 py-3">Acción</th>
    </tr>
</thead>
<tbody>
<?php
try {
    $registrosPorPagina = 8;
    $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $offset = ($paginaActual - 1) * $registrosPorPagina;

    // Total de registros
    $queryTotal = $conexion->query("SELECT COUNT(*) FROM mobiliario");
    $totalRegistros = $queryTotal->fetchColumn();
    $totalPaginas = $totalRegistros > 0 ? ceil($totalRegistros / $registrosPorPagina) : 1;

    // Búsqueda
    $busqueda = isset($_GET['search']) ? $_GET['search'] : '';

    $query = "SELECT * FROM mobiliario";
    if ($busqueda !== '') {
        $query .= " WHERE codigo_inventario LIKE :busqueda
                    OR descripcion LIKE :busqueda
                    OR categoria LIKE :busqueda
                    OR modelo LIKE :busqueda
                    OR marca LIKE :busqueda
                    OR condicion LIKE :busqueda
                    OR estado_actual LIKE :busqueda
                    OR disponibilidad LIKE :busqueda";
    }
    $query .= " ORDER BY id_mobiliario ASC LIMIT :limit OFFSET :offset";

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
            echo '<td class="px-6 py-4">'.htmlspecialchars($fila['codigo_inventario']).'</td>';
            
            // Imagen
            echo '<td class="px-6 py-4">';
            if (!empty($fila['foto'])) {
                echo '<img src="../uploads/mobiliario/'.htmlspecialchars($fila['foto']).'" alt="foto" class="w-12 h-12 rounded">';
            } else {
                echo '-';
            }
            echo '</td>';

            echo '<td class="px-6 py-4">'.htmlspecialchars($fila['descripcion']).'</td>';
            echo '<td class="px-6 py-4">'.htmlspecialchars($fila['categoria']).'</td>';
            echo '<td class="px-6 py-4">'.htmlspecialchars($fila['modelo']).'</td>';
            echo '<td class="px-6 py-4">'.htmlspecialchars($fila['marca']).'</td>';
            echo '<td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">
                        '.htmlspecialchars($fila['condicion']).'
                    </span>
                  </td>';
            echo '<td class="px-6 py-4">'.htmlspecialchars($fila['estado_actual']).'</td>';
            echo '<td class="px-6 py-4">'.htmlspecialchars($fila['total']).'</td>';
            echo '<td class="px-6 py-4">$'.number_format($fila['costo'],2).'</td>';
            echo '<td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-200 text-green-800">
                        '.htmlspecialchars($fila['disponibilidad']).'
                    </span>
                  </td>';
            // Botones acción
            echo '<td class="px-6 py-4 text-center flex justify-center space-x-2">';
            // Ver
            echo '<button data-row-id="' . $fila['id_mobiliario'] . '" type="button" data-drawer-show="drawer-right-example"  class="open-drawer p-2 text-green-600 hover:text-green-800 rounded-full bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500" title="Ver detalles">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>';
            // Editar
            echo '<button data-id-mobiliario="' . $fila['id_mobiliario'] . '" data-modal-target="crud-edit" data-modal-toggle="crud-edit" type="button" class="p-2 text-blue-600 hover:text-blue-800 rounded-full bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500" title="Editar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/>
                    </svg>
                </button>';
            // Eliminar
            echo '<button data-id-mobiliario="'.$fila['id_mobiliario'].'" type="button" class="delete-button p-2 text-red-600 hover:text-red-800 rounded-full bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500" title="Eliminar">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>';
            echo '</td>';

            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="12" class="px-6 py-4 text-center text-gray-500">No se encontraron registros.</td></tr>';
    }
} catch (PDOException $e) {
    echo '<tr><td colspan="12" class="px-6 py-4 text-red-600">Error: '.$e->getMessage().'</td></tr>';
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
                    Editar Mobiliario
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-edit">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Cerrar ventana</span>
                </button>
            </div>
            <?php include 'mobiliario/formEdit.php'; ?>
            <!-- Modal body -->
            <div id="modal-container">

            </div>
        </div>
    </div>
</div>




    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script> -->

<!-- Drawer -->
<div id="drawer-right-example" class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white dark:bg-gray-800" tabindex="-1" aria-labelledby="drawer-right-label">
    <h5 id="drawer-right-label" class="mr-4 inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400">
        <svg class="w-4 h-4 me-2.5 mr-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
        </svg>
        Características
    </h5>
    <button type="button" data-drawer-hide="drawer-right-example" aria-controls="drawer-right-example" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 end-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white">
        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
        </svg>
        <span class="sr-only">Cerrar</span>
    </button>
    <div class="border-t border-gray-700 mt-4 w-full p-4 text-left infocaracteristicas"></div>
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
        console.log('ID Mobiliario:', rowId);

        // Abrir drawer
        var $drawer = $('#drawer-right-example');
        $drawer.removeClass('translate-x-full');

        // Cargar datos vía AJAX
        $.ajax({
            url: 'mobiliario/recupera_caracteristicas.php',
            type: 'GET',
            data: { id: rowId },
            success: function(data) {
                var mobiliarioInfo = JSON.parse(data);

                $('.infocaracteristicas').html(`
                    <h3 class="text-lg font-semibold mb-4">Detalle del Mobiliario</h3>
                    <div class="mb-2"><span class="font-medium text-gray-600">Descripción:</span><p class="text-gray-300">${mobiliarioInfo.descripcion}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Modelo:</span><p class="text-gray-300">${mobiliarioInfo.modelo}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Marca:</span><p class="text-gray-300">${mobiliarioInfo.marca}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Categoría:</span><p class="text-gray-300">${mobiliarioInfo.categoria}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Número de Serie:</span><p class="text-gray-300">${mobiliarioInfo.numero_serie}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Condición:</span><p class="text-gray-300">${mobiliarioInfo.condicion}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Estado Actual:</span><p class="text-gray-300">${mobiliarioInfo.estado_actual}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Total:</span><p class="text-gray-300">${mobiliarioInfo.total}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Responsable:</span><p class="text-gray-300">${mobiliarioInfo.responsable}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Costo:</span><p class="text-gray-300">${mobiliarioInfo.costo}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Fecha de Adquisición:</span><p class="text-gray-300">${mobiliarioInfo.fecha_adquisicion}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Garantía Hasta:</span><p class="text-gray-300">${mobiliarioInfo.garantia_vencimiento}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Depreciación Anual:</span><p class="text-gray-300">${mobiliarioInfo.depreciacion_anual}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Disponibilidad:</span><p class="text-gray-300">${mobiliarioInfo.disponibilidad}</p></div>
                    <div class="mb-2"><span class="font-medium text-gray-600">Notas:</span><p class="text-gray-300">${mobiliarioInfo.notas}</p></div>
                `);
            },
            error: function() {
                console.error('Error al obtener información del mobiliario.');
                Swal.fire({
                    title: 'Error',
                    text: 'No se pudo obtener la información del mobiliario.',
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    background: '#2c2f38',
                    color: '#fff',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });

    // Botón cerrar
    $('[data-drawer-hide="drawer-right-example"]').on('click', function() {
        $('#drawer-right-example').addClass('translate-x-full');
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
    $('#agregarMobiliario').on('click', function (e) {
        e.preventDefault();

        var formData = new FormData();

        formData.append('codigo_inventario', $('#codigo_inventario').val());
        formData.append('descripcion', $('#descripcion').val());
        formData.append('categoria', $('#categoria').val());
        formData.append('modelo', $('#modelo').val());
        formData.append('marca', $('#marca').val());
        formData.append('numero_serie', $('#numero_serie').val());
        formData.append('condicion', $('#condicion').val());
        formData.append('estado_actual', $('#estado_actual').val());
        formData.append('total', $('#total').val());
        formData.append('responsable', $('#responsable').val());
        formData.append('costo', $('#costo').val());
        formData.append('fecha_adquisicion', $('#fecha_adquisicion').val());
        formData.append('garantia_vencimiento', $('#garantia_vencimiento').val());
        formData.append('depreciacion_anual', $('#depreciacion_anual').val());
        formData.append('disponibilidad', $('#disponibilidad').val());
        formData.append('domicilio', $('#domicilio').val());
        formData.append('notas', $('#notas').val());

        // 🚀 Adjuntar foto si hay
        var foto = $('#foto')[0].files[0];
        if (foto) {
            formData.append('foto', foto);
        }

        $.ajax({
            type: 'POST',
            url: 'mobiliario/addMobiliario.php', // ruta adaptada para mobiliario
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Mobiliario registrado!',
                        text: data.message,
                        confirmButtonText: 'Aceptar',
                        confirmButtonColor: '#16a34a'
                    }).then(() => {
                        // limpiar formulario después del registro
                        $('#crud-modal form')[0].reset(); // limpia todos los campos del form
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
    $('#elimina_mobiliario').on('click', function(e) {
        e.preventDefault(); // Evitar comportamiento por defecto

        // Verificar si la tabla tiene filas antes de proceder
        if ($('#tbl_mobiliario tbody tr').length === 0) {
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
            text: "¡Esta acción eliminará todos los registros de Mobiliario!",
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
                    url: 'mobiliario/DeleteTable.php', // Archivo PHP que elimina registros
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
                            $('#tbl_mobiliario tbody').html('');
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
$('[data-modal-target="crud-edit"]').on('click', function() {
    var idMobiliario = $(this).data('id-mobiliario'); // ID del mobiliario seleccionado
    rellenarModalMobiliario(idMobiliario);
});

// Función para rellenar los datos del modal de mobiliario
function rellenarModalMobiliario(idMobiliario) {
    fetch('mobiliario/edit_mobiliario.php?id=' + idMobiliario)
        .then(response => response.json())
        .then(data => {
            // Rellenar los campos del modal
            $('#id_edit').val(data.id_mobiliario); // hidden
            $('#codigo_inventario_edit').val(data.codigo_inventario || '');
            $('#descripcion_edit').val(data.descripcion || '');
            $('#categoria_edit').val(data.categoria || '');
            $('#modelo_edit').val(data.modelo || '');
            $('#marca_edit').val(data.marca || '');
            $('#numero_serie_edit').val(data.numero_serie || '');
            $('#condicion_edit').val(data.condicion || '');
            $('#estado_actual_edit').val(data.estado_actual || '');
            $('#total_edit').val(data.total || '');
            $('#responsable_edit').val(data.responsable || '');
            $('#costo_edit').val(data.costo || '');
            $('#fecha_adquisicion_edit').val(data.fecha_adquisicion || '');
            $('#garantia_vencimiento_edit').val(data.garantia_vencimiento || '');
            $('#depreciacion_anual_edit').val(data.depreciacion_anual || '');
            $('#disponibilidad_edit').val(data.disponibilidad || '');
            $('#domicilio_edit').val(data.id_domicilio || '');
            $('#notas_edit').val(data.notas || '');

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
            $('#actualizarMobiliario')
                .text('Actualizar Mobiliario')
                .data('action', 'editMobiliario'); 
        })
        .catch(error => console.error('Error al cargar los datos del mobiliario:', error));
}
</script>

<script>
document.getElementById('actualizarMobiliario').addEventListener('click', function(e){
    e.preventDefault();

    const form = document.getElementById('formMobiliario');
    const formData = new FormData(form);

    // Debug más detallado
    console.log('=== DATOS DEL FORMULARIO ===');
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    console.log('=== FIN DATOS ===');

    fetch('mobiliario/update_mobiliario.php', {
        method: 'POST',
        body: formData
    })
    .then(res => {
        console.log('Status:', res.status);
        console.log('Headers:', res.headers);
        return res.json();
    })
    .then(data => {
        console.log('Respuesta del servidor:', data);
        if(data.success){
            alert('✅ ' + data.message);
            location.reload();
        }else{
            alert('⚠️ ' + data.message);
        }
    })
    .catch(err => {
        console.error('Error completo:', err);
        alert('Error de conexión con el servidor. Revisa la consola.');
    });
});

</script>



    


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch(`domicilio/get_direccion_id.php`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('domicilio');
                    data.forEach(domicilio => {
                        const option = document.createElement('option');
                        option.value = domicilio.id;
                        option.textContent = domicilio.direccion;
                        select.appendChild(option);
                    });
                })
            .catch(error => console.error('Error cargando domicilios:', error));
        });

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




<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('click', function (e) {
        const button = e.target.closest('.delete-button');
        if (!button) return;

        const id = button.getAttribute('data-id-mobiliario');

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
                // Petición al servidor
                fetch(`mobiliario/deleteMobiliario.php?id=${id}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#4CAF50'
                        }).then(() => {
                            // Refrescar tabla o página
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            confirmButtonColor: '#F44336'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un problema en el servidor',
                        icon: 'error',
                        confirmButtonColor: '#F44336'
                    });
                });
            }
        });
    });
});
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectCondicion = document.getElementById('condicion');
    const extraSection = document.getElementById('extra-section');

    function toggleExtra() {
        if (selectCondicion.value === 'nuevo') {
            extraSection.classList.remove('hidden');
        } else {
            extraSection.classList.add('hidden');
        }
    }

    // Ejecuta al cambiar y también al cargar la página (por si ya viene seleccionado)
    selectCondicion.addEventListener('change', toggleExtra);
    toggleExtra();
});
</script>
        <script>
            // Generar código tipo MOB-2025-XXXX
            function generarCodigoInventario() {
                const year = new Date().getFullYear();
                const random = Math.random().toString(36).substring(2, 6).toUpperCase();
                return `MOB-${year}-${random}`;
            }
            
            // Asignar el código automáticamente al cargar el formulario
            document.addEventListener('DOMContentLoaded', () => {
                const inputCodigo = document.getElementById('codigo_inventario');
                if (inputCodigo && !inputCodigo.value) {
                    inputCodigo.value = generarCodigoInventario();
                }
            });
        </script>
</body>

</html>