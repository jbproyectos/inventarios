<?php
include '../../includes/conexionbd.php';

if (!isset($_FILES["subecomputadoras"]) || $_FILES["subecomputadoras"]["error"] !== UPLOAD_ERR_OK) {
    exit("Error al subir el archivo. Por favor, verifica que sea un archivo válido.");
}

$fh = fopen($_FILES["subecomputadoras"]["tmp_name"], "r");
if ($fh === false) {
    exit("No se pudo abrir el archivo CSV.");
}

$rowCount = 0; // Contador de filas procesadas
$errors = []; // Lista para capturar errores

try {
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    while (($row = fgetcsv($fh)) !== false) {
        // Salta la primera fila si contiene encabezados
        if ($rowCount === 0) {
            $rowCount++;
            continue;
        }

        try {
            // Reemplaza con el ID correspondiente si tienes lógica específica
            // $idOficina = $row[2] ?? null; 

            $stmt = $conexion->prepare("INSERT INTO computadora(asignado_a, Id_departamento, Id_oficina, correo_asociado, contrasenaGmail1, contrasenaOutlook1, 
                correoAsociado2, contrasenaGmail2, contrasenaOutlook2, correoAsociado3, contrasenaWindow, tipo, modelo, marca, tipoDeDisco, procesador, ram, condicion,
                costoEquipoActual, fechaDeAsignacion, anoDeProcesador, fechaDeLanzamiento, status, posibleFechaParaVenta, nuevaCompra, foto, pcAnterior, 
                posibleAsignacion, total, costoAlComprar, costoALaVenta, disponibilidad, propietario_Destino, foto2, fechaDeReasignacion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $row[0], $row[1], $row[2], $row[3], $row[4], $row[5], $row[6], $row[7], $row[8], $row[9], $row[10], $row[11], $row[12], $row[13],
                $row[14], $row[15], $row[16], $row[17], $row[18], $row[19], $row[20], $row[21], $row[22], $row[23], $row[24], $row[25], $row[26], $row[27], $row[28],
                $row[29], $row[30], $row[31], $row[32], $row[33], $row[34]
            ]);

            $rowCount++;
        } catch (Exception $ex) {
            $errors[] = "Fila $rowCount: Error al insertar - " . $ex->getMessage();
        }
        
        $totalProcesadas = $rowCount - 1; // quitando encabezado
$totalErrores = count($errors);
$totalInsertadas = $totalProcesadas - $totalErrores;

echo "Total en CSV: $totalProcesadas<br>";
echo "Insertadas: $totalInsertadas<br>";
echo "Errores: $totalErrores<br>";

if ($totalErrores > 0) {
    echo "Lista de errores:<br>" . implode("<br>", $errors);
}

    }

    fclose($fh);
    
    echo "Computadoras cargadas con éxito. Total filas procesadas: $rowCount<br>";
    if (!empty($errors)) {
        echo "Errores encontrados:<br>" . implode("<br>", $errors);
    }
} catch (Exception $e) {
    exit("Error al procesar el archivo: " . $e->getMessage());
}
