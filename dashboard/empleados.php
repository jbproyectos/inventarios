<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

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

// Obtener departamentos y puestos de la base de datos
$departamentos = $conexion->query("SELECT Id_departamento, nombre FROM departamentos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
$puestos = $conexion->query("SELECT Id_puesto, nombre FROM puestos ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Procesar operaciones CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $response = ['success' => false, 'message' => ''];
    
    try {
        switch ($_POST['action']) {
            case 'add_employee':
                $nombre = trim($_POST['nombre']);
                $departamento_id = $_POST['departamento_id'];
                $puesto_id = $_POST['puesto_id'];
                $email = trim($_POST['email']);
                $telefono = trim($_POST['telefono']);
                
                if (empty($nombre) || empty($departamento_id) || empty($puesto_id)) {
                    $response['message'] = 'Nombre, departamento y puesto son obligatorios';
                } else {
                    $stmt = $conexion->prepare("INSERT INTO Empleados (nombre, departamento, puesto, email, telefono) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt->execute([$nombre, $departamento_id, $puesto_id, $email, $telefono])) {
                        $response['success'] = true;
                        $response['message'] = 'Empleado agregado correctamente';
                    } else {
                        $response['message'] = 'Error al ejecutar la consulta';
                    }
                }
                break;
                
            case 'edit_employee':
                $id = $_POST['id'];
                $nombre = trim($_POST['nombre']);
                $departamento_id = $_POST['departamento_id'];
                $puesto_id = $_POST['puesto_id'];
                $email = trim($_POST['email']);
                $telefono = trim($_POST['telefono']);
                
                if (empty($nombre) || empty($departamento_id) || empty($puesto_id)) {
                    $response['message'] = 'Nombre, departamento y puesto son obligatorios';
                } else {
                    $stmt = $conexion->prepare("UPDATE Empleados SET nombre = ?, departamento = ?, puesto = ?, email = ?, telefono = ? WHERE id = ?");
                    if ($stmt->execute([$nombre, $departamento_id, $puesto_id, $email, $telefono, $id])) {
                        $response['success'] = true;
                        $response['message'] = 'Empleado actualizado correctamente';
                    } else {
                        $response['message'] = 'Error al ejecutar la consulta';
                    }
                }
                break;
                
            case 'delete_employee':
                $id = $_POST['id'];
                $stmt = $conexion->prepare("DELETE FROM Empleados WHERE id = ?");
                if ($stmt->execute([$id])) {
                    $response['success'] = true;
                    $response['message'] = 'Empleado eliminado correctamente';
                } else {
                    $response['message'] = 'Error al ejecutar la consulta';
                }
                break;
                
            case 'delete_all_employees':
                $stmt = $conexion->prepare("DELETE FROM Empleados");
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Todos los empleados han sido eliminados';
                } else {
                    $response['message'] = 'Error al ejecutar la consulta';
                }
                break;
        }
    } catch (PDOException $e) {
        $response['message'] = 'Error en la operación: ' . $e->getMessage();
    }
    
    // Si es una solicitud AJAX, enviar respuesta JSON
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

// Obtener empleados para mostrar en la tabla
$registrosPorPagina = 10;
$paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($paginaActual - 1) * $registrosPorPagina;

// Búsqueda
$busqueda = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT e.*, d.nombre as departamento_nombre, p.nombre as puesto_nombre 
          FROM Empleados e 
          LEFT JOIN departamentos d ON e.departamento = d.Id_departamento 
          LEFT JOIN puestos p ON e.puesto = p.Id_puesto";

if ($busqueda !== '') {
    $query .= " WHERE e.nombre LIKE :busqueda 
                OR d.nombre LIKE :busqueda 
                OR p.nombre LIKE :busqueda
                OR e.email LIKE :busqueda";
}

$query .= " ORDER BY e.id ASC LIMIT :limit OFFSET :offset";

$stmt = $conexion->prepare($query);
$stmt->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

if ($busqueda !== '') {
    $likeBusqueda = "%$busqueda%";
    $stmt->bindParam(':busqueda', $likeBusqueda, PDO::PARAM_STR);
}

$stmt->execute();
$empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Total de registros para paginación
$queryTotal = "SELECT COUNT(*) FROM Empleados e 
               LEFT JOIN departamentos d ON e.departamento = d.Id_departamento 
               LEFT JOIN puestos p ON e.puesto = p.Id_puesto";
               
if ($busqueda !== '') {
    $queryTotal .= " WHERE e.nombre LIKE :busqueda 
                     OR d.nombre LIKE :busqueda 
                     OR p.nombre LIKE :busqueda
                     OR e.email LIKE :busqueda";
}

$stmtTotal = $conexion->prepare($queryTotal);
if ($busqueda !== '') {
    $stmtTotal->bindParam(':busqueda', $likeBusqueda, PDO::PARAM_STR);
}
$stmtTotal->execute();
$totalRegistros = $stmtTotal->fetchColumn();
$totalPaginas = $totalRegistros > 0 ? ceil($totalRegistros / $registrosPorPagina) : 1;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empleados | Kabzo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="../assets/css/profile.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        /* Estilos para modales centrados */
        .modal-overlay {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            margin: auto;
            max-height: 90vh;
            overflow-y: auto;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-600">

    <!-- Navbar -->
    <?php include 'includes/nav.php' ?>

    <div class="flex">
        <!-- Sidebar -->
        <?php include 'includes/aside.php' ?>
        
        <main class="flex-1 p-4">
            <div class="grid grid-cols-1 gap-2 mb-2">
                <div class="bg-white p-4 shadow rounded-lg">
                    <h4 class="mb-4 font-semibold text-gray-600">Mis Colaboradores</h4>
                    
                    <div class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white">
                        <div class="">
                            <?php if ($canAdd): ?>
                                <!-- Modal toggle para agregar empleado -->
                                <button id="open-add-modal" 
                                    class="inline-flex text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Agregar Empleado
                                </button>
                            <?php endif; ?>

                            <?php if ($canDelete): ?>
                                <button id="open-delete-all-modal" 
                                    class="border-l border-gray-200 inline-flex text-gray-500 hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium ml-4 text-sm px-5 py-2.5 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </div>

                        <!-- Formulario de búsqueda -->
                        <form method="GET" class="mb-4">
                            <input type="text" name="search" placeholder="Buscar colaborador..." 
                                   value="<?= htmlspecialchars($busqueda) ?>" 
                                   class="p-2 border rounded w-80">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded ml-2">Buscar</button>
                        </form>
                    </div>

                    <!-- Tabla de empleados -->
                    <div id="tbl_empleados" style="overflow-x: auto;" class="h-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nombre</th>
                                    <th scope="col" class="px-6 py-3">Departamento</th>
                                    <th scope="col" class="px-6 py-3">Puesto</th>
                                    <th scope="col" class="px-6 py-3">Email</th>
                                    <th scope="col" class="px-6 py-3">Teléfono</th>
                                    <th scope="col" class="px-6 py-3">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($empleados): ?>
                                    <?php foreach ($empleados as $empleado): ?>
                                        <tr class="bg-white border-b hover:bg-gray-50">
                                            <td class="px-6 py-4"><?= htmlspecialchars($empleado['nombre']) ?></td>
                                            <td class="px-6 py-4"><?= htmlspecialchars($empleado['departamento_nombre']) ?></td>
                                            <td class="px-6 py-4"><?= htmlspecialchars($empleado['puesto_nombre']) ?></td>
                                            <td class="px-6 py-4"><?= htmlspecialchars($empleado['email'] ?? '') ?></td>
                                            <td class="px-6 py-4"><?= htmlspecialchars($empleado['telefono'] ?? '') ?></td>
                                            <td class="px-6 py-4 text-center flex justify-center space-x-2">
                                                
                                                <!-- Ver detalle -->
                                                <button data-id="<?= $empleado['id'] ?>" class="view-employee p-2 text-green-600 hover:text-green-800 rounded-full bg-green-100 hover:bg-green-200" title="Ver detalles">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </button>

                                                <!-- Editar -->
                                                <?php if ($canEdit): ?>
                                                    <button data-id="<?= $empleado['id'] ?>" 
                                                            data-nombre="<?= htmlspecialchars($empleado['nombre']) ?>"
                                                            data-departamento="<?= $empleado['departamento'] ?>"
                                                            data-puesto="<?= $empleado['puesto'] ?>"
                                                            data-email="<?= htmlspecialchars($empleado['email'] ?? '') ?>"
                                                            data-telefono="<?= htmlspecialchars($empleado['telefono'] ?? '') ?>"
                                                            class="edit-employee p-2 text-blue-600 hover:text-blue-800 rounded-full bg-blue-100 hover:bg-blue-200" title="Editar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/>
                                                        </svg>
                                                    </button>
                                                <?php endif; ?>

                                                <!-- Eliminar -->
                                                <?php if ($canDelete): ?>
                                                    <button data-id="<?= $empleado['id'] ?>" class="delete-employee p-2 text-red-600 hover:text-red-800 rounded-full bg-red-100 hover:bg-red-200" title="Eliminar">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                        </svg>
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron empleados.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Paginación -->
                        <div class="paginacion mt-4">
                            <ul class="flex justify-left space-x-2">
                                <?php if ($paginaActual > 1): ?>
                                    <li>
                                        <a href="?pagina=<?= $paginaActual - 1 ?><?= $busqueda ? '&search='.urlencode($busqueda) : '' ?>" 
                                           class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                                            &laquo;
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $rangoPaginacion = 2;
                                $startPage = max(1, $paginaActual - $rangoPaginacion);
                                $endPage = min($totalPaginas, $paginaActual + $rangoPaginacion);

                                if ($startPage > 1) {
                                    echo '<li><a href="?pagina=1'.($busqueda ? '&search='.urlencode($busqueda) : '').'" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">1</a></li>';
                                    if ($startPage > 2) {
                                        echo '<li class="px-4 py-2">...</li>';
                                    }
                                }

                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    echo '<li>';
                                    echo '<a href="?pagina=' . $i . ($busqueda ? '&search='.urlencode($busqueda) : '') . '" class="px-4 py-2 ' . ($i == $paginaActual ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300') . ' rounded-md">' . $i . '</a>';
                                    echo '</li>';
                                }

                                if ($endPage < $totalPaginas) {
                                    if ($endPage < $totalPaginas - 1) {
                                        echo '<li class="px-4 py-2">...</li>';
                                    }
                                    echo '<li><a href="?pagina=' . $totalPaginas . ($busqueda ? '&search='.urlencode($busqueda) : '') . '" class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">' . $totalPaginas . '</a></li>';
                                }
                                ?>

                                <?php if ($paginaActual < $totalPaginas): ?>
                                    <li>
                                        <a href="?pagina=<?= $paginaActual + 1 ?><?= $busqueda ? '&search='.urlencode($busqueda) : '' ?>" 
                                           class="px-4 py-2 bg-gray-200 rounded-md hover:bg-gray-300">
                                            &raquo;
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal para agregar empleado -->
    <div id="add-employee-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto modal-overlay">
        <div class="relative w-full max-w-md max-h-full modal-content">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Agregar Empleado</h3>
                    <button type="button" class="close-modal text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <form id="add-employee-form" class="p-4 md:p-5">
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label for="nombre" class="block mb-2 text-sm font-medium text-gray-900">Nombre *</label>
                            <input type="text" name="nombre" id="nombre" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5" required>
                        </div>
                        <div class="col-span-2">
                            <label for="departamento_id" class="block mb-2 text-sm font-medium text-gray-900">Departamento *</label>
                            <select name="departamento_id" id="departamento_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5" required>
                                <option value="">Seleccione un departamento</option>
                                <?php foreach ($departamentos as $depto): ?>
                                    <option value="<?= $depto['Id_departamento'] ?>"><?= htmlspecialchars($depto['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="puesto_id" class="block mb-2 text-sm font-medium text-gray-900">Puesto *</label>
                            <select name="puesto_id" id="puesto_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5" required>
                                <option value="">Seleccione un puesto</option>
                                <?php foreach ($puestos as $puesto): ?>
                                    <option value="<?= $puesto['Id_puesto'] ?>"><?= htmlspecialchars($puesto['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                            <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                        </div>
                        <div class="col-span-2">
                            <label for="telefono" class="block mb-2 text-sm font-medium text-gray-900">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                        </div>
                    </div>
                    <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Agregar Empleado
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar empleado -->
    <div id="edit-employee-modal" tabindex="-1" aria-hidden="true" class="hidden fixed inset-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto modal-overlay">
        <div class="relative w-full max-w-md max-h-full modal-content">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Editar Empleado</h3>
                    <button type="button" class="close-modal text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                    </button>
                </div>
                <form id="edit-employee-form" class="p-4 md:p-5">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="grid gap-4 mb-4 grid-cols-2">
                        <div class="col-span-2">
                            <label for="edit_nombre" class="block mb-2 text-sm font-medium text-gray-900">Nombre *</label>
                            <input type="text" name="nombre" id="edit_nombre" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5" required>
                        </div>
                        <div class="col-span-2">
                            <label for="edit_departamento_id" class="block mb-2 text-sm font-medium text-gray-900">Departamento *</label>
                            <select name="departamento_id" id="edit_departamento_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5" required>
                                <option value="">Seleccione un departamento</option>
                                <?php foreach ($departamentos as $depto): ?>
                                    <option value="<?= $depto['Id_departamento'] ?>"><?= htmlspecialchars($depto['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="edit_puesto_id" class="block mb-2 text-sm font-medium text-gray-900">Puesto *</label>
                            <select name="puesto_id" id="edit_puesto_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5" required>
                                <option value="">Seleccione un puesto</option>
                                <?php foreach ($puestos as $puesto): ?>
                                    <option value="<?= $puesto['Id_puesto'] ?>"><?= htmlspecialchars($puesto['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-span-2">
                            <label for="edit_email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                            <input type="email" name="email" id="edit_email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                        </div>
                        <div class="col-span-2">
                            <label for="edit_telefono" class="block mb-2 text-sm font-medium text-gray-900">Teléfono</label>
                            <input type="text" name="telefono" id="edit_telefono" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5">
                        </div>
                    </div>
                    <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                        <svg class="me-1 -ms-1 w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Actualizar Empleado
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para eliminar todos -->
    <div id="delete-all-modal" tabindex="-1" class="hidden fixed inset-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto modal-overlay">
        <div class="relative w-full max-w-md max-h-full modal-content">
            <div class="relative bg-white rounded-lg shadow">
                <div class="p-4 md:p-5 text-center">
                    <svg class="mx-auto mb-4 text-gray-400 w-12 h-12" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                    </svg>
                    <h3 class="mb-5 text-lg font-normal text-gray-500">¿Está seguro que desea eliminar todos los empleados?</h3>
                    <button id="delete-all-employees" type="button" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center me-2">
                        Sí, eliminar todos
                    </button>
                    <button type="button" class="close-modal text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Backdrop para modales -->
    <div id="modal-backdrop" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-40"></div>

<script>
        $(document).ready(function() {
            // Función para mostrar modal
            function showModal(modalId) {
                $('#' + modalId).removeClass('hidden');
                $('#modal-backdrop').removeClass('hidden');
                $('body').addClass('overflow-hidden');
            }

            // Función para cerrar modal
            function closeModal() {
                $('.fixed.inset-0').addClass('hidden');
                $('#modal-backdrop').addClass('hidden');
                $('body').removeClass('overflow-hidden');
            }

            // Abrir modal de agregar
            $('#open-add-modal').on('click', function() {
                showModal('add-employee-modal');
            });

            // Abrir modal de eliminar todos
            $('#open-delete-all-modal').on('click', function() {
                showModal('delete-all-modal');
            });

            // Cerrar modales
            $('.close-modal').on('click', function() {
                closeModal();
            });

            // Cerrar modal al hacer click en el backdrop
            $('#modal-backdrop').on('click', function() {
                closeModal();
            });

            // Agregar empleado
            $('#add-employee-form').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('action', 'add_employee');
                formData.append('ajax', 'true');
                
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Agregando empleado',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    type: 'POST',
                    url: '',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();
                        try {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (data.success) {
                                closeModal();
                                $('#add-employee-form')[0].reset();
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: data.message,
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message,
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        } catch (e) {
                            console.error('Error:', e, response);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al procesar la respuesta',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        console.error('AJAX Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo conectar con el servidor',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            });

            // Editar empleado - Abrir modal
            $('.edit-employee').on('click', function() {
                const id = $(this).data('id');
                const nombre = $(this).data('nombre');
                const departamento = $(this).data('departamento');
                const puesto = $(this).data('puesto');
                const email = $(this).data('email');
                const telefono = $(this).data('telefono');
                
                $('#edit_id').val(id);
                $('#edit_nombre').val(nombre);
                $('#edit_departamento_id').val(departamento);
                $('#edit_puesto_id').val(puesto);
                $('#edit_email').val(email);
                $('#edit_telefono').val(telefono);
                
                showModal('edit-employee-modal');
            });

            // Editar empleado - Enviar formulario
            $('#edit-employee-form').on('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('action', 'edit_employee');
                formData.append('ajax', 'true');
                
                Swal.fire({
                    title: 'Procesando...',
                    text: 'Actualizando empleado',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    type: 'POST',
                    url: '',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        Swal.close();
                        try {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            
                            if (data.success) {
                                closeModal();
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Éxito!',
                                    text: data.message,
                                    confirmButtonText: 'Aceptar'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message,
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        } catch (e) {
                            console.error('Error:', e, response);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error al procesar la respuesta',
                                confirmButtonText: 'Aceptar'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();
                        console.error('AJAX Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo conectar con el servidor',
                            confirmButtonText: 'Aceptar'
                        });
                    }
                });
            });

            // Eliminar empleado individual
            $('.delete-employee').on('click', function() {
                const employeeId = $(this).data('id');
                const employeeName = $(this).closest('tr').find('td:first').text();
                
                Swal.fire({
                    title: '¿Está seguro?',
                    html: `¿Está seguro que desea eliminar al empleado: <strong>${employeeName}</strong>?<br><br>Esta acción no se puede deshacer.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('action', 'delete_employee');
                        formData.append('id', employeeId);
                        formData.append('ajax', 'true');
                        
                        Swal.fire({
                            title: 'Procesando...',
                            text: 'Eliminando empleado',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        $.ajax({
                            type: 'POST',
                            url: '',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.close();
                                try {
                                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                                    
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: '¡Eliminado!',
                                            text: data.message,
                                            confirmButtonText: 'Aceptar'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message,
                                            confirmButtonText: 'Aceptar'
                                        });
                                    }
                                } catch (e) {
                                    console.error('Error:', e, response);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Error al procesar la respuesta',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.close();
                                console.error('AJAX Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error de conexión',
                                    text: 'No se pudo conectar con el servidor',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        });
                    }
                });
            });

            // Eliminar todos los empleados
            $('#delete-all-employees').on('click', function() {
                closeModal();
                
                Swal.fire({
                    title: '¿Está completamente seguro?',
                    html: '¿Está seguro que desea eliminar <strong>TODOS</strong> los empleados?<br><br><span class="text-red-600 font-bold">Esta acción es irreversible y eliminará todos los registros.</span>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar todo',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const formData = new FormData();
                        formData.append('action', 'delete_all_employees');
                        formData.append('ajax', 'true');
                        
                        Swal.fire({
                            title: 'Procesando...',
                            text: 'Eliminando todos los empleados',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        
                        $.ajax({
                            type: 'POST',
                            url: '',
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                Swal.close();
                                try {
                                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                                    
                                    if (data.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: '¡Eliminados!',
                                            text: data.message,
                                            confirmButtonText: 'Aceptar'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message,
                                            confirmButtonText: 'Aceptar'
                                        });
                                    }
                                } catch (e) {
                                    console.error('Error:', e, response);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Error al procesar la respuesta',
                                        confirmButtonText: 'Aceptar'
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.close();
                                console.error('AJAX Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error de conexión',
                                    text: 'No se pudo conectar con el servidor',
                                    confirmButtonText: 'Aceptar'
                                });
                            }
                        });
                    }
                });
            });

            // Ver detalles del empleado
            $('.view-employee').on('click', function() {
                const employeeId = $(this).data('id');
                const employeeName = $(this).closest('tr').find('td:first').text();
                const department = $(this).closest('tr').find('td:nth-child(2)').text();
                const position = $(this).closest('tr').find('td:nth-child(3)').text();
                const email = $(this).closest('tr').find('td:nth-child(4)').text();
                const phone = $(this).closest('tr').find('td:nth-child(5)').text();
                
                Swal.fire({
                    title: `Detalles del Empleado`,
                    html: `
                        <div class="text-left">
                            <p><strong>Nombre:</strong> ${employeeName}</p>
                            <p><strong>Departamento:</strong> ${department}</p>
                            <p><strong>Puesto:</strong> ${position}</p>
                            <p><strong>Email:</strong> ${email || 'No especificado'}</p>
                            <p><strong>Teléfono:</strong> ${phone || 'No especificado'}</p>
                            <p><strong>ID:</strong> ${employeeId}</p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Cerrar',
                    width: '600px'
                });
            });
        });
    </script>
</body>
</html>