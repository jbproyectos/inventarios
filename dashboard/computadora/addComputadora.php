<?php
include '../../includes/conexionbd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar datos del formulario
    $asignado_a = $_POST['asignado_a'];
    $Id_departamento = $_POST['departamento'];
    $Id_oficina = $_POST['oficina'];
    $correo_asociado = $_POST['correo_asociado'];
    $contrasenaGmail1 = $_POST['contrasenaGmail1'];
    $contrasenaOutlook1 = $_POST['contrasenaOutlook1'];
    $correoAsociado2 = $_POST['correoAsociado2'];
    $contrasenaGmail2 = $_POST['contrasenaGmail2'];
    $contrasenaOutlook2 = $_POST['contrasenaOutlook2'];
    $correoAsociado3 = $_POST['correoAsociado3'];
    $contrasenaWindow = $_POST['contrasenaWindow'];
    $tipo = $_POST['tipo'];
    $modelo = $_POST['modelo'];
    $marca = $_POST['marca'];
    $tipoDeDisco = $_POST['tipoDeDisco'];
    $procesador = $_POST['procesador'];
    $ram = $_POST['ram'];
    $condicion = $_POST['condicion'];
    $costoEquipoActual = $_POST['costoEquipoActual'];
    $fechaDeAsignacion = $_POST['fechaDeAsignacion'];
    $anoDeProcesador = $_POST['anoDeProcesador'];
    $fechaDeLanzamiento = $_POST['fechaDeLanzamiento'];
    $status = $_POST['status'];
    $posibleFechaParaVenta = $_POST['posibleFechaParaVenta'];
    $nuevaCompra = $_POST['nuevaCompra'];
    $pcAnterior = $_POST['pcAnterior'];
    $posibleAsignacion = $_POST['posibleAsignacion'];
    $total = $_POST['total'];
    $costoAlComprar = $_POST['costoAlComprar'];
    $costoALaVenta = $_POST['costoALaVenta'];
    $disponibilidad = $_POST['disponibilidad'];
    $propietario_Destino = $_POST['propietario_Destino'];
    $fechaDeReasignacion = $_POST['fechaDeReasignacion'];

    // Validar campos obligatorios
    if (empty($asignado_a) || empty($Id_departamento) || empty($Id_oficina) || empty($contrasenaWindow) || empty($modelo)) {
        $response = array('success' => false, 'message' => 'Todos los campos son obligatorios.');
        echo json_encode($response);
        exit;
    }

    // Subida de imágenes
    $carpeta = '../../assets/images/';
    $ruta = '';
    $ruta2 = '';

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nombre_foto = basename($_FILES['foto']['name']);
        $ruta = $carpeta . $nombre_foto;
        move_uploaded_file($_FILES['foto']['tmp_name'], $ruta);
    }

    if (isset($_FILES['foto2']) && $_FILES['foto2']['error'] === UPLOAD_ERR_OK) {
        $nombre_foto2 = basename($_FILES['foto2']['name']);
        $ruta2 = $carpeta . $nombre_foto2;
        move_uploaded_file($_FILES['foto2']['tmp_name'], $ruta2);
    }

    // Hash de contraseña
    // $hashedPassword = password_hash($contrasenaWindow, PASSWORD_DEFAULT);

    // Preparar consulta SQL
    try {
        $sql = "INSERT INTO computadora (asignado_a, Id_departamento, Id_oficina, correo_asociado, contrasenaGmail1, contrasenaOutlook1, correoAsociado2, contrasenaGmail2, 
        contrasenaOutlook2, correoAsociado3, contrasenaWindow, tipo, modelo, marca, tipoDeDisco, procesador, ram, condicion, costoEquipoActual, fechaDeAsignacion, 
        anoDeProcesador, fechaDeLanzamiento, status, posibleFechaParaVenta, nuevaCompra, foto, pcAnterior, posibleAsignacion, total, costoAlComprar, costoALaVenta, 
        disponibilidad, propietario_Destino, foto2, fechaDeReasignacion) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $asignado_a, $Id_departamento, $Id_oficina, $correo_asociado, $contrasenaGmail1, $contrasenaOutlook1, $correoAsociado2, $contrasenaGmail2,
            $contrasenaOutlook2, $correoAsociado3, $contrasenaWindow, $tipo, $modelo, $marca, $tipoDeDisco, $procesador, $ram, $condicion, $costoEquipoActual,
            $fechaDeAsignacion, $anoDeProcesador, $fechaDeLanzamiento, $status, $posibleFechaParaVenta, $nuevaCompra, $ruta, $pcAnterior, $posibleAsignacion,
            $total, $costoAlComprar, $costoALaVenta, $disponibilidad, $propietario_Destino, $ruta2, $fechaDeReasignacion
        ]);

        $response = array('success' => true, 'message' => 'Registro exitoso.');
    } catch (PDOException $e) {
        $response = array('success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage());
    }

    // Cerrar conexión y responder
    $conexion = null;
    echo json_encode($response);
} else {
    echo json_encode(array('success' => false, 'message' => 'No se recibieron datos por POST.'));
}
?>
