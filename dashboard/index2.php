<?php
session_start();
include "../includes/conexionbd.php";
require_once '../errores/error_handler.php';


// Evitar caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}


// Consulta SQL
$sql = "SELECT modelo, posibleFechaParaVenta, Id_computadora
        FROM computadora
        WHERE STR_TO_DATE(posibleFechaParaVenta, '%d/%m/%Y') 
        BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 6 MONTH)";

$stmt = $conexion->query($sql);

// Preparar los eventos
$eventos = [];
while ($row = $stmt->fetch()) {
    $fecha = date('Y-m-d', strtotime(str_replace('/', '-', $row['posibleFechaParaVenta'])));
    $eventos[] = [
        'title' => $row['modelo'],
        'fecha' => $fecha,
        'id' => $row['Id_computadora']
    ];
}

include '../includes/middleware.php';
verificarSesion();

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
    <title>Modern Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pikaday/css/pikaday.css">
    <script src="https://cdn.jsdelivr.net/npm/pikaday/pikaday.js"></script>
    <link rel="stylesheet" href="../assets/css/calendar.css">
    <link rel="stylesheet" href="../assets/css/profile.css">
    <!-- <script src="../assets/js/dash.js"></script> -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.46.0/dist/apexcharts.min.js"></script>

    <script src="../assets/js/chartCompu.js"></script>
    <script src="../assets/js/chartdepartamentos.js"></script>

</head>

<body class="bg-gray-100 text-gray-800">


    <!-- Navbar -->
    <?php include 'includes/nav.php'?>


    <div class="flex">
        <!-- Sidebar -->

        <?php include 'includes/aside.php'?>




        <!-- Main Content -->
        <main class="flex-1 p-4">
            <!-- First Section -->

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-2 mb-2">

                <!-- Total de Equipos -->

                <div class="bg-white p-5 shadow-lg rounded-2xl flex flex-col items-center relative group">
                    <div class="text-blue-500 bg-blue-100 rounded-full p-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h11M9 21v-6m-6-5 2-6h12l2 6m-8 0v2m0 4v2m6-8h3" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold">Total de Equipos</h2>
                    <?php
                    try {
                        // Consulta para obtener el total de equipos
                        $query = "SELECT COUNT(*) AS total_equipos FROM computadora";
                        $stmt = $conexion->query($query);
                        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                        $total_equipos = $resultado['total_equipos'];
                    } catch (PDOException $e) {
                        echo "<p class='text-red-500'>Error en la conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
                        exit;
                    }
                    // Mostrar el total de equipos
                    echo '<p class="text-gray-600 text-3xl font-bold">' . htmlspecialchars($total_equipos) . " </p> ";
                    ?>

                    <p class="text-sm text-gray-400" id="total-anio">Actualizado recientemente</p>

                    <!-- Puntos de interacción -->
                    <div class="absolute top-3 right-3 flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-gray-500 hover:text-blue-500" title="Descargar Reporte">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7v10M7 7l4 4m-4-4l-4 4" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-green-500" title="Ir al Inventario">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- All in One -->
                <div class="bg-white p-5 shadow-lg rounded-2xl flex flex-col items-center relative group">
                    <div class="text-green-500 bg-green-100 rounded-full p-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16h8m0 0V5a1 1 0 00-1-1H9a1 1 0 00-1 1v11zm0 0h-4" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold">All in One</h2>
                    <!-- <p class="text-gray-600 text-3xl font-bold">12</p> -->
                    <?php
                    try {
                        // Consulta para contar los equipos con status 'STOCK'
                        $query = "SELECT COUNT(*) AS total_stock FROM computadora WHERE tipo = 'All in One'";
                        $stmt = $conexion->query($query);
                        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                        $total_stock = $resultado['total_stock'];
                    } catch (PDOException $e) {
                        echo "<p class='text-red-500'>Error en la conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
                        exit;
                    }
                    echo "<p class='text-gray-600 text-3xl font-bold'>" . htmlspecialchars($total_stock) . " </p> ";

                    ?>
                    <p class="text-sm text-gray-400">28% del total</p>

                    <!-- Puntos de interacción -->
                    <div class="absolute top-3 right-3 flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-gray-500 hover:text-blue-500" title="Descargar Reporte">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7v10M7 7l4 4m-4-4l-4 4" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-green-500" title="Ir al Inventario">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Micro CPU -->
                <div class="bg-white p-5 shadow-lg rounded-2xl flex flex-col items-center relative group">
                    <div class="text-orange-500 bg-orange-100 rounded-full p-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h8m8 0h-6m0 0v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6zm0 0V5a2 2 0 012-2h4a2 2 0 012 2v5z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold">Micro CPU</h2>
                    <!-- <p class="text-gray-600 text-3xl font-bold">18</p> -->
                    <?php
                    try {
                        // Consulta para contar los equipos con status 'STOCK'
                        $query = "SELECT COUNT(*) AS total_venta FROM computadora WHERE tipo = 'Micro CPU'";
                        $stmt = $conexion->query($query);
                        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                        $total_venta = $resultado['total_venta'];
                    } catch (PDOException $e) {
                        echo "<p class='text-red-500'>Error en la conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
                        exit;
                    }
                    echo "<p class='text-gray-600 text-3xl font-bold'>" . htmlspecialchars($total_venta) . " </p> ";

                    ?>
                    <p class="text-sm text-gray-400">40% del total</p>

                    <!-- Puntos de interacción -->
                    <div class="absolute top-3 right-3 flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-gray-500 hover:text-blue-500" title="Descargar Reporte">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7v10M7 7l4 4m-4-4l-4 4" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-green-500" title="Ir al Inventario">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Laptop -->
                <div class="bg-white p-5 shadow-lg rounded-2xl flex flex-col items-center relative group">
                    <div class="text-purple-500 bg-purple-100 rounded-full p-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 16h14M5 16l-2 3m16-3l2 3m-2-3H5m7 0v3m-2-6V4a1 1 0 011-1h2a1 1 0 011 1v9m-4 0h4" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold">Laptop</h2>
                    <p class="text-gray-600 text-3xl font-bold">
                        <?php
                        try {
                            // Consulta para contar los equipos con status 'STOCK'
                            $query = "SELECT COUNT(*) AS total_venta FROM computadora WHERE tipo = 'Laptop'";
                            $stmt = $conexion->query($query);
                            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
                            $total_lap = $resultado['total_venta'];
                        } catch (PDOException $e) {
                            echo "<p class='text-red-500'>Error en la conexión: " . htmlspecialchars($e->getMessage()) . "</p>";
                            exit;
                        }
                        echo "<p class='text-gray-600 text-3xl font-bold'>" . htmlspecialchars($total_lap) . " </p> ";

                        ?>
                    </p>

                    <p class="text-sm text-gray-400">33% del total</p>

                    <!-- Puntos de interacción -->
                    <div class="absolute top-3 right-3 flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-gray-500 hover:text-blue-500" title="Descargar Reporte">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7v10M7 7l4 4m-4-4l-4 4" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-green-500" title="Ir al Inventario">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                <!-- Laptop -->
                <div class="bg-white p-5 shadow-lg rounded-2xl flex flex-col items-center relative group">
                    <div class="text-red-500 bg-red-100 rounded-full p-3 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 16h14M5 16l-2 3m16-3l2 3m-2-3H5m7 0v3m-2-6V4a1 1 0 011-1h2a1 1 0 011 1v9m-4 0h4" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold">Desktop</h2>
                    <p class="text-gray-600 text-3xl font-bold">15</p>
                    <p class="text-sm text-gray-400">33% del total</p>


                    <!-- Puntos de interacción -->
                    <div class="absolute top-3 right-3 flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <a href="#" class="text-gray-500 hover:text-blue-500" title="Descargar Reporte">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 7v10M7 7l4 4m-4-4l-4 4" />
                            </svg>
                        </a>
                        <a href="#" class="text-gray-500 hover:text-green-500" title="Ir al Inventario">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>

            </div>

            <!-- Second Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-2">
                <!-- Chart 1 -->
                <div class="bg-white p-6 shadow rounded-lg md:col-span-2">
                    <h2 class="text-xl font-semibold mb-4">Inversion por marca</h2>
                    <div id="graficoEquipos"></div>
                </div>
                <!-- Chart 2 -->
                <div class="bg-white p-6 shadow rounded-lg">
                    <h2 class="text-xl font-semibold mb-4">Proximos vencimientos</h2>
                    <div class="flex justify-between items-center calendar-navigation mb-4">
                        <button id="prevMonth" class="px-3 py-2  text-gray-800 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </button>
                        <span id="currentMonth" class="text-sm font-semibold text-gray-700"></span>
                        <button id="nextMonth" class="px-3 py-2 text-gray-800 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Calendario -->
                    <div id="calendar" class="bg-white shadow-sm rounded-lg overflow-hidden transition-all duration-300"></div>

                    <!-- Información del evento -->
                    <div id="event-info" class="event-info mt-4"></div>
                </div>
            </div>

            <!-- Third Section -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-2">
                <div class="bg-white p-4 shadow rounded-lg md:col-span-2">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white" id="total-users">0</h5>

                    <h2 class="text-xl font-semibold">Inversion por departamento</h2>
                    <div id="area-chart" class="mt-4"></div>
                </div>
                <div class="bg-white p-4 shadow rounded-lg ">
                    <h2 class="text-xl font-semibold">Estado</h2>
                    <p class="text-gray-600">Details</p>
                    <canvas id="pie-chart" style="width: 30%; height: 60%;"></canvas>

                </div>

            </div>
            <!-- cuarta Section -->

            <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-2">
                <div class="bg-white p-4 shadow rounded-lg">
                    <h2 class="text-xl font-semibold">Estatus</h2>
                    <p class="text-gray-600">Details</p>
                    <canvas id="pie-status" style="width: 100%; height: 100%;" width="400" height="400"></canvas>

                </div>
                <div class="bg-white p-4 shadow rounded-lg md:col-span-2">
                    <h5 class="text-3xl font-bold text-gray-900 dark:text-white" id="total-Oficinas">0</h5>

                    <h2 class="text-xl font-semibold">inversion por oficina</h2>
                    <p class="text-gray-600">Details</p>
                    <div id="area-oficina" class="mt-4"></div>

                </div>
                <div class="bg-white p-4 shadow rounded-lg">
                    <h2 class="text-xl font-semibold">Recursos de computadoras</h2>
                    <p class="text-gray-600">Details</p>
                    <table class="table-auto w-full border-collapse border border-gray-300">
                        <thead>
                            <tr>
                                <th class="border border-gray-300 p-2 text-left">Categoría</th>
                                <th class="border border-gray-300 p-2 text-left">Total</th>
                                <th class="border border-gray-300 p-2 text-left">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-body"> <!-- Categorías dinámicas aquí -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var eventInfoEl = document.getElementById('event-info');
            var currentMonthDisplay = document.getElementById('currentMonth');

            var currentDate = new Date();
            var currentYear = currentDate.getFullYear();
            var currentMonth = currentDate.getMonth(); // 0 - Enero, 1 - Febrero, ...

            // Función para actualizar el calendario y mostrar eventos
            function renderCalendar() {
                calendarEl.innerHTML = ''; // Limpiar el calendario
                currentMonthDisplay.innerText = `${currentMonth + 1}-${currentYear}`;

                var daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
                var firstDay = new Date(currentYear, currentMonth, 1).getDay();
                var calendarTable = document.createElement('table');
                calendarTable.classList.add('w-full', 'table-auto', 'text-center', 'border-collapse');
                var tbody = document.createElement('tbody');
                var tr = document.createElement('tr');

                // Crear las cabeceras de los días
                const daysOfWeek = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
                daysOfWeek.forEach(day => {
                    var th = document.createElement('th');
                    th.classList.add('px-2', 'py-1', 'text-xs', 'text-gray-600', 'font-semibold');
                    th.innerText = day;
                    tr.appendChild(th);
                });
                tbody.appendChild(tr);

                // Crear los días del mes
                var dayCounter = 1;
                for (var i = 0; i < 6; i++) {
                    tr = document.createElement('tr');
                    for (var j = 0; j < 7; j++) {
                        var td = document.createElement('td');
                        td.classList.add('py-2', 'border-b', 'border-gray-200');
                        if (i === 0 && j < firstDay || dayCounter > daysInMonth) {
                            td.innerText = '';
                        } else {
                            td.innerText = dayCounter;
                            var dateStr = `${currentYear}-${(currentMonth + 1).toString().padStart(2, '0')}-${dayCounter.toString().padStart(2, '0')}`;

                            // Verificar si esta fecha tiene eventos
                            <?php foreach ($eventos as $event) : ?>
                                if (dateStr === '<?php echo $event['fecha']; ?>') {
                                    var eventElem = document.createElement('span');
                                    eventElem.className = 'event-date px-2 py-1 text-xs';
                                    eventElem.innerText = '📅';
                                    // eventElem.innerText = '<?php echo $event['title']; ?>';

                                    eventElem.onclick = function() {
                                        eventInfoEl.innerHTML = '<strong>Equipo:</strong> <?php echo $event['title']; ?><br><strong>Fecha:</strong> <?php echo $event['fecha']; ?>';
                                        eventInfoEl.style.display = 'block';
                                    };
                                    td.appendChild(eventElem);
                                }
                            <?php endforeach; ?>

                            dayCounter++;
                        }
                        tr.appendChild(td);
                    }
                    tbody.appendChild(tr);
                }
                calendarTable.appendChild(tbody);
                calendarEl.appendChild(calendarTable);
            }

            // Inicializar el calendario
            renderCalendar();

            // Navegar al mes anterior
            document.getElementById('prevMonth').addEventListener('click', function() {
                if (currentMonth === 0) {
                    currentMonth = 11; // Diciembre
                    currentYear--;
                } else {
                    currentMonth--;
                }
                renderCalendar();
            });

            // Navegar al mes siguiente
            document.getElementById('nextMonth').addEventListener('click', function() {
                if (currentMonth === 11) {
                    currentMonth = 0; // Enero
                    currentYear++;
                } else {
                    currentMonth++;
                }
                renderCalendar();
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