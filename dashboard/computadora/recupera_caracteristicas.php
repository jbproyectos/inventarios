<?php
include '../../includes/conexionbd.php';

$id = $_GET['id'];

$consulta = $conexion->prepare('SELECT computadora.*, oficina.nombre AS nombre_oficina, departamentos.nombre AS nombre_departamento FROM computadora
                                INNER JOIN oficina ON computadora.Id_oficina = oficina.Id_Oficina
                                INNER JOIN departamentos ON computadora.Id_departamento = departamentos.Id_departamento
                                WHERE computadora.Id_computadora = :id');
$consulta->bindParam(':id', $id);
$consulta->execute();

$computadora = $consulta->fetch(PDO::FETCH_ASSOC);

echo json_encode($computadora);
?>