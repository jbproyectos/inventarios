<?php
$dsn = 'mysql:host=localhost;dbname=db_sistemas;charset=utf8mb4';
$usuario = 'root';
$contraseña = '';

try {
    $conexion = new PDO($dsn, $usuario, $contraseña);
} catch (PDOException $e) {
    die('Error de conexión: ' . $e->getMessage());
}
