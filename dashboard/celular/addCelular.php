<?php
include '../../includes/middleware.php';
include "../../includes/conexionbd.php";

try {
    $conexion->beginTransaction();
    
    // Insertar teléfono
    $stmt = $conexion->prepare("
        INSERT INTO telefonos (
            modelo, marca, bateria, costo, oficina, imei, status,
            posible_venta, puk, pin, observaciones, departamento_actual
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_POST['modelo'],
        $_POST['marca'],
        $_POST['bateria'],
        $_POST['costo'] ?? null,
        $_POST['oficina'] ?? null,
        $_POST['imei'],
        $_POST['status'] ?? 'ACTIVO',
        $_POST['posible_venta'] ?? null,
        $_POST['puk'] ?? null,
        $_POST['pin'] ?? null,
        $_POST['observaciones'] ?? null,
        $_POST['departamento_actual'] ?? null
    ]);
    
    $telefono_id = $conexion->lastInsertId();
    
    // Insertar propietario actual
    $stmtProp = $conexion->prepare("
        INSERT INTO historial_propietarios (
            telefono_id, nombre, fecha_asignacion, es_actual,
            mismo_numero, numero_contacto, numero_llamadas, numero_whatsapp
        ) VALUES (?, ?, ?, 1, ?, ?, ?, ?)
    ");
    
    $stmtProp->execute([
        $telefono_id,
        $_POST['propietario_actual'],
        $_POST['fecha_asignacion'] ?? date('Y-m-d'),
        $_POST['mismo_numero'] ?? 1,
        $_POST['numero_contacto'] ?? null,
        $_POST['mismo_numero'] == 1 ? null : ($_POST['numero_llamadas'] ?? null),
        $_POST['mismo_numero'] == 1 ? null : ($_POST['numero_whatsapp'] ?? null)
    ]);
    
    // Insertar cuentas iCloud si existen
    if (isset($_POST['cuentas_icloud']) && is_array($_POST['cuentas_icloud'])) {
        $stmtCuenta = $conexion->prepare("
            INSERT INTO cuentas_icloud (telefono_id, icloud, password, es_actual)
            VALUES (?, ?, ?, 1)
        ");
        
        foreach ($_POST['cuentas_icloud'] as $index => $icloud) {
            if (!empty($icloud) && !empty($_POST['passwords'][$index])) {
                $stmtCuenta->execute([
                    $telefono_id,
                    $icloud,
                    $_POST['passwords'][$index]
                ]);
            }
        }
    }
    
    $conexion->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Teléfono registrado exitosamente'
    ]);
    
} catch (PDOException $e) {
    $conexion->rollBack();
    
    echo json_encode([
        'success' => false,
        'message' => 'Error al registrar el teléfono: ' . $e->getMessage()
    ]);
}
?>