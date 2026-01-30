<?php
include '../../includes/conexionbd.php';
header('Content-Type: application/json');

// DEBUG: guardar los datos que llegan del POST
file_put_contents('debug.txt', print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del formulario
        $id                   = $_POST['id_mobiliario'] ?? null;
        $codigo_inventario    = $_POST['codigo_inventario'] ?? '';
        $descripcion          = $_POST['descripcion'] ?? '';
        $categoria            = $_POST['categoria'] ?? '';
        $modelo               = $_POST['modelo'] ?? '';
        $marca                = $_POST['marca'] ?? '';
        $numero_serie         = $_POST['numero_serie'] ?? '';
        $condicion            = $_POST['condicion'] ?? '';
        $estado_actual        = $_POST['estado_actual'] ?? '';
        $total                = $_POST['total'] ?? 0;
        $responsable          = $_POST['responsable'] ?? '';
        $costo                = $_POST['costo'] ?? 0;
        $fecha_adquisicion    = !empty($_POST['fecha_adquisicion']) ? $_POST['fecha_adquisicion'] : null;
        $garantia_vencimiento = !empty($_POST['garantia_vencimiento']) ? $_POST['garantia_vencimiento'] : null;
        $depreciacion_anual   = $_POST['depreciacion_anual'] ?? 0;
        $disponibilidad       = $_POST['disponibilidad'] ?? '';
        $domicilio            = $_POST['id_domicilio'] ?? '';
        $notas                = $_POST['notas'] ?? '';

        // Validar campos obligatorios
        if (empty($id) || trim($descripcion) === '' || trim($modelo) === '' || trim($marca) === '') {
            echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios (Descripción, Modelo, Marca).']);
            exit();
        }

        // Verificar si hay imagen nueva
        $fotoNombre = null;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $fotoNombre = uniqid('mobi_') . "." . $ext;
            $rutaDestino = "../../uploads/mobiliario/" . $fotoNombre;
            
            // Crear directorio si no existe con permisos adecuados
            $uploadDir = "../../uploads/mobiliario/";
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    echo json_encode(['success' => false, 'message' => 'No se pudo crear el directorio de uploads.']);
                    exit();
                }
            }
            
            // Verificar que el directorio es escribible
            if (!is_writable($uploadDir)) {
                echo json_encode(['success' => false, 'message' => 'El directorio no tiene permisos de escritura.']);
                exit();
            }

            // Debug de la foto
            error_log("Procesando foto: " . $fotoNombre . " - Ruta: " . $rutaDestino);
            
            // Mover archivo con verificación
            if (!move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
                $error = error_get_last();
                error_log("Error moviendo archivo: " . $error['message']);
                echo json_encode(['success' => false, 'message' => 'No se pudo guardar la imagen. Error: ' . $error['message']]);
                exit();
            }
            
            // Verificar que el archivo se creó
            if (!file_exists($rutaDestino)) {
                echo json_encode(['success' => false, 'message' => 'La imagen no se guardó correctamente.']);
                exit();
            }
        }

        // Construir SQL dinámico
        $sql = "UPDATE mobiliario SET
            codigo_inventario = :codigo_inventario,
            descripcion = :descripcion,
            categoria = :categoria,
            modelo = :modelo,
            marca = :marca,
            numero_serie = :numero_serie,
            condicion = :condicion,
            estado_actual = :estado_actual,
            total = :total,
            responsable = :responsable,
            costo = :costo,
            fecha_adquisicion = :fecha_adquisicion,
            garantia_vencimiento = :garantia_vencimiento,
            depreciacion_anual = :depreciacion_anual,
            disponibilidad = :disponibilidad,
            id_domicilio = :domicilio,
            notas = :notas,
            ultima_actualizacion = NOW()";

        if ($fotoNombre) {
            $sql .= ", foto = :foto";
        }

        $sql .= " WHERE id_mobiliario = :id";

        $stmt = $conexion->prepare($sql);

        // Asignar parámetros
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':codigo_inventario', $codigo_inventario);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':categoria', $categoria);
        $stmt->bindParam(':modelo', $modelo);
        $stmt->bindParam(':marca', $marca);
        $stmt->bindParam(':numero_serie', $numero_serie);
        $stmt->bindParam(':condicion', $condicion);
        $stmt->bindParam(':estado_actual', $estado_actual);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':responsable', $responsable);
        $stmt->bindParam(':costo', $costo);
        $stmt->bindParam(':fecha_adquisicion', $fecha_adquisicion);
        $stmt->bindParam(':garantia_vencimiento', $garantia_vencimiento);
        $stmt->bindParam(':depreciacion_anual', $depreciacion_anual);
        $stmt->bindParam(':disponibilidad', $disponibilidad);
        $stmt->bindParam(':domicilio', $domicilio);
        $stmt->bindParam(':notas', $notas);

        if ($fotoNombre) {
            $stmt->bindParam(':foto', $fotoNombre, PDO::PARAM_STR);
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Mobiliario actualizado correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el mobiliario.']);
        }

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>