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
    <title>Mi buzon | Sistema</title>
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
                            <div class="min-w-0 p-4 bg-white rounded-xl shadow-xs dark:bg-gray-800">
                                <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
                                    Mi Búzon </h4>
                                <div class="">


                                    <div class=" sm:rounded-lg">
                                        <div class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-800">
                                            <div>
                                                <button id="dropdownActionButton" data-dropdown-toggle="dropdownAction" class="inline-flex items-center text-gray-500 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-3 py-1.5 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700" type="button">
                                                    <span class="sr-only">Action button</span>
                                                    Action
                                                    <svg class="w-2.5 h-2.5 ms-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                                    </svg>
                                                </button>
                                                <!-- Dropdown menu -->
                                                <div id="dropdownAction" class="z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700 dark:divide-gray-600">
                                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownActionButton">
                                                        <li>
                                                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Exportar</a>
                                                        </li>
                                                        <li>
                                                            <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Limpiar bandeja</a>
                                                        </li>

                                                    </ul>
                                                    <div class="py-1">
                                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Delete User</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <label for="table-search" class="sr-only">Buscar</label>
                                            <div class="relative">

                                                <input type="text" id="table-search-users" class="w-full block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar solicitud">
                                            </div>
                                        </div>
                                        <div style="overflow-x: auto;">
                                            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                    <tr>

                                                        <th scope="col" class="px-6 py-3">
                                                            Remitente
                                                        </th>
                                                        <!-- <th scope="col" class="px-6 py-3">
                                                            Mensaje
                                                        </th> -->
                                                        <th scope="col" class="px-6 py-3">
                                                            Motivo solicitud
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Tipo solicitud
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Tipo de producto
                                                        </th>
                                                        <!-- <th scope="col" class="px-6 py-3">
                                                            Estatu
                                                        </th> -->
                                                        <th scope="col" class="px-6 py-3">
                                                            Fecha solicitud
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Fecha autorización
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Estatu
                                                        </th>
                                                        <th scope="col" class="px-6 py-3">
                                                            Action
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>





                                                    <?php
                                                    $user_id = $_SESSION['user_id'];
                                                    $solicitudesEncontradas = false;
                                                    try {
                                                        $sqlTablaDinamica = "SELECT DISTINCT tabla FROM solicitudbaja";
                                                        $stmtTablaDinamica = $conexion->prepare($sqlTablaDinamica);
                                                        $stmtTablaDinamica->execute();
                                                        while ($rowTablaDinamica = $stmtTablaDinamica->fetch(PDO::FETCH_ASSOC)) {
                                                            $tablaDinamica = $rowTablaDinamica['tabla'];
                                                            $sqlColumnaDinamica = "SHOW COLUMNS FROM $tablaDinamica";
                                                            $stmtColumnaDinamica = $conexion->prepare($sqlColumnaDinamica);
                                                            $stmtColumnaDinamica->execute();
                                                            $rowColumnaDinamica = $stmtColumnaDinamica->fetch(PDO::FETCH_ASSOC);
                                                            $nombreColumnaDinamica = $rowColumnaDinamica['Field'];
                                                            $sql = "SELECT
                                                                            solicitudbaja.*,
                                                                            usuarios.nombre AS nombre_usuario,
                                                                            usuarios.Id_departamento,
                                                                            usuarios.email AS correo_usuario,
                                                                            roles.subname AS n_rol,
                                                                            $tablaDinamica.modelo as telname
                                                                        FROM
                                                                            solicitudbaja
                                                                        JOIN
                                                                            usuarios 
                                                                            -- usuarios ON solicitudbaja.Id_usuario = usuarios.Id_Usuario
                                                                        JOIN
                                                                            roles ON solicitudbaja.id_rol_soli = roles.id_rol
                                                                        LEFT JOIN
                                                                            $tablaDinamica ON solicitudbaja.Id_inventario = $tablaDinamica.$nombreColumnaDinamica
                                                                        WHERE
                                                                            solicitudbaja.id_rol_soli = usuarios.rolActual
                                                                            AND usuarios.Id_Usuario = :user_id";


// $sql = "SELECT
//                                                                             solicitudbaja.*,
//                                                                             usuarios_registro.nombre AS nombre_usuario,
//                                                                             usuarios_registro.Id_departamento,
//                                                                             usuarios_registro.email AS correo_usuario,
//                                                                             roles.subname AS n_rol,
//                                                                             $tablaDinamica.modelo as telname
//                                                                         FROM
//                                                                             solicitudbaja
//                                                                         JOIN
//                                                                             usuarios usuarios_registro ON solicitudbaja.Id_usuario = usuarios_registro.Id_Usuario
//                                                                             -- usuarios ON solicitudbaja.Id_usuario = usuarios.Id_Usuario
//                                                                         JOIN
//                                                                             roles ON solicitudbaja.id_rol_soli = roles.id_rol
//                                                                         JOIN
//                                                                             $tablaDinamica ON solicitudbaja.Id_inventario = $tablaDinamica.$nombreColumnaDinamica
//                                                                         ";


                                                            $stmt = $conexion->prepare($sql);
                                                            $stmt->bindParam(':user_id', $user_id);
                                                            $stmt->execute();
                                                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                if ($row['telname'] != '') {
                                                                    echo '<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">';
                                                                    echo '<th scope="row" class="flex items-center font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                    <div class="ps-3">
                                                                        <div class="text-base font-semibold"> ' . $row['nombre_usuario'] . ' </div>
                                                                        <div class="font-normal text-gray-500">' . $row['correo_usuario'] . '</div>
                                                                    </div>
                                                                    </th>';
                                                                    echo '<td class="px-6 py-4">' . $row['motivoBaja'] . ' </td>';
                                                                    echo '<td class="px-6 py-4">' . $row['n_rol'] . ' </td>';
                                                                    echo '<td class="px-6 py-4">' . $row['telname'] . ' </td>';
                                                                    echo '<td class="px-6 py-4">' . $row['fechaSolicitud'] . ' </td>';
                                                                    echo '<td class="px-6 py-4">' . $row['fechaAutorizacion'] . ' </td>';
                                                                    echo '<td class="px-6 py-4">' . $row['estatu'] . ' </td>';
                                                                    echo '<td class="px-6 py-4"> 
                                                                <button type="button" data-drawer-target="drawer-right-example" data-drawer-show="drawer-right-example" data-drawer-placement="right" aria-controls="drawer-right-example" type="button" class="focus:outline-none text-white bg-yellow-400 hover:bg-yellow-500 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-2 py-2 mb-2 dark:focus:ring-yellow-900">Revisar</button>
                                                                </td>';
                                                                    echo '</tr>';
                                                                    $solicitudesEncontradas = true;
                                                                }
                                                            }
                                                        }
                                                        if (!$solicitudesEncontradas) {
                                                            echo '<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">';
                                                            echo '<td class="px-6 py-4">No hay solicitudes actualmente </td>';
                                                            echo '</tr>';
                                                        }
                                                    } catch (PDOException $e) {
                                                        echo "Error al ejecutar la consulta: " . $e->getMessage();
                                                    }

                                                    ?>




                                                </tbody>
                                            </table>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>


    <!-- drawer component -->
    <div id="drawer-right-example" class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white w-80 dark:bg-gray-800" tabindex="-1" aria-labelledby="drawer-right-label">
        <h5 id="drawer-right-label" class="mr-4 inline-flex items-center mb-4 text-base font-semibold text-gray-500 dark:text-gray-400"><svg class="w-4 h-4 me-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
            </svg>Rastrear</h5>
        <button type="button" data-drawer-hide="drawer-right-example" aria-controls="drawer-right-example" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 end-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white">
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
            </svg>
            <span class="sr-only">Close menu</span>
        </button>






        <ol class="relative border-s border-gray-200 dark:border-gray-700">
            <li class="mb-10 ms-6">
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <img class="rounded-full shadow-lg" src="https://i.pinimg.com/originals/ae/ec/c2/aeecc22a67dac7987a80ac0724658493.jpg" alt="Bonnie image" />
                </span>
                <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:bg-gray-700 dark:border-gray-600">
                    <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">just now</time>
                    <div class="text-sm font-normal text-gray-500 dark:text-gray-300">Bonnie moved <a href="#" class="font-semibold text-blue-600 dark:text-blue-500 hover:underline">Jese Leos</a> to <span class="bg-gray-100 text-gray-800 text-xs font-normal me-2 px-2.5 py-0.5 rounded dark:bg-gray-600 dark:text-gray-300">Funny Group</span></div>
                </div>
            </li>
            <li class="mb-10 ms-6">
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <img class="rounded-full shadow-lg" src="https://i.pinimg.com/originals/ae/ec/c2/aeecc22a67dac7987a80ac0724658493.jpg" alt="Thomas Lean image" />
                </span>
                <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600">
                    <div class="items-center justify-between mb-3 sm:flex">
                        <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">2 hours ago</time>
                        <div class="text-sm font-normal text-gray-500 lex dark:text-gray-300">Thomas Lean commented on <a href="#" class="font-semibold text-gray-900 dark:text-white hover:underline">Flowbite Pro</a></div>
                    </div>
                    <div class="p-3 text-xs italic font-normal text-gray-500 border border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-300">Hi ya'll! I wanted to share a webinar zeroheight is having regarding how to best measure your design system! This is the second session of our new webinar series on #DesignSystems discussions where we'll be speaking about Measurement.</div>
                </div>
            </li>
            <li class="ms-6">
                <span class="absolute flex items-center justify-center w-6 h-6 bg-blue-100 rounded-full -start-3 ring-8 ring-white dark:ring-gray-900 dark:bg-blue-900">
                    <img class="rounded-full shadow-lg" src="https://i.pinimg.com/originals/ae/ec/c2/aeecc22a67dac7987a80ac0724658493.jpg" alt="Jese Leos image" />
                </span>
                <div class="items-center justify-between p-4 bg-white border border-gray-200 rounded-lg shadow-sm sm:flex dark:bg-gray-700 dark:border-gray-600">
                    <time class="mb-1 text-xs font-normal text-gray-400 sm:order-last sm:mb-0">1 day ago</time>
                    <div class="text-sm font-normal text-gray-500 lex dark:text-gray-300">Jese Leos has changed <a href="#" class="font-semibold text-blue-600 dark:text-blue-500 hover:underline">Pricing page</a> task status to <span class="font-semibold text-gray-900 dark:text-white">Finished</span></div>
                </div>
            </li>
        </ol>






    </div>

</body>

</html>