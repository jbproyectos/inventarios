<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../../includes/conexionbd.php'; // aquí debe crearse $conexion como instancia de PDO

// Configurar headers para archivo Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="computadoras_export.xlsx"');

// Usar PhpSpreadsheet
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Encabezados
$headers = [
    'ID', 'Asignado a', 'Departamento', 'Oficina', 'Tipo', 'Marca', 'Modelo',
    'Procesador', 'RAM', 'Disco', 'Condición', 'Estado', 'Disponibilidad',
    'Fecha Asignación', 'Costo Actual', 'Correo Asociado'
];

$col = 1;
foreach ($headers as $header) {
    $columnLetter = Coordinate::stringFromColumnIndex($col);
    $sheet->setCellValue($columnLetter . '1', $header);
    $col++;
}

// Construir consulta
$sql = "SELECT 
    c.Id_computadora,
    c.asignado_a,
    d.nombre AS departamento,
    o.nombre AS oficina,
    c.tipo,
    c.marca,
    c.modelo,
    c.procesador,
    c.ram,
    c.tipoDeDisco,
    c.condicion,
    c.status,
    c.disponibilidad,
    c.fechaDeAsignacion,
    c.costoEquipoActual,
    c.correo_asociado
FROM computadora c
LEFT JOIN departamentos d ON c.Id_departamento = d.Id_departamento
LEFT JOIN oficina o ON c.Id_oficina = o.Id_oficina
WHERE 1=1";

$params = [];

// Filtros
if (isset($_GET['tipo']) && $_GET['tipo'] === 'filtrados') {
    if (!empty($_GET['filtro_marca'])) {
        $sql .= " AND c.marca = ?";
        $params[] = $_GET['filtro_marca'];
    }
    if (!empty($_GET['filtro_status'])) {
        $sql .= " AND c.status = ?";
        $params[] = $_GET['filtro_status'];
    }
    if (!empty($_GET['filtro_tipo'])) {
        $sql .= " AND c.tipo = ?";
        $params[] = $_GET['filtro_tipo'];
    }
    if (!empty($_GET['filtro_departamento'])) {
        $sql .= " AND c.Id_departamento = ?";
        $params[] = $_GET['filtro_departamento'];
    }
    if (!empty($_GET['filtro_oficina'])) {
        $sql .= " AND c.Id_oficina = ?";
        $params[] = $_GET['filtro_oficina'];
    }
    if (!empty($_GET['filtro_disponibilidad'])) {
        $sql .= " AND c.disponibilidad = ?";
        $params[] = $_GET['filtro_disponibilidad'];
    }
    if (!empty($_GET['filtro_procesador'])) {
        $sql .= " AND c.procesador LIKE ?";
        $params[] = '%' . $_GET['filtro_procesador'] . '%';
    }
    if (!empty($_GET['filtro_ram'])) {
        $sql .= " AND c.ram = ?";
        $params[] = $_GET['filtro_ram'];
    }
    if (!empty($_GET['filtro_modelo'])) {
        $sql .= " AND c.modelo LIKE ?";
        $params[] = '%' . $_GET['filtro_modelo'] . '%';
    }
    if (!empty($_GET['filtro_asignado'])) {
        $sql .= " AND c.asignado_a LIKE ?";
        $params[] = '%' . $_GET['filtro_asignado'] . '%';
    }
    if (!empty($_GET['search'])) {
        $sql .= " AND (c.asignado_a LIKE ? OR c.modelo LIKE ? OR c.marca LIKE ? OR c.procesador LIKE ?)";
        $searchTerm = '%' . $_GET['search'] . '%';
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
}

if (!empty($_GET['filtro_costo_min'])) {
    $sql .= " AND c.costoEquipoActual >= ?";
    $params[] = $_GET['filtro_costo_min'];
}
if (!empty($_GET['filtro_costo_max'])) {
    $sql .= " AND c.costoEquipoActual <= ?";
    $params[] = $_GET['filtro_costo_max'];
}

// Preparar y ejecutar con PDO
$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Escribir filas
$rowNum = 2;
foreach ($result as $row) {
    $col = 1;
    foreach ($row as $value) {
        $columnLetter = Coordinate::stringFromColumnIndex($col);
        $sheet->setCellValue($columnLetter . $rowNum, $value);
        $col++;
    }
    $rowNum++;
}

// Exportar
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
