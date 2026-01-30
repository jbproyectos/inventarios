<?php
include "../includes/conexionbd.php";

$tipo = $_GET['tipo'] ?? '';

// Consultas para obtener estadísticas completas
$query = "SELECT c.*, d.nombre AS departamento, o.nombre AS oficina,
                 u.nombre AS usuario_nombre, u.apellido AS usuario_apellido
          FROM computadora c
          JOIN departamentos d ON c.Id_departamento = d.Id_departamento
          JOIN oficina o ON c.Id_oficina = o.Id_oficina
          LEFT JOIN usuarios u ON c.asignado_a = CONCAT(u.nombre, ' ', u.apellido)
          ORDER BY d.nombre, o.nombre, c.Id_computadora";

$stmt = $conexion->query($query);
$computadoras = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Estadísticas adicionales
$stats_query = "SELECT 
    COUNT(*) as total_equipos,
    COUNT(DISTINCT Id_departamento) as total_departamentos,
    COUNT(DISTINCT Id_oficina) as total_oficinas,
    SUM(CASE WHEN status = 'STOCK' THEN 1 ELSE 0 END) as en_stock,
    SUM(CASE WHEN status = 'ASIGNADO' THEN 1 ELSE 0 END) as asignados,
    SUM(CASE WHEN status = 'MANTENIMIENTO' THEN 1 ELSE 0 END) as mantenimiento,
    SUM(CASE WHEN status = 'BAJA' THEN 1 ELSE 0 END) as baja,
    SUM(CASE WHEN condicion = 'Excelente' THEN 1 ELSE 0 END) as excelente,
    SUM(CASE WHEN condicion = 'Bueno' THEN 1 ELSE 0 END) as bueno,
    SUM(CASE WHEN condicion = 'Regular' THEN 1 ELSE 0 END) as regular,
    SUM(CASE WHEN condicion = 'Malo' THEN 1 ELSE 0 END) as malo
    FROM computadora";

$stats_stmt = $conexion->query($stats_query);
$stats = $stats_stmt->fetch(PDO::FETCH_ASSOC);

// Agrupar por departamento y oficina
$estructura = [];
foreach ($computadoras as $comp) {
    $dep = $comp['departamento'];
    $ofi = $comp['oficina'];

    if (!isset($estructura[$dep])) {
        $estructura[$dep] = [
            'total_equipos' => 0,
            'total_oficinas' => 0,
            'oficinas' => []
        ];
    }
    if (!isset($estructura[$dep]['oficinas'][$ofi])) {
        $estructura[$dep]['oficinas'][$ofi] = [];
    }
    $estructura[$dep]['oficinas'][$ofi][] = $comp;
    $estructura[$dep]['total_equipos']++;
}

// Calcular total de oficinas por departamento
foreach ($estructura as $dep => $data) {
    $estructura[$dep]['total_oficinas'] = count($data['oficinas']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard de Equipos | Reporte Completo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .dashboard-card {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border-top: 4px solid;
        }
        
        .dashboard-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
            border-left: 4px solid #667eea;
        }
        
        .progress-ring {
            transform: rotate(-90deg);
        }
        
        .progress-ring-circle {
            stroke-linecap: round;
            transition: stroke-dashoffset 0.5s;
        }
        
        .fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .slide-in-left {
            animation: slideInLeft 0.5s ease-out forwards;
            opacity: 0;
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .stagger-item {
            animation: stagger 0.4s ease-out forwards;
            opacity: 0;
        }
        
        @keyframes stagger {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        
        .type-badge {
            position: relative;
            overflow: hidden;
        }
        
        .type-badge::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }
        
        .type-badge:hover::after {
            left: 100%;
        }
        
        .status-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
        }
        
        .hover-lift {
            transition: transform 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(102, 126, 234, 0.3);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(102, 126, 234, 0.5);
        }
        
        .grid-masonry {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            grid-auto-rows: auto;
            grid-gap: 20px;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen font-sans">
    <!-- Main Container -->
    <div class="container mx-auto px-4 py-6 max-w-7xl">
        
        <!-- Dashboard Header -->
        <div class="mb-8 fade-in-up">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-600 to-purple-600 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                Dashboard de Equipos
                            </h1>
                            <p class="text-gray-600">Panel de control integral de activos tecnológicos</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap gap-3">
                    <button onclick="window.print()" class="px-4 py-3 bg-white border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-50 transition-all duration-300 hover:shadow-lg flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        <span>Exportar PDF</span>
                    </button>
                    <button onclick="exportToExcel()" class="px-4 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white rounded-xl hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Exportar Excel</span>
                    </button>
                    <button onclick="toggleFilters()" class="px-4 py-3 bg-gradient-to-r from-blue-500 to-cyan-500 text-white rounded-xl hover:shadow-lg transition-all duration-300 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filtros</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- KPI Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Equipment Card -->
            <div class="glass-card rounded-2xl p-6 dashboard-card fade-in-up" style="animation-delay: 100ms">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-cyan-500 flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-gray-500">Total Equipos</span>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_equipos']); ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Inventario Activo</span>
                        <span>100%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2 rounded-full" style="width: 100%"></div>
                    </div>
                </div>
            </div>

            <!-- Departments Card -->
            <div class="glass-card rounded-2xl p-6 dashboard-card fade-in-up" style="animation-delay: 200ms">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-gray-500">Departamentos</span>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_departamentos']; ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Distribución</span>
                        <span><?php echo count($estructura); ?> activos</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full" 
                             style="width: <?php echo min(($stats['total_departamentos']/10)*100, 100); ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Offices Card -->
            <div class="glass-card rounded-2xl p-6 dashboard-card fade-in-up" style="animation-delay: 300ms">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-gray-500">Oficinas</span>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total_oficinas']; ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Ubicaciones Activas</span>
                        <span><?php echo $stats['total_oficinas']; ?> locaciones</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full" 
                             style="width: <?php echo min(($stats['total_oficinas']/20)*100, 100); ?>%"></div>
                    </div>
                </div>
            </div>

            <!-- Utilization Card -->
            <div class="glass-card rounded-2xl p-6 dashboard-card fade-in-up" style="animation-delay: 400ms">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-orange-500 to-red-500 flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-medium text-gray-500">Tasa de Uso</span>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php 
                            $utilizacion = $stats['total_equipos'] > 0 ? 
                                round(($stats['asignados'] / $stats['total_equipos']) * 100) : 0;
                            echo $utilizacion . '%';
                            ?>
                        </p>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Equipos Asignados</span>
                        <span><?php echo $stats['asignados']; ?> / <?php echo $stats['total_equipos']; ?></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-gradient-to-r from-orange-500 to-red-500 h-2 rounded-full" 
                             style="width: <?php echo $utilizacion; ?>%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Statistics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Status Distribution Chart -->
            <div class="lg:col-span-2">
                <div class="glass-card rounded-2xl p-6 slide-in-left">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Distribución por Estado</h2>
                            <p class="text-gray-600 text-sm">Estado actual del inventario</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-500">Actualizado hoy</span>
                            <div class="w-2 h-2 rounded-full bg-green-500"></div>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="statusChart"></canvas>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                        <div class="text-center p-3 bg-blue-50 rounded-xl">
                            <p class="text-2xl font-bold text-blue-600"><?php echo $stats['en_stock']; ?></p>
                            <p class="text-sm text-gray-600">En Stock</p>
                        </div>
                        <div class="text-center p-3 bg-green-50 rounded-xl">
                            <p class="text-2xl font-bold text-green-600"><?php echo $stats['asignados']; ?></p>
                            <p class="text-sm text-gray-600">Asignados</p>
                        </div>
                        <div class="text-center p-3 bg-yellow-50 rounded-xl">
                            <p class="text-2xl font-bold text-yellow-600"><?php echo $stats['mantenimiento']; ?></p>
                            <p class="text-sm text-gray-600">Mantenimiento</p>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-xl">
                            <p class="text-2xl font-bold text-red-600"><?php echo $stats['baja']; ?></p>
                            <p class="text-sm text-gray-600">Baja</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Condition Overview -->
            <div>
                <div class="glass-card rounded-2xl p-6 h-full">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">Condición General</h2>
                            <p class="text-gray-600 text-sm">Estado físico de equipos</p>
                        </div>
                        <div class="relative w-20 h-20">
                            <svg class="w-full h-full" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#e5e7eb" stroke-width="10"/>
                                <?php
                                $excelente_percent = $stats['total_equipos'] > 0 ? ($stats['excelente'] / $stats['total_equipos']) * 100 : 0;
                                $bueno_percent = $stats['total_equipos'] > 0 ? ($stats['bueno'] / $stats['total_equipos']) * 100 : 0;
                                $regular_percent = $stats['total_equipos'] > 0 ? ($stats['regular'] / $stats['total_equipos']) * 100 : 0;
                                $malo_percent = $stats['total_equipos'] > 0 ? ($stats['malo'] / $stats['total_equipos']) * 100 : 0;
                                ?>
                                <circle cx="50" cy="50" r="40" fill="none" stroke="#10b981" stroke-width="10" 
                                        stroke-dasharray="251.2" stroke-dashoffset="<?php echo 251.2 - ($excelente_percent * 2.512); ?>"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-lg font-bold text-gray-800"><?php echo round(($stats['excelente'] + $stats['bueno']) / $stats['total_equipos'] * 100); ?>%</span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                                <span class="text-sm text-gray-700">Excelente</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900"><?php echo $stats['excelente']; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-emerald-500 mr-2"></div>
                                <span class="text-sm text-gray-700">Bueno</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900"><?php echo $stats['bueno']; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-yellow-500 mr-2"></div>
                                <span class="text-sm text-gray-700">Regular</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900"><?php echo $stats['regular']; ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full bg-red-500 mr-2"></div>
                                <span class="text-sm text-gray-700">Requiere atención</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900"><?php echo $stats['malo']; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Departments Breakdown -->
        <div class="glass-card rounded-2xl p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Desglose por Departamento</h2>
                    <p class="text-gray-600 text-sm">Distribución detallada de equipos</p>
                </div>
                <div class="flex space-x-2">
                    <button onclick="expandAllDepartments()" class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Expandir Todo
                    </button>
                    <button onclick="collapseAllDepartments()" class="px-3 py-2 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                        Colapsar Todo
                    </button>
                </div>
            </div>

            <div class="space-y-4">
                <?php 
                $deptIndex = 0;
                foreach ($estructura as $departamento => $data): 
                    $deptIndex++;
                ?>
                <div class="stagger-item bg-gradient-to-r from-gray-50 to-white rounded-xl border border-gray-200 overflow-hidden"
                     style="animation-delay: <?php echo $deptIndex * 100; ?>ms">
                    
                    <!-- Department Header -->
                    <button onclick="toggleDepartment('dept-<?php echo md5($departamento); ?>')" 
                            class="w-full p-5 text-left hover:bg-gray-50/50 transition-colors duration-300 collapsed group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-white rounded-full border-2 border-white flex items-center justify-center">
                                        <span class="text-xs font-bold text-blue-600"><?php echo $data['total_equipos']; ?></span>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800"><?php echo $departamento; ?></h3>
                                    <div class="flex items-center space-x-3 mt-1">
                                        <span class="text-sm text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                            </svg>
                                            <?php echo $data['total_oficinas']; ?> oficinas
                                        </span>
                                        <span class="text-sm text-gray-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                            <?php echo $data['total_equipos']; ?> equipos
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="text-sm px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                                    <?php echo round(($data['total_equipos'] / $stats['total_equipos']) * 100, 1); ?>%
                                </span>
                                <svg class="w-5 h-5 text-gray-400 collapse-icon transition-transform duration-300 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </button>
                    
                    <!-- Department Content -->
                    <div id="dept-<?php echo md5($departamento); ?>" class="hidden border-t border-gray-200">
                        <div class="p-5">
                            <div class="grid-masonry">
                                <?php 
                                $officeIndex = 0;
                                foreach ($data['oficinas'] as $oficina => $equipos): 
                                    $officeIndex++;
                                ?>
                                <div class="bg-white rounded-xl border border-gray-200 p-4 hover-lift">
                                    <!-- Office Header -->
                                    <div class="flex items-center justify-between mb-3">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <h4 class="font-semibold text-gray-800"><?php echo $oficina; ?></h4>
                                                <p class="text-xs text-gray-500"><?php echo count($equipos); ?> equipos</p>
                                            </div>
                                        </div>
                                        <button onclick="toggleOffice('office-<?php echo md5($departamento.$oficina); ?>')" 
                                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Ver equipos
                                        </button>
                                    </div>
                                    
                                    <!-- Office Content -->
                                    <div id="office-<?php echo md5($departamento.$oficina); ?>" class="hidden mt-3 space-y-3 custom-scrollbar" style="max-height: 300px; overflow-y: auto;">
                                        <?php foreach ($equipos as $comp): ?>
                                        <div class="bg-gray-50 rounded-lg p-3 border border-gray-100 hover:bg-gray-100 transition-colors">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <div class="flex items-center space-x-2 mb-1">
                                                        <span class="text-xs font-semibold px-2 py-1 rounded-full 
                                                            <?php echo match($comp['status']) {
                                                                'STOCK' => 'bg-blue-100 text-blue-800',
                                                                'ASIGNADO' => 'bg-green-100 text-green-800',
                                                                'MANTENIMIENTO' => 'bg-yellow-100 text-yellow-800',
                                                                'BAJA' => 'bg-red-100 text-red-800',
                                                                default => 'bg-gray-100 text-gray-800'
                                                            }; ?>">
                                                            <?php echo $comp['status']; ?>
                                                        </span>
                                                        <span class="text-xs text-gray-500">#<?php echo $comp['Id_computadora']; ?></span>
                                                    </div>
                                                    <h5 class="text-sm font-medium text-gray-900 truncate"><?php echo $comp['modelo']; ?></h5>
                                                    <p class="text-xs text-gray-600 mt-1"><?php echo $comp['asignado_a']; ?></p>
                                                </div>
                                                <span class="text-xs px-2 py-1 bg-gray-200 text-gray-700 rounded-full">
                                                    <?php echo $comp['tipo']; ?>
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
                                                <div class="text-gray-600">CPU: <span class="font-medium"><?php echo $comp['procesador']; ?></span></div>
                                                <div class="text-gray-600">RAM: <span class="font-medium"><?php echo $comp['ram']; ?>GB</span></div>
                                                <div class="text-gray-600">Cond: <span class="font-medium"><?php echo $comp['condicion']; ?></span></div>
                                                <div class="text-gray-600">Marca: <span class="font-medium"><?php echo $comp['marca']; ?></span></div>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Stats Footer -->
        <div class="glass-card rounded-2xl p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600 mb-2"><?php echo $stats['total_equipos']; ?></div>
                    <p class="text-sm text-gray-600">Equipos Totales</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-purple-600 mb-2"><?php echo $stats['total_departamentos']; ?></div>
                    <p class="text-sm text-gray-600">Departamentos</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600 mb-2"><?php echo $stats['total_oficinas']; ?></div>
                    <p class="text-sm text-gray-600">Oficinas</p>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-orange-600 mb-2"><?php echo round($utilizacion); ?>%</div>
                    <p class="text-sm text-gray-600">Tasa de Utilización</p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            // Animate staggered items
            const staggerItems = document.querySelectorAll('.stagger-item');
            staggerItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.animationDelay = `${index * 100}ms`;
                    item.style.opacity = 1;
                }, 100);
            });

            // Initialize charts
            initializeCharts();
        });

        // Toggle sections
        function toggleDepartment(id) {
            const element = document.getElementById(id);
            const button = element.previousElementSibling;
            
            element.classList.toggle('hidden');
            button.classList.toggle('collapsed');
            
            // Smooth height animation
            if (!element.classList.contains('hidden')) {
                element.style.maxHeight = element.scrollHeight + 'px';
            } else {
                element.style.maxHeight = '0px';
            }
        }
        
        function toggleOffice(id) {
            const element = document.getElementById(id);
            element.classList.toggle('hidden');
        }
        
        // Expand/Collapse all
        function expandAllDepartments() {
            document.querySelectorAll('[id^="dept-"]').forEach(el => {
                el.classList.remove('hidden');
                el.previousElementSibling?.classList.remove('collapsed');
                el.style.maxHeight = el.scrollHeight + 'px';
            });
        }
        
        function collapseAllDepartments() {
            document.querySelectorAll('[id^="dept-"]').forEach(el => {
                el.classList.add('hidden');
                el.previousElementSibling?.classList.add('collapsed');
                el.style.maxHeight = '0px';
            });
        }
        
        // Export functions
        function exportToExcel() {
            // Simulate export
            const btn = event.target.closest('button');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span>Exportando...</span>';
            btn.disabled = true;
            
            setTimeout(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                showNotification('Reporte exportado exitosamente', 'success');
            }, 1500);
        }
        
        function toggleFilters() {
            showNotification('Funcionalidad de filtros en desarrollo', 'info');
        }
        
        // Notification system
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-6 py-3 rounded-xl shadow-lg transform translate-x-full transition-transform duration-300 z-50 ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => notification.remove(), 300);
                }, 3000);
            }, 100);
        }
        
        // Initialize charts
        function initializeCharts() {
            const ctx = document.getElementById('statusChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Stock', 'Asignados', 'Mantenimiento', 'Baja'],
                    datasets: [{
                        label: 'Cantidad de Equipos',
                        data: [
                            <?php echo $stats['en_stock']; ?>,
                            <?php echo $stats['asignados']; ?>,
                            <?php echo $stats['mantenimiento']; ?>,
                            <?php echo $stats['baja']; ?>
                        ],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(34, 197, 94, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)'
                        ],
                        borderColor: [
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)'
                        ],
                        borderWidth: 1,
                        borderRadius: 6,
                        hoverBackgroundColor: [
                            'rgb(59, 130, 246)',
                            'rgb(34, 197, 94)',
                            'rgb(245, 158, 11)',
                            'rgb(239, 68, 68)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 12
                            },
                            bodyFont: {
                                size: 12
                            },
                            padding: 12,
                            cornerRadius: 6
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Add transition for max-height
        document.querySelectorAll('[id^="dept-"]').forEach(el => {
            el.style.transition = 'max-height 0.3s ease-out';
            el.style.overflow = 'hidden';
            el.style.maxHeight = '0px';
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                expandAllDepartments();
            }
            if (e.ctrlKey && e.key === 'c') {
                e.preventDefault();
                collapseAllDepartments();
            }
            if (e.key === 'Escape') {
                collapseAllDepartments();
            }
        });
    </script>
</body>
</html>