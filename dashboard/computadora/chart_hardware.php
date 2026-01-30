<?php
header('Content-Type: application/json');

include '../../includes/conexionbd.php'; // $conexion debe ser una instancia de PDO.

try {
    // Consulta para totales de RAM
    $sqlRAM = "
    SELECT COUNT(*) AS total_ram
    FROM computadora
    WHERE ram IS NOT NULL
";

$sqlDiscos = "
    SELECT COUNT(*) AS total_discos
    FROM computadora
    WHERE tipoDeDisco IS NOT NULL
";

$sqlProcesadores = "
    SELECT COUNT(*) AS total_procesadores
    FROM computadora
    WHERE procesador IS NOT NULL
";

    // Consulta para detalles (RAM, discos, procesadores)
    $sqlDetalles = "
        SELECT 'RAM' AS categoria, c.RAM AS valor, COUNT(*) AS cantidad
        FROM computadora c
        GROUP BY c.RAM
        UNION
        SELECT 'Disco' AS categoria, LOWER(c.tipoDeDisco) AS valor, COUNT(*) AS cantidad
        FROM computadora c
        GROUP BY LOWER(c.tipoDeDisco)
        UNION
        SELECT 'Procesador' AS categoria, LOWER(c.procesador) AS valor, COUNT(*) AS cantidad
        FROM computadora c
        GROUP BY LOWER(c.procesador)
        ORDER BY categoria, cantidad DESC
    ";

    // Ejecutar las consultas
    $stmtRAM = $conexion->prepare($sqlRAM);
    $stmtRAM->execute();
    $totalRAM = $stmtRAM->fetch(PDO::FETCH_ASSOC)['total_ram'];

    $stmtDiscos = $conexion->prepare($sqlDiscos);
    $stmtDiscos->execute();
    $totalDiscos = $stmtDiscos->fetch(PDO::FETCH_ASSOC)['total_discos'];

    $stmtProcesadores = $conexion->prepare($sqlProcesadores);
    $stmtProcesadores->execute();
    $totalProcesadores = $stmtProcesadores->fetch(PDO::FETCH_ASSOC)['total_procesadores'];

    $stmtDetalles = $conexion->prepare($sqlDetalles);
    $stmtDetalles->execute();
    $detalles = $stmtDetalles->fetchAll(PDO::FETCH_ASSOC);

    // Construir respuesta
    $response = [
        "totals" => [
            "ram" => $totalRAM,
            "discos" => $totalDiscos,
            "procesadores" => $totalProcesadores
        ],
        "detalles" => $detalles
    ];

    echo json_encode($response);

} catch (PDOException $e) {
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}
?>
