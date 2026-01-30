<?php
include '../includes/middleware.php';
verificarSesion();
include "../includes/conexionbd.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $id_celular = $_POST['id_celular'];
        $asignado_a = $_POST['asignado_a'];
        $marca = $_POST['marca'];
        $modelo = $_POST['modelo'];
        $imei = $_POST['imei'];
        $porcentaje_pila = $_POST['porcentaje_pila'];
        $whatsapp = $_POST['whatsapp'];
        $cuenta_vinculada = $_POST['cuenta_vinculada'];
        $contrasena_cuenta = $_POST['contrasena_cuenta'];
        $status = $_POST['status'];
        $costo = $_POST['costo'];

        $stmt = $conexion->prepare("
            UPDATE celulares SET 
                asignado_a = ?, marca = ?, modelo = ?, imei = ?, 
                porcentaje_pila = ?, whatsapp = ?, cuenta_vinculada = ?, 
                contrasena_cuenta = ?, status = ?, costo = ?
            WHERE Id_celular = ?
        ");

        $stmt->execute([
            $asignado_a, $marca, $modelo, $imei, $porcentaje_pila,
            $whatsapp, $cuenta_vinculada, $contrasena_cuenta, $status, $costo, $id_celular
        ]);

        echo json_encode(['success' => true, 'message' => 'Celular actualizado correctamente']);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>