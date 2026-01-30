<?php

include '../includes/middleware.php';
verificarSesion();
include "../includes/conexionbd.php";

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

// Obtener listas para dropdowns
$oficinas = $conexion->query("SELECT DISTINCT nombre FROM oficina WHERE nombre IS NOT NULL ORDER BY nombre")->fetchAll(PDO::FETCH_COLUMN);
$departamentos = $conexion->query("SELECT DISTINCT nombre FROM departamentos WHERE nombre IS NOT NULL ORDER BY nombre")->fetchAll(PDO::FETCH_COLUMN);
$empleados = $conexion->query("SELECT DISTINCT nombre FROM Empleados WHERE nombre IS NOT NULL ORDER BY nombre")->fetchAll(PDO::FETCH_COLUMN);

// Marcas de teléfonos comunes
$marcas = ['Apple', 'Samsung', 'Xiaomi', 'Huawei', 'Motorola', 'LG', 'Google', 'OnePlus', 'Sony', 'Nokia', 'Oppo', 'Vivo', 'Realme', 'Tecno', 'Infinix', 'Otro'];

// Obtener sugerencias de mantenimiento
$sugerencias = $conexion->query("
    SELECT t.*, 
           CASE 
               WHEN t.bateria < 80 THEN 'Necesita cambio de batería'
               WHEN t.bateria < 90 THEN 'Batería en estado regular'
               ELSE 'Batería en buen estado'
           END as estado_bateria,
           CASE 
               WHEN t.posible_venta <= YEAR(CURDATE()) THEN 'Listo para venta'
               WHEN t.posible_venta <= YEAR(CURDATE()) + 1 THEN 'Posible venta próximo año'
               ELSE 'Mantener en inventario'
           END as sugerencia_venta
    FROM telefonos t
    ORDER BY t.bateria ASC, t.posible_venta ASC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teléfonos | Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        /* Estilos compactos e interactivos */
        :root {
            --compact-padding: 0.375rem;
            --compact-radius: 0.375rem;
            --compact-font: 0.875rem;
        }

        /* Tabla ultra compacta para móvil */
        @media (max-width: 768px) {
            .mobile-table-wrapper {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                margin: 0 -0.5rem;
                padding: 0 0.5rem;
            }

            .compact-table {
                min-width: 900px;
                font-size: 0.8125rem;
            }

            .compact-table th,
            .compact-table td {
                padding: 0.375rem 0.5rem;
            }

            .mobile-hidden {
                display: none;
            }

            .mobile-stack {
                flex-direction: column;
                gap: 0.5rem;
            }

            .mobile-full {
                width: 100%;
            }

            .badge-compact {
                padding: 0.125rem 0.375rem;
                font-size: 0.6875rem;
            }

            .bateria-mobile {
                width: 50px;
                height: 18px;
            }
        }

        /* Tabla compacta general */
        .compact-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.875rem;
        }

        .compact-table thead {
            background: linear-gradient(to bottom, #f9fafb, #f3f4f6);
        }

        .compact-table thead th {
            padding: 0.75rem 0.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.025em;
            color: #4b5563;
            border-bottom: 2px solid #e5e7eb;
            white-space: nowrap;
        }

        .compact-table tbody td {
            padding: 0.625rem 0.5rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .compact-table tbody tr:hover {
            background-color: #f9fafb;
        }

        /* Indicador de batería compacto */
        .bateria-indicator {
            width: 60px;
            height: 20px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            display: inline-block;
        }

        .bateria-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s;
        }

        .bateria-baja {
            background: linear-gradient(90deg, #ef4444, #dc2626);
        }

        .bateria-media {
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }

        .bateria-alta {
            background: linear-gradient(90deg, #10b981, #059669);
        }

        .bateria-text {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6875rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
        }

        /* Badge compacto */
        .badge-compact {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.625rem;
            border-radius: 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
        }

        .badge-activo {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .badge-inactivo {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .badge-reparacion {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .badge-danado {
            background: #f3e8ff;
            color: #6b21a8;
            border: 1px solid #e9d5ff;
        }

        /* Botones de acción compactos */
        .action-grid {
            display: flex;
            gap: 0.25rem;
            flex-wrap: nowrap;
        }

        .action-btn {
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 0.8125rem;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .action-btn-view {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .action-btn-edit {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
        }

        .action-btn-delete {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fecaca;
        }

        .action-btn-history {
            background: #f3e8ff;
            color: #7c3aed;
            border-color: #e9d5ff;
        }

        /* Formulario interactivo */
        .form-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .form-section:hover {
            border-color: #cbd5e1;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .section-header h4 {
            font-weight: 600;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 0;
        }

        .section-content {
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .section-collapsed .section-content {
            display: none;
        }

        .collapse-icon {
            transition: transform 0.3s ease;
        }

        .section-collapsed .collapse-icon {
            transform: rotate(-90deg);
        }

        /* Grid para formulario */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .form-group {
            margin-bottom: 0.75rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
        }

        .form-label.required::after {
            content: " *";
            color: #ef4444;
        }

        .form-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-select {
            width: 100%;
            padding: 0.5rem 2.5rem 0.5rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background-color: white;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
        }

        /* Contenedor de cuentas iCloud */
        .icloud-container {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 0.75rem;
            position: relative;
        }

        .btn-remove {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            color: #ef4444;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0.25rem;
            border-radius: 0.25rem;
        }

        .btn-remove:hover {
            background: #fee2e2;
        }

        /* Switch para números */
        .switch-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding: 0.5rem;
            background: #f1f5f9;
            border-radius: 0.375rem;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 44px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #3b82f6;
        }

        input:checked+.slider:before {
            transform: translateX(20px);
        }

        /* Range personalizado para batería */
        .range-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .range-slider {
            flex: 1;
            height: 6px;
            -webkit-appearance: none;
            appearance: none;
            background: #e5e7eb;
            border-radius: 3px;
            outline: none;
        }

        .range-slider::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #3b82f6;
            cursor: pointer;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .range-value {
            min-width: 60px;
            font-weight: 600;
            color: #374151;
        }

        /* Estados de batería */
        .bateria-estado {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            display: inline-block;
        }

        .estado-baja {
            background: #fee2e2;
            color: #991b1b;
        }

        .estado-media {
            background: #fef3c7;
            color: #92400e;
        }

        .estado-alta {
            background: #dcfce7;
            color: #166534;
        }

        /* Modal compacto */
        .modal-compact .modal-body {
            max-height: 80vh;
            overflow-y: auto;
        }

        /* Botones compactos */
        .btn-compact {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border-color: #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        /* Sugerencias compactas */
        .suggestion-compact {
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
        }

        .suggestion-critical {
            background: #fee2e2;
            border-left: 4px solid #dc2626;
        }

        .suggestion-warning {
            background: #fef3c7;
            border-left: 4px solid #d97706;
        }

        .suggestion-info {
            background: #dbeafe;
            border-left: 4px solid #3b82f6;
        }

        /* Paginación compacta */
        .pagination-compact {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
            margin-top: 1rem;
        }

        .page-btn {
            min-width: 32px;
            height: 32px;
            padding: 0 0.5rem;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #d1d5db;
            cursor: pointer;
        }

        .page-btn.active {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-700">

    <?php include 'includes/nav.php' ?>

    <div class="flex">
        <?php include 'includes/aside.php' ?>

        <main class="flex-1 p-3 md:p-4">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Teléfonos</h1>
                        <p class="text-sm text-gray-500">Gestión de inventario de dispositivos móviles</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <?php if ($canAdd): ?>
                            <button data-modal-target="crud-modal" data-modal-toggle="crud-modal"
                                class="btn-compact btn-primary">
                                <i class="fas fa-plus"></i>
                                <span>Nuevo Teléfono</span>
                            </button>
                        <?php endif; ?>

                        <button data-modal-target="progress-modal" data-modal-toggle="progress-modal"
                            class="btn-compact btn-secondary">
                            <i class="fas fa-file-import"></i>
                            <span>Importar CSV</span>
                        </button>

                        <?php if ($canDelete): ?>
                            <button onclick="confirmEmptyTable()"
                                class="btn-compact bg-red-600 hover:bg-red-700 text-white border-red-600">
                                <i class="fas fa-trash-alt"></i>
                                <span>Vaciar Tabla</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Buscador y filtros -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="form-label">Buscar teléfono</label>
                            <div class="relative">
                                <input type="text" name="search" placeholder="Modelo, IMEI, propietario..."
                                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                                    class="form-input pl-10">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">Status</label>
                            <select id="filter-status" class="form-select">
                                <option value="">Todos</option>
                                <option value="Disponible en almacen">Disponible en almacen</option>
                                <option value="Asignado a usuario">Asignado a usuario</option>
                                <option value="Asignacion temporal">Asignacion temporal</option>
                                <option value="Devuelto">Devuelto</option>
                                <option value="En revision tecnica">En revision tecnica</option>
                                <option value="Pendiente de formateo">Pendiente de formateo</option>
                                <option value="En venta">En venta</option>
                                <option value="Baja definitiva">Baja definitiva</option>

                            </select>
                        </div>

                        <div>
                            <label class="form-label">Batería</label>
                            <select id="filter-bateria" class="form-select">
                                <option value="">Todas</option>
                                <option value="<80">Menos del 80%</option>
                                <option value="80-90">80% - 90%</option>
                                <option value=">90">Más del 90%</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label">Oficina</label>
                            <select id="filter-oficina" class="form-select">
                                <option value="">Todas</option>
                                <?php foreach ($oficinas as $ofi): ?>
                                    <option value="<?= htmlspecialchars($ofi) ?>"><?= htmlspecialchars($ofi) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end mt-4">
                        <button onclick="loadTelefonos()" class="btn-compact btn-primary">
                            <i class="fas fa-filter"></i>
                            Aplicar Filtros
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Tabla principal -->
                <div class="lg:col-span-3">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="font-medium text-gray-700 flex items-center">
                                <i class="fas fa-mobile-alt text-blue-500 mr-2"></i>
                                Teléfonos Registrados
                            </h3>
                            <div class="text-sm text-gray-500">
                                <span id="total-registros">0</span> registros
                            </div>
                        </div>

                        <div class="mobile-table-wrapper">
                            <table class="compact-table">
                                <thead>
                                    <tr>
                                        <th class="w-12">ID</th>
                                        <th>Modelo</th>
                                        <th class="mobile-hidden">Marca</th>
                                        <th>Batería</th>
                                        <th class="mobile-hidden">IMEI</th>
                                        <th>Propietario</th>
                                        <th class="mobile-hidden">Status</th>
                                        <th class="w-28">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="telefonos-body">
                                    <tr>
                                        <td colspan="8" class="text-center py-8 text-gray-500">
                                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                                            <p class="mt-2">Cargando teléfonos...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div id="pagination" class="p-4 border-t border-gray-200"></div>
                    </div>
                </div>

                <!-- Sidebar -->

            </div>
        </main>
    </div>

    <!-- MODAL PARA AGREGAR TELÉFONO - COMPLETO E INTERACTIVO -->
    <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-2 md:p-4 w-full max-w-6xl max-h-full">
            <div class="relative bg-white rounded-lg shadow-xl max-h-[95vh] flex flex-col">
                <!-- Header -->
                <div class="sticky top-0 bg-white border-b p-5 flex items-center justify-between rounded-t-lg z-20">
                    <div>
                        <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                            <i class="fas fa-plus-circle text-blue-500 mr-2"></i>
                            Registrar Nuevo Teléfono
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Complete todos los campos obligatorios (*)</p>
                    </div>
                    <button type="button" class="text-gray-400 hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 flex justify-center items-center" data-modal-toggle="crud-modal">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="flex-1 overflow-y-auto p-5">
                    <form id="formTelefono" class="space-y-6">
                        <!-- Sección 1: Información Básica -->
                        <div class="form-section" id="section-basica">
                            <div class="section-header" onclick="toggleSection('basica')">
                                <h4>
                                    <i class="fas fa-info-circle text-blue-500"></i>
                                    Información Básica del Teléfono
                                </h4>
                                <i class="fas fa-chevron-down collapse-icon text-gray-400"></i>
                            </div>
                            <div class="section-content">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label required">Modelo</label>
                                        <input type="text" id="modelo" name="modelo" required class="form-input" placeholder="Ej: iPhone 13 Pro">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label required">Marca</label>
                                        <select id="marca" name="marca" required class="form-select">
                                            <option value="">Seleccionar marca</option>
                                            <?php foreach ($marcas as $marca): ?>
                                                <option value="<?= htmlspecialchars($marca) ?>"><?= htmlspecialchars($marca) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label required">Estado de Batería</label>
                                        <div class="range-container">
                                            <input type="range" id="bateria" name="bateria" min="0" max="100" value="100"
                                                class="range-slider" oninput="updateBateria(this.value)">
                                            <span id="bateriaValue" class="range-value">100%</span>
                                        </div>
                                        <div id="bateriaEstado" class="bateria-estado estado-alta">Excelente - No necesita cambio</div>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label required">IMEI</label>
                                        <input type="text" id="imei" name="imei" required class="form-input" placeholder="Ej: 123456789012345">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Costo</label>
                                        <input type="number" step="0.01" id="costo" name="costo" class="form-input" placeholder="Ej: 15000.00">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">PUK</label>
                                        <input type="text" id="puk" name="puk" class="form-input">
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">PIN</label>
                                        <input type="text" id="pin" name="pin" class="form-input">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 2: Ubicación -->
                        <div class="form-section" id="section-ubicacion">
                            <div class="section-header" onclick="toggleSection('ubicacion')">
                                <h4>
                                    <i class="fas fa-map-marker-alt text-green-500"></i>
                                    Ubicación Actual
                                </h4>
                                <i class="fas fa-chevron-down collapse-icon text-gray-400"></i>
                            </div>
                            <div class="section-content">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">Oficina</label>
                                        <select id="oficina" name="oficina" class="form-select">
                                            <option value="">Seleccionar oficina</option>
                                            <?php foreach ($oficinas as $ofi): ?>
                                                <option value="<?= htmlspecialchars($ofi) ?>"><?= htmlspecialchars($ofi) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Departamento Actual</label>
                                        <select id="departamento_actual" name="departamento_actual" class="form-select">
                                            <option value="">Seleccionar departamento</option>
                                            <?php foreach ($departamentos as $depto): ?>
                                                <option value="<?= htmlspecialchars($depto) ?>"><?= htmlspecialchars($depto) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 3: Propietario -->
                        <div class="form-section" id="section-propietario">
                            <div class="section-header" onclick="toggleSection('propietario')">
                                <h4>
                                    <i class="fas fa-user text-purple-500"></i>
                                    Propietario Actual
                                </h4>
                                <i class="fas fa-chevron-down collapse-icon text-gray-400"></i>
                            </div>
                            <div class="section-content">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label required">Nombre del Propietario</label>
                                        <select name="propietario_actual" required class="form-select">
                                            <option value="">Seleccionar empleado</option>
                                            <?php foreach ($empleados as $emp): ?>
                                                <option value="<?= htmlspecialchars($emp) ?>"><?= htmlspecialchars($emp) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Fecha de Asignación</label>
                                        <input type="date" name="fecha_asignacion" value="<?= date('Y-m-d') ?>" class="form-input">
                                    </div>
                                </div>

                                <!-- Números de Contacto -->
                                <div class="mt-4">
                                    <div class="switch-container">
                                        <label class="switch">
                                            <input type="checkbox" id="mismo_numero" name="mismo_numero" value="1" checked onchange="toggleNumerosContacto()">
                                            <span class="slider"></span>
                                        </label>
                                        <span class="text-sm font-medium text-gray-700">Usar el mismo número para llamadas y WhatsApp</span>
                                    </div>

                                    <div id="contacto-unico" class="mt-3">
                                        <div class="form-group">
                                            <label class="form-label">Número de Contacto</label>
                                            <input type="text" name="numero_contacto" class="form-input" placeholder="Ej: 8118221668">
                                        </div>
                                    </div>

                                    <div id="contacto-separado" class="mt-3" style="display: none;">
                                        <div class="form-grid">
                                            <div class="form-group">
                                                <label class="form-label">Número para Llamadas</label>
                                                <input type="text" name="numero_llamadas" class="form-input" placeholder="Ej: 8118221668">
                                            </div>

                                            <div class="form-group">
                                                <label class="form-label">Número para WhatsApp</label>
                                                <input type="text" name="numero_whatsapp" class="form-input" placeholder="Ej: 8123902071">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cuentas iCloud -->
                                <div class="mt-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h5 class="font-medium text-gray-700">Cuentas iCloud Asociadas</h5>
                                        <button type="button" class="btn-compact btn-secondary text-sm" onclick="agregarCuentaIcloud()">
                                            <i class="fas fa-plus"></i> Agregar Cuenta
                                        </button>
                                    </div>

                                    <div id="cuentas-icloud-container" class="space-y-3">
                                        <div class="icloud-container">
                                            <div class="form-grid">
                                                <div class="form-group">
                                                    <label class="form-label">Cuenta iCloud</label>
                                                    <input type="email" name="cuentas_icloud[]" class="form-input" placeholder="ejemplo@icloud.com">
                                                </div>
                                                <div class="form-group">
                                                    <label class="form-label">Contraseña</label>
                                                    <div class="flex">
                                                        <input type="password" name="passwords[]" class="form-input rounded-r-none">
                                                        <button type="button" class="toggle-password bg-gray-100 px-3 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-remove" onclick="eliminarCuenta(this)" style="display: none;">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección 4: Configuración -->
                        <div class="form-section" id="section-config">
                            <div class="section-header" onclick="toggleSection('config')">
                                <h4>
                                    <i class="fas fa-cog text-gray-500"></i>
                                    Configuración y Observaciones
                                </h4>
                                <i class="fas fa-chevron-down collapse-icon text-gray-400"></i>
                            </div>
                            <div class="section-content">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label class="form-label">Status del Teléfono</label>
                                        <select id="status" name="status" class="form-select">
                                            <option value="Disponible en almacen">Disponible en almacen</option>
                                            <option value="Asignado a usuario">Asignado a usuario</option>
                                            <option value="Asignacion temporal">Asignacion temporal</option>
                                            <option value="Devuelto">Devuelto</option>
                                            <option value="En revision tecnica">En revision tecnica</option>
                                            <option value="Pendiente de formateo">Pendiente de formateo</option>
                                            <option value="En venta">En venta</option>
                                            <option value="Baja definitiva">Baja definitiva</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="form-label">Posible Venta (Año)</label>
                                        <input type="number" id="posible_venta" name="posible_venta" min="2024" max="2030"
                                            class="form-input" placeholder="Ej: 2026">
                                    </div>
                                </div>

                                <div class="form-group mt-4">
                                    <label class="form-label">Observaciones</label>
                                    <textarea id="observaciones" name="observaciones" rows="3"
                                        class="form-input" placeholder="Notas adicionales sobre el teléfono..."></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="sticky bottom-0 bg-white border-t p-5 rounded-b-lg">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Campos marcados con <span class="text-red-500">*</span> son obligatorios
                        </div>
                        <div class="flex space-x-3">
                            <button type="button" data-modal-toggle="crud-modal" class="btn-compact btn-secondary">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </button>
                            <button type="submit" form="formTelefono" class="btn-compact btn-primary">
                                <i class="fas fa-save"></i>
                                Guardar Teléfono
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- MODALES RESTANTES (mantienen la misma funcionalidad) -->
    <!-- Modal para importar CSV -->
    <div id="progress-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-md max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="p-4 md:p-5">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-file-import text-blue-500 text-2xl mr-3"></i>
                        <h3 class="text-xl font-bold text-gray-900">Importar Teléfonos desde CSV</h3>
                    </div>

                    <form id="cargamasivatelefonos" enctype="multipart/form-data" class="space-y-4">
                        <div class="flex items-center justify-center w-full">
                            <label for="dropzone-file-telefonos" id="file-telefonos" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                <div id="preview-telefonos" class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4"></i>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">Haz clic para subir archivo</span>
                                    </p>
                                    <p class="text-xs text-gray-500">CSV con las columnas: Modelo, MARCA, ESTADO DE BATERIA, IMEI, OFICINA, DUEÑO ACTUAL, PW, STATUS (+), POSIBLE VENTA, PUK, PIN, LLAMADA ACTUAL, WP ACTUAL, wp b, DPTO ACTUAL</p>
                                </div>
                                <input id="dropzone-file-telefonos" type="file" name="archivo_csv" class="hidden" accept=".csv,.txt">
                            </label>
                        </div>

                        <div class="text-sm text-gray-600">
                            <p><strong>Formato requerido:</strong></p>
                            <p class="text-xs">La primera fila debe contener los encabezados exactos como en el ejemplo</p>
                        </div>

                        <div class="flex justify-end space-x-3 pt-4">
                            <button data-modal-hide="progress-modal" type="button" class="px-4 py-2 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                                Cancelar
                            </button>
                            <button type="submit" class="px-4 py-2 text-white bg-blue-700 hover:bg-blue-800 rounded-lg">
                                <i class="fas fa-upload mr-2"></i> Importar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalles -->
    <div id="modal-detalles" tabindex="-1" class="hidden fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/40">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Detalles del Teléfono</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" onclick="cerrarModalDetalles()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="contenido-detalles" class="p-6">
                    <!-- Contenido cargado dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de historial -->
<div id="modal-historial"
     tabindex="-1"
     class="hidden fixed inset-0 z-50 flex items-center justify-center overflow-y-auto overflow-x-hidden bg-black/40">
        <div class="relative p-4 w-full max-w-4xl max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-xl font-semibold text-gray-900">Historial de Propietarios</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" onclick="cerrarModalHistorial()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="contenido-historial" class="p-6">
                    <!-- Contenido cargado dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar -->
<div id="crud-edit"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40">
        <div class="relative p-4 w-full max-h-full">
            <div class="relative bg-white rounded-lg shadow-lg max-w-4xl mx-auto">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Editar Teléfono</h3>
                    <button type="button"
    onclick="cerrarEdit()"
    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 inline-flex justify-center items-center">
    <i class="fas fa-times"></i>
</button>

                </div>
                <div id="modal-container-edit" class="p-6">
                    <!-- Contenido cargado dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function limpiarBackdrop() {
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('overflow-hidden');
}

        function abrirEdit() {
    const modal = document.getElementById('crud-edit');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function cerrarEdit() {
    document.getElementById('crud-edit').classList.add('hidden');
    limpiarBackdrop();
}


    </script>

    <script>
        let currentPage = 1;
        let totalPages = 1;

        $(document).ready(function() {
            loadTelefonos();

            // Búsqueda en tiempo real
            $('input[name="search"]').on('keyup', debounce(function() {
                currentPage = 1;
                loadTelefonos();
            }, 500));

            // Filtros
            $('#filter-status, #filter-bateria, #filter-oficina').on('change', function() {
                currentPage = 1;
                loadTelefonos();
            });

            // Enviar formulario de registro
            $('#formTelefono').on('submit', function(e) {
                e.preventDefault();
                registrarTelefono();
            });

            // Importar CSV
            $('#cargamasivatelefonos').on('submit', function(e) {
                e.preventDefault();
                importarCSV();
            });

            // Inicializar secciones plegables
            initSections();

            // Inicializar números de contacto
            toggleNumerosContacto();
        });

        function initSections() {
            // Inicializar todas las secciones como expandidas
            const sections = ['basica', 'ubicacion', 'propietario', 'config'];
            sections.forEach(section => {
                const element = document.getElementById(`section-${section}`);
                if (element) {
                    element.classList.remove('section-collapsed');
                }
            });
        }

        function toggleSection(sectionId) {
            const section = document.getElementById(`section-${sectionId}`);
            section.classList.toggle('section-collapsed');
        }

        function updateBateria(value) {
            const bateriaValue = document.getElementById('bateriaValue');
            const bateriaEstado = document.getElementById('bateriaEstado');

            bateriaValue.textContent = value + '%';

            let estado = '';
            let clase = '';

            if (value < 80) {
                estado = 'Malo - Necesita cambio urgente';
                clase = 'estado-baja';
            } else if (value < 90) {
                estado = 'Regular - Considerar cambio pronto';
                clase = 'estado-media';
            } else {
                estado = 'Excelente - No necesita cambio';
                clase = 'estado-alta';
            }

            bateriaEstado.textContent = estado;
            bateriaEstado.className = `bateria-estado ${clase}`;
        }

        function toggleNumerosContacto() {
            const mismoNumero = document.getElementById('mismo_numero').checked;
            const contactoUnico = document.getElementById('contacto-unico');
            const contactoSeparado = document.getElementById('contacto-separado');

            if (mismoNumero) {
                contactoUnico.style.display = 'block';
                contactoSeparado.style.display = 'none';
            } else {
                contactoUnico.style.display = 'none';
                contactoSeparado.style.display = 'block';
            }
        }

        function agregarCuentaIcloud() {
            const container = document.getElementById('cuentas-icloud-container');
            const newIndex = container.children.length;

            const nuevaCuenta = document.createElement('div');
            nuevaCuenta.className = 'icloud-container';
            nuevaCuenta.innerHTML = `
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Cuenta iCloud</label>
                        <input type="email" name="cuentas_icloud[]" class="form-input" placeholder="ejemplo@icloud.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <div class="flex">
                            <input type="password" name="passwords[]" class="form-input rounded-r-none">
                            <button type="button" class="toggle-password bg-gray-100 px-3 border border-l-0 border-gray-300 rounded-r-md hover:bg-gray-200">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn-remove" onclick="eliminarCuenta(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;

            container.appendChild(nuevaCuenta);

            // Mostrar botón eliminar en la primera cuenta si hay más de una
            if (container.children.length > 1) {
                container.children[0].querySelector('.btn-remove').style.display = 'block';
            }
        }

        function eliminarCuenta(button) {
            const container = button.closest('.icloud-container');
            container.remove();

            // Ocultar botón eliminar de la primera cuenta si solo queda una
            const cuentaContainer = document.getElementById('cuentas-icloud-container');
            if (cuentaContainer.children.length === 1) {
                cuentaContainer.children[0].querySelector('.btn-remove').style.display = 'none';
            }
        }

        // Toggle para mostrar/ocultar contraseñas
        document.addEventListener('click', function(e) {
            if (e.target.closest('.toggle-password')) {
                const button = e.target.closest('.toggle-password');
                const input = button.previousElementSibling;
                const icon = button.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.className = 'fas fa-eye-slash';
                } else {
                    input.type = 'password';
                    icon.className = 'fas fa-eye';
                }
            }
        });

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function loadTelefonos() {
            const search = $('input[name="search"]').val();
            const status = $('#filter-status').val();
            const bateria = $('#filter-bateria').val();
            const oficina = $('#filter-oficina').val();

            $.ajax({
                url: 'celular/load_telefonos.php',
                method: 'GET',
                dataType: 'json',
                data: {
                    page: currentPage,
                    search: search,
                    status: status,
                    bateria: bateria,
                    oficina: oficina
                },
                beforeSend: function() {
                    $('#telefonos-body').html('<tr><td colspan="8" class="text-center py-8 text-gray-500"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div><p class="mt-2">Cargando teléfonos...</p></td></tr>');
                },
                success: function(response) {
                    if (response && response.success) {
                        renderTelefonos(response.data);
                        renderPagination(response.pagination);
                        $('#total-registros').text(response.pagination.totalItems || 0);
                    } else {
                        $('#telefonos-body').html('<tr><td colspan="8" class="text-center py-8 text-gray-500">' + (response ? response.message : 'Error al cargar') + '</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    $('#telefonos-body').html('<tr><td colspan="8" class="text-center py-8 text-red-500">Error al cargar los teléfonos</td></tr>');
                }
            });
        }

        function renderTelefonos(telefonos) {
            let html = '';

            if (!telefonos || telefonos.length === 0) {
                html = '<tr><td colspan="8" class="text-center py-8 text-gray-500">No se encontraron teléfonos</td></tr>';
                $('#telefonos-body').html(html);
                return;
            }

            telefonos.forEach(tel => {
                const modelo = tel.modelo || '';
                const marca = tel.marca || '';
                const bateria = parseInt(tel.bateria) || 0;
                const imei = tel.imei || '';
                const status = tel.status || 'ACTIVO';
                const duenoActual = tel.dueno_actual || 'Sin asignar';

                // Determinar color de batería
                let bateriaClass = 'bateria-alta';
                if (bateria < 80) bateriaClass = 'bateria-baja';
                else if (bateria < 90) bateriaClass = 'bateria-media';

                // Determinar clase de status (100% dinámico)
                const statusText = (status || 'SIN ESTADO').replace(/^>+/, '').trim();

                // generar nombre de clase válido a partir del texto
                const statusSlug = statusText
                    .toLowerCase()
                    .replace(/\s+/g, '-')
                    .replace(/[^a-z0-9\-]/g, '');

                const statusClass = `badge-compact badge-${statusSlug}`;

                // crear color único por estado
                let hash = 0;
                for (let i = 0; i < statusText.length; i++) {
                    hash = statusText.charCodeAt(i) + ((hash << 5) - hash);
                }
                const color = `hsl(${Math.abs(hash) % 360},70%,45%)`;

                // inyectar CSS solo una vez por estado
                if (!document.getElementById(`style-${statusSlug}`)) {
                    const style = document.createElement('style');
                    style.id = `style-${statusSlug}`;
                    style.innerHTML = `
        .badge-${statusSlug} {
            background: ${color};
            color: #fff;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            padding: 2px 8px;
            white-space: nowrap;
        }
    `;
                    document.head.appendChild(style);
                }


                html += `
                    <tr>
                        <td class="text-center font-mono text-xs text-gray-500">${tel.id}</td>
                        <td>
                            <div class="font-medium text-gray-900">${escapeHtml(modelo)}</div>
                        </td>
                        <td class="mobile-hidden">${escapeHtml(marca)}</td>
                        <td>
                            <div class="bateria-indicator ${window.innerWidth < 768 ? 'bateria-mobile' : ''}">
                                <div class="bateria-fill ${bateriaClass}" style="width: ${bateria}%"></div>
                                <div class="bateria-text">${bateria}%</div>
                            </div>
                        </td>
                        <td class="mobile-hidden">
                            ${tel.imei 
                                ? `<div class="truncate" title="${escapeHtml(tel.imei)}">${escapeHtml(tel.imei)}</div>` 
                                : `<button 
                                        class="px-2 py-1 text-xs font-mono border rounded hover:bg-gray-100"
                                        onclick="asignarImei(${tel.id})"
                                    >
                                        Asignar IMEI
                                    </button>`
                            }

                        </td>
                        <td>
                            <div class="font-medium">${escapeHtml(duenoActual)}</div>
                            ${tel.fecha_asignacion ? '<div class="text-xs text-gray-500">' + escapeHtml(tel.fecha_asignacion) + '</div>' : ''}
                        </td>
                        <td class="mobile-hidden">
                            <span class="${statusClass}">${escapeHtml(status)}</span>
                        </td>
                        <td>
                            <div class="action-grid">
                                <button onclick="verDetalles(${tel.id})" class="action-btn action-btn-view" title="Ver detalles">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="verHistorial(${tel.id})" class="action-btn action-btn-history" title="Ver historial">
                                    <i class="fas fa-history"></i>
                                </button>
                                ${<?= $canEdit ? 'true' : 'false' ?> ? `
                                <button onclick="editarTelefono(${tel.id})" class="action-btn action-btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ` : ''}
                                ${<?= $canDelete ? 'true' : 'false' ?> ? `
                                <button onclick="eliminarTelefono(${tel.id})" class="action-btn action-btn-delete" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                                ` : ''}
                            </div>
                        </td>
                    </tr>
                `;
            });

            $('#telefonos-body').html(html);
        }

        function renderPagination(pagination) {
            if (!pagination || pagination.totalPages <= 1) {
                $('#pagination').html('');
                return;
            }

            let html = '<div class="pagination-compact">';

            // Botón anterior
            if (pagination.currentPage > 1) {
                html += `<button onclick="changePage(${pagination.currentPage - 1})" class="page-btn">
                          <i class="fas fa-chevron-left"></i>
                        </button>`;
            }

            // Números de página
            for (let i = 1; i <= pagination.totalPages; i++) {
                if (i == 1 || i == pagination.totalPages || (i >= pagination.currentPage - 2 && i <= pagination.currentPage + 2)) {
                    html += `<button onclick="changePage(${i})" class="page-btn ${i == pagination.currentPage ? 'active' : ''}">
                              ${i}
                            </button>`;
                } else if (i == pagination.currentPage - 3 || i == pagination.currentPage + 3) {
                    html += '<span class="page-btn bg-transparent border-none">...</span>';
                }
            }

            // Botón siguiente
            if (pagination.currentPage < pagination.totalPages) {
                html += `<button onclick="changePage(${pagination.currentPage + 1})" class="page-btn">
                          <i class="fas fa-chevron-right"></i>
                        </button>`;
            }

            html += '</div>';
            $('#pagination').html(html);
        }

        function changePage(page) {
            currentPage = page;
            loadTelefonos();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Funciones originales manteniendo toda la funcionalidad
        function registrarTelefono() {
            const formData = new FormData(document.getElementById('formTelefono'));

            Swal.fire({
                title: 'Guardando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('celular/addCelular.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            document.getElementById('formTelefono').reset();
                            const modal = document.querySelector('[data-modal-toggle="crud-modal"]');
                            modal.click();
                            loadTelefonos();
                            updateBateria(100); // Resetear batería a 100%
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonText: 'Cerrar'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor',
                        confirmButtonText: 'Cerrar'
                    });
                });
        }

        function verDetalles(id) {
            fetch(`celular/getDetalles.php?id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('contenido-detalles').innerHTML = html;
                    document.getElementById('modal-detalles').classList.remove('hidden');
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudieron cargar los detalles',
                        confirmButtonText: 'Cerrar'
                    });
                });
        }

        function verHistorial(id) {
            fetch(`celular/getHistorial.php?id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('contenido-historial').innerHTML = html;
                    document.getElementById('modal-historial').classList.remove('hidden');
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar el historial',
                        confirmButtonText: 'Cerrar'
                    });
                });
        }



        function eliminarTelefono(id) {
            Swal.fire({
                title: '¿Eliminar teléfono?',
                text: 'Esta acción no se puede deshacer',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`celular/deleteCelular.php?id=${id}`, {
                            method: 'POST'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Eliminado!',
                                    text: data.message
                                }).then(() => loadTelefonos());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error en la conexión'
                            });
                        });
                }
            });
        }

        function confirmEmptyTable() {
            Swal.fire({
                title: '¿Vaciar toda la tabla?',
                text: 'Se eliminarán todos los teléfonos. Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, vaciar tabla',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('celular/delete_masivo.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: 'action=eliminar_todos'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: data.message
                                }).then(() => loadTelefonos());
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error en la conexión'
                            });
                        });
                }
            });
        }

        function importarCSV() {
            const formData = new FormData(document.getElementById('cargamasivatelefonos'));

            Swal.fire({
                title: 'Importando...',
                text: 'Procesando archivo CSV',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            fetch('celular/import_masivo.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    Swal.close();

                    if (data.includes('completada') || data.includes('éxito')) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Importación exitosa!',
                            html: '<div class="text-left max-h-60 overflow-y-auto">' + data.replace(/\n/g, '<br>') + '</div>',
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            const modal = document.querySelector('[data-modal-toggle="progress-modal"]');
                            modal.click();
                            loadTelefonos();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error en importación',
                            html: '<div class="text-left max-h-60 overflow-y-auto">' + data.replace(/\n/g, '<br>') + '</div>',
                            confirmButtonText: 'Cerrar'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo importar el archivo',
                        confirmButtonText: 'Cerrar'
                    });
                });
        }

        function cerrarModalDetalles() {
            document.getElementById('modal-detalles').classList.add('hidden');
        }

        function cerrarModalHistorial() {
            document.getElementById('modal-historial').classList.add('hidden');
        }

        function escapeHtml(text) {
            if (text === null || text === undefined) return '';
            const div = document.createElement('div');
            div.textContent = text.toString();
            return div.innerHTML;
        }

        function asignarImei(telefonoId) {
            Swal.fire({
                title: 'Asignar IMEI',
                input: 'text',
                inputLabel: 'Ingrese el IMEI',
                inputPlaceholder: 'IMEI del teléfono',
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Debe ingresar un IMEI';
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const imei = result.value;
                    // AJAX para enviar a backend
                    $.ajax({
                        url: 'celular/asignar_imei.php',
                        method: 'POST',
                        data: {
                            id: telefonoId,
                            imei: imei
                        },
                        success: function(res) {
                            const response = JSON.parse(res);
                            if (response.success) {
                                Swal.fire('¡Guardado!', 'IMEI asignado correctamente', 'success');
                                loadTelefonos(); // recarga la tabla
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'No se pudo asignar el IMEI', 'error');
                        }
                    });
                }
            });
        }

        function editarTelefono(id) {
            fetch(`celular/edit_celular.php?id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('modal-container-edit').innerHTML = html;

                    // Cerrar cualquier modal abierto primero
                    const modals = document.querySelectorAll('[data-modal-hide]');
                    modals.forEach(modal => {
                        modal.click();
                    });

                    // Abrir el modal de edición manualmente
                    const editModal = document.getElementById('crud-edit');
                    editModal.classList.remove('hidden');
                    editModal.setAttribute('aria-hidden', 'false');

                    // Agregar backdrop si no existe
                    if (!document.querySelector('.modal-backdrop')) {
                        const backdrop = document.createElement('div');
                        backdrop.className = 'fixed inset-0 bg-gray-900 bg-opacity-50 z-40 modal-backdrop';
                        document.body.appendChild(backdrop);
                    }

                    // Deshabilitar el scroll del body
                    document.body.style.overflow = 'hidden';

                    // Inicializar interactividad
                    setTimeout(() => {
                        // Inicializar batería
                        const bateriaInput = document.getElementById('bateria_edit');
                        if (bateriaInput) {
                            bateriaInput.addEventListener('input', function() {
                                const valor = parseInt(this.value);
                                const estado = document.getElementById('bateriaEstadoEdit');
                                const valorSpan = document.getElementById('bateriaValueEdit');

                                if (valorSpan) valorSpan.textContent = valor + '%';

                                if (valor < 80) {
                                    if (estado) {
                                        estado.textContent = 'Malo - Necesita cambio urgente';
                                        estado.style.color = '#dc2626';
                                    }
                                } else if (valor < 90) {
                                    if (estado) {
                                        estado.textContent = 'Regular - Considerar cambio pronto';
                                        estado.style.color = '#f59e0b';
                                    }
                                } else if (valor < 95) {
                                    if (estado) {
                                        estado.textContent = 'Bueno - Funciona correctamente';
                                        estado.style.color = '#10b981';
                                    }
                                } else {
                                    if (estado) {
                                        estado.textContent = 'Excelente - No necesita cambio';
                                        estado.style.color = '#10b981';
                                    }
                                }
                            });
                        }

                        // Inicializar mismo número toggle
                        const mismoNumeroInputs = document.querySelectorAll('[id^="mismo_numero_edit_"]');
                        mismoNumeroInputs.forEach(input => {
                            input.addEventListener('change', function() {
                                const id = this.id.split('_').pop();
                                const contactoUnico = document.getElementById(`contacto-unico-edit-${id}`);
                                const contactoSeparado = document.getElementById(`contacto-separado-edit-${id}`);

                                if (contactoUnico && contactoSeparado) {
                                    if (this.checked) {
                                        contactoUnico.style.display = 'grid';
                                        contactoSeparado.style.display = 'none';
                                    } else {
                                        contactoUnico.style.display = 'none';
                                        contactoSeparado.style.display = 'grid';
                                    }
                                }
                            });
                        });

                        // Inicializar botón de actualizar
                        const btnActualizar = document.getElementById('btn-actualizar-telefono');
                        if (btnActualizar) {
                            // Remover event listeners anteriores
                            const newBtn = btnActualizar.cloneNode(true);
                            btnActualizar.parentNode.replaceChild(newBtn, btnActualizar);

                            // Agregar nuevo event listener
                            newBtn.addEventListener('click', function() {
                                actualizarTelefono();
                            });
                        }

                        // Inicializar toggles de contraseña
                        document.querySelectorAll('.toggle-password').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const input = this.previousElementSibling;
                                const icon = this.querySelector('i');

                                if (input.type === 'password') {
                                    input.type = 'text';
                                    icon.className = 'fas fa-eye-slash';
                                } else {
                                    input.type = 'password';
                                    icon.className = 'fas fa-eye';
                                }
                            });
                        });
                    }, 100);
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo cargar el formulario de edición',
                        confirmButtonText: 'Cerrar'
                    });
                });
        }

        // Función para cerrar el modal de edición
        function cerrarModalEditar() {
            const editModal = document.getElementById('crud-edit');
            editModal.classList.add('hidden');
            editModal.setAttribute('aria-hidden', 'true');

            // Remover backdrop
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }

            // Restaurar scroll del body
            document.body.style.overflow = 'auto';
        }

        // Función para actualizar teléfono
        function actualizarTelefono() {
            // Recoger datos del teléfono
            const telefonoData = {
                id: document.getElementById('editTelefonoId')?.value,
                modelo: document.getElementById('modelo_edit')?.value,
                marca: document.getElementById('marca_edit')?.value,
                bateria: document.getElementById('bateria_edit')?.value,
                imei: document.getElementById('imei_edit')?.value,
                oficina: document.getElementById('oficina_edit')?.value,
                departamento_actual: document.getElementById('departamento_actual_edit')?.value,
                status: document.getElementById('status_edit')?.value,
                posible_venta: document.getElementById('posible_venta_edit')?.value,
                observaciones: document.getElementById('observaciones_edit')?.value,
                propietarios: []
            };

            // Validar campos requeridos
            if (!telefonoData.modelo || !telefonoData.marca || !telefonoData.imei) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Los campos Modelo, Marca e IMEI son requeridos',
                    confirmButtonText: 'Cerrar'
                });
                return;
            }

            // Recoger propietarios
            const propContainers = document.querySelectorAll('.propietario-container');
            propContainers.forEach((container, index) => {
                const propData = {
                    id: container.querySelector('.propietario-id')?.value || '',
                    es_actual: container.querySelector('.propietario-es-actual')?.value || '0',
                    nombre: container.querySelector('.propietario-nombre')?.value || '',
                    fecha_asignacion: container.querySelector('.propietario-fecha')?.value || '',
                    mismo_numero: container.querySelector('.propietario-mismo-numero')?.checked ? '1' : '0',
                    numero_contacto: container.querySelector('.propietario-numero-contacto')?.value || '',
                    numero_llamadas: container.querySelector('.propietario-numero-llamadas')?.value || '',
                    numero_whatsapp: container.querySelector('.propietario-numero-whatsapp')?.value || '',
                    cuentas: []
                };

                // Si es mismo número, usar numero_contacto para ambos
                if (propData.mismo_numero === '1' && propData.numero_contacto) {
                    propData.numero_llamadas = propData.numero_contacto;
                    propData.numero_whatsapp = propData.numero_contacto;
                }

                // Recoger cuentas iCloud
                const cuentaContainers = container.querySelectorAll('.cuenta-container');
                cuentaContainers.forEach(cuentaContainer => {
                    const cuentaData = {
                        id: cuentaContainer.getAttribute('data-cuenta-id') || '',
                        icloud: cuentaContainer.querySelector('.cuenta-icloud')?.value || '',
                        password: cuentaContainer.querySelector('.cuenta-password')?.value || ''
                    };

                    if (cuentaData.icloud) {
                        propData.cuentas.push(cuentaData);
                    }
                });

                telefonoData.propietarios.push(propData);
            });

            // Validar propietario actual
            const propietarioActual = telefonoData.propietarios.find(p => p.es_actual === '1');
            if (!propietarioActual || !propietarioActual.nombre) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Debe haber al menos un Propietario Actual con nombre',
                    confirmButtonText: 'Cerrar'
                });
                return;
            }

            // Mostrar loading
            Swal.fire({
                title: 'Actualizando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => Swal.showLoading()
            });

            // Enviar datos
            fetch('celular/updateTelefono.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(telefonoData)
                })
                .then(response => response.json())
                .then(data => {
                    Swal.close();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            cerrarModalEditar();
                            loadTelefonos();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                            confirmButtonText: 'Cerrar'
                        });
                    }
                })
                .catch(error => {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo actualizar el teléfono',
                        confirmButtonText: 'Cerrar'
                    });
                    console.error('Error:', error);
                });
        }
    </script>
</body>

</html>