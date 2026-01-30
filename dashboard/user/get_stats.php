<?php
include '../../includes/conexionbd.php';
include '../../includes/middleware.php';

verificarSesion();

$idUsuario = $_SESSION["user_id"];

// Total de usuarios
$queryTotal = $conexion->prepare("
    SELECT COUNT(*) as total 
    FROM usuarios 
    WHERE Id_departamento IN (
        SELECT Id_departamento FROM usuarios_departamentos WHERE Id_usuario = ?
    )
");
$queryTotal->execute([$idUsuario]);
$totalUsers = $queryTotal->fetch(PDO::FETCH_ASSOC)['total'];

// Usuarios activos
$queryActive = $conexion->prepare("
    SELECT COUNT(*) as active 
    FROM usuarios 
    WHERE estatu = 1 
    AND Id_departamento IN (
        SELECT Id_departamento FROM usuarios_departamentos WHERE Id_usuario = ?
    )
");
$queryActive->execute([$idUsuario]);
$activeUsers = $queryActive->fetch(PDO::FETCH_ASSOC)['active'];

// PCs verificados
$queryVerified = $conexion->prepare("
    SELECT COUNT(DISTINCT u.Id_Usuario) as verified 
    FROM usuarios u
    JOIN computadora c ON u.email = c.correo_asociado
    WHERE c.revisado = 2 
    AND u.Id_departamento IN (
        SELECT Id_departamento FROM usuarios_departamentos WHERE Id_usuario = ?
    )
");
$queryVerified->execute([$idUsuario]);
$verifiedPCs = $queryVerified->fetch(PDO::FETCH_ASSOC)['verified'];

// Usuarios en línea (últimos 5 minutos)
$queryOnline = $conexion->prepare("
    SELECT COUNT(*) as online 
    FROM usuarios 
    WHERE fechaUltimoIngreso > NOW() - INTERVAL 5 MINUTE
    AND Id_departamento IN (
        SELECT Id_departamento FROM usuarios_departamentos WHERE Id_usuario = ?
    )
");
$queryOnline->execute([$idUsuario]);
$onlineNow = $queryOnline->fetch(PDO::FETCH_ASSOC)['online'];

echo json_encode([
    'success' => true,
    'total_users' => $totalUsers,
    'active_users' => $activeUsers,
    'verified_pcs' => $verifiedPCs,
    'online_now' => $onlineNow
]);
?>