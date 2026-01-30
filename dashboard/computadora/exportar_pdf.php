<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Verificar permisos
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

// Usar una librería como TCPDF
require_once '../vendor/tecnickcom/tcpdf/tcpdf.php';

class MYPDF extends TCPDF {
    // Cabecera
    public function Header() {
        // Logo
        $image_file = '../assets/logo.png';
        if (file_exists($image_file)) {
            $this->Image($image_file, 10, 10, 30, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }
        // Título
        $this->SetFont('helvetica', 'B', 20);
        $this->Cell(0, 15, 'Reporte de Computadoras', 0, false, 'C', 0, '', 0, false, 'M', 'M');
        $this->Ln(20);
    }
    
    // Pie de página
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

// Crear nuevo documento PDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Información del documento
$pdf->SetCreator('Sistema de Computadoras');
$pdf->SetAuthor('Sistema');
$pdf->SetTitle('Reporte de Computadoras');
$pdf->SetSubject('Reporte PDF');

// Agregar una página
$pdf->AddPage();

// Construir consulta (similar a exportar_excel.php)
$sql = "SELECT 
    c.Id_computadora,
    c.asignado_a,
    d.nombre_departamento,
    o.nombre_oficina,
    c.tipo,
    c.marca,
    c.modelo,
    c.procesador,
    c.ram,
    c.tipoDeDisco,
    c.condicion,
    c.status,
    c.disponibilidad,
    DATE_FORMAT(c.fechaDeAsignacion, '%d/%m/%Y') as fechaDeAsignacion,
    CONCAT('$', FORMAT(c.costoEquipoActual, 2)) as costoEquipoActual,
    c.correo_asociado
FROM computadora c
LEFT JOIN departamentos d ON c.Id_departamento = d.Id_departamento
LEFT JOIN oficinas o ON c.Id_oficina = o.Id_oficina
WHERE 1=1";

$params = [];
$types = '';

// Aplicar filtros si vienen en la URL
if (isset($_GET['tipo']) && $_GET['tipo'] === 'filtrados') {
    // Procesar filtros
    if (!empty($_GET['filtro_marca'])) {
        $sql .= " AND c.marca = ?";
        $params[] = $_GET['filtro_marca'];
        $types .= 's';
    }
    
    if (!empty($_GET['filtro_status'])) {
        $sql .= " AND c.status = ?";
        $params[] = $_GET['filtro_status'];
        $types .= 's';
    }
}

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

// Crear tabla HTML para el PDF
$html = '<h2>Lista de Computadoras</h2>';
$html .= '<p>Fecha de generación: ' . date('d/m/Y H:i:s') . '</p>';

// Agregar información de filtros si aplica
if (isset($_GET['tipo']) && $_GET['tipo'] === 'filtrados') {
    $html .= '<p><strong>Filtros aplicados:</strong></p><ul>';
    foreach ($_GET as $key => $value) {
        if ($key !== 'tipo' && $value !== '') {
            $html .= '<li>' . htmlspecialchars($key) . ': ' . htmlspecialchars($value) . '</li>';
        }
    }
    $html .= '</ul>';
}

$html .= '<table border="1" cellpadding="4">
    <thead>
        <tr style="background-color:#f2f2f2;">
            <th>ID</th>
            <th>Asignado a</th>
            <th>Departamento</th>
            <th>Oficina</th>
            <th>Tipo</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Procesador</th>
            <th>RAM</th>
            <th>Estado</th>
            <th>Fecha Asignación</th>
            <th>Costo</th>
        </tr>
    </thead>
    <tbody>';

while ($row = $result->fetch_assoc()) {
    $html .= '<tr>';
    $html .= '<td>' . $row['Id_computadora'] . '</td>';
    $html .= '<td>' . $row['asignado_a'] . '</td>';
    $html .= '<td>' . $row['nombre_departamento'] . '</td>';
    $html .= '<td>' . $row['nombre_oficina'] . '</td>';
    $html .= '<td>' . $row['tipo'] . '</td>';
    $html .= '<td>' . $row['marca'] . '</td>';
    $html .= '<td>' . $row['modelo'] . '</td>';
    $html .= '<td>' . $row['procesador'] . '</td>';
    $html .= '<td>' . $row['ram'] . '</td>';
    $html .= '<td>' . $row['status'] . '</td>';
    $html .= '<td>' . $row['fechaDeAsignacion'] . '</td>';
    $html .= '<td>' . $row['costoEquipoActual'] . '</td>';
    $html .= '</tr>';
}

$html .= '</tbody></table>';

// Escribir contenido HTML
$pdf->writeHTML($html, true, false, true, false, '');

// Salida del PDF
$pdf->Output('computadoras_export.pdf', 'I');
exit;
?>