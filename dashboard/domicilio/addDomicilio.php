<?php
include '../../includes/conexionbd.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recuperar datos del formulario
    $direccion = $_POST['direccion'];
    $municipio = $_POST['municipio'];
    $ubicacion = $_POST['ubicacion'];
    $escritorios = $_POST['escritorios'];
    $sillas_escritorios = $_POST['sillas_de_escritorios'];
    $sillas = $_POST['sillas'];
    $mesa_escritorio = $_POST['mesa_escritorio'];
    $sillones = $_POST['sillones'];
    $mesa_centro = $_POST['mesa_de_centro'];
    $cajoneras = $_POST['cajoneras'];
    $estantes = $_POST['estantes'];
    $otros = $_POST['otros'];

    // 🚀 Guardar todas las empresas en un solo campo (separadas por coma)
    $empresas = $_POST['empresas'] ?? [];
    $empresas_str = implode(', ', $empresas); // "Empresa A, Empresa B"

    // Validar campos obligatorios mínimos
    if (empty($direccion) || empty($municipio)) {
        $response = array('success' => false, 'message' => 'Dirección y municipio son obligatorios.');
        echo json_encode($response);
        exit;
    }

    try {
        $sql = "INSERT INTO domicilios (
                    direccion, empresa1, municipio, ubicacion, escritorios, sillas_de_escritorios, sillas, 
                    mesa_escritorio, sillones, mesa_de_centro, cajoneras, estantes, otros
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conexion->prepare($sql);
        $stmt->execute([
            $direccion, $empresas_str, $municipio, $ubicacion, $escritorios, $sillas_escritorios, $sillas,
            $mesa_escritorio, $sillones, $mesa_centro, $cajoneras, $estantes, $otros
        ]);

        $response = array('success' => true, 'message' => 'Registro exitoso en domicilios.');
    } catch (PDOException $e) {
        $response = array('success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage());
    }

    $conexion = null;
    echo json_encode($response);
} else {
    echo json_encode(array('success' => false, 'message' => 'No se recibieron datos por POST.'));
}
