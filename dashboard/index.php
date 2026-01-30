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
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta property="og:title" content="Mi Sistema de Gestión">
  <meta property="og:description" content="Administra tus sistemas y datos de forma fácil y segura.">
  <meta property="og:image" content="http://kabzo.ddns.net/sistemas/img/preview.png">
  <meta property="og:url" content="http://kabzo.ddns.net/sistemas/dashboard/">
  <meta property="og:type" content="website">
    <title>Dashboard v2</title>
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
    
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/shepherd.js/dist/css/shepherd.css">
<script src="https://cdn.jsdelivr.net/npm/shepherd.js/dist/js/shepherd.min.js"></script>

</head>

<body class="bg-gray-100 text-gray-800">


    <!-- Navbar -->
    <?php include 'includes/nav.php'?>


    <div class="flex">
        <!-- Sidebar -->

        <?php include 'includes/aside.php'?>




        <!-- Main Content -->
        <?php if($canViewMoney): ?>
        <main class="flex-1 p-4">
            <!-- First Section -->

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 xl:grid-cols-7 gap-2 mb-2">

    <?php
    try {
        // Obtener estadísticas completas en una sola consulta
        $query = "SELECT 
                    tipo,
                    COUNT(*) as cantidad
                  FROM computadora 
                  WHERE tipo IS NOT NULL AND tipo != ''
                  GROUP BY tipo
                  ORDER BY cantidad DESC, tipo";
        
        $stmt_tipos = $conexion->query($query);
        $tipos_data = $stmt_tipos->fetchAll(PDO::FETCH_ASSOC);
        
        // Obtener total general
        $query_total = "SELECT COUNT(*) as total FROM computadora";
        $stmt_total = $conexion->query($query_total);
        $total_result = $stmt_total->fetch(PDO::FETCH_ASSOC);
        $total_equipos = $total_result['total'];
        
        // Arrays para colores e iconos dinámicos
        $colors = [
            'blue' => ['text-blue-500', 'bg-blue-100'],
            'green' => ['text-green-500', 'bg-green-100'],
            'red' => ['text-red-500', 'bg-red-100'],
            'purple' => ['text-purple-500', 'bg-purple-100'],
            'orange' => ['text-orange-500', 'bg-orange-100'],
            'cyan' => ['text-cyan-500', 'bg-cyan-100'],
            'pink' => ['text-pink-500', 'bg-pink-100'],
            'indigo' => ['text-indigo-500', 'bg-indigo-100'],
            'teal' => ['text-teal-500', 'bg-teal-100'],
            'amber' => ['text-amber-500', 'bg-amber-100'],
        ];
        
        $icon_paths = [
            'M3 10h11M9 21v-6m-6-5 2-6h12l2 6m-8 0v2m0 4v2m6-8h3',
            'M8 16h8m0 0V5a1 1 0 00-1-1H9a1 1 0 00-1 1v11zm0 0h-4',
            'M3 10h8m8 0h-6m0 0v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6zm0 0V5a2 2 0 012-2h4a2 2 0 012 2v5z',
            'M5 16h14M5 16l-2 3m16-3l2 3m-2-3H5m7 0v3m-2-6V4a1 1 0 011-1h2a1 1 0 011 1v9m-4 0h4',
            'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
            'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z',
            'M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z',
            'M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4',
        ];
        
        $color_keys = array_keys($colors);
        $color_index = 0;
        
        // Primero mostrar el total
        $current_color = $colors[$color_keys[$color_index % count($color_keys)]];
        $color_index++;
    ?>
    
    <!-- Tarjeta Total -->
    <div class="bg-white p-3 shadow-md rounded-xl flex items-center justify-between relative group hover:shadow-lg transition-shadow">
        <div class="flex items-center space-x-3">
            <div class="<?php echo $current_color[1]; ?> rounded-lg p-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?php echo $current_color[0]; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $icon_paths[0]; ?>" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-800">Total</h3>
                <p class="text-lg font-bold text-gray-900"><?php echo $total_equipos; ?></p>
                <p class="text-lg font-bold text-gray-900 hidden" id="total-anio"></p>
            </div>
        </div>
        
        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex space-x-1">
            <a href="reporte_detallado.php?tipo=total" class="text-gray-400 hover:text-blue-500 p-1" title="Reporte">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </a>
            <a href="inventario.php" class="text-gray-400 hover:text-green-500 p-1" title="Inventario">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>

    <?php
        // Mostrar cada tipo dinámicamente
        foreach ($tipos_data as $index => $tipo_info) {
            $tipo = $tipo_info['tipo'];
            $cantidad = $tipo_info['cantidad'];
            
            // Calcular porcentaje
            $porcentaje = $total_equipos > 0 ? round(($cantidad / $total_equipos) * 100, 1) : 0;
            
            // Asignar color rotativo
            $current_color = $colors[$color_keys[$color_index % count($color_keys)]];
            $icon_index = $color_index % count($icon_paths);
            $color_index++;
            
            // Generar URL amigable
            $url_tipo = strtolower(preg_replace('/[^a-z0-9]+/i', '_', $tipo));
    ?>
    
    <!-- Tarjeta para tipo: <?php echo htmlspecialchars($tipo); ?> -->
    <div class="bg-white p-3 shadow-md rounded-xl flex items-center justify-between relative group hover:shadow-lg transition-shadow">
        <div class="flex items-center space-x-3">
            <div class="<?php echo $current_color[1]; ?> rounded-lg p-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 <?php echo $current_color[0]; ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="<?php echo $icon_paths[$icon_index]; ?>" />
                </svg>
            </div>
            <div class="min-w-0">
                <h3 class="text-sm font-semibold text-gray-800 truncate" title="<?php echo htmlspecialchars($tipo); ?>">
                    <?php echo htmlspecialchars($tipo); ?>
                </h3>
                <div class="flex items-baseline space-x-2">
                    <p class="text-lg font-bold text-gray-900"><?php echo $cantidad; ?></p>
                    <p class="text-xs text-gray-500"><?php echo $porcentaje; ?>%</p>
                </div>
            </div>
        </div>
        
        <div class="opacity-0 group-hover:opacity-100 transition-opacity flex space-x-1">
            <a href="reporte_detallado.php?tipo=<?php echo $url_tipo; ?>" class="text-gray-400 hover:text-blue-500 p-1" title="Reporte">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </a>
            <a href="inventario.php?filtro_tipo=<?php echo urlencode($tipo); ?>" class="text-gray-400 hover:text-green-500 p-1" title="Inventario">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
    
    <?php
        }
        
    } catch (PDOException $e) {
        echo "<div class='col-span-full p-3 bg-red-50 text-red-600 text-sm rounded-lg'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    ?>

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
 <?php else: ?>
        
       <div class="w-full bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-md text-center" role="alert">
    <h2 class="font-semibold text-xl mb-2">¡Acceso denegado!</h2>
    <p>No tienes autorización para ver esta página.</p>
</div>

</div>

                                        <?php endif; ?>


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