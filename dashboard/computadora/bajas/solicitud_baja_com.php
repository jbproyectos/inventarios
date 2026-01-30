<?php
include '../../../includes/conexionbd.php';
session_start();

$idProducto = $_POST['idCom'];
$motivo = $_POST['motivo'];
$id_user = $_SESSION['user_id'];
$departamento = $_POST['areasoli'];
$table = "computadora";

try {
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO solicitudbaja (Id_inventario, motivoBaja, Id_usuario, id_rol_soli, tabla) VALUES (:idProducto, :motivo, :id_user, :departamento, :table)";
    $stmt = $conexion->prepare($sql);

    $stmt->bindParam(':idProducto', $idProducto);
    $stmt->bindParam(':motivo', $motivo);
    $stmt->bindParam(':id_user', $id_user);
    $stmt->bindParam(':departamento', $departamento);
    $stmt->bindParam(':table', $table);

    $stmt->execute();

    echo "Solicitud enviada con éxito";
} catch (PDOException $e) {
    echo "Error al enviar la solicitud: " . $e->getMessage();
}
?>