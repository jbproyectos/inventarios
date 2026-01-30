<?php
include '../../includes/conexionbd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar datos del formulario
    $codigo_inventario = $_POST['codigo_inventario'];
    $descripcion = $_POST['descripcion'];
    $categoria = $_POST['categoria'];
    $modelo = $_POST['modelo'] ?? null;
    $marca = $_POST['marca'] ?? null;
    $numero_serie = $_POST['numero_serie'] ?? null;
    $condicion = $_POST['condicion'] ?? 'bueno';
    $estado_actual = $_POST['estado_actual'] ?? 'activo';
    $total = $_POST['total'] ?? 1;
    $responsable = $_POST['responsable'] ?? null;
    $costo = $_POST['costo'] ?? null;
    $fecha_adquisicion = !empty($_POST['fecha_adquisicion']) ? $_POST['fecha_adquisicion'] : null;
    $garantia_vencimiento = !empty($_POST['garantia_vencimiento']) ? $_POST['garantia_vencimiento'] : null;
    $depreciacion_anual = $_POST['depreciacion_anual'] ?? null;
    $disponibilidad = $_POST['disponibilidad'] ?? 'disponible';
    $domicilio = $_POST['domicilio'] ?? 'domicilio';
    $notas = $_POST['notas'] ?? null;

    // Manejo de foto
    $foto_nombre = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $foto_nombre = uniqid('mob_') . '_' . basename($_FILES['foto']['name']);
        $foto_ruta = '../../uploads/mobiliario/' . $foto_nombre;
        if (!move_uploaded_file($foto_tmp, $foto_ruta)) {
            $foto_nombre = null; // Si no se pudo subir, se guarda como null
        }
    }

    // Validar campos obligatorios
    if (empty($codigo_inventario) || empty($descripcion) || empty($categoria)) {
        echo json_encode([
            'success' => false,
            'message' => 'Código de inventario, descripción y categoría son obligatorios.'
        ]);
        exit;
    }

    try {
        $sql = "INSERT INTO mobiliario (
                    codigo_inventario, descripcion, categoria, modelo, marca, numero_serie,
                    condicion, estado_actual, total, responsable, costo, fecha_adquisicion,
                    garantia_vencimiento, depreciacion_anual, disponibilidad, id_domicilio, notas, foto
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $codigo_inventario, $descripcion, $categoria, $modelo, $marca, $numero_serie,
            $condicion, $estado_actual, $total, $responsable, $costo, $fecha_adquisicion,
            $garantia_vencimiento, $depreciacion_anual, $disponibilidad, $domicilio, $notas, $foto_nombre
        ]);

        $response = [
            'success' => true,
            'message' => 'Mobiliario registrado correctamente.'
        ];
    } catch (PDOException $e) {
        $response = [
            'success' => false,
            'message' => 'Error en la base de datos: ' . $e->getMessage()
        ];
    }

    $conexion = null;
    echo json_encode($response);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No se recibieron datos por POST.'
    ]);
}
