<?php
include '../../includes/conexionbd.php';

if (isset($_GET['puesto_id'])) {
    $puestoId = $_GET['puesto_id'];

    // Verificar si el puesto seleccionado es "EMPRESA" (ID = 1 en este caso)
    if ($puestoId == 1) {
        try {
            // Consultar la base de datos para obtener los CEOS (usuarios cuyo Id_puesto es el puesto de CEO)
            $sql = "SELECT * from CEO";
            $stmt = $conexion->prepare($sql);
            $stmt->execute();
            $ceos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($ceos);
        } catch (PDOException $e) {
            echo json_encode(array('error' => 'Error en la base de datos: ' . $e->getMessage()));
        }
    } else {
        echo json_encode(array('error' => 'Puesto no encontrado.'));
    }
}
?>
