<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
include '../../includes/conexionbd.php';
include '../../includes/middleware.php';

verificarSesion();

$format = $_GET['format'] ?? 'excel';
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$department = $_GET['department'] ?? '';
$role = $_GET['role'] ?? '';
$pcVerification = $_GET['pcVerification'] ?? '';

// Consulta base
$sql = "SELECT id, nombre, correo, Id_departamento, rolActual, estatu FROM usuarios WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (nombre LIKE :search OR correo LIKE :search)";
    $params[':search'] = "%$search%";
}
if ($status) {
    $sql .= " AND estatu = :status";
    $params[':status'] = $status;
}
if ($department) {
    $sql .= " AND Id_departamento = :department";
    $params[':department'] = $department;
}
if ($role) {
    $sql .= " AND rolActual = :role";
    $params[':role'] = $role;
}
if ($pcVerification) {
    $sql .= " AND pc_verificacion = :pcVerification";
    $params[':pcVerification'] = $pcVerification;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si no hay datos, salimos
if (!$data) {
    die("No se encontraron registros para exportar");
}

// ================= EXPORTAR A EXCEL =================
if ($format === 'excel') {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=usuarios.xls");

    // Encabezados de columnas
    echo implode("\t", array_keys($data[0])) . "\n";

    // Filas
    foreach ($data as $row) {
        echo implode("\t", $row) . "\n";
    }
    exit;
}

// ================= EXPORTAR A PDF =================
if ($format === 'pdf') {
    // Usando tabla HTML simple
    $html = "<h2>Lista de Usuarios</h2><table border='1' cellspacing='0' cellpadding='5'>";
    $html .= "<tr>";
    foreach (array_keys($data[0]) as $col) {
        $html .= "<th>$col</th>";
    }
    $html .= "</tr>";

    foreach ($data as $row) {
        $html .= "<tr>";
        foreach ($row as $val) {
            $html .= "<td>$val</td>";
        }
        $html .= "</tr>";
    }
    $html .= "</table>";

    // Generar PDF con Dompdf
    require '../../vendor/autoload.php';
    use Dompdf\Dompdf;

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();
    $dompdf->stream("usuarios.pdf", ["Attachment" => true]);
    exit;
}

// ================= SI NO HAY FORMATO VÁLIDO =================
die("Formato no soportado");
