<?php
include '../../includes/conexionbd.php';

if (!isset($_FILES["subeDomicilios"]) || $_FILES["subeDomicilios"]["error"] !== UPLOAD_ERR_OK) {
    exit("Error al subir el archivo. Por favor, verifica que sea un archivo válido.");
}

$fh = fopen($_FILES["subeDomicilios"]["tmp_name"], "r");
if ($fh === false) {
    exit("No se pudo abrir el archivo CSV.");
}

$rowCount = 0; // Contador de filas procesadas
$errors = []; // Lista para capturar errores

try {
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    while (($row = fgetcsv($fh)) !== false) {
        // Saltar encabezado
        if ($rowCount === 0) {
            $rowCount++;
            continue;
        }

        try {
            $stmt = $conexion->prepare("INSERT INTO domicilios(
                direccion, empresa1, empresa2, municipio, ubicacion, escritorios, sillas_de_escritorios, sillas, mesa_escritorio, sillones, mesa_de_centro, cajoneras, estantes, otros
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->execute([
                $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], $row[12], $row[13]
            ]);

            $rowCount++;
        } catch (Exception $ex) {
            $errors[] = "Fila $rowCount: Error al insertar - " . $ex->getMessage();
        }
    }

    fclose($fh);

    $totalProcesadas = $rowCount - 1; // quitando encabezado
    $totalErrores = count($errors);
    $totalInsertadas = $totalProcesadas - $totalErrores;

    echo "Total en CSV: $totalProcesadas<br>";
    echo "Insertadas: $totalInsertadas<br>";
    echo "Errores: $totalErrores<br>";

    if ($totalErrores > 0) {
        echo "Lista de errores:<br>" . implode("<br>", $errors);
    }

} catch (Exception $e) {
    exit("Error al procesar el archivo: " . $e->getMessage());
}
?>
