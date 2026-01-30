<?php
include '../../includes/conexionbd.php';

$id = $_GET['id'];

// Seleccionamos directamente el domicilio
$consulta = $conexion->prepare('SELECT * FROM mobiliario WHERE id_mobiliario = :id');
$consulta->bindParam(':id', $id);
$consulta->execute();

$domicilio = $consulta->fetch(PDO::FETCH_ASSOC);

// Devolvemos los datos como JSON
echo json_encode($domicilio);
?>
