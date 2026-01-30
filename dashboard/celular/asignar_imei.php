<?php
include '../../includes/conexionbd.php';

$id = $_POST['id'] ?? null;
$imei = $_POST['imei'] ?? null;

if (!$id || !$imei) {
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    exit;
}

try {
    $stmt = $conexion->prepare("UPDATE telefonos SET imei = ? WHERE id = ?");
    $stmt->execute([$imei, $id]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
