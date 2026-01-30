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
    <title>Computadoras | Inventario</title>
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
    
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js/dist/css/shepherd.css">
<script src="https://cdn.jsdelivr.net/npm/shepherd.js/dist/js/shepherd.min.js"></script>

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
                            Mis Computadoras </h4>
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
                                                Registrar Computador
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
<button id="mark-reviewed-button" type="button" 
    class="inline-flex items-center text-blue-700 hover:text-white border border-blue-700 
           hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 
           font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 light:border-blue-500 
           light:text-blue-500 light:hover:text-white light:hover:bg-blue-500 light:focus:ring-blue-800">
    
    <!-- SVG de check / revisión -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
         stroke-width="1.5" stroke="currentColor" class="w-6 h-6 me-2">
        <path stroke-linecap="round" stroke-linejoin="round" 
              d="M4.5 12.75l6 6 9-13.5" />
    </svg>
    
    Revisar
</button>


<!-- Botón -->
<button id="pc-commands-open" type="button" 
    class="inline-flex items-center text-blue-700 hover:text-white border border-blue-700 
           hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 
           font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2">
    
    <!-- SVG -->
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" 
         stroke-width="1.5" stroke="currentColor" class="w-6 h-6 me-2">
        <path stroke-linecap="round" stroke-linejoin="round" 
              d="M4.5 12.75l6 6 9-13.5" />
    </svg>
    Ver comandos de PC
</button>

<!-- Modal -->
<div id="pc-commands-modal" class="fixed inset-0 hidden bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white rounded-lg shadow-lg w-11/12 max-w-3xl p-6 overflow-y-auto max-h-[90vh]">
    
    <!-- Header -->
    <div class="flex justify-between items-center border-b pb-3">
      <h3 class="text-lg font-semibold text-gray-800">Comandos para verificar tu PC</h3>
      <button id="pc-commands-close-x" class="text-gray-500 hover:text-gray-700">
        ✖
      </button>
    </div>

    <!-- Contenido -->
    <div class="mt-4 space-y-6 text-sm text-gray-700">
      
      <!-- CMD -->
      <div>
        <h4 class="font-bold text-blue-600">📟 CMD (Símbolo del sistema)</h4>
        <pre class="bg-gray-100 p-3 rounded text-xs overflow-x-auto">
Marca y modelo:      wmic csproduct get vendor, name, identifyingnumber
RAM total:           wmic computersystem get TotalPhysicalMemory
CPU:                 wmic cpu get Name, NumberOfCores, NumberOfLogicalProcessors, MaxClockSpeed
Discos:              wmic diskdrive get Model, MediaType, Size
GPU:                 wmic path win32_videocontroller get name
Sistema operativo:   systeminfo | findstr /B /C:"OS Name" /C:"OS Version"
        </pre>
      </div>

      <!-- PowerShell -->
      <div>
        <h4 class="font-bold text-green-600">⚡ PowerShell</h4>
        <pre class="bg-gray-100 p-3 rounded text-xs overflow-x-auto">
Marca y modelo:   Get-CimInstance Win32_ComputerSystem | Select Manufacturer,Model
RAM total (GB):   (Get-CimInstance Win32_ComputerSystem).TotalPhysicalMemory / 1GB
CPU:              Get-CimInstance Win32_Processor | Select Name,NumberOfCores,NumberOfLogicalProcessors,MaxClockSpeed
RAM por módulo:   Get-CimInstance Win32_PhysicalMemory | Select Manufacturer,Capacity,Speed,PartNumber
Discos (GB):      Get-CimInstance Win32_DiskDrive | Select Model,InterfaceType,MediaType,@{Name="SizeGB";Expression={[math]::Round($_.Size/1GB,2)}}
GPU:              Get-CimInstance Win32_VideoController | Select Name
SO:               Get-ComputerInfo | Select WindowsProductName,WindowsVersion,OsHardwareAbstractionLayer
        </pre>
      </div>

    </div>

    <!-- Footer -->
    <div class="mt-6 flex justify-end">
      <button id="pc-commands-close-btn" 
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Cerrar
      </button>
    </div>
  </div>
</div>

<!-- Script -->
<script>
document.getElementById('pc-commands-open').addEventListener('click', () => {
  document.getElementById('pc-commands-modal').classList.remove('hidden');
});
document.getElementById('pc-commands-close-x').addEventListener('click', () => {
  document.getElementById('pc-commands-modal').classList.add('hidden');
});
document.getElementById('pc-commands-close-btn').addEventListener('click', () => {
  document.getElementById('pc-commands-modal').classList.add('hidden');
});
</script>


                                        


                                        <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                            <div class="relative p-4 w-full max-w-md max-h-full">
                                                <div class="relative bg-white rounded-lg shadow light:bg-gray-700">
                                                    <div class="p-4 md:p-5 text-center">
                                                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 light:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                        </svg>
                                                        <h3 class="mb-5 text-lg font-normal text-gray-500 light:text-gray-400">Está seguro que desea vaciar la tabla?</h3>
                                                        <button id="elimina_compu" data-modal-hide="popup-modal" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 light:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
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
                                                                        <div id="preview-compu" class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                            <svg class="w-8 h-8 mb-4 text-gray-500 light:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                                            </svg>
                                                                            <p class="mb-2 text-sm text-gray-500 light:text-gray-400"><span class="font-semibold">Click para cargar archivo</span></p>
                                                                            <p class="text-xs text-gray-500 light:text-gray-400">Soporta CSV o TXT</p>
                                                                        </div>


                                                                        <input id="dropzone-file" type="file" name="subecomputadoras" class="hidden" accept=".csv,.txt" />
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
                                                            Nuevo registro de computadora
                                                        </h3>
                                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center light:hover:bg-gray-600 light:hover:text-white" data-modal-toggle="crud-modal">
                                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                            </svg>
                                                            <span class="sr-only">Cerrar ventana</span>
                                                        </button>
                                                    </div>
                                                    <!-- Modal body -->
                                                    <form method="#" class="p-4 md:p-5">


                                                        <div class="mb-4 border-b border-gray-200 light:border-gray-700">
                                                            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-purple-600 hover:text-purple-600 light:text-purple-500 light:hover:text-purple-500 border-purple-600 light:border-purple-500" data-tabs-inactive-classes="light:border-transparent text-gray-500 hover:text-gray-600 light:text-gray-400 border-gray-100 hover:border-gray-300 light:border-gray-700 light:hover:text-gray-300" role="tablist">
                                                                <li class="me-2" role="presentation">
                                                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 light:hover:text-gray-300" id="dashboard-styled-tab" data-tabs-target="#styled-dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Ubicacion</button>
                                                                </li>
                                                                <li class="me-2" role="presentation">
                                                                    <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab" data-tabs-target="#styled-profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Datos de la computadora</button>
                                                                </li>
                                                                <li class="me-2" role="presentation">
                                                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 light:hover:text-gray-300" id="settings-styled-tab" data-tabs-target="#styled-settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Cuentas</button>
                                                                </li>
                                                                <li role="presentation">
                                                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 light:hover:text-gray-300" id="contacts-styled-tab" data-tabs-target="#styled-contacts" type="button" role="tab" aria-controls="contacts" aria-selected="false">Componentes</button>
                                                                </li>
                                                                <li role="presentation">
                                                                    <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 light:hover:text-gray-300" id="contacts-styled-tab" data-tabs-target="#styled-seg" type="button" role="tab" aria-controls="contacts" aria-selected="false">Seguimiento</button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <div id="default-styled-tab-content">
                                                            <div class="hidden p-4 rounded-lg bg-gray-50 light:bg-gray-800" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab">
                                                                <p class="text-sm text-gray-500 light:text-gray-400"></p>
                                                                <div class="grid gap-5 mb-4 grid-cols-2">
                                                                    <?php
                                                                    try {
                                                                        $consulta = $conexion->query("SELECT * FROM Empleados");
                                                                        $departamentos = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                                                    } catch (PDOException $e) {
                                                                        die('Error en la consulta: ' . $e->getMessage());
                                                                    }
                                                                    ?>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="asignado_a" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Asignado A <span class="text-red-500">*</span></label>
                                                                        <!-- <input type="text" name="asignado_a" id="asignado_a" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required="" placeholder="JUAN BAUTISTA"> -->
                                                                        <select id="asignado_a" name="asignado_a" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500">
                                                                            <option class="light:text-white" selected="">Selecciona Empleado</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="tipo" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Tipo <span class="text-red-500">*</span></label>
                                                                        <select
                                                                            name="tipo"
                                                                            id="tipo"
                                                                            class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500"
                                                                            required>
                                                                            <option value="" disabled selected>Selecciona un tipo</option>
                                                                            <option value="All-in-One">All-in-One</option>
                                                                            <option value="Custom">Custom</option>
                                                                            <option value="Laptop">Laptop</option>
                                                                            <option value="Desktop">Desktop</option>
                                                                            <option value="Mini PC">Mini PC</option>
                                                                            <option value="Servidor">Servidor</option>
                                                                            <option value="Workstation">Workstation</option>
                                                                            <option value="Tablet">Tablet</option>
                                                                        </select>
                                                                    </div>
                                                                    <?php


                                                                    // Consultar los valores del ENUM
                                                                    $query = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'computadora' AND COLUMN_NAME = 'marca'";
                                                                    $stmt = $conexion->prepare($query);
                                                                    $stmt->execute();
                                                                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                                                                    // Extraer los valores del ENUM
                                                                    $enumValues = [];
                                                                    if ($result) {
                                                                        $enum = $result['COLUMN_TYPE']; // Obtiene "enum('ACER','ACTECK',...)"
                                                                        preg_match_all("/'([^']+)'/", $enum, $matches);
                                                                        $enumValues = $matches[1]; // Extrae los valores entre comillas simples
                                                                    }

                                                                    // Generar las opciones del <select>
                                                                    ?>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="marca" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">
                                                                            Marca <span class="text-red-500">*</span>
                                                                        </label>
                                                                        <select name="marca" id="marca" class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required>
                                                                            <option value="" selected disabled>Selecciona una marca</option>
                                                                            <?php foreach ($enumValues as $value): ?>

                                                                                <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($value) ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="modelo" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Modelo <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="modelo" id="modelo" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required="">
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="condicion" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Condición <span class="text-red-500">*</span></label>
                                                                        <select
                                                                            name="condicion"
                                                                            id="condicion"
                                                                            class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500"
                                                                            required>
                                                                            <option value="" disabled selected>Selecciona una condición</option>
                                                                            <option value="Buena">Buena</option>
                                                                            <option value="Regular">Regular</option>
                                                                            <option value="Mala">Mala</option>
                                                                            <option value="Defectuosa">Defectuosa</option>
                                                                            <option value="Excelente">Excelente</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="costoEquipoActual" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Costo Equipo Actual <span class="text-red-500">*</span></label>
                                                                        <input type="number" name="costoEquipoActual" id="costoEquipoActual" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required="">
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="fechaDeAsignacion" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Fecha De Asignación <span class="text-red-500">*</span></label>
                                                                        <input type="date" name="fechaDeAsignacion" id="fechaDeAsignacion" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required="">
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="anoDeProcesador" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Año De Procesador <span class="text-red-500">*</span></label>
                                                                        <input type="number" name="anoDeProcesador" id="anoDeProcesador" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required="">
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="fechaDeLanzamiento" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Fecha De Lanzamiento <span class="text-red-500">*</span></label>
                                                                        <input type="date" name="fechaDeLanzamiento" id="fechaDeLanzamiento" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required="">
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="status" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Status <span class="text-red-500">*</span></label>
                                                                        <select
                                                                            name="status"
                                                                            id="status"
                                                                            class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500"
                                                                            required>
                                                                            <option value="" disabled selected>Selecciona un status</option>
                                                                            <option value="Activa">Activa</option>
                                                                            <option value="Vendida">Vendida</option>
                                                                            <option value="Venta">Venta</option>
                                                                            <option value="Stock">Stock</option>
                                                                            <option value="Domicilio Fiscal">Domicilio Fiscal</option>
                                                                            <option value="Reservada">Reservada</option>
                                                                            <option value="Retirada">Retirada</option>
                                                                            <option value="Descontinuada">Descontinuada</option>
                                                                            <option value="Otra">Otra</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="hidden p-4 rounded-lg bg-gray-50 light:bg-gray-800" id="styled-dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                                                                <p class="text-sm text-gray-500 light:text-gray-400">Ubicacion</p>
                                                                <div class="grid gap-5 mb-4 grid-cols-2">
                                                                    <?php
                                                                    try {
                                                                        $consulta = $conexion->query("SELECT * FROM departamentos");
                                                                        $departamentos = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                                                    } catch (PDOException $e) {
                                                                        die('Error en la consulta: ' . $e->getMessage());
                                                                    }
                                                                    ?>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="departamento" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Departamento <span class="text-red-500">*</span></label>
                                                                        <select id="departamento" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required>
                                                                            <option class="light:text-white" selected="">Selecciona Departamento</option>
                                                                            <?php foreach ($departamentos as $departamento) : ?>
                                                                                <option class="light:text-white" value="<?= $departamento['Id_departamento'] ?>"><?= htmlspecialchars($departamento['nombre']) ?></option>
                                                                            <?php endforeach; ?>
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
                                                                        <label for="oficina" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Oficina <span class="text-red-500">*</span></label>
                                                                        <select id="oficina" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required>
                                                                            <option class="light:text-white" selected="">Selecciona Oficina</option>
                                                                            <?php foreach ($oficinas as $oficina) : ?>
                                                                                <option class="light:text-white" value="<?= $oficina['Id_Oficina'] ?>"><?= htmlspecialchars($oficina['nombre']) ?></option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="hidden p-4 rounded-lg bg-gray-50 light:bg-gray-800" id="styled-settings" role="tabpanel" aria-labelledby="settings-tab">
                                                                <p class="text-sm text-gray-500 light:text-gray-400">Cuentas</p>
                                                                <div class="grid gap-5 mb-4 grid-cols-2">
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="correo_asociado" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Correo Asociado <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="correo_asociado" id="correo_asociado" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500 " required placeholder="desarrollo@example.org">
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="contrasenaGmail1" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Contraseña Gmail <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="contrasenaGmail1" id="contrasenaGmail1" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required placeholder="********">
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="contrasenaOutlook1" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Contraseña Outlook <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="contrasenaOutlook1" id="contrasenaOutlook1" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required placeholder="********">
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="correoAsociado2" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Correo Asociado 2</label>
                                                                        <input type="text" name="correoAsociado2" id="correoAsociado2" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="desarrollo@example.org">
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="contrasenaGmail2" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Contraseña Gmail 2</label>
                                                                        <input type="text" name="contrasenaGmail2" id="contrasenaGmail2" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="********">
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="contrasenaOutlook2" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Contraseña Outlook 2</label>
                                                                        <input type="text" name="contrasenaOutlook2" id="contrasenaOutlook2" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="********">
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="correoAsociado3" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Correo Asociado 3</label>
                                                                        <input type="text" name="correoAsociado3" id="correoAsociado3" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="desarrollo@example.org">
                                                                    </div>
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="contrasenaWindow" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Contraseña Windows <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="contrasenaWindow" id="contrasenaWindow" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required placeholder="********">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="hidden p-4 rounded-lg bg-gray-50 light:bg-gray-800" id="styled-contacts" role="tabpanel" aria-labelledby="contacts-tab">
                                                                <p class="text-sm text-gray-500 light:text-gray-400">Componentes</p>
                                                                <div class="grid gap-5 mb-4 grid-cols-2">
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="tipoDeDisco" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Tipo de disco <span class="text-red-500">*</span></label>
                                                                        <select
                                                                            name="tipoDeDisco"
                                                                            id="tipoDeDisco"
                                                                            class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500"
                                                                            required>
                                                                            <option value="" disabled selected>Selecciona un tipo de disco</option>
                                                                            <option value="SSD">SSD</option>
                                                                            <option value="HDD">HDD</option>
                                                                            <option value="Hybrid (SSHD)">Hybrid (SSHD)</option>
                                                                            <option value="NVMe">NVMe</option>
                                                                            <option value="SATA">SATA</option>
                                                                            <option value="Otra">Otra</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="procesador" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Procesador <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="procesador" id="procesador" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1 relative">
                                                                        <label for="ram" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Ram <span class="text-red-500">*</span></label>
                                                                        <div class="flex items-center">
                                                                            <input
                                                                                type="number"
                                                                                name="ram"
                                                                                id="ram"
                                                                                class="block p-2 pe-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500"
                                                                                placeholder="Ejemplo: 4"
                                                                                min="1"
                                                                                required>
                                                                            <span class="ml-2 text-sm text-gray-600 light:text-gray-400">GB</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="hidden p-4 rounded-lg bg-gray-50 light:bg-gray-800" id="styled-seg" role="tabpanel" aria-labelledby="contacts-tab">
                                                                <p class="text-sm text-gray-500 light:text-gray-400">Datos para seguimiento</p>
                                                                <div class="grid gap-5 mb-4 grid-cols-2">
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="posibleFechaParaVenta" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Posible Fecha Para Venta <span class="text-red-500">*</span></label>
                                                                        <input type="date" name="posibleFechaParaVenta" id="posibleFechaParaVenta" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="nuevaCompra" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Nueva Compra <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="nuevaCompra" id="nuevaCompra" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required>
                                                                    </div>

                                                                    <!-- Foto php -->
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="foto" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Foto <span class="text-red-500">*</span></label>
                                                                        <input type="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 light:text-gray-400 focus:outline-none light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400" name="foto" id="foto" accept="image/*" required>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="pcAnterior" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">PC Anterior <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="pcAnterior" id="pcAnterior" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="posibleAsignacion" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Posible Asignación <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="posibleAsignacion" id="posibleAsignacion" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="total" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Total <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="total" id="total" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="$2,050.98" required>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="costoAlComprar" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Costo Al Comprar <span class="text-red-500">*</span></label>
                                                                        <input type="number" name="costoAlComprar" id="costoAlComprar" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="$1,999" required>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="costoALaVenta" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Costo A La Venta <span class="text-red-500">*</span></label>
                                                                        <input type="number" name="costoALaVenta" id="costoALaVenta" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" placeholder="$2,000" required>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="disponibilidad" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Disponibilidad <span class="text-red-500">*</span></label>
                                                                        <select name="disponibilidad" id="disponibilidad" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required>
                                                                            <option value="" disabled selected>Selecciona disponibilidad</option>
                                                                            <option value="Disponible">Disponible</option>
                                                                            <option value="No Disponible">No Disponible</option>
                                                                            <option value="En Uso">En Uso</option>
                                                                            <option value="Mantenimiento">Mantenimiento</option>
                                                                            <option value="Asignada">Asignada</option>
                                                                            <option value="Reservada">Reservada</option>
                                                                            <option value="Descontinuada">Descontinuada</option>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="propietario_Destino" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Propietario Destino <span class="text-red-500">*</span></label>
                                                                        <input type="text" name="propietario_Destino" id="propietario_Destino" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required>
                                                                    </div>

                                                                    <!-- Foto2 php -->
                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="foto2" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Foto <span class="text-red-500">*</span></label>
                                                                        <input type="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 light:text-gray-400 focus:outline-none light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400" name="foto2" id="foto2" accept="image/*" required>
                                                                    </div>

                                                                    <div class="col-span-2 sm:col-span-1">
                                                                        <label for="fechaDeReasignacion" class="block mb-2 text-sm font-medium text-gray-900 light:text-white">Fecha Reasignación <span class="text-red-500">*</span></label>
                                                                        <input type="date" name="fechaDeReasignacion" id="fechaDeReasignacion" class="block p-2  text-sm text-gray-900 border border-gray-300 w-full rounded-lg  bg-gray-50 focus:ring-blue-500 focus:border-blue-500 light:bg-gray-700 light:border-gray-600 light:placeholder-gray-400 light:text-white light:focus:ring-blue-500 light:focus:border-blue-500" required>
                                                                    </div>
                                                                </div>

                                                                <button type="submit" id="agregaEquipo" data-action='editEquipo' class="text-white inline-flex items-center w-full mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center light:bg-blue-600 light:hover:bg-blue-700 light:focus:ring-blue-800">
                                                                    <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Agregar Computadora
                                                                </button>
                                                            </div>

                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <!-- <button id="mark-reviewed-button" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 light:border-blue-500 light:text-blue-500 light:hover:text-white light:hover:bg-blue-500 light:focus:ring-blue-800">Marcar como Revisados</button> -->

                                    
                                    
                                    <form method="GET" class="mb-4">
                                        <input type="text" name="search" placeholder="Buscar computadora..." 
                                               value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" 
                                               class="p-2 border rounded w-80">
                                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded ml-2">Buscar</button>
                                    </form>

                                </div>
                                <div id="tbl_pc" style="overflow-x: auto;" class="h-auto">

                                    <table id="compu-table" class="w-full text-sm text-left rtl:text-right text-gray-500 light:text-gray-400 mb-4">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 light:bg-gray-700 light:text-gray-400">
                                            <tr>
                                                <th scope="col" class="px-6 py-3">
                                                  <input id="select-all" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 light:focus:ring-blue-600 light:ring-offset-gray-800 focus:ring-2 light:bg-gray-700 light:border-gray-600">
                                                </th>
                                                <!-- <th scope="col" class="px-6 py-3">
                                                            pw
                                                        </th> -->
                                                <th scope="col" class="px-6 py-3">
                                                    Logo
                                                </th>
                                                <th scope="col" class="px-6 py-3">

                                                    Marca
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Modelo
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Procesador
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Oficina
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Departamento
                                                </th>
                                                
                                                <?php
                                                 if ($canViewMoney) {
                                                        echo '
                                                        <th scope="col" class="px-6 py-3">
                                                    Costo
                                                </th>
                                                        ';
 } else {
                                                            echo "<span class='text-gray-500'></span>";
                                                        }
                                                ?>
                                                <th scope="col" class="px-6 py-3">
                                                    Asignado a
                                                </th>
                                                <th scope="col" class="px-6 py-3">
                                                    Action
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            try {


                                                $idUsuario = $_SESSION["user_id"];

                                                $registrosPorPagina = 7;

                                                // Obtener la página actual desde la URL (por defecto será la página 1)
                                                $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

                                                // Calcular el índice del primer registro de la página actual
                                                $offset = ($paginaActual - 1) * $registrosPorPagina;

                                                // Obtener el total de registros en la base de datos
                                                // Obtener el total de computadoras solo de los departamentos asignados al usuario
                                                $queryTotal = $conexion->prepare("
                                                    SELECT COUNT(*) FROM computadora
                                                    WHERE Id_departamento IN (
                                                        SELECT Id_departamento FROM usuarios_departamentos WHERE Id_usuario = :idUsuario
                                                    )
                                                    ");
                                                $queryTotal->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
                                                $queryTotal->execute();
                                                $totalRegistros = $queryTotal->fetchColumn();

                                                // Calcular el número total de páginas con los registros visibles para el usuario
                                                $totalPaginas = $totalRegistros > 0 ? ceil($totalRegistros / $registrosPorPagina) : 1;


                                                // Obtener el ID del usuario logueado

                                                // Verificar si el usuario tiene departamentos asignados
                                                $queryDepartamentosAsignados = "SELECT COUNT(*) AS total FROM usuarios_departamentos WHERE Id_usuario = :idUsuario";
                                                $stmtDepartamentosAsignados = $conexion->prepare($queryDepartamentosAsignados);
                                                $stmtDepartamentosAsignados->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
                                                $stmtDepartamentosAsignados->execute();
                                                $resultadoDepartamentos = $stmtDepartamentosAsignados->fetch(PDO::FETCH_ASSOC);

                                                if ($resultadoDepartamentos['total'] == 0) {
                                                    // Si no tiene departamentos asignados, mostrar una alerta con botón de cierre
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
// Obtener el rol del usuario
$queryRol = "
    SELECT r.nombre 
    FROM usuarios ur
    JOIN roless r ON ur.rolActual = r.id
    WHERE ur.Id_Usuario = :idUsuario
    LIMIT 1
";
$stmtRol = $conexion->prepare($queryRol);
$stmtRol->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
$stmtRol->execute();
$rolUsuario = $stmtRol->fetchColumn(); // devuelve "Admin", "Staff", etc.


                                                // Consulta para obtener los registros de computadoras asociados a los departamentos del usuario
$idUsuario = $_SESSION["user_id"];
$busqueda = isset($_GET['search']) ? $_GET['search'] : '';

// Obtener email y rol
$stmtUsuario = $conexion->prepare("SELECT email, rolActual FROM usuarios WHERE id_Usuario = :idUsuario");
$stmtUsuario->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
$stmtUsuario->execute();
$usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

$emailUsuario = $usuario['email']; // para Staff
$rolUsuario   = $usuario['rolActual']; // string, ej "10"

// Consulta base
$query = "
    SELECT computadora.*, oficina.nombre as nombre_ofi, departamentos.nombre as nombre_depa
    FROM computadora
    JOIN oficina ON computadora.Id_Oficina = oficina.Id_Oficina
    JOIN departamentos ON computadora.Id_departamento = departamentos.Id_departamento
";

$params = []; // array para bind

// Filtrado por rol
if ($rolUsuario === "10") { // Staff
    $query .= " WHERE TRIM(LOWER(computadora.correo_asociado)) = LOWER(:emailUsuario) ";
    $params[':emailUsuario'] = $emailUsuario;
} else { // Otros roles
    $query .= " WHERE computadora.Id_departamento IN (
        SELECT Id_departamento
        FROM usuarios_departamentos
        WHERE Id_usuario = :idUsuario
    )";
    $params[':idUsuario'] = $idUsuario;
}

// Filtro de búsqueda
if (!empty($busqueda)) {
    $query .= " AND (
        computadora.marca LIKE :busqueda OR
        computadora.modelo LIKE :busqueda OR
        computadora.procesador LIKE :busqueda OR
        computadora.asignado_a LIKE :busqueda OR
        computadora.Id_departamento LIKE :busqueda
    )";
    $params[':busqueda'] = "%$busqueda%";
}

// Paginación
$query .= " ORDER BY Id_computadora ASC LIMIT :limit OFFSET :offset";
$params[':limit']  = $registrosPorPagina;
$params[':offset'] = $offset;

// Preparar y ejecutar
$stmt = $conexion->prepare($query);
foreach ($params as $key => $val) {
    if ($key === ':limit' || $key === ':offset') {
        $stmt->bindValue($key, $val, PDO::PARAM_INT);
    } else {
        $stmt->bindValue($key, $val, PDO::PARAM_STR);
    }
}
$stmt->execute();
$resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);



// Consulta de departamentos (no cambia)
$queryDepartamentosAsignados = "
    SELECT d.nombre, COUNT(c.Id_computadora) AS total
    FROM departamentos d
    LEFT JOIN computadora c ON d.Id_departamento = c.Id_departamento
    WHERE d.Id_departamento IN (
        SELECT Id_departamento
        FROM usuarios_departamentos
        WHERE Id_usuario = :idUsuario
    )
    GROUP BY d.nombre
    HAVING COUNT(c.Id_computadora) > 0
";

$stmtDepartamentosAsignados = $conexion->prepare($queryDepartamentosAsignados);
$stmtDepartamentosAsignados->bindParam(':idUsuario', $idUsuario, PDO::PARAM_INT);
$stmtDepartamentosAsignados->execute();
$departamentos = $stmtDepartamentosAsignados->fetchAll(PDO::FETCH_ASSOC);



                                                $colores = [
                                                    'bg-blue-100 text-blue-800 border-blue-400',
                                                    'bg-gray-100 ext-gray-600 border-gray-500',
                                                    'bg-red-100 text-red-800 border-red-400',
                                                    'bg-green-100 text-green-800 border-green-400',
                                                    'bg-yellow-100 text-yello0 border-yellow-300',
                                                    'bg-indigo-100 text-indigo-800 border-indigo-400',
                                                    'bg-purple-100 text-purple-800 border-purple-400',
                                                    'bg-pink-100 text-pink-800 border-pink-400'
                                                ];
$marca_colores_map = [];
$oficina_colores_map = [];
$departamento_colores_map = [];

function getColor(&$map, $key, $colores) {
    if (!isset($map[$key])) {
        $map[$key] = $colores[array_rand($colores)];
    }
    return $map[$key];
}

                                                // Mostrar los departamentos y el número de computadoras en el formato deseado
                                                echo '<div class="flex flex-wrap gap-2 mb-8">';
                                                foreach ($departamentos as $index => $departamento) {
                                                    // Seleccionar un color del array (usamos el índice para ciclar los colores)
                                                    $color = $colores[$index % count($colores)];
                                                    echo '<span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm ' . $color . '">';
                                                    echo strtoupper($departamento['nombre']) . ': ' . $departamento['total'];
                                                    echo '</span>';
                                                }
                                                echo '</div>';

                                                // Mostrar los resultados en la tabla
                                                if ($resultado) {
                                                    foreach ($resultado as $fila) {
                                                        $marca = $fila['marca'];
                                                        $oficina = $fila['nombre_ofi'];
                                                        $departamento = $fila['nombre_depa'];

    $marcaBadge = getColor($marca_colores_map, $marca, $colores);
    $oficinaBadge = getColor($oficina_colores_map, $oficina, $colores);
    $departamentoBadge = getColor($departamento_colores_map, $departamento, $colores);


                                                        echo '<tr class="bg-white border-b light:bg-gray-800 light:border-gray-700 hover:bg-gray-50 light:hover:bg-gray-900">';

                                                        echo '<td class="px-6 py-4">
                                                                    <div class="flex items-center me-4">
                                                                        <input data-popover-target="popover-' . $fila['Id_computadora'] . '"  data-popover-placement="right"  type="checkbox" 
                                                                            class="row-checkbox w-4 h-4 ' .
                                                            ($fila['revisado'] == 1 ? 'text-blue-600 focus:ring-blue-500' : ($fila['revisado'] == 2 ? 'text-orange-600 focus:ring-orange-500' : 'text-gray-600 focus:ring-gray-500')) . ' 
                                                                            bg-gray-100 border-gray-300 rounded light:focus:ring-blue-600 light:ring-offset-gray-800 focus:ring-2 light:bg-gray-700 light:border-gray-600" 
                                                                            data-id="' . $fila['Id_computadora'] . '" 
                                                                data-comment="' . htmlspecialchars($fila['comment'] ?? '') . '" 
                                                                            ' . ($fila['revisado'] == 1 || $fila['revisado'] == 2 ? 'checked disabled' : '') . '>
                                                                        <label for="checkbox-' . $fila['Id_computadora'] . '" class="ms-2 text-sm font-medium text-gray-900 light:text-gray-300"></label>
                                                                    </div>
                                                                    <div data-popover id="popover-' . $fila['Id_computadora'] . '" role="tooltip" class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 light:text-gray-400 light:border-gray-600 light:bg-gray-800 left-full ml-2">
                                                                        <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 rounded-t-lg light:border-gray-600 light:bg-gray-700">
                                                                            <h3 class="font-semibold text-gray-900 light:text-white">Comentario</h3>
                                                                        </div>
                                                                        <div class="px-3 py-2 comment-content">
                                                                            <div class="px-3 py-2 comment-content">';

                                                        if ($fila['revisado'] == 2) {
                                                            // Separar campos y comentario adicional
                                                            $parts = explode('|', $fila['comment']);
                                                            $campos = isset($parts[0]) ? $parts[0] : '';
                                                            $comentarioAdicional = isset($parts[1]) ? $parts[1] : '';

                                                            // Formatear los campos y comentario adicional
                                                            $formattedCampos = '<strong>Campos con errores:</strong><br>' . nl2br(str_replace(',', '<br>', trim($campos)));
                                                            $formattedComentario = $comentarioAdicional ? '<br><strong>Comentario adicional:</strong><br>' . htmlspecialchars(trim($comentarioAdicional)) : '';

                                                            echo '<p class="text-gray-700 light:text-gray-300">' . $formattedCampos . $formattedComentario . '</p>';
                                                        } else if ($fila['revisado'] == 1) {
                                                            echo '<p class="text-green-700 light:text-gray-300"><strong>Verificado ' . $fila['comment'] . '</strong></p>';
                                                        } else {
                                                            echo '<p class="text-red-700 light:text-gray-300"><strong>No Verificado</strong></p>';
                                                        }
                                                        echo '                                                                     
                                                                                </div>
                                                                            <div data-popper-arrow></div>
                                                                        </div>
                                                                    </td>';

                                                        echo '<td class="px-6 py-4"><img src="https://logo.clearbit.com/' . $marca . '.com" alt="' . $marca . ' logo" class="w-6 h-6 inline-block mr-2"></td>';

                                                        echo '<td class="px-6 py-4"><span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded light:bg-gray-700 light:text-white ' . $marcaBadge . '">' . $marca . '</span></td>';
                                                        echo '<td class="px-6 py-4">' . $fila['modelo'] . '</td>';
                                                        echo '<td class="px-6 py-4">' . $fila['procesador'] . '</td>';
                                                        echo '<td class="px-6 py-4"> <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded light:bg-gray-700 light:text-white ' . $oficinaBadge . '">' . $oficina . '</span></td>';
                                                        echo '<td class="px-6 py-4"><span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded light:bg-gray-700 light:text-white ' . $departamentoBadge . '">' . $departamento . '</span></td>';

                                                        // Dentro del loop que recorre las filas de la base de datos
                                                        $costo = $fila['costoEquipoActual']; // Obtén el valor del costo

                                                        // Asignamos un color dependiendo del valor de $costo
                                                        if ($costo == 0) {
                                                            $colorClase = 'text-red-600'; // Rojo si el costo es 0
                                                        } else {
                                                            $colorClase = 'text-green-600 font-bold'; // Verde si el costo es mayor que 0
                                                        }
 if ($canViewMoney) {
                                                        echo '<td class="px-6 py-4 ' . $colorClase . '">' . $costo . '</td>';
 } else {
                                                            echo "<span class='text-gray-500'></span>";
                                                        }

                                                        echo '<td class="px-6 py-4">' . $fila['asignado_a'] . '</td>';
                                                        echo '<td class="px-6 py-4"><div class="inline-flex rounded-md shadow-sm" role="group">
                                                                        <button data-drawer-target="drawer-right-example" data-drawer-show="drawer-right-example" data-drawer-placement="right" aria-controls="drawer-right-example" data-row-id="' . $fila['Id_computadora'] . '" type="button" class="border border-gray-200 border border-gray-200inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 light:bg-gray-700 light:border-gray-600 light:text-white light:hover:text-white light:hover:bg-gray-600 light:focus:ring-blue-500 light:focus:text-white">
                                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 me-2">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                            </svg>
                                                                        </button>';

                                                        // Aquí es donde se evalúa la condición para el segundo botón
                                                        if ($canEdit) {
                                                            echo '<button data-id-com="' . $fila['Id_computadora'] . '" data-user-id="' . $_SESSION['user_id'] . '" data-modal-target="crud-edit" data-modal-toggle="crud-edit" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 light:bg-gray-700 light:border-gray-600 light:text-white light:hover:text-white light:hover:bg-gray-600 light:focus:ring-blue-500 light:focus:text-white">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 me-2">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                                                                </svg>
                                                                            </button>';
                                                        } else {
                                                            echo "<span class='text-gray-500'></span>";
                                                        }

                                                        echo '</div></td>';

                                                        echo '</tr>';
                                                    }
                                                } else {
                                                    echo "No se encontraron registros.";
                                                }
                                            } catch (PDOException $e) {
                                                die('Error de conexión: ' . $e->getMessage());
                                            }

                                            // $conexion = null;  // Cerrar la conexión al finalizar
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
                    Editar equipo
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-toggle="crud-edit">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Cerrar ventana</span>
                </button>
            </div>
            <?php include 'computadora/formEdit.php'; ?>
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
            const previewContainer = document.getElementById('preview-compu');

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
                console.log(rowId)

                $.ajax({
                    url: 'computadora/recupera_caracteristicas.php',
                    type: 'GET',
                    data: {
                        id: rowId
                    },
                    success: function(data) {
                        var computadoraInfo = JSON.parse(data);

                        $('.infocaracteristicas').html(`
                        
                        <div>
                         <img class="h-auto max-w-full rounded-lg" src="sistemas/${computadoraInfo.foto}" alt="">
                        </div>
                        <p class="mb-6 mt-4 text-sm text-gray-500 dark:text-gray-400">
                            Caracteristicas
                        </p>

                        
                        <!-- Agrega más líneas según las propiedades que tengas -->

                        <div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Asignado a:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.asignado_a}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">ID Departamento:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.nombre_departamento}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">ID Oficina:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.nombre_oficina}</p>
</div>
 <?php if ($canViewPw) { ?>
                                                
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Correo Asociado:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.correo_asociado}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Contraseña Gmail 1:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.contrasenaGmail1}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Contraseña Outlook 1:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.contrasenaOutlook1}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Correo Asociado 2:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.correoAsociado2}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Contraseña Gmail 2:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.contrasenaGmail2}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Contraseña Outlook 2:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.contrasenaOutlook2}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Correo Asociado 3:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.correoAsociado3}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Contraseña Windows:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.contrasenaWindow}</p>
</div>

<?php     } else {
        echo "<span class='text-red-500'>*Tu usuario no tiene acceso a ver las contrasenas*</span>";
    }
?>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Tipo:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.tipo}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Modelo:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.modelo}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Marca:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.marca}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Tipo de Disco:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.tipoDeDisco}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Procesador:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.procesador}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">RAM:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.ram}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Condición:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.condicion}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Costo Equipo Actual:</span>
     <?php if ($canViewPw) { ?>

    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.costoEquipoActual}</p>
    <?php     } else {
        echo "<span class='text-red-500'>*Sin acceso*</span>";
    }
?>
</div>

<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Fecha de Asignación:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.fechaDeAsignacion}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Año de Procesador:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.anoDeProcesador}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Fecha de Lanzamiento:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.fechaDeLanzamiento}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Estatus:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.status}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Posible Fecha Para Venta:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.posibleFechaParaVenta}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Nueva Compra:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.nuevaCompra}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Foto:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.foto}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">PC Anterior:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.pcAnterior}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Posible Asignación:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.posibleAsignacion}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Total:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.total}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Costo al Comprar:</span>
         <?php if ($canViewPw) { ?>

    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.costoAlComprar}</p>
        <?php     } else {
        echo "<span class='text-red-500'>*Sin acceso*</span>";
    }
?>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Costo a la Venta:</span>
         <?php if ($canViewPw) { ?>

    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.costoALaVenta}</p>
        <?php     } else {
        echo "<span class='text-red-500'>*Sin acceso*</span>";
    }
?>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Disponibilidad:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.disponibilidad}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Propietario/Destino:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.propietario_Destino}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Foto 2:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.foto2}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Fecha de Reasignación:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.fechaDeReasignacion}</p>
</div>

                    `);
                    },
                    error: function() {
                        console.error('Error al obtener información de la base de datos.');
                    }
                });
            });
        });

        function formatearMoneda(monto, idioma = 'es-ES', moneda = 'MXN') {
            return new Intl.NumberFormat(idioma, {
                style: 'currency',
                currency: moneda
            }).format(monto);
        }



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
                url: "computadora/import_masivo.php",
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
        $(document).ready(function() {

            $('#crud-modal button[type="submit"]').on('click', function(e) {
                e.preventDefault();

                var selectedDepartamentolId = $('#departamento').val();
                var selectedOficinalId = $('#oficina').val();


                var image = document.getElementById('foto');
                var image_data = image.files[0];

                var image2 = document.getElementById('foto2');
                var image_data2 = image2.files[0];

                var formData = new FormData();

                /*nueva */
                formData.append('asignado_a', $('#asignado_a').val());
                formData.append('departamento', selectedDepartamentolId);
                formData.append('oficina', selectedOficinalId);
                formData.append('correo_asociado', $('#correo_asociado').val());
                formData.append('contrasenaGmail1', $('#contrasenaGmail1').val());
                formData.append('contrasenaOutlook1', $('#contrasenaOutlook1').val());
                formData.append('correoAsociado2', $('#correoAsociado2').val());
                formData.append('contrasenaGmail2', $('#contrasenaGmail2').val());
                formData.append('contrasenaOutlook2', $('#contrasenaOutlook2').val());
                formData.append('correoAsociado3', $('#correoAsociado3').val());
                formData.append('contrasenaWindow', $('#contrasenaWindow').val());
                formData.append('tipo', $('#tipo').val());
                formData.append('modelo', $('#modelo').val());
                formData.append('marca', $('#marca').val());
                formData.append('tipoDeDisco', $('#tipoDeDisco').val());
                formData.append('procesador', $('#procesador').val());
                formData.append('ram', $('#ram').val());
                formData.append('condicion', $('#condicion').val());
                formData.append('costoEquipoActual', $('#costoEquipoActual').val());
                formData.append('fechaDeAsignacion', $('#fechaDeAsignacion').val());
                formData.append('anoDeProcesador', $('#anoDeProcesador').val());
                formData.append('fechaDeLanzamiento', $('#fechaDeLanzamiento').val());
                formData.append('status', $('#status').val());
                formData.append('posibleFechaParaVenta', $('#posibleFechaParaVenta').val());
                formData.append('nuevaCompra', $('#nuevaCompra').val());
                formData.append('foto', image_data);
                formData.append('pcAnterior', $('#pcAnterior').val());
                formData.append('posibleAsignacion', $('#posibleAsignacion').val());
                formData.append('total', $('#total').val());
                formData.append('costoAlComprar', $('#costoAlComprar').val());
                formData.append('costoALaVenta', $('#costoALaVenta').val());
                formData.append('disponibilidad', $('#disponibilidad').val());
                formData.append('propietario_Destino', $('#propietario_Destino').val());
                formData.append('foto2', image_data2);
                formData.append('fechaDeReasignacion', $('#fechaDeReasignacion').val());
                console.log(formData);

                $.ajax({
                    type: 'POST',
                    url: 'computadora/addComputadora.php',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (data.success) {
                            alert("Registro exitoso")
                        } else {
                            alert('Error al registrar el computadora');
                        }
                    },
                    error: function(error) {
                        console.error('Error en la solicitud AJAX:', error);
                    }
                });
            });
        });





        $(document).ready(function() {
            $('#elimina_compu').on('click', function(e) {
                e.preventDefault(); // Evitar el comportamiento por defecto del botón

                // Verificar si la tabla tiene filas antes de proceder
                if ($('#tbl_pc tbody tr').length === 0) {
                    Swal.fire({
                        title: 'Tabla vacía',
                        text: 'No hay registros para eliminar.',
                        icon: 'info',
                        confirmButtonText: 'Aceptar',
                        background: '#2c2f38',
                        color: '#fff',
                        confirmButtonColor: '#3085d6'
                    });
                    return; // Detener la ejecución si la tabla está vacía
                }

                // Confirmación antes de eliminar
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡Esta acción eliminará todos los registros de Computadoras!",
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
                        // Hacer la solicitud AJAX para eliminar todos los registros
                        $.ajax({
                            type: 'POST',
                            url: 'computadora/DeleteTable.php', // Ruta al archivo PHP que elimina los registros
                            data: {
                                action: 'eliminar_todos'
                            }, // Enviar un parámetro de acción
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
                                    $('#tbl_pc tbody').html('');
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: data.message, // Mensaje de error
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
        $(document).ready(function() {
            $('#departamento').on('change', function() {
                var departamento = $('#departamento option:selected').text(); // Capturar el texto del departamento

                if (departamento !== "Selecciona Departamento") { // Evitar enviar el texto por defecto
                    $.ajax({
                        url: '../includes/get_empleados.php', // Archivo PHP para procesar la consulta
                        method: 'POST',
                        data: {
                            departamento: departamento
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                var empleados = response.data;
                                $('#asignado_a').empty(); // Limpiar opciones anteriores

                                // Agregar una opción predeterminada
                                $('#asignado_a').append(new Option('Selecciona un empleado', ''));

                                // Iterar sobre los resultados y agregar opciones
                                empleados.forEach(function(empleado) {
                                    $('#asignado_a').append(new Option(empleado.nombre, empleado.nombre)); // Nombre como texto, ID como value
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'No se pudo obtener la lista de empleados.',
                                icon: 'error',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    });
                }
            });
        });


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
            var idCom = $(this).data('id-com'); // ID de la computadora seleccionada

            rellenarModal(idCom);
        });

        // Función para rellenar los datos del modal
        function rellenarModal(idCom) {
            fetch('computadora/edit_equipo.php?Id_computadora=' + idCom)
                .then(response => response.json())
                .then(data => {
                    let valor = data.costoEquipoActual;
                    valor = valor.replace(/[^0-9.]/g, '');

                    // Rellena los campos del modal
                    $('#id_equipo').val(data.Id_computadora); // Campo oculto
                    $('#asignado_a_edit').val(data.asignado_a); // Aquí se asigna el valor del empleado

                    $('#departamento_edit').val(data.Id_departamento).trigger('change');
                    $('#oficina_edit').val(data.Id_oficina);
                    $('#correo_asociado_edit').val(data.correo_asociado);
                    $('#contrasenaGmail1_edit').val(data.contrasenaGmail1);
                    $('#contrasenaOutlook1_edit').val(data.contrasenaOutlook1);
                    $('#correoAsociado2_edit').val(data.correoAsociado2);
                    $('#contrasenaGmail2_edit').val(data.contrasenaGmail2);
                    $('#contrasenaOutlook2_edit').val(data.contrasenaOutlook2);
                    $('#correoAsociado3_edit').val(data.correoAsociado3);
                    $('#contrasenaWindow_edit').val(data.contrasenaWindow);
                    $('#tipo_edit').val(data.tipo);
                    $('#modelo_edit').val(data.modelo);
                    $('#marca_edit').val(data.marca);
                    $('#tipoDeDisco_edit').val(data.tipoDeDisco);
                    $('#procesador_edit').val(data.procesador);
                    $('#ram_edit').val(data.ram);
                    $('#condicion_edit').val(data.condicion);
                    $('#costoEquipoActual_edit').val(valor);
                    $('#fechaDeAsignacion_edit').val(data.fechaDeAsignacion);
                    $('#anoDeProcesador_edit').val(data.anoDeProcesador);
                    $('#fechaDeLanzamiento_edit').val(data.fechaDeLanzamiento);
                    $('#status_edit').val(data.status);
                    $('#posibleFechaParaVenta_edit').val(data.posibleFechaParaVenta);
                    $('#nuevaCompra_edit').val(data.nuevaCompra);
                    $('#pcAnterior_edit').val(data.pcAnterior);
                    $('#posibleAsignacion_edit').val(data.posibleAsignacion);
                    $('#total_edit').val(data.total);
                    $('#costoAlComprar_edit').val(data.costoAlComprar);
                    $('#costoALaVenta_edit').val(data.costoALaVenta);
                    $('#disponibilidad_edit').val(data.disponibilidad);
                    $('#propietario_Destino_edit').val(data.propietario_Destino);
                    $('#fechaDeReasignacion_edit').val(data.fechaDeReasignacion);

                    // Limpiar bordes rojos previos
                    $('input, select, textarea').removeClass('border-red-500');

                    if (data.comment) {
                        // Eliminar todo después del '|' y solo mantener los campos con errores antes de él
                        const camposConErrores = data.comment.split('|')[0].split(','); // Se toma solo la parte antes del '|'

                        // Recorrer cada campo con error
                        camposConErrores.forEach((campo) => {
                            campo = campo.trim(); // Eliminar espacios extra

                            // Si el campo tiene el prefijo "Id_", lo eliminamos
                            if (campo.startsWith('Id_')) {
                                campo = campo.replace('Id_', ''); // Eliminamos el prefijo "Id_"
                            }

                            // Crear el ID del campo con el sufijo '_edit'
                            const inputField = $(`#${campo}_edit`);

                            if (inputField.length > 0) {
                                // Aplicar el borde rojo a los campos con errores
                                inputField.addClass('border-red-500');
                            }
                        });
                    }




                    // Cambia el texto del botón y la acción
                    $('#agregaEquipo')
                        .text('Guardar Equipo')
                        .data('action', 'editEquipo'); // Actualiza la acción
                })
                .catch(error => console.error('Error al cargar los datos del equipo:', error));
        }
    </script>

    <script>
        document.getElementById('actualizarEquipo').addEventListener('click', function() {
            // Capturando los valores del formulario
            const idEquipo = document.getElementById('id_equipo').value;
            const asignadoA = document.getElementById('asignado_a_edit').value;
            const departamento = document.getElementById('departamento_edit').value;
            const oficina = document.getElementById('oficina_edit').value;
            const correoAsociado = document.getElementById('correo_asociado_edit').value;
            const contrasenaGmail1 = document.getElementById('contrasenaGmail1_edit').value;
            const contrasenaOutlook1 = document.getElementById('contrasenaOutlook1_edit').value;
            const correoAsociado2 = document.getElementById('correoAsociado2_edit').value;
            const contrasenaGmail2 = document.getElementById('contrasenaGmail2_edit').value;
            const contrasenaOutlook2 = document.getElementById('contrasenaOutlook2_edit').value;
            const correoAsociado3 = document.getElementById('correoAsociado3_edit').value;
            const contrasenaWindow = document.getElementById('contrasenaWindow_edit').value;
            const tipo = document.getElementById('tipo_edit').value;
            const modelo = document.getElementById('modelo_edit').value;
            const marca = document.getElementById('marca_edit').value;
            const tipoDeDisco = document.getElementById('tipoDeDisco_edit').value;
            const procesador = document.getElementById('procesador_edit').value;
            const ram = document.getElementById('ram_edit').value;
            const condicion = document.getElementById('condicion_edit').value;
            const costoEquipoActual = document.getElementById('costoEquipoActual_edit').value;
            const fechaDeAsignacion = document.getElementById('fechaDeAsignacion_edit').value;
            const anoDeProcesador = document.getElementById('anoDeProcesador_edit').value;
            const fechaDeLanzamiento = document.getElementById('fechaDeLanzamiento_edit').value;
            const status = document.getElementById('status_edit').value;
            const posibleFechaParaVenta = document.getElementById('posibleFechaParaVenta_edit').value;
            const nuevaCompra = document.getElementById('nuevaCompra_edit').value;
            const pcAnterior = document.getElementById('pcAnterior_edit').value;
            const posibleAsignacion = document.getElementById('posibleAsignacion_edit').value;
            const total = document.getElementById('total_edit').value;
            const costoAlComprar = document.getElementById('costoAlComprar_edit').value;
            const costoALaVenta = document.getElementById('costoALaVenta_edit').value;
            const disponibilidad = document.getElementById('disponibilidad_edit').value;
            const propietarioDestino = document.getElementById('propietario_Destino_edit').value;
            const fechaDeReasignacion = document.getElementById('fechaDeReasignacion_edit').value;

            // Validar campos requeridos (opcional)
            if (!idEquipo || !departamento || !asignadoA) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campos incompletos',
                    text: 'Por favor completa todos los campos obligatorios.'
                });
                return;
            }

            // Crear un objeto con los datos
            const formData = new FormData();
            formData.append('id_equipo', idEquipo);
            formData.append('departamento', departamento);
            formData.append('oficina', oficina);
            formData.append('asignado_a', asignadoA);
            formData.append('tipo', tipo);
            formData.append('marca', marca);
            formData.append('modelo', modelo);
            formData.append('condicion', condicion);
            formData.append('costoEquipoActual', costoEquipoActual);
            formData.append('fechaDeAsignacion', fechaDeAsignacion);
            formData.append('anoDeProcesador', anoDeProcesador);
            formData.append('fechaDeLanzamiento', fechaDeLanzamiento);
            formData.append('status', status);
            formData.append('correo_asociado', correoAsociado);
            formData.append('contrasenaGmail1', contrasenaGmail1);
            formData.append('contrasenaOutlook1', contrasenaOutlook1);
            formData.append('correoAsociado2', correoAsociado2);
            formData.append('contrasenaGmail2', contrasenaGmail2);
            formData.append('contrasenaOutlook2', contrasenaOutlook2);
            formData.append('correoAsociado3', correoAsociado3);
            formData.append('contrasenaWindow', contrasenaWindow);
            formData.append('tipoDeDisco', tipoDeDisco);
            formData.append('procesador', procesador);
            formData.append('ram', ram);
            formData.append('posibleFechaParaVenta', posibleFechaParaVenta);
            formData.append('nuevaCompra', nuevaCompra);
            formData.append('pcAnterior', pcAnterior);
            formData.append('posibleAsignacion', posibleAsignacion);
            formData.append('total', total);
            formData.append('costoAlComprar', costoAlComprar);
            formData.append('costoALaVenta', costoALaVenta);
            formData.append('disponibilidad', disponibilidad);
            formData.append('propietario_Destino', propietarioDestino);
            formData.append('fechaDeReasignacion', fechaDeReasignacion);

            // Enviar datos mediante fetch
            fetch('computadora/update_equipo.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Actualización exitosa',
                            text: 'Equipo actualizado correctamente.'
                        }).then(() => {
                            // Opcional: recargar la página o limpiar el formulario
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
            const selectAll = document.getElementById('select-all'); // Checkbox maestro
            const rowCheckboxes = document.querySelectorAll('.row-checkbox'); // Checkboxes individuales
            const markReviewedButton = document.getElementById('mark-reviewed-button'); // Botón

            // Manejo del checkbox maestro
            selectAll.addEventListener('change', () => {
                rowCheckboxes.forEach(checkbox => {
                    if (!checkbox.disabled) { // Ignorar los deshabilitados
                        checkbox.checked = selectAll.checked;
                    }
                });
            });

            // Manejo de checkboxes individuales
            rowCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    selectAll.checked = rowCheckboxes.length === document.querySelectorAll('.row-checkbox:checked:not(:disabled)').length;
                });
            });

            // Acción del botón "Marcar como Revisados"
            markReviewedButton.addEventListener('click', () => {
                const selectedIds = [];
                rowCheckboxes.forEach(checkbox => {
                    if (checkbox.checked && !checkbox.disabled) { // Ignorar los revisados
                        selectedIds.push(checkbox.getAttribute('data-id'));
                    }
                });

                if (selectedIds.length > 0) {
                    // Mostrar una alerta de confirmación personalizada
                    Swal.fire({
                        title: '¿Encontraste errores en el registro?',
                        text: `Marcarás ${selectedIds.length} registros como revisados.`,
                        icon: 'question',
                        showCancelButton: true,
                        showDenyButton: true, // Botón adicional para pedir comentarios
                        confirmButtonColor: '#4b5563', // Gris neutro oscuro
                        cancelButtonColor: '#d1d5db', // Gris claro
                        denyButtonColor: '#9ca3af', // Gris medio
                        confirmButtonText: 'No, marcar revisión',
                        denyButtonText: 'Sí, añadir comentario',
                        cancelButtonText: 'Cancelar',
                        
                        
                        didOpen: () => {
                                            const confirmBtn = Swal.getConfirmButton();
                                    
                                            // Deshabilitar
                                            confirmBtn.disabled = true;
                                    
                                            // Darle estilo tipo badge
                                            confirmBtn.style.backgroundColor = "#e5e7eb"; // gris claro
                                            confirmBtn.style.color = "#6b7280"; // texto gris oscuro
                                            confirmBtn.style.cursor = "not-allowed";
                                            confirmBtn.style.border = "1px solid #d1d5db";
                                            confirmBtn.style.pointerEvents = "none"; // evita hover
                                            confirmBtn.textContent = "🔒 Deshabilitado"; // badge visual
                                            window.confirmBtnSwal = confirmBtn;
                                        }
    
    
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Acción para marcar como revisado directamente
                            fetch('computadora/marcar_revisados.php', {
                                    method: 'POST',
                                    body: JSON.stringify({
                                        ids: selectedIds
                                    }),
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Swal.fire({
                                            title: '¡Éxito!',
                                            text: 'Los registros han sido marcados como revisados.',
                                            icon: 'success',
                                            confirmButtonColor: '#4b5563', // Gris neutro oscuro
                                            confirmButtonText: 'Aceptar',
                                        });
                                        // Actualizar la interfaz para reflejar cambios
                                        selectedIds.forEach(id => {
                                            const checkbox = document.querySelector(`.row-checkbox[data-id="${id}"]`);
                                            if (checkbox) {
                                                checkbox.disabled = true; // Deshabilitar el checkbox
                                                checkbox.checked = true; // Mantenerlo marcado
                                            }
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'Error',
                                            text: 'No se pudo completar la operación. Inténtalo de nuevo.',
                                            icon: 'error',
                                            confirmButtonColor: '#4b5563', // Gris neutro oscuro
                                            confirmButtonText: 'Aceptar',
                                        });
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    Swal.fire({
                                        title: 'Error',
                                        text: 'Ocurrió un error al procesar la solicitud.',
                                        icon: 'error',
                                        confirmButtonColor: '#4b5563', // Gris neutro oscuro
                                        confirmButtonText: 'Aceptar',
                                    });
                                });
                        } else if (result.isDenied) {
                            // Acción para añadir un comentario si hay errores
                            Swal.fire({
                                title: 'Selecciona los campos con errores',
                                html: `
                            <div style="text-align: left; max-height: 300px; overflow-y: auto;">
                                <label><input type="checkbox" class="error-checkbox" value="asignado_a"> asignado_a</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="Id_departamento"> Id_departamento</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="Id_oficina"> Id_oficina</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="correo_asociado"> correo_asociado</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="contrasenaGmail1"> contrasenaGmail1</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="contrasenaOutlook1"> contrasenaOutlook1</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="correoAsociado2"> correoAsociado2</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="contrasenaGmail2"> contrasenaGmail2</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="contrasenaOutlook2"> contrasenaOutlook2</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="correoAsociado3"> correoAsociado3</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="contrasenaWindow"> contrasenaWindow</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="tipo"> tipo</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="modelo"> modelo</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="marca"> marca</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="tipoDeDisco"> tipoDeDisco</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="procesador"> procesador</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="ram"> ram</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="condicion"> condicion</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="costoEquipoActual"> costoEquipoActual</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="fechaDeAsignacion"> fechaDeAsignacion</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="anoDeProcesador"> anoDeProcesador</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="fechaDeLanzamiento"> fechaDeLanzamiento</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="status"> status</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="posibleFechaParaVenta"> posibleFechaParaVenta</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="nuevaCompra"> nuevaCompra</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="foto"> foto</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="pcAnterior"> pcAnterior</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="posibleAsignacion"> posibleAsignacion</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="total"> total</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="costoAlComprar"> costoAlComprar</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="costoALaVenta"> costoALaVenta</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="disponibilidad"> disponibilidad</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="propietario_Destino"> propietario_Destino</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="foto2"> foto2</label><br>
                                <label><input type="checkbox" class="error-checkbox" value="fechaDeReasignacion"> fechaDeReasignacion</label><br>
                                <div style="text-align: left;">
                                    <label for="extra-comment" style="font-weight: bold; display: block; margin-bottom: 8px;">Comentario extra:</label>
                                    <textarea id="extra-comment" class="swal2-textarea" placeholder="Escribe aquí tu comentario..." style="width: 90%; height: 100px; resize: none; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 14px;" require></textarea>
                                </div>
                            </div>
                        `,
                                showCancelButton: true,
                                confirmButtonColor: '#4b5563', // Gris neutro oscuro
                                cancelButtonColor: '#d1d5db', // Gris claro
                                confirmButtonText: 'Enviar',
                                cancelButtonText: 'Cancelar',
                                preConfirm: () => {
                                                    const popup = Swal.getPopup();
                                                    const selectedErrors = [];
                                                    popup.querySelectorAll('.error-checkbox:checked').forEach(cb => selectedErrors.push(cb.value));
                                                    const extraComment = popup.querySelector('#extra-comment').value.trim();
                                            
                                                    if (selectedErrors.length === 0) {
                                                        Swal.showValidationMessage('Debes seleccionar al menos un campo con errores');
                                                        return false; // Evita cerrar el modal
                                                    }
                                            
                                                    if (!extraComment) {
                                                        Swal.showValidationMessage('Debes escribir un comentario extra antes de continuar');
                                                        return false; // Evita cerrar el modal
                                                    }
                                            
                                                    return { selectedErrors, extraComment }; // Devuelve los datos para luego usar en .then()
                                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
        const { selectedErrors, extraComment } = result.value;
        const comment = selectedErrors.join(', ') + ` | Comentario adicional: ${extraComment}`;

        // Aquí tu fetch para enviar al servidor
        fetch('computadora/marcar_revisados.php', {
            method: 'POST',
            body: JSON.stringify({
                ids: selectedIds,
                comment: comment,
                status: 2
            }),
            headers: { 'Content-Type': 'application/json' },
        })
        .then(resp => resp.json())
        .then(data => {
            if (data.success) {
                Swal.fire('¡Éxito!', 'Los errores han sido registrados correctamente.', 'success');
            } else {
                Swal.fire('Error', 'No se pudo completar la operación. Inténtalo de nuevo.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Error', 'Ocurrió un error al procesar la solicitud.', 'error');
        });
    }
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Sin selección',
                        text: 'Debes seleccionar al menos un registro para marcarlo como revisado.',
                        icon: 'info',
                        confirmButtonColor: '#4b5563', // Gris neutro oscuro
                        confirmButtonText: 'Aceptar',
                    });
                }
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
    
    
    
</body>

</html>