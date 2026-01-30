<?php
include "../includes/conexionbd.php";
session_start();
// echo $_SESSION["user_id"];
?>
<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" x-data="data()" lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Computadoras | Sistema</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/tailwind.output.css" />
    <!-- <link rel="stylesheet" href="../assets/css/tailwind.css" /> -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script src="../assets/js/init-alpine.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <!-- Asegúrate de cargar jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Luego, carga Toastr -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.dataTables.min.css">

<!-- JavaScript DataTables -->
<script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>

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

        /* Aplica Tailwind a la caja de búsqueda de DataTables */
.dataTables_filter input {
    padding: 0.5rem 1rem; /* Tailwind equivalent: px-4 py-2 */
    border-radius: 0.5rem; /* Tailwind equivalent: rounded-lg */
    border: 1px solid #d1d5db; /* Tailwind equivalent: border-gray-300 */
    background-color: #1f2937; /* Tailwind equivalent: dark:bg-gray-800 */
    color: white; /* Tailwind equivalent: dark:text-white */
    font-size: 1rem; /* Tailwind equivalent: text-sm */
    outline: none; /* Remove outline for cleaner focus effect */
    transition: border-color 0.3s, box-shadow 0.3s; /* Add smooth transition */
}

.dataTables_filter input:focus {
    border-color: #3b82f6; /* Tailwind equivalent: focus:ring-2 focus:ring-blue-500 */
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5); /* Tailwind focus effect */
}

/* Cambiar el texto del placeholder */
.dataTables_filter input::placeholder {
    color: #9ca3af; /* Tailwind equivalent: text-gray-400 */
    font-style: italic;
}


    </style>

</head>

<body>
    <div class="flex bg-gray-50 dark:bg-gray-900" :class="{ 'overflow-hidden': isSideMenuOpen }">
        <!-- Desktop sidebar -->
        <?php
        include "../includes/menu.php";
        ?>
        <div class="h-screen w-full">
            <?php
            include "../includes/Navbar.php";
            ?>
            <!-- <main class="h-screen flex justify-center items-center bg-gray-100 dark:bg-gray-900"> -->
            <main class=" justify-center items-center dark:bg-gray-900">
                <div class="container px-2 sm:px-2 mx-auto">
                    <div class="flex h-auto flex-col gap-6 mt-8 mb-8 md:flex-row">
                        <!-- Primer div que abarca dos columnas -->
                        <div class="w-full md:w-1/3 p-4 bg-white rounded-xl shadow-xs dark:bg-gray-800 h-auto md:h-64">
                            <div class="min-w-0 p-4 bg-white rounded-xl dark:bg-gray-800">
                                <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
                                    Mis Computadoras </h4>
                                <div class="">


                                    <div class=" sm:rounded-lg">
                                        <div class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-800">
                                            <div>

                                                <!-- Modal toggle -->
                                                <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="inline-flex text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                                    </svg>
                                                    Registrar Computadora
                                                </button>

                                                <button onclick="testAlert()" data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="border-l border-gray-200 inline-flex text-gray-500 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium ml-4 text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                                <!-- <button class="border-l border-gray-200 inline-flex text-gray-500 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-gray-700 dark:focus:ring-blue-800" type="button" id="openModalButton">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                                    </svg>
                                                </button> -->

                                                <button data-modal-target="progress-modal" data-modal-toggle="progress-modal" class="hover:bg-gray-100 border-l border-gray-200 inline-flex text-gray-500  focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                                    </svg>
                                                </button>
                                                <button id="mark-reviewed-button" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">
                                                    Marcar como Revisados</button>


                                                <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                                    <div class="relative p-4 w-full max-w-md max-h-full">
                                                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                                            <div class="p-4 md:p-5 text-center">
                                                                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                </svg>
                                                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Está seguro que desea vaciar la tabla?</h3>
                                                                <button id="elimina_compu" data-modal-hide="popup-modal" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                                                                    Si, seguro
                                                                </button>
                                                                <button data-modal-hide="popup-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No, cancelar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Modal de filtrado -->
                                                <div id="filterModal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                                    <div class="relative p-4 w-full max-h-full">
                                                        <!-- Modal content -->
                                                        <div class="relative bg-white rounded-lg shadow-lg max-w-xl mx-auto dark:bg-gray-700">
                                                            <!-- Modal header -->
                                                            <div class="flex items-start justify-between p-4 border-b dark:border-gray-600">
                                                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                                                                    Filtrar Datos
                                                                </h3>
                                                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" id="closeModalButton">
                                                                    <svg class="w-5 h-5" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
                                                                        <path fill-rule="evenodd" d="M6 6L14 14M6 14L14 6" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <!-- Modal body -->
                                                            <div class="p-6 space-y-6">
                                                                <form id="filterForm">
                                                                    <!-- Filtro de Fecha -->
                                                                    <div class="mb-4">
                                                                        <label for="filterDate" class="block text-sm font-medium text-gray-900 dark:text-white">Fecha</label>
                                                                        <input type="date" id="filterDate" class="block w-full p-2 mt-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                                    </div>

                                                                    <!-- Filtro de Estado -->
                                                                    <div class="mb-4">
                                                                        <label for="filterStatus" class="block text-sm font-medium text-gray-900 dark:text-white">Estado</label>
                                                                        <select id="filterStatus" class="block w-full p-2 mt-2 text-sm text-gray-900 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                                            <option value="">Selecciona Estado</option>
                                                                            <option value="Activo">Activo</option>
                                                                            <option value="Inactivo">Inactivo</option>
                                                                            <option value="Pendiente">Pendiente</option>
                                                                        </select>
                                                                    </div>
                                                                </form>
                                                            </div>

                                                            <!-- Modal footer -->
                                                            <div class="flex items-center justify-end p-4 space-x-4 border-t dark:border-gray-600">
                                                                <button type="button" class="text-gray-500 bg-transparent hover:bg-gray-100 hover:text-gray-900 rounded-lg text-sm px-5 py-2.5 focus:outline-none dark:hover:bg-gray-600 dark:hover:text-white" id="closeModalButton2">
                                                                    Cancelar
                                                                </button>
                                                                <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5" id="applyFiltersButton">
                                                                    Aplicar Filtros
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Main modal -->
                                                <div id="progress-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                                    <div class="relative p-4 w-full max-w-md max-h-full">
                                                        <!-- Modal content -->
                                                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">

                                                            <div class="p-4 md:p-5">
                                                                <svg class="w-10 h-10 text-gray-400 dark:text-gray-500 mb-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                                                                    <path d="M8 5.625c4.418 0 8-1.063 8-2.375S12.418.875 8 .875 0 1.938 0 3.25s3.582 2.375 8 2.375Zm0 13.5c4.963 0 8-1.538 8-2.375v-4.019c-.052.029-.112.054-.165.082a8.08 8.08 0 0 1-.745.353c-.193.081-.394.158-.6.231l-.189.067c-2.04.628-4.165.936-6.3.911a20.601 20.601 0 0 1-6.3-.911l-.189-.067a10.719 10.719 0 0 1-.852-.34 8.08 8.08 0 0 1-.493-.244c-.053-.028-.113-.053-.165-.082v4.019C0 17.587 3.037 19.125 8 19.125Zm7.09-12.709c-.193.081-.394.158-.6.231l-.189.067a20.6 20.6 0 0 1-6.3.911 20.6 20.6 0 0 1-6.3-.911l-.189-.067a10.719 10.719 0 0 1-.852-.34 8.08 8.08 0 0 1-.493-.244C.112 6.035.052 6.01 0 5.981V10c0 .837 3.037 2.375 8 2.375s8-1.538 8-2.375V5.981c-.052.029-.112.054-.165.082a8.08 8.08 0 0 1-.745.353Z" />
                                                                </svg>
                                                                <h3 class="mb-1 text-xl font-bold text-gray-900 dark:text-white">Carga massiva | Tabla Computadoras</h3>
                                                                <p class="text-gray-500 dark:text-gray-400 mb-6">¿Está seguro cargar un archivo a la base de datos?
                                                                <p>
                                                                <form id="cargamasivapc" class="p-2" method="post">
                                                                    <div class="flex justify-between mb-1 text-gray-500 dark:text-gray-400">
                                                                        <div class="flex items-center justify-center w-full">
                                                                            <label for="dropzone-file" id="file-compu" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                                                                <div id="preview-compu" class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                                                                    </svg>
                                                                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click para cargar archivo</span></p>
                                                                                    <p class="text-xs text-gray-500 dark:text-gray-400">Soporta CSV o TXT</p>
                                                                                </div>


                                                                                <input id="dropzone-file" type="file" name="subecomputadoras" class="hidden" accept=".csv,.txt" />
                                                                            </label>
                                                                        </div>
                                                                    </div>


                                                                    <!-- Modal footer -->
                                                                    <div class="flex items-center mt-6 space-x-2 rtl:space-x-reverse">
                                                                        <button data-modal-hide="progress-modal" type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Subir</button>
                                                                        <button data-modal-hide="progress-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Cancelar</button>
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
                                                        <div class="relative bg-white rounded-lg shadow-lg max-w-2xl mx-auto dark:bg-gray-700"> <!-- Cambié max-w-lg a max-w-xl -->
                                                            <!-- Modal header -->
                                                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                                                <h3 id="registroCompu" class="text-lg font-semibold text-gray-900 dark:text-white">
                                                                    Nuevo registro de computadora
                                                                </h3>
                                                                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-modal">
                                                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                                    </svg>
                                                                    <span class="sr-only">Cerrar ventana</span>
                                                                </button>
                                                            </div>
                                                            <!-- Modal body -->
                                                            <form method="#" class="p-4 md:p-5">


                                                                <div class="mb-4 border-b border-gray-200 dark:border-gray-700">
                                                                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="default-styled-tab" data-tabs-toggle="#default-styled-tab-content" data-tabs-active-classes="text-purple-600 hover:text-purple-600 dark:text-purple-500 dark:hover:text-purple-500 border-purple-600 dark:border-purple-500" data-tabs-inactive-classes="dark:border-transparent text-gray-500 hover:text-gray-600 dark:text-gray-400 border-gray-100 hover:border-gray-300 dark:border-gray-700 dark:hover:text-gray-300" role="tablist">
                                                                        <li class="me-2" role="presentation">
                                                                            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="dashboard-styled-tab" data-tabs-target="#styled-dashboard" type="button" role="tab" aria-controls="dashboard" aria-selected="false">Ubicacion</button>
                                                                        </li>
                                                                        <li class="me-2" role="presentation">
                                                                            <button class="inline-block p-4 border-b-2 rounded-t-lg" id="profile-styled-tab" data-tabs-target="#styled-profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Datos de la computadora</button>
                                                                        </li>
                                                                        <li class="me-2" role="presentation">
                                                                            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="settings-styled-tab" data-tabs-target="#styled-settings" type="button" role="tab" aria-controls="settings" aria-selected="false">Cuentas</button>
                                                                        </li>
                                                                        <li role="presentation">
                                                                            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="contacts-styled-tab" data-tabs-target="#styled-contacts" type="button" role="tab" aria-controls="contacts" aria-selected="false">Componentes</button>
                                                                        </li>
                                                                        <li role="presentation">
                                                                            <button class="inline-block p-4 border-b-2 rounded-t-lg hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300" id="contacts-styled-tab" data-tabs-target="#styled-seg" type="button" role="tab" aria-controls="contacts" aria-selected="false">Seguimiento</button>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div id="default-styled-tab-content">
                                                                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-profile" role="tabpanel" aria-labelledby="profile-tab">
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400"></p>
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
                                                                                <label for="asignado_a" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Asignado A <span class="text-red-500">*</span></label>
                                                                                <!-- <input type="text" name="asignado_a" id="asignado_a" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="" placeholder="JUAN BAUTISTA"> -->
                                                                                <select id="asignado_a" name="asignado_a" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                                                    <option class="dark:text-white" selected="">Selecciona Empleado</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="tipo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo <span class="text-red-500">*</span></label>
                                                                                <select
                                                                                    name="tipo"
                                                                                    id="tipo"
                                                                                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
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
                                                                                <label for="marca" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                                                                    Marca <span class="text-red-500">*</span>
                                                                                </label>
                                                                                <select name="marca" id="marca" class="block p-2 text-sm text-gray-900 border border-gray-300 rounded-lg w-full bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                                                    <option value="" selected disabled>Selecciona una marca</option>
                                                                                    <?php foreach ($enumValues as $value): ?>

                                                                                        <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($value) ?></option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                            </div>

                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="modelo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Modelo <span class="text-red-500">*</span></label>
                                                                                <input type="text" name="modelo" id="modelo" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
                                                                            </div>

                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="condicion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Condición <span class="text-red-500">*</span></label>
                                                                                <select
                                                                                    name="condicion"
                                                                                    id="condicion"
                                                                                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
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
                                                                                <label for="costoEquipoActual" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Costo Equipo Actual <span class="text-red-500">*</span></label>
                                                                                <input type="number" name="costoEquipoActual" id="costoEquipoActual" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="fechaDeAsignacion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha De Asignación <span class="text-red-500">*</span></label>
                                                                                <input type="date" name="fechaDeAsignacion" id="fechaDeAsignacion" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="anoDeProcesador" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Año De Procesador <span class="text-red-500">*</span></label>
                                                                                <input type="number" name="anoDeProcesador" id="anoDeProcesador" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="fechaDeLanzamiento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha De Lanzamiento <span class="text-red-500">*</span></label>
                                                                                <input type="date" name="fechaDeLanzamiento" id="fechaDeLanzamiento" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status <span class="text-red-500">*</span></label>
                                                                                <select
                                                                                    name="status"
                                                                                    id="status"
                                                                                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
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

                                                                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-dashboard" role="tabpanel" aria-labelledby="dashboard-tab">
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400">Ubicacion</p>
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
                                                                                <label for="departamento" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Departamento <span class="text-red-500">*</span></label>
                                                                                <select id="departamento" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                                                    <option class="dark:text-white" selected="">Selecciona Departamento</option>
                                                                                    <?php foreach ($departamentos as $departamento) : ?>
                                                                                        <option class="dark:text-white" value="<?= $departamento['Id_departamento'] ?>"><?= htmlspecialchars($departamento['nombre']) ?></option>
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
                                                                                <label for="oficina" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Oficina <span class="text-red-500">*</span></label>
                                                                                <select id="oficina" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                                                    <option class="dark:text-white" selected="">Selecciona Oficina</option>
                                                                                    <?php foreach ($oficinas as $oficina) : ?>
                                                                                        <option class="dark:text-white" value="<?= $oficina['Id_Oficina'] ?>"><?= htmlspecialchars($oficina['nombre']) ?></option>
                                                                                    <?php endforeach; ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-settings" role="tabpanel" aria-labelledby="settings-tab">
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400">Cuentas</p>
                                                                        <div class="grid gap-5 mb-4 grid-cols-2">
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="correo_asociado" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo Asociado <span class="text-red-500">*</span></label>
                                                                                <input type="text" name="correo_asociado" id="correo_asociado" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 " required placeholder="desarrollo@example.org">
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="contrasenaGmail1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contraseña Gmail <span class="text-red-500">*</span></label>
                                                                                <input type="text" name="contrasenaGmail1" id="contrasenaGmail1" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="********">
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="contrasenaOutlook1" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contraseña Outlook <span class="text-red-500">*</span></label>
                                                                                <input type="text" name="contrasenaOutlook1" id="contrasenaOutlook1" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="********">
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="correoAsociado2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo Asociado 2</label>
                                                                                <input type="text" name="correoAsociado2" id="correoAsociado2" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="desarrollo@example.org">
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="contrasenaGmail2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contraseña Gmail 2</label>
                                                                                <input type="text" name="contrasenaGmail2" id="contrasenaGmail2" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="********">
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="contrasenaOutlook2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contraseña Outlook 2</label>
                                                                                <input type="text" name="contrasenaOutlook2" id="contrasenaOutlook2" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="********">
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="correoAsociado3" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Correo Asociado 3</label>
                                                                                <input type="text" name="correoAsociado3" id="correoAsociado3" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="desarrollo@example.org">
                                                                            </div>
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="contrasenaWindow" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contraseña Windows <span class="text-red-500">*</span></label>
                                                                                <input type="text" name="contrasenaWindow" id="contrasenaWindow" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required placeholder="********">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-contacts" role="tabpanel" aria-labelledby="contacts-tab">
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400">Componentes</p>
                                                                        <div class="grid gap-5 mb-4 grid-cols-2">
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="tipoDeDisco" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo de disco <span class="text-red-500">*</span></label>
                                                                                <select
                                                                                    name="tipoDeDisco"
                                                                                    id="tipoDeDisco"
                                                                                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
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
                                                                                <label for="procesador" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Procesador <span class="text-red-500">*</span></label>
                                                                                <input type="text" name="procesador" id="procesador" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                                            </div>

                                                                            <div class="col-span-2 sm:col-span-1 relative">
                                                                                <label for="ram" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ram <span class="text-red-500">*</span></label>
                                                                                <div class="flex items-center">
                                                                                    <input
                                                                                        type="number"
                                                                                        name="ram"
                                                                                        id="ram"
                                                                                        class="block p-2 pe-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                                                                        placeholder="Ejemplo: 4"
                                                                                        min="1"
                                                                                        required>
                                                                                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">GB</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="hidden p-4 rounded-lg bg-gray-50 dark:bg-gray-800" id="styled-seg" role="tabpanel" aria-labelledby="contacts-tab">
                                                                        <p class="text-sm text-gray-500 dark:text-gray-400">Datos para seguimiento</p>
                                                                        <div class="grid gap-5 mb-4 grid-cols-2">
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="posibleFechaParaVenta" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Posible Fecha Para Venta <span class="text-red-500">*</span></label>
                                                                                <input type="date" name="posibleFechaParaVenta" id="posibleFechaParaVenta" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                                            </div>

                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="nuevaCompra" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nueva Compra <span class="text-red-500">*</span></label>
                                                                                <input type="text" name="nuevaCompra" id="nuevaCompra" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                                            </div>

                                                                            <!-- Foto php -->
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="foto" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Foto <span class="text-red-500">*</span></label>
                                                                                <input type="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" name="foto" id="foto" accept="image/*" required>
                                                                            </div>

                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="pcAnterior" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">PC Anterior <span class="text-red-500">*</span></label>
                                                                                <input type="text" name="pcAnterior" id="pcAnterior" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                                            </div>

                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="posibleAsignacion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Posible Asignación <span class="text-red-500">*</span></label>
                                                                                <input type="text" name="posibleAsignacion" id="posibleAsignacion" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                                            </div>

                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="total" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Total <span class="text-red-500">*</span></label>
                                                                                <input type="text" name="total" id="total" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="$2,050.98" required>
                                                                            </div>

                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="costoAlComprar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Costo Al Comprar <span class="text-red-500">*</span></label>
                                                                                <input type="number" name="costoAlComprar" id="costoAlComprar" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="$1,999" required>
                                                                            </div>

                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="costoALaVenta" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Costo A La Venta <span class="text-red-500">*</span></label>
                                                                                <input type="number" name="costoALaVenta" id="costoALaVenta" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="$2,000" required>
                                                                            </div>

                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="disponibilidad" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Disponibilidad <span class="text-red-500">*</span></label>
                                                                                <select name="disponibilidad" id="disponibilidad" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
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
                                                                                <label for="propietario_Destino" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Propietario Destino <span class="text-red-500">*</span></label>
                                                                                <input type="text" name="propietario_Destino" id="propietario_Destino" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                                            </div>

                                                                            <!-- Foto2 php -->
                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="foto2" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Foto <span class="text-red-500">*</span></label>
                                                                                <input type="file" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" name="foto2" id="foto2" accept="image/*" required>
                                                                            </div>

                                                                            <div class="col-span-2 sm:col-span-1">
                                                                                <label for="fechaDeReasignacion" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha Reasignación <span class="text-red-500">*</span></label>
                                                                                <input type="date" name="fechaDeReasignacion" id="fechaDeReasignacion" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                                                            </div>
                                                                        </div>

                                                                        <button type="submit" id="agregaEquipo" data-action='editEquipo' class="text-white inline-flex items-center w-full mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
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
                                            <!-- <button id="mark-reviewed-button" class="text-blue-700 hover:text-white border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-blue-500 dark:text-blue-500 dark:hover:text-white dark:hover:bg-blue-500 dark:focus:ring-blue-800">Marcar como Revisados</button> -->

                                            <label for="table-search" class="sr-only">Buscar</label>
                                            <div class="relative">
                                                <input type="text" id="table-search-users" class="w-full  p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar celular">
                                            </div>
                                        </div>
                                        <div id="tbl_pc" style="overflow-x: auto;" class="h-auto">
                                            
                                        <table id="user-table" class="w-full text-sm text-left text-gray-500 dark:text-gray-400 mb-4">
                                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                    <tr>
                                                    <th scope="col" class="px-6 py-3">
                                                        
                                                        <input id="select-all" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                    </th>

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
                                                        <th scope="col" class="px-6 py-3">
                                                            Costo
                                                        </th>
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


    // Mapeo de marcas a colores (lo mantengo igual que antes)
    $marca_colores = [
        'ACER' => 'bg-green-100 text-green-800 border-green-400',
        'ACTECK' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'ASUS' => 'bg-blue-100 text-blue-800 border-blue-400',
        'CUSTOM' => 'bg-indigo-100 text-indigo-800 border-indigo-400',
        'DELL' => 'bg-red-100 text-red-800 border-red-400',
        'HP' => 'bg-purple-100 text-purple-800 border-purple-400',
        'LENOVO' => 'bg-pink-100 text-pink-800 border-pink-400',
        'MAC' => 'bg-gray-100 text-gray-800 border-gray-500',
        'TOSHIBA' => 'bg-indigo-100 text-indigo-800 border-indigo-400',
        'SIN MARCA' => 'bg-gray-100 text-gray-800 border-gray-500',
    ];

    // Mapeo de oficinas a colores
    $oficina_colores = [
        'OFICINA' => 'bg-teal-100 text-teal-800 border-teal-400',
        'CUBIK DER' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'CUBIK IZQ' => 'bg-blue-100 text-blue-800 border-blue-400',
        'TEC' => 'bg-gray-100 text-gray-800 border-gray-500',
        '102' => 'bg-pink-100 text-pink-800 border-pink-400',
        'KABZO' => 'bg-indigo-100 text-indigo-800 border-indigo-400',
        'CAPITEL 1004' => 'bg-green-100 text-green-800 border-green-400',
        'CAPITEL 905' => 'bg-orange-100 text-orange-800 border-orange-400',
        '806' => 'bg-blue-100 text-blue-800 border-blue-400',
        '807' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'HERRADURA' => 'bg-purple-100 text-purple-800 border-purple-400',
        'GUADALAJARA' => 'bg-indigo-100 text-indigo-800 border-indigo-400',
        'PLAYA' => 'bg-pink-100 text-pink-800 border-pink-400',
        'MONITOREO' => 'bg-teal-100 text-teal-800 border-teal-400',
        'CUBIK DERECHO' => 'bg-red-100 text-red-800 border-red-400',
        'HOME OFFICE' => 'bg-gray-100 text-gray-800 border-gray-500',
        'N/A' => 'bg-gray-100 text-gray-800 border-gray-500',
    ];

    // Mapeo de departamentos a colores
    $departamento_colores = [
        'SEGURIDAD' => 'bg-red-100 text-red-800 border-red-400',
        'TESORERIA GUADALAJARA' => 'bg-teal-100 text-teal-800 border-teal-400',
        'PERSONAL DOMESTICO' => 'bg-green-100 text-green-800 border-green-400',
        'CEO' => 'bg-blue-100 text-blue-800 border-blue-400',
        'DIRECTOR' => 'bg-indigo-100 text-indigo-800 border-indigo-400',
        'COBRANZA' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'CONTABILIDAD' => 'bg-purple-100 text-purple-800 border-purple-400',
        'FACTURACION' => 'bg-pink-100 text-pink-800 border-pink-400',
        'BANCOS' => 'bg-gray-100 text-gray-800 border-gray-500',
        'DOMICILIOS' => 'bg-indigo-100 text-indigo-800 border-indigo-400',
        'IMSS' => 'bg-orange-100 text-orange-800 border-orange-400',
        'RECURSOS HUMANOS' => 'bg-blue-100 text-blue-800 border-blue-400',
        'JURIDICO' => 'bg-green-100 text-green-800 border-green-400',
        'LOGISTICA' => 'bg-red-100 text-red-800 border-red-400',
        'MANTENIMIENTO' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'SISTEMAS' => 'bg-purple-100 text-purple-800 border-purple-400',
        'OPERACIONES' => 'bg-teal-100 text-teal-800 border-teal-400',
        'VERIFICACIÓN' => 'bg-indigo-100 text-indigo-800 border-indigo-400',
        'PRESUPUESTOS' => 'bg-pink-100 text-pink-800 border-pink-400',
        'TESORERIA' => 'bg-gray-100 text-gray-800 border-gray-500',
        'ENTREGAS' => 'bg-red-100 text-red-800 border-red-400',
        'TESORERIA PLAYA' => 'bg-teal-100 text-teal-800 border-teal-400',
        'AGENDA CORPORATIVA' => 'bg-blue-100 text-blue-800 border-blue-400',
        'PRODUCCIONES' => 'bg-purple-100 text-purple-800 border-purple-400',
        'PROYECTOS' => 'bg-pink-100 text-pink-800 border-pink-400',
    ];


    $query = "SELECT computadora.*, oficina.nombre as nombre_ofi, departamentos.nombre as nombre_depa
FROM computadora 
JOIN oficina ON computadora.Id_Oficina = oficina.Id_Oficina
JOIN departamentos ON computadora.Id_departamento = departamentos.Id_departamento
ORDER BY Id_computadora ASC";

    $resultado = $conexion->query($query);

    if ($resultado->rowCount() > 0) {
        foreach ($resultado as $fila) {
            $marca = $fila['marca'];
            $oficina = $fila['nombre_ofi'];
            $departamento = $fila['nombre_depa'];

            // Obtener el color del badge basado en la marca
            $marcaBadge = isset($marca_colores[$marca]) ? $marca_colores[$marca] : 'bg-gray-100 text-gray-800 border-gray-500';

            // Obtener el color del badge basado en la oficina
            $oficinaBadge = isset($oficina_colores[$oficina]) ? $oficina_colores[$oficina] : 'bg-gray-100 text-gray-800 border-gray-500';

            // Obtener el color del badge basado en el departamento
            $departamentoBadge = isset($departamento_colores[$departamento]) ? $departamento_colores[$departamento] : 'bg-gray-100 text-gray-800 border-gray-500';

            echo '<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">';
            echo '<td class="px-6 py-4">
                <div class="flex items-center me-4">
                    <input data-popover-target="popover-'.$fila['Id_computadora'].'"  data-popover-placement="right"  type="checkbox" 
                        class="row-checkbox w-4 h-4 ' . 
                        ($fila['revisado'] == 1 ? 'text-blue-600 focus:ring-blue-500' : ($fila['revisado'] == 2 ? 'text-orange-600 focus:ring-orange-500' : 'text-gray-600 focus:ring-gray-500')) . ' 
                        bg-gray-100 border-gray-300 rounded dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600" 
                        data-id="' . $fila['Id_computadora'] . '" 
                        data-comment="' . htmlspecialchars($fila['comment']) . '" 
                        ' . ($fila['revisado'] == 1 || $fila['revisado'] == 2 ? 'checked disabled' : '') . '>
                    <label for="checkbox-'.$fila['Id_computadora'].'" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300"></label>
                </div>
                <div data-popover id="popover-'.$fila['Id_computadora'].'" role="tooltip" class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-white border border-gray-200 rounded-lg shadow-sm opacity-0 dark:text-gray-400 dark:border-gray-600 dark:bg-gray-800 left-full ml-2">
                    <div class="px-3 py-2 bg-gray-100 border-b border-gray-200 rounded-t-lg dark:border-gray-600 dark:bg-gray-700">
                        <h3 class="font-semibold text-gray-900 dark:text-white">Comentario</h3>
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

                            echo '<p class="text-gray-700 dark:text-gray-300">' . $formattedCampos . $formattedComentario . '</p>';
                        } else if ($fila['revisado'] == 1){
                            echo '<p class="text-green-700 dark:text-gray-300"><strong>Verificado</strong></p>';
                        }
                        else{
                            echo '<p class="text-red-700 dark:text-gray-300"><strong>No Verificado</strong></p>';
                        }
                        echo '                                                                     
                        </div>
                    <div data-popper-arrow></div>
                </div>
            </td>';





            echo '<td class="px-6 py-4"><img src="https://logo.clearbit.com/'. $marca .'.com" alt="' . $marca . ' logo" class="w-6 h-6 inline-block mr-2"></td>';


            echo '<td class="px-6 py-4"><span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-white ' . $marcaBadge . '">' . $marca . '</span></td>';
            echo '<td class="px-6 py-4">' . $fila['modelo'] . '</td>';
            echo '<td class="px-6 py-4">' . $fila['procesador'] . '</td>';
            echo '<td class="px-6 py-4"> <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-white ' . $oficinaBadge . '">' . $oficina . '</span></td>';
            echo '<td class="px-6 py-4"><span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-white ' . $departamentoBadge . '">' . $departamento . '</span></td>';
            
            // Dentro del loop que recorre las filas de la base de datos
            $costo = $fila['costoEquipoActual']; // Obtén el valor del costo
            
            // Asignamos un color dependiendo del valor de $costo
            if ($costo == 0) {
                $colorClase = 'text-red-600'; // Rojo si el costo es 0
            } else {
                $colorClase = 'text-green-600 font-bold'; // Verde si el costo es mayor que 0
            }
            
            echo '<td class="px-6 py-4 ' . $colorClase . '">$' . $costo . '</td>';
            
                                                                            echo '<td class="px-6 py-4">' . $fila['asignado_a'] . '</td>';
            echo '<td class="px-6 py-4"><div class="inline-flex rounded-md shadow-sm" role="group">
           
                    <button data-drawer-target="drawer-right-example" data-drawer-show="drawer-right-example" data-drawer-placement="right" aria-controls="drawer-right-example" data-row-id="' . $fila['Id_computadora'] . '" type="button" class="border border-gray-200 border border-gray-200inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border-t border-b border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 me-2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    
                    <button data-id-com="' . $fila['Id_computadora'] . '" data-user-id="' . $_SESSION['user_id'] . '" data-modal-target="crud-edit" data-modal-toggle="crud-edit" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-e-lg hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-2 focus:ring-blue-700 focus:text-blue-700 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-blue-500 dark:focus:text-white" >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 me-2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>

</div>
</td>';
            echo '</tr>';
        }
    } else {
        echo "No se encontraron registros.";
    }

// $conexion = null;  // Cerrar la conexión al finalizar
?>

</tbody>
</table>


                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </main>
        </div>
    </div>

    <!-- Contenedor donde se cargará el modal -->
    <div id="crud-edit" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-h-full">
            <!-- Modal content -->
            <div class="relative bg-white rounded-lg shadow-lg max-w-2xl mx-auto dark:bg-gray-700"> <!-- Cambié max-w-lg a max-w-xl -->
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 id="registroCompu" class="text-lg font-semibold text-gray-900 dark:text-white">
                        Editar equipo
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-edit">
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

    <div id="drawer-right-example" class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-80 dark:bg-gray-800" tabindex="-1" aria-labelledby="drawer-right-label">
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" crossorigin="anonymous"></script>

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
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.Id_departamento}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">ID Oficina:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.Id_oficina}</p>
</div>
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
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.costoEquipoActual}</p>
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
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.costoAlComprar}</p>
</div>
<div class="mb-2">
    <span class="font-medium text-gray-600 dark:text-gray-400">Costo a la Venta:</span>
    <p class="font-medium text-gray-600 dark:text-gray-300">${computadoraInfo.costoALaVenta}</p>
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

        function testAlert() {
            toastr.info('hablame del plan fit me')
            toastr.info('y porque no lo aplicas en ti perro')

            toastr["success"]("alo, adrian herrero cou", "Godoy")
        }

        $("#cargamasivapc").submit(function(e) {
            e.preventDefault();
            var parametros = new FormData($(this)[0])
            console.log(parametros);
            $.ajax({
                type: "POST",
                url: "computadora/import_masivo.php",
                cache: false,
                data: parametros,
                contentType: false,
                processData: false,
                success: function(data) {
                    toastr.success('Datos agregados correctamente, actualice la pagina');
                    //location.reload()
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.error("Error en la solicitud AJAX:", textStatus, errorThrown);
                }
            })
        })
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

                                    // Opcional: Actualizar la tabla o eliminar los elementos de la interfaz
                                    $('#tbl_pc').html(''); // Limpia la tabla de mobiliarios en el frontend
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
                    $('#costoEquipoActual_edit').val(data.costoEquipoActual);
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
  document.getElementById('actualizarEquipo').addEventListener('click', function () {
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
    if (!idEquipo || !departamento || !asignadoA || !tipo || !marca) {
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
            }).then((result) => {
                if (result.isConfirmed) {
                    // Acción para marcar como revisado directamente
                    fetch('computadora/marcar_revisados.php', {
                        method: 'POST',
                        body: JSON.stringify({ ids: selectedIds }),
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
                                    <textarea id="extra-comment" class="swal2-textarea" placeholder="Escribe aquí tu comentario..." style="width: 90%; height: 100px; resize: none; border: 1px solid #d1d5db; border-radius: 4px; padding: 8px; font-size: 14px;"></textarea>
                                </div>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonColor: '#4b5563', // Gris neutro oscuro
                        cancelButtonColor: '#d1d5db', // Gris claro
                        confirmButtonText: 'Enviar',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
    if (result.isConfirmed) {
        // Recopilar los campos seleccionados
        const selectedErrors = [];
        document.querySelectorAll('.error-checkbox:checked').forEach((checkbox) => {
            selectedErrors.push(checkbox.value);
        });


        const extraComment = document.getElementById('extra-comment').value.trim();


        if (selectedErrors.length > 0) {
            const comment = selectedErrors.join(', ') + (extraComment ? ` | Comentario adicional: ${extraComment}` : '');

            // Enviar al servidor
            fetch('computadora/marcar_revisados.php', {
                method: 'POST',
                body: JSON.stringify({
                    ids: selectedIds, // IDs seleccionados previamente
                    comment: comment,
                    status: 2, // Enviar 2 si hay errores
                }),
                headers: {
                    'Content-Type': 'application/json',
                },
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Los errores han sido registrados correctamente.',
                            icon: 'success',
                            confirmButtonColor: '#4b5563',
                            confirmButtonText: 'Aceptar',
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'No se pudo completar la operación. Inténtalo de nuevo.',
                            icon: 'error',
                            confirmButtonColor: '#4b5563',
                            confirmButtonText: 'Aceptar',
                        });
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Ocurrió un error al procesar la solicitud.',
                        icon: 'error',
                        confirmButtonColor: '#4b5563',
                        confirmButtonText: 'Aceptar',
                    });
                });
        } else {
            Swal.fire({
                title: 'Sin selección',
                text: 'Debes seleccionar al menos un campo con errores para continuar.',
                icon: 'info',
                confirmButtonColor: '#4b5563',
                confirmButtonText: 'Aceptar',
            });
        }
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
<!-- CSS DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- JavaScript DataTables -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<!-- CSS DataTables Buttons -->
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.3.1/css/buttons.dataTables.min.css">

<!-- JS DataTables Buttons y JS Zip para exportar Excel o CSV -->
<script src="https://cdn.datatables.net/buttons/2.3.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.1/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function () {
    $('#user-table').DataTable({
        paging: true,             // Activar paginación
        searching: true,          // Activar búsqueda global
        ordering: true,           // Habilitar ordenamiento por columnas
        lengthChange: true,       // Cambiar cantidad de registros por página
        pageLength: 10,           // Registros por página por defecto
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-MX.json" // Traducción al español
        },
        dom: 'Bfrtip',            // Esto indica que se agregarán botones de descarga
        buttons: [
            // 'copy',               // Copiar al portapapeles
            'csv',                // Descargar en CSV
            'excel',              // Descargar en Excel
            'pdf',                // Descargar en PDF
            // 'print'               // Imprimir la tabla
        ],
    });
});

</script>


</body>

</html>