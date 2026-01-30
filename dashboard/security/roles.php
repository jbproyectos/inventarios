<?php
// rseguridad/roles.php

function getUserRole($userId) {
    global $conexion;
    $stmt = $conexion->prepare("SELECT role_id FROM roles_usuarios WHERE user_id = ?");
    $stmt->execute([$userId]);
    $role = $stmt->fetch(PDO::FETCH_ASSOC);
    return $role ? $role['role_id'] : null;
}
?>
