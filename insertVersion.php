<?php
// Configuración de conexión a la base de datos con PDO
// Configuración de conexión a la base de datos con PDO
include 'includes/conexionbd.php';

// Obtener los datos enviados por POST
$input = json_decode(file_get_contents('php://input'), true);  // Decodificar el JSON recibido

// Verificar si 'versions' es un array
$versions = isset($input['versions']) ? $input['versions'] : [];

if (empty($versions)) {
    echo json_encode(array("status" => "error", "message" => "Falta el parámetro 'versions'"));
    exit;
}

try {
    $sql_delete = "DELETE FROM versiones";
    $conexion->exec($sql_delete);
    // Preparar el query de inserción para versiones
    $sql = "INSERT INTO versiones (version) VALUES (:version)";
    $stmt = $conexion->prepare($sql);
    
    // Comenzar la transacción para evitar múltiples inserciones
    $conexion->beginTransaction();

    // Insertar cada versión como una fila separada
    foreach ($versions as $version) {
        // Verificar el tamaño de la versión antes de insertarla (opcional)
        if (strlen($version) > 255) {
            throw new Exception("La versión '$version' es demasiado larga. El límite es de 255 caracteres.");
        }

        // Enlazar el parámetro y ejecutar la inserción para cada versión
        $stmt->bindParam(':version', $version, PDO::PARAM_STR);
        $stmt->execute();
    }

    // Confirmar la transacción si todo fue exitoso
    $conexion->commit();
    
    // Si la inserción fue exitosa
    echo json_encode(array("status" => "success", "message" => "Versiones insertadas correctamente"));
} catch (PDOException $e) {
    // Si ocurre un error en cualquier parte del proceso, revertir la transacción
    $conexion->rollBack();
    echo json_encode(array("status" => "error", "message" => "Error al procesar la solicitud: " . $e->getMessage()));
} catch (Exception $e) {
    // Si hay un error de validación del tamaño o cualquier otro, revertir la transacción
    $conexion->rollBack();
    echo json_encode(array("status" => "error", "message" => $e->getMessage()));
}

$conn = null; // Cerrar la conexión
