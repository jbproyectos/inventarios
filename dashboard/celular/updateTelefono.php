<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
include '../../includes/conexionbd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

try {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) throw new Exception('JSON inválido');

    $id = (int)($data['id'] ?? 0);
    $modelo = trim($data['modelo'] ?? '');
    $marca = trim($data['marca'] ?? '');
    $bateria = (int)($data['bateria'] ?? 100);
    $imei = trim($data['imei'] ?? '0');
    $oficina = trim($data['oficina'] ?? '');
    $departamento_actual = trim($data['departamento_actual'] ?? '');
    $status = strtoupper(trim($data['status'] ?? 'ACTIVO'));
    $posible_venta = trim($data['posible_venta'] ?? '');
    $observaciones = trim($data['observaciones'] ?? '');
    $propietariosData = $data['propietarios'] ?? [];

    if ($id <= 0 || $modelo === '' || $marca === '') {
        throw new Exception('Datos obligatorios faltantes');
    }

    // Validar IMEI duplicado (excepto 0)
    if ($imei !== '0') {
        $stmtCheck = $conexion->prepare("SELECT COUNT(*) FROM telefonos WHERE imei = ? AND id != ?");
        $stmtCheck->execute([$imei, $id]);
        if ($stmtCheck->fetchColumn() > 0) throw new Exception('IMEI duplicado');
    }

    $conexion->beginTransaction();

    // Obtener el estado actual del teléfono antes de actualizar
    $stmtEstadoActual = $conexion->prepare("SELECT status FROM telefonos WHERE id = ?");
    $stmtEstadoActual->execute([$id]);
    $estadoActual = $stmtEstadoActual->fetchColumn();

    // Actualizar teléfono
    $stmt = $conexion->prepare("
        UPDATE telefonos SET
            modelo = :modelo,
            marca = :marca,
            bateria = :bateria,
            imei = :imei,
            oficina = :oficina,
            departamento_actual = :departamento_actual,
            status = :status,
            posible_venta = :posible_venta,
            observaciones = :observaciones,
            updated_at = NOW()
        WHERE id = :id
    ");
    $stmt->execute([
        ':modelo'=>$modelo,
        ':marca'=>$marca,
        ':bateria'=>$bateria,
        ':imei'=>$imei,
        ':oficina'=>$oficina,
        ':departamento_actual'=>$departamento_actual,
        ':status'=>$status,
        ':posible_venta'=>$posible_venta,
        ':observaciones'=>$observaciones,
        ':id'=>$id
    ]);

    // Primero, obtener el propietario actual ANTES de cualquier cambio
    $stmtPropActualAntes = $conexion->prepare("
        SELECT * FROM historial_propietarios 
        WHERE telefono_id = ? AND es_actual = 1 
        ORDER BY id DESC LIMIT 1
    ");
    $stmtPropActualAntes->execute([$id]);
    $propActualAntes = $stmtPropActualAntes->fetch(PDO::FETCH_ASSOC);
    
    // Verificar si el estado ha cambiado y es DEVUELTO o ASIGNADO A USUARIO
    $estadoCambiado = ($estadoActual !== $status);
    $necesitaDuplicacion = $estadoCambiado && 
                          ($status === 'DEVUELTO' || $status === 'ASIGNADO A USUARIO');

    if ($necesitaDuplicacion && $propActualAntes) {
        // IMPORTANTE: Cambiar es_actual a 0 para el registro anterior ANTES de procesar el formulario
        $stmtUpdateAnterior = $conexion->prepare("
            UPDATE historial_propietarios 
            SET es_actual = 0 
            WHERE id = ?
        ");
        $stmtUpdateAnterior->execute([$propActualAntes['id']]);

        // Determinar el nombre para el nuevo registro
        $nuevoNombre = '';
        $nuevoNumeroContacto = $propActualAntes['numero_contacto'] ?? '';
        $nuevoNumeroLlamadas = $propActualAntes['numero_llamadas'] ?? '';
        $nuevoNumeroWhatsapp = $propActualAntes['numero_whatsapp'] ?? '';
        
        if ($status === 'DEVUELTO') {
            $nuevoNombre = 'VACANTE';
        } elseif ($status === 'ASIGNADO A USUARIO') {
            // Para ASIGNADO A USUARIO, buscar si hay un nombre en los datos del formulario
            $nombreDelFormulario = '';
            foreach ($propietariosData as $propData) {
                if (isset($propData['es_actual']) && $propData['es_actual'] == '1') {
                    $nombreDelFormulario = trim($propData['nombre'] ?? '');
                    break;
                }
            }
            
            // Usar el nombre del formulario si existe, de lo contrario usar el nombre anterior
            $nuevoNombre = !empty($nombreDelFormulario) ? $nombreDelFormulario : $propActualAntes['nombre'];
        }

        // Insertar nuevo registro duplicado con es_actual = 1
        if (!empty($nuevoNombre)) {
            $stmtInsertDuplicado = $conexion->prepare("
                INSERT INTO historial_propietarios (
                    telefono_id,
                    nombre,
                    fecha_asignacion,
                    mismo_numero,
                    numero_contacto,
                    numero_llamadas,
                    numero_whatsapp,
                    es_actual
                ) VALUES (?, ?, NOW(), ?, ?, ?, ?, 1)
            ");
            
            // Verificar si los números siguen siendo los mismos
            $mismoNumero = (
                $nuevoNumeroContacto === $propActualAntes['numero_contacto'] &&
                $nuevoNumeroLlamadas === $propActualAntes['numero_llamadas'] &&
                $nuevoNumeroWhatsapp === $propActualAntes['numero_whatsapp']
            ) ? 1 : 0;
            
            $stmtInsertDuplicado->execute([
                $id,
                $nuevoNombre,
                $mismoNumero,
                $nuevoNumeroContacto,
                $nuevoNumeroLlamadas,
                $nuevoNumeroWhatsapp
            ]);
            
            // Obtener el ID del nuevo registro insertado
            $nuevoPropId = $conexion->lastInsertId();
            
            // Si estamos en modo de duplicación, NO procesar los propietarios del formulario
            // porque ya creamos el registro duplicado
            $propietariosData = []; // Vaciamos el array para no procesar duplicados
        }
    }

    // Ahora procesar los propietarios del formulario (solo si NO hubo duplicación automática)
    if (empty($propietariosData) || !$necesitaDuplicacion) {
        // Resetear todos a es_actual = 0 (excepto si acabamos de hacer duplicación)
        if (!$necesitaDuplicacion) {
            $stmtResetActual = $conexion->prepare("
                UPDATE historial_propietarios 
                SET es_actual = 0 
                WHERE telefono_id = ?
            ");
            $stmtResetActual->execute([$id]);
        }
        
        // Procesar cada propietario del formulario
        foreach ($propietariosData as $propData) {
            $propId = !empty($propData['id']) ? (int)$propData['id'] : null;
            $nombre = trim($propData['nombre'] ?? '');
            $fechaAsignacion = trim($propData['fecha_asignacion'] ?? date('Y-m-d'));
            $mismoNumero = isset($propData['mismo_numero']) ? (int)$propData['mismo_numero'] : 0;
            $numeroContacto = trim($propData['numero_contacto'] ?? '');
            $numeroLlamadas = trim($propData['numero_llamadas'] ?? '');
            $numeroWhatsapp = trim($propData['numero_whatsapp'] ?? '');
            $esActual = isset($propData['es_actual']) ? (int)$propData['es_actual'] : 0;
            $cuentas = $propData['cuentas'] ?? [];

            if (empty($nombre)) {
                continue; // Saltar si no hay nombre
            }

            if ($mismoNumero == 1 && !empty($numeroContacto)) {
                $numeroLlamadas = $numeroContacto;
                $numeroWhatsapp = $numeroContacto;
            }

            if ($propId) {
                // Actualizar propietario existente
                $stmtUpdateProp = $conexion->prepare("
                    UPDATE historial_propietarios SET
                        nombre = ?,
                        fecha_asignacion = ?,
                        mismo_numero = ?,
                        numero_contacto = ?,
                        numero_llamadas = ?,
                        numero_whatsapp = ?,
                        es_actual = ?
                    WHERE id = ? AND telefono_id = ?
                ");
                $stmtUpdateProp->execute([
                    $nombre,
                    $fechaAsignacion,
                    $mismoNumero,
                    $numeroContacto,
                    $numeroLlamadas,
                    $numeroWhatsapp,
                    $esActual,
                    $propId,
                    $id
                ]);
            } else {
                // Insertar nuevo propietario
                $stmtInsertProp = $conexion->prepare("
                    INSERT INTO historial_propietarios (
                        telefono_id,
                        nombre,
                        fecha_asignacion,
                        mismo_numero,
                        numero_contacto,
                        numero_llamadas,
                        numero_whatsapp,
                        es_actual
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmtInsertProp->execute([
                    $id,
                    $nombre,
                    $fechaAsignacion,
                    $mismoNumero,
                    $numeroContacto,
                    $numeroLlamadas,
                    $numeroWhatsapp,
                    $esActual
                ]);
                $propId = $conexion->lastInsertId();
            }

            // Procesar cuentas iCloud
            if ($propId) {
                // Primero eliminar cuentas existentes para este propietario
                $stmtDeleteCuentas = $conexion->prepare("
                    DELETE FROM cuentas_icloud 
                    WHERE telefono_id = ? AND propietario_id = ?
                ");
                $stmtDeleteCuentas->execute([$id, $propId]);

                // Insertar nuevas cuentas
                foreach ($cuentas as $cuentaData) {
                    $icloud = trim($cuentaData['icloud'] ?? '');
                    $password = trim($cuentaData['password'] ?? '');

                    if (!empty($icloud)) {
                        $stmtInsertCuenta = $conexion->prepare("
                            INSERT INTO cuentas_icloud (
                                telefono_id,
                                propietario_id,
                                icloud,
                                password
                            ) VALUES (?, ?, ?, ?)
                        ");
                        $stmtInsertCuenta->execute([$id, $propId, $icloud, $password]);
                    }
                }
            }
        }
    }

    $conexion->commit();
    echo json_encode(['success'=>true,'message'=>'Teléfono actualizado correctamente']);

} catch (Throwable $e) {
    if ($conexion->inTransaction()) $conexion->rollBack();
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}