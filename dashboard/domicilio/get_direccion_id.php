<?php
include '../../includes/conexionbd.php';
$stmt = $conexion->prepare("SELECT id, direccion FROM domicilios ORDER BY direccion ASC");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($result);
