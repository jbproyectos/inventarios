<?php
// Incluir la conexión a la base de datos
include '../../includes/conexionbd.php';

// Preparar la consulta
$consulta = $conexion->prepare('SELECT * FROM mobiliario');

// Ejecutar la consulta
$consulta->execute();

// Obtener los resultados
$datos = $consulta->fetchAll(PDO::FETCH_ASSOC);

// Devolver los datos en formato JSON
header('Content-Type: application/json');
echo json_encode($datos);
exit;
?>