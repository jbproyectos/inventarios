<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../../includes/middleware.php';
include "../../includes/conexionbd.php";

$page = $_GET['page'] ?? 1;
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$bateria = $_GET['bateria'] ?? '';
$oficina = $_GET['oficina'] ?? '';

$registrosPorPagina = 10;
$offset = ($page - 1) * $registrosPorPagina;

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(t.modelo LIKE ? OR t.marca LIKE ? OR t.imei LIKE ? OR hp.nombre LIKE ? OR t.oficina LIKE ?)";
    $likeSearch = "%$search%";
    array_push($params, $likeSearch, $likeSearch, $likeSearch, $likeSearch, $likeSearch);
}

if (!empty($status)) {
    $where[] = "t.status = ?";
    $params[] = $status;
}

if (!empty($bateria)) {
    if ($bateria == '<80') {
        $where[] = "t.bateria < 80";
    } elseif ($bateria == '80-90') {
        $where[] = "t.bateria BETWEEN 80 AND 90";
    } elseif ($bateria == '>90') {
        $where[] = "t.bateria > 90";
    }
}

if (!empty($oficina)) {
    $where[] = "t.oficina = ?";
    $params[] = $oficina;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

try {
    // Consulta para obtener datos
    $query = "
    SELECT 
        t.id,
        t.modelo,
        t.marca,
        t.bateria,
        t.imei,
        t.oficina,
        t.departamento_actual,
        t.status,
        t.posible_venta,
        hp.nombre AS dueno_actual,  -- CAMBIADO: sin tilde para compatibilidad
        hp.fecha_asignacion
    FROM telefonos t
    LEFT JOIN historial_propietarios hp
        ON hp.telefono_id = t.id
       AND hp.es_actual = 1
    $whereClause
    ORDER BY t.id DESC
    LIMIT $registrosPorPagina OFFSET $offset
    ";

    $stmt = $conexion->prepare($query);
    
    if (!empty($params)) {
        $stmt->execute($params);
    } else {
        $stmt->execute();
    }
    
    $telefonos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debug: ver qué datos estamos obteniendo
    error_log("Datos obtenidos: " . print_r($telefonos, true));

    // Total de registros
    $queryTotal = "
    SELECT COUNT(*) as total
    FROM telefonos t
    LEFT JOIN historial_propietarios hp
        ON hp.telefono_id = t.id
       AND hp.es_actual = 1
    $whereClause
    ";

    $stmtTotal = $conexion->prepare($queryTotal);
    if (!empty($params)) {
        $stmtTotal->execute($params);
    } else {
        $stmtTotal->execute();
    }
    $total = $stmtTotal->fetchColumn();

    // Asegurarse de que todas las columnas necesarias existan
    foreach ($telefonos as &$tel) {
        $tel['dueno_actual'] = $tel['dueno_actual'] ?? null;  // CAMBIADO: sin tilde
        $tel['fecha_asignacion'] = $tel['fecha_asignacion'] ?? null;
        $tel['posible_venta'] = $tel['posible_venta'] ?? null;
        $tel['departamento_actual'] = $tel['departamento_actual'] ?? null;
        $tel['marca'] = $tel['marca'] ?? 'No especificada';
        $tel['oficina'] = $tel['oficina'] ?? null;
        $tel['bateria'] = $tel['bateria'] ?? 0;
        $tel['status'] = $tel['status'] ?? 'ACTIVO';
        
        // Debug cada teléfono
        error_log("Teléfono procesado - Marca: " . $tel['marca'] . ", Modelo: " . $tel['modelo']);
    }

    echo json_encode([
        'success' => true,
        'data' => $telefonos,
        'pagination' => [
            'currentPage' => (int)$page,
            'totalPages' => ceil($total / $registrosPorPagina),
            'total' => $total,
            'start' => min($offset + 1, $total),
            'end' => min($offset + $registrosPorPagina, $total)
        ]
    ]);

} catch (PDOException $e) {
    error_log("Error en load_telefonos.php: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'Error en la consulta: ' . $e->getMessage(),
        'data' => [],
        'pagination' => [
            'currentPage' => 1,
            'totalPages' => 0,
            'total' => 0,
            'start' => 0,
            'end' => 0
        ]
    ]);
}
?>