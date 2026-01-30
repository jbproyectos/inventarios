<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Incluir el archivo de conexión
include '../../includes/conexionbd.php';

// Verificar que la conexión esté definida
if (!isset($conexion)) {
    die(json_encode(['error' => 'Error: La conexión a la base de datos no está definida.']));
}

// Verificar si se recibió el puesto
if (!isset($_GET['puesto']) || empty($_GET['puesto'])) {
    die(json_encode(['error' => 'Puesto no recibido o vacío.']));
}

$puesto = $_GET['puesto'];

// Definir las tablas de manera segura
$tablas = [
    '1' => 'CEO',
    '2' => 'director',
    '3' => 'supervisor',
    '4' => 'lider',
    '5' => 'staff' // Aquí corregí el puesto 5
];

// Validar si el puesto existe en la lista
if (!array_key_exists($puesto, $tablas)) {
    die(json_encode(['error' => 'Puesto inválido o tabla no definida']));
}

$tabla = $tablas[$puesto];

try {
    // Preparar la consulta de forma segura
    $stmt = $conexion->prepare("SELECT id, nombre FROM $tabla");
    $stmt->execute();
    $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retornar los datos como JSON
    echo json_encode($datos);
} catch (PDOException $e) {
    error_log("Error en get_nivel.php: " . $e->getMessage());
    die(json_encode(['error' => 'Error en la consulta: ' . $e->getMessage()]));
}
?>
