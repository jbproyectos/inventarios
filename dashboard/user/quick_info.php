<?php
include '../../includes/conexionbd.php';
include '../../includes/middleware.php';

verificarSesion();

if (isset($_GET['id'])) {
    $userId = $_GET['id'];
    $currentUserId = $_SESSION["user_id"];
    
    $query = $conexion->prepare("
        SELECT 
            u.*,
            o.nombre AS nombre_oficina,
            d.nombre AS nombre_departamento,
            p.nombre AS nombre_puesto,
            r.nombre AS nombre_rol,
            c.revisado AS pc_revisado,
            c.comment AS pc_fecha_revision,
            (u.fechaUltimoIngreso > NOW() - INTERVAL 5 MINUTE) AS en_linea
        FROM usuarios u
        LEFT JOIN oficina o ON u.Id_oficina = o.Id_oficina
        LEFT JOIN departamentos d ON u.Id_departamento = d.Id_departamento
        LEFT JOIN puestos p ON u.Id_puesto = p.Id_puesto
        LEFT JOIN roless r ON u.rolActual = r.id
        LEFT JOIN computadora c ON u.email = c.correo_asociado
        WHERE u.Id_Usuario = ?
        AND u.Id_departamento IN (
            SELECT Id_departamento FROM usuarios_departamentos WHERE Id_usuario = ?
        )
    ");
    
    $query->execute([$userId, $currentUserId]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo json_encode([
            'success' => true,
            'nombre_completo' => $user['nombre'] . ' ' . $user['apellido'],
            'iniciales' => strtoupper(substr($user['nombre'], 0, 1) . substr($user['apellido'], 0, 1)),
            'email' => $user['email'],
            'fecha_registro' => $user['fechaRegistro'],
            'puesto' => $user['nombre_puesto'] ?? 'No asignado',
            'departamento' => $user['nombre_departamento'] ?? 'No asignado',
            'rol' => $user['nombre_rol'] ?? 'No asignado',
            'oficina' => $user['nombre_oficina'] ?? 'No asignada',
            'pc_verificado' => $user['pc_revisado'] == 1,
            'pc_fecha_revision' => $user['pc_fecha_revision'] ?? null,
            'ultimo_acceso' => $user['ultimo_acceso'] ?? 'No disponible',
            'en_linea' => $user['en_linea']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    }
}
?>