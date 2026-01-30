<?php
include '../../includes/middleware.php';
include "../../includes/conexionbd.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['archivo_csv'])) {
    die('Método no permitido o archivo no enviado');
}

$archivo = $_FILES['archivo_csv']['tmp_name'];

if (!file_exists($archivo)) {
    die('Archivo no encontrado');
}

// Leer archivo CSV
$handle = fopen($archivo, 'r');
if (!$handle) {
    die('No se pudo abrir el archivo');
}

// Obtener encabezados
$headers = fgetcsv($handle);
if (!$headers) {
    die('Archivo CSV vacío o corrupto');
}

// Verificar encabezados mínimos
$requiredHeaders = ['Modelo', 'MARCA', 'ESTADO DE BATERIA', 'IMEI'];
foreach ($requiredHeaders as $required) {
    if (!in_array($required, $headers)) {
        die("Falta el encabezado requerido: $required");
    }
}

$conexion->beginTransaction();
$successCount = 0;
$errorCount = 0;
$errors = [];

try {
    while (($data = fgetcsv($handle)) !== false) {
        if (count($data) !== count($headers)) {
            $errors[] = "Línea con número incorrecto de columnas";
            $errorCount++;
            continue;
        }

        // Mapear datos a array asociativo
        $row = array_combine($headers, $data);

        // Limpiar y validar datos
        $modelo = trim($row['Modelo']);
        $marca = trim($row['MARCA']);
        $bateria = intval(str_replace('%', '', trim($row['ESTADO DE BATERIA'])));
        $costo = (int) str_replace([',', '$', ' '], '', $row['COSTO']);
        $imei = trim($row['IMEI']);
        $oficina = isset($row['OFICINA']) ? trim($row['OFICINA']) : null;
        $propietario = isset($row['DUEÑO ACTUAL']) ? trim($row['DUEÑO ACTUAL']) : null;
        $password = isset($row['PW']) ? trim($row['PW']) : null;
        $status = isset($row['STATUS (+)']) ? trim($row['STATUS (+)']) : 'ACTIVO';
        $posible_venta = isset($row['POSIBLE VENTA']) ? trim($row['POSIBLE VENTA']) : null;
        $puk = isset($row['PUK']) ? trim($row['PUK']) : null;
        $pin = isset($row['PIN']) ? trim($row['PIN']) : null;
        $llamada_actual = isset($row['LLAMADA ACTUAL']) ? trim($row['LLAMADA ACTUAL']) : null;
        $wp_actual = isset($row['WP ACTUAL']) ? trim($row['WP ACTUAL']) : null;
        $wp_b = isset($row['wp b']) ? trim($row['wp b']) : null;
        $dpto_actual = isset($row['DPTO ACTUAL']) ? trim($row['DPTO ACTUAL']) : null;

        // Validar datos requeridos
        if (empty($modelo) || empty($marca)) {
            $errors[] = "Línea con datos faltantes: Modelo=$modelo, Marca=$marca";
            $errorCount++;
            continue;
        }

        // Insertar teléfono
        $stmt = $conexion->prepare("
            INSERT INTO telefonos (
                modelo, marca, bateria, costo, oficina, imei, status,
                posible_venta, puk, pin, departamento_actual
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $modelo,
            $marca,
            $bateria,
            $costo,
            $oficina,
            $imei,
            $status,
            $posible_venta,
            $puk,
            $pin,
            $dpto_actual
        ]);

        $telefono_id = $conexion->lastInsertId();

        // Insertar propietario si existe
        if (!empty($propietario)) {
            $stmtProp = $conexion->prepare("
                INSERT INTO historial_propietarios (
                    telefono_id, nombre, fecha_asignacion, es_actual,
                    mismo_numero, numero_contacto, numero_llamadas, numero_whatsapp
                ) VALUES (?, ?, CURDATE(), 1, ?, ?, ?, ?)
            ");

            // LÓGICA CORREGIDA PARA MANEJAR LOS NÚMEROS DE CONTACTO
            
            // 1. Primero, determinar si es "mismo_numero"
            // "mismo_numero" = 1 cuando llamada_actual y wp_actual son IGUALES
            $mismo_numero = 0;
            if (!empty($llamada_actual) && !empty($wp_actual) && $llamada_actual === $wp_actual) {
                $mismo_numero = 1;
            }
            
            // 2. Determinar qué número poner en cada campo
            $numero_contacto = null;
            $numero_llamadas = null;
            $numero_whatsapp = null;
            
            if ($mismo_numero == 1) {
                // Si es el mismo número, usamos numero_contacto para ambos
                $numero_contacto = $llamada_actual; // O $wp_actual, son iguales
                $numero_llamadas = $llamada_actual;
                $numero_whatsapp = $wp_actual;
            } else {
                // Si son números diferentes
                if (!empty($llamada_actual)) {
                    $numero_llamadas = $llamada_actual;
                }
                
                if (!empty($wp_actual)) {
                    $numero_whatsapp = $wp_actual;
                } else if (!empty($wp_b)) {
                    $numero_whatsapp = $wp_b;
                }
                
                // Si solo hay un número, lo ponemos en numero_contacto también
                if ($numero_llamadas && !$numero_whatsapp) {
                    $numero_contacto = $numero_llamadas;
                } else if (!$numero_llamadas && $numero_whatsapp) {
                    $numero_contacto = $numero_whatsapp;
                }
            }
            
            // 3. Insertar el propietario
            $stmtProp->execute([
                $telefono_id,
                $propietario,
                $mismo_numero,
                $numero_contacto,
                $numero_llamadas,
                $numero_whatsapp
            ]);

            $propietario_id = $conexion->lastInsertId();

            // Insertar cuenta iCloud si hay password
            if (!empty($password)) {
                // Buscar cuenta iCloud en los datos
                $icloud = null;
                
                // Buscar en columnas específicas primero
                $columnasIcloud = ['icloud', 'iCloud', 'ICLOUD', 'correo', 'email', 'mail', 'apple id'];
                foreach ($columnasIcloud as $columna) {
                    if (isset($row[$columna]) && !empty(trim($row[$columna]))) {
                        $icloud = trim($row[$columna]);
                        break;
                    }
                }
                
                // Si no se encontró, buscar cualquier campo que parezca email
                if (!$icloud) {
                    foreach ($row as $key => $value) {
                        $value = trim($value);
                        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $icloud = $value;
                            break;
                        }
                    }
                }
                
                // Si encontramos un iCloud, insertarlo
                if ($icloud) {
                    $stmtCuenta = $conexion->prepare("
                        INSERT INTO cuentas_icloud (telefono_id, propietario_id, icloud, password, es_actual)
                        VALUES (?, ?, ?, ?, 1)
                    ");
                    $stmtCuenta->execute([$telefono_id, $propietario_id, $icloud, $password]);
                }
            }
        }

        $successCount++;
    }

    $conexion->commit();

    fclose($handle);

    echo "Importación completada:<br>";
    echo "Registros exitosos: $successCount<br>";
    echo "Registros con error: $errorCount<br>";

    if (!empty($errors)) {
        echo "<br>Errores:<br>";
        foreach (array_slice($errors, 0, 10) as $error) {
            echo "- $error<br>";
        }
        if (count($errors) > 10) {
            echo "... y " . (count($errors) - 10) . " errores más";
        }
    }
} catch (Exception $e) {
    $conexion->rollBack();
    fclose($handle);
    die("Error en la importación: " . $e->getMessage());
}