<?php
include '../../includes/conexionbd.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del POST
        $id          = $_POST['id'];
        $direccion   = $_POST['direccion'];
        $empresa1    = $_POST['empresa1'];
        $empresa2    = $_POST['empresa2'];
        $municipio   = $_POST['municipio'];
        $ubicacion   = $_POST['ubicacion'];
        $escritorios = $_POST['escritorios'];
        $sillas_de_escritorios = $_POST['sillas_de_escritorios'];
        $sillas      = $_POST['sillas'];
        $mesa_escritorio = $_POST['mesa_escritorio'];
        $sillones    = $_POST['sillones'];
        $mesa_de_centro = $_POST['mesa_de_centro'];
        $cajoneras   = $_POST['cajoneras'];
        $estantes    = $_POST['estantes'];
        $otros       = $_POST['otros'];

        // Validar campos obligatorios
        if (empty($id) || empty($direccion) || empty($municipio)) {
            echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios.']);
            exit();
        }

        // Preparar la consulta SQL
        $sql = "UPDATE domicilios SET 
                    direccion = :direccion,
                    empresa1 = :empresa1,
                    empresa2 = :empresa2,
                    municipio = :municipio,
                    ubicacion = :ubicacion,
                    escritorios = :escritorios,
                    sillas_de_escritorios = :sillas_de_escritorios,
                    sillas = :sillas,
                    mesa_escritorio = :mesa_escritorio,
                    sillones = :sillones,
                    mesa_de_centro = :mesa_de_centro,
                    cajoneras = :cajoneras,
                    estantes = :estantes,
                    otros = :otros
                WHERE id = :id";

        $stmt = $conexion->prepare($sql);

        // Asignar parámetros
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':direccion', $direccion, PDO::PARAM_STR);
        $stmt->bindParam(':empresa1', $empresa1, PDO::PARAM_STR);
        $stmt->bindParam(':empresa2', $empresa2, PDO::PARAM_STR);
        $stmt->bindParam(':municipio', $municipio, PDO::PARAM_STR);
        $stmt->bindParam(':ubicacion', $ubicacion, PDO::PARAM_STR);
        $stmt->bindParam(':escritorios', $escritorios, PDO::PARAM_STR);
        $stmt->bindParam(':sillas_de_escritorios', $sillas_de_escritorios, PDO::PARAM_STR);
        $stmt->bindParam(':sillas', $sillas, PDO::PARAM_STR);
        $stmt->bindParam(':mesa_escritorio', $mesa_escritorio, PDO::PARAM_STR);
        $stmt->bindParam(':sillones', $sillones, PDO::PARAM_STR);
        $stmt->bindParam(':mesa_de_centro', $mesa_de_centro, PDO::PARAM_STR);
        $stmt->bindParam(':cajoneras', $cajoneras, PDO::PARAM_STR);
        $stmt->bindParam(':estantes', $estantes, PDO::PARAM_STR);
        $stmt->bindParam(':otros', $otros, PDO::PARAM_STR);

        // Ejecutar consulta
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el domicilio.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
