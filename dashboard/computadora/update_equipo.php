<?php
include '../../includes/conexionbd.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener datos del POST
        $id_equipo = $_POST['id_equipo'];
        $departamento = $_POST['departamento'];
        $oficina = isset($_POST['oficina']) ? $_POST['oficina'] : NULL; // Oficina puede ser nulo
        $asignado_a = $_POST['asignado_a'];
        $tipo = $_POST['tipo'];
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $condicion = $_POST['condicion'];
        $costoEquipoActual = $_POST['costoEquipoActual'];
        $fechaDeAsignacion = $_POST['fechaDeAsignacion'];
        $anoDeProcesador = $_POST['anoDeProcesador'];
        $fechaDeLanzamiento = $_POST['fechaDeLanzamiento'];
        $status = $_POST['status'];
        $correo_asociado = $_POST['correo_asociado'];
        $contrasenaGmail1 = $_POST['contrasenaGmail1'];
        $contrasenaOutlook1 = isset($_POST['contrasenaOutlook1']) ? $_POST['contrasenaOutlook1'] : NULL;
        $correoAsociado2 = isset($_POST['correoAsociado2']) ? $_POST['correoAsociado2'] : NULL;
        $contrasenaGmail2 = isset($_POST['contrasenaGmail2']) ? $_POST['contrasenaGmail2'] : NULL;
        $contrasenaOutlook2 = isset($_POST['contrasenaOutlook2']) ? $_POST['contrasenaOutlook2'] : NULL;
        $correoAsociado3 = isset($_POST['correoAsociado3']) ? $_POST['correoAsociado3'] : NULL;
        $contrasenaWindow = isset($_POST['contrasenaWindow']) ? $_POST['contrasenaWindow'] : NULL;
        $tipoDeDisco = $_POST['tipoDeDisco'];
        $procesador = $_POST['procesador'];
        $ram = $_POST['ram'];
        $posibleFechaParaVenta = isset($_POST['posibleFechaParaVenta']) ? $_POST['posibleFechaParaVenta'] : NULL;
        $nuevaCompra = isset($_POST['nuevaCompra']) ? $_POST['nuevaCompra'] : NULL;
        $pcAnterior = isset($_POST['pcAnterior']) ? $_POST['pcAnterior'] : NULL;
        $posibleAsignacion = isset($_POST['posibleAsignacion']) ? $_POST['posibleAsignacion'] : NULL;
        $total = $_POST['total'];
        $costoAlComprar = $_POST['costoAlComprar'];
        $costoALaVenta = $_POST['costoALaVenta'];
        $disponibilidad = $_POST['disponibilidad'];
        $propietarioDestino = isset($_POST['propietario_Destino']) ? $_POST['propietario_Destino'] : NULL;
        $fechaDeReasignacion = isset($_POST['fechaDeReasignacion']) ? $_POST['fechaDeReasignacion'] : NULL;
        $revisado = 1;
        $comment = 'Correccion de datos';

        // Validar los campos obligatorios
        if (empty($id_equipo) || empty($asignado_a)) {
            echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios.']);
            exit();
        }

        // Preparar la consulta SQL
        $sql = "UPDATE computadora SET 
            id_departamento = :departamento,
            id_oficina = :oficina,
            asignado_a = :asignado_a,
            tipo = :tipo,
            marca = :marca,
            modelo = :modelo,
            condicion = :condicion,
            costoEquipoActual = :costoEquipoActual,
            fechaDeAsignacion = :fechaDeAsignacion,
            anoDeProcesador = :anoDeProcesador,
            fechaDeLanzamiento = :fechaDeLanzamiento,
            status = :status,
            correo_asociado = :correo_asociado,
            contrasenaGmail1 = :contrasenaGmail1,
            contrasenaOutlook1 = :contrasenaOutlook1,
            correoAsociado2 = :correoAsociado2,
            contrasenaGmail2 = :contrasenaGmail2,
            contrasenaOutlook2 = :contrasenaOutlook2,
            correoAsociado3 = :correoAsociado3,
            contrasenaWindow = :contrasenaWindow,
            tipoDeDisco = :tipoDeDisco,
            procesador = :procesador,
            ram = :ram,
            posibleFechaParaVenta = :posibleFechaParaVenta,
            nuevaCompra = :nuevaCompra,
            pcAnterior = :pcAnterior,
            posibleAsignacion = :posibleAsignacion,
            total = :total,
            costoAlComprar = :costoAlComprar,
            costoALaVenta = :costoALaVenta,
            disponibilidad = :disponibilidad,
            propietario_Destino = :propietarioDestino,
            fechaDeReasignacion = :fechaDeReasignacion,
            revisado = :revisado,
            comment = :comment
            WHERE Id_computadora = :id_equipo";  // El WHERE sigue aquí para asegurar la actualización correcta

        $stmt = $conexion->prepare($sql);

        // Asignar los parámetros, incluyendo el ID
        $stmt->bindParam(':id_equipo', $id_equipo, PDO::PARAM_INT);
        $stmt->bindParam(':departamento', $departamento, PDO::PARAM_STR);
        $stmt->bindParam(':oficina', $oficina, PDO::PARAM_STR);
        $stmt->bindParam(':asignado_a', $asignado_a, PDO::PARAM_STR);
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindParam(':marca', $marca, PDO::PARAM_STR);
        $stmt->bindParam(':modelo', $modelo, PDO::PARAM_STR);
        $stmt->bindParam(':condicion', $condicion, PDO::PARAM_STR);
        $stmt->bindParam(':costoEquipoActual', $costoEquipoActual, PDO::PARAM_STR);
        $stmt->bindParam(':fechaDeAsignacion', $fechaDeAsignacion, PDO::PARAM_STR);
        $stmt->bindParam(':anoDeProcesador', $anoDeProcesador, PDO::PARAM_INT);
        $stmt->bindParam(':fechaDeLanzamiento', $fechaDeLanzamiento, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':correo_asociado', $correo_asociado, PDO::PARAM_STR);
        $stmt->bindParam(':contrasenaGmail1', $contrasenaGmail1, PDO::PARAM_STR);
        $stmt->bindParam(':contrasenaOutlook1', $contrasenaOutlook1, PDO::PARAM_STR);
        $stmt->bindParam(':correoAsociado2', $correoAsociado2, PDO::PARAM_STR);
        $stmt->bindParam(':contrasenaGmail2', $contrasenaGmail2, PDO::PARAM_STR);
        $stmt->bindParam(':contrasenaOutlook2', $contrasenaOutlook2, PDO::PARAM_STR);
        $stmt->bindParam(':correoAsociado3', $correoAsociado3, PDO::PARAM_STR);
        $stmt->bindParam(':contrasenaWindow', $contrasenaWindow, PDO::PARAM_STR);
        $stmt->bindParam(':tipoDeDisco', $tipoDeDisco, PDO::PARAM_STR);
        $stmt->bindParam(':procesador', $procesador, PDO::PARAM_STR);
        $stmt->bindParam(':ram', $ram, PDO::PARAM_STR);
        $stmt->bindParam(':posibleFechaParaVenta', $posibleFechaParaVenta, PDO::PARAM_STR);
        $stmt->bindParam(':nuevaCompra', $nuevaCompra, PDO::PARAM_STR);
        $stmt->bindParam(':pcAnterior', $pcAnterior, PDO::PARAM_STR);
        $stmt->bindParam(':posibleAsignacion', $posibleAsignacion, PDO::PARAM_STR);
        $stmt->bindParam(':total', $total, PDO::PARAM_STR);
        $stmt->bindParam(':costoAlComprar', $costoAlComprar, PDO::PARAM_STR);
        $stmt->bindParam(':costoALaVenta', $costoALaVenta, PDO::PARAM_STR);
        $stmt->bindParam(':disponibilidad', $disponibilidad, PDO::PARAM_STR);
        $stmt->bindParam(':propietarioDestino', $propietarioDestino, PDO::PARAM_STR);
        $stmt->bindParam(':fechaDeReasignacion', $fechaDeReasignacion, PDO::PARAM_STR);
        $stmt->bindParam(':revisado', $revisado, PDO::PARAM_STR);

        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);


        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el equipo.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>
