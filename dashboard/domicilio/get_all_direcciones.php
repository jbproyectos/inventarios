<?php
include '../../includes/conexionbd.php';
$stmt = $conexion->prepare("SELECT direccion, empresa1, empresa2 FROM domicilios");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($result);
