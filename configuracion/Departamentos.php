<?php
include "../includes/conexionbd.php";
session_start();
//echo $_SESSION["user_id"];
?>
<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" x-data="data()" lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Departamento | Sistema</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/tailwind.output.css" />
    <!-- <link rel="stylesheet" href="../assets/css/tailwind.css" /> -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script src="../assets/js/init-alpine.js"></script>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css" /> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" defer></script>
    <script src="../assets/js/charts-lines.js" defer></script>
    <script src="../assets/js/charts-pie.js" defer></script>
    <!-- <link rel="stylesheet" href="../assets/css/style.css"> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <!-- Asegúrate de cargar jQuery primero -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Luego, carga Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

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
            <main class=" justify-center items-center bg-gray-100 dark:bg-gray-900">
                <div class="container px-6 sm:px-2 mx-auto">
                    <div class="flex h-auto flex-col gap-6 mt-8 mb-8 md:flex-row">
                        <!-- Primer div que abarca dos columnas -->
                        <div class="w-full md:w-1/3 p-4 bg-white rounded-xl shadow-xs dark:bg-gray-800 h-auto md:h-64">
                            <div class="min-w-0 p-4 bg-white rounded-xl dark:bg-gray-800">
                                <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
                                    Departamentos </h4>
                                <div class="">


                                    <div class=" sm:rounded-lg">
                                        <div class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-800">
                                            <div>

                                                <!-- Modal toggle -->
                                                <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="inline-flex text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                                    </svg>
                                                    Registrar Departamento
                                                </button>

                                                <button onclick="testAlert()" data-modal-target="popup-modal" data-modal-toggle="popup-modal" class="border-l border-gray-800 inline-flex text-white hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium ml-4 text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                               


                                                <div id="popup-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                                    <div class="relative p-4 w-full max-w-md max-h-full">
                                                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                                            <div class="p-4 md:p-5 text-center">
                                                                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                                                </svg>
                                                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Está seguro que desea vaciar la tabla?</h3>
                                                                <button data-modal-hide="popup-modal" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                                                                    Si, seguro
                                                                </button>
                                                                <button data-modal-hide="popup-modal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No, cancelar</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                                <!-- Main modal -->
                                                <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                                    <div class="relative p-4 w-full max-w-md max-h-full">
                                                        <!-- Modal content -->
                                                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                                            <!-- Modal header -->
                                                            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                                    Nuevo registro de Departamento
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
                                                                <div class="grid gap-6 mb-6 md:p-4">
                                                                    <div class="mb-6">
                                                                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                                                                        <input type="text" name="nombre" id="nombre" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" required="">
                                                                    </div>
                                                                </div>
                                                                <button type="submit" class="text-white inline-flex items-center w-full mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                                                    <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                                                                    </svg>
                                                                    Agregar Departamento
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                            <label for="table-search" class="sr-only">Buscar</label>
                                            <div class="relative">
                                                <input type="text" id="table-search-users" class="w-full  p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar celular">
                                            </div>
                                        </div>
                                        <div style="overflow-x: auto;" class="h-auto">
                                            <table id="user-table" class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400 mb-4">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                    <tr>
                                                        <th scope="col" class="px-6 py-3">
                                                            Nombre
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                    <?php

                                                    try {
                                                        $page = isset($_GET['page']) ? $_GET['page'] : 1;
                                                        $recordsPerPage = 8;
                                                        $offset = ($page - 1) * $recordsPerPage;

                                                        $query = "SELECT * from departamentos
                                                        LIMIT $recordsPerPage OFFSET $offset";

                                                        $resultado = $conexion->query($query);

                                                        if ($resultado->rowCount() > 0) {
                                                            foreach ($resultado as $fila) {
                                                                echo '<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">';
                                                                echo '<td class="px-6 py-4">' . $fila['nombre'] . '</td>';
                                                                
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
                                            <?php
                                            include '../includes/conexionbd.php';

                                            $totalRecordsQuery = "SELECT COUNT(*) as total FROM departamentos";
                                            $totalRecordsResult = $conexion->query($totalRecordsQuery);
                                            $totalRecords = $totalRecordsResult->fetch(PDO::FETCH_ASSOC)['total'];

                                            $totalPages = ceil($totalRecords / $recordsPerPage);

                                         echo '<nav aria-label="flex justify-end mt-4 pt-4">';
                                            echo ' <ul class="inline-flex -space-x-px text-sm">';

                                            if ($page > 1) {
                                                echo '<li>';
                                                echo '<a href="?page=' . ($page - 1) . '" class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Previous</a>';
                                                echo '</li>';
                                            }

                                            $minPage = max(1, $page - 2);
                                            $maxPage = min($totalPages, $page + 2);

                                            for ($i = $minPage; $i <= $maxPage; $i++) {
                                                echo '<li>';
                                                echo '<a href="?page=' . $i . '" class="flex items-center ' . ($i == $page ? 'bg-gray-500 text-white' : 'text-blue-500') . ' justify-center px-3 h-8 leading-tight border border-gray-300 hover:bg-gray-100 hover:text-gray-700  dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">' . $i . '</a>';
                                                echo '</li>';
                                            }

                                            if ($page < $totalPages) {
                                                echo '<li>';
                                                echo '<a href="?page=' . ($page + 1) . '" class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Next</a>';
                                                echo '</li>';
                                            }

                                            echo '</ul>';
                                            echo '</nav>';
                                            ?>

                                        </div>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </main>
        </div>
    </div>
   


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function obtenerValorDelCampo(nombreCampo) {
            return document.getElementById(nombreCampo).value;
        }

        $(document).ready(function() {
            $("#table-search-users").on("input", function() {
                var searchText = $(this).val().toLowerCase();
        
                $("#user-table tbody tr").filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                });
            });
        });
    </script>

    <script src="departamento/scriptDepartamento.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" crossorigin="anonymous"></script>
</body>
</html>
