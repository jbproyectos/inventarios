<?php
include "../includes/conexionbd.php";

try {
    // Obtener datos enviados por POST
    $input = json_decode(file_get_contents('php://input'), true);

    // Si se solicita llenar el campo de tablas afectadas
    if (isset($input['accion']) && $input['accion'] === 'cargar_tablas') {
        $stmt = $conexion->prepare("SELECT DISTINCT tabla_afectada FROM auditoria");
        $stmt->execute();
        $tablas = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo json_encode($tablas);
        exit;
    }

    // Construir la consulta principal
    $query = "SELECT id, usuario, tabla_afectada, operacion, registro_id, fecha FROM auditoria";
    $params = [];

    // Filtros dinámicos
    if (!empty($input['fecha_inicio']) && !empty($input['fecha_fin'])) {
        $query .= " WHERE fecha BETWEEN :fecha_inicio AND :fecha_fin";
        $params[':fecha_inicio'] = $input['fecha_inicio'];
        $params[':fecha_fin'] = $input['fecha_fin'];
    }

    if (!empty($input['operacion'])) {
        $query .= (strpos($query, 'WHERE') !== false ? " AND" : " WHERE") . " operacion = :operacion";
        $params[':operacion'] = $input['operacion'];
    }

    if (!empty($input['tabla_afectada'])) {
        $query .= (strpos($query, 'WHERE') !== false ? " AND" : " WHERE") . " tabla_afectada = :tabla_afectada";
        $params[':tabla_afectada'] = $input['tabla_afectada'];
    }

    // Paginación: Obtener la página actual y el número de registros por página
    $pagina = isset($input['pagina']) ? (int)$input['pagina'] : 1;
    $registros_por_pagina = 15; // Cambia este valor según sea necesario

    // Calcular el OFFSET para la consulta SQL
    $offset = ($pagina - 1) * $registros_por_pagina;

    // Agregar orden, límite y OFFSET (paginación)
    $query .= " ORDER BY fecha DESC LIMIT :limite OFFSET :offset";

    // Prepara la consulta
    $stmt = $conexion->prepare($query);
    
    // Asignar los valores de límite y offset como enteros
    $stmt->bindParam(':limite', $registros_por_pagina, PDO::PARAM_INT);
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

    // Asignar el resto de los parámetros
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }

    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total de registros (para paginación)
    $total = $conexion->query("SELECT COUNT(*) FROM auditoria")->fetchColumn();

    echo json_encode([
        'data' => $data,
        'total' => $total,
        'pagina' => $pagina,
        'registros_por_pagina' => $registros_por_pagina
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
