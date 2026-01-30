<?php
include '../../includes/middleware.php';
include "../../includes/conexionbd.php";

$id = $_GET['id'];

$stmt = $conexion->prepare("
    SELECT h.*, t.modelo, t.marca, t.imei
    FROM historial_propietarios h
    JOIN telefonos t ON h.telefono_id = t.id
    WHERE h.telefono_id = ?
    ORDER BY h.fecha_asignacion DESC
");
$stmt->execute([$id]);
$historial = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<div class="space-y-4">';
echo '<div class="mb-4">';
echo '<h4 class="text-lg font-semibold text-gray-700">' . htmlspecialchars($historial[0]['modelo']) . ' ' . htmlspecialchars($historial[0]['marca']) . '</h4>';
echo '<p class="text-sm text-gray-600">IMEI: ' . htmlspecialchars($historial[0]['imei']) . '</p>';
echo '</div>';

if (empty($historial)) {
    echo '<p class="text-gray-500 text-center py-4">No hay historial de propietarios</p>';
} else {
    echo '<div class="overflow-x-auto">';
    echo '<table class="w-full text-sm text-left text-gray-500">';
    echo '<thead class="text-xs text-gray-700 uppercase bg-gray-50">';
    echo '<tr>';
    echo '<th class="px-4 py-3">Propietario</th>';
    echo '<th class="px-4 py-3">Fecha Asignación</th>';
    echo '<th class="px-4 py-3">Wp B</th>';
    echo '<th class="px-4 py-3">Contacto</th>';
    echo '<th class="px-4 py-3">Estado</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    foreach ($historial as $item) {
        $estado = $item['es_actual'] ? '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">Actual</span>' : '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">Anterior</span>';
        
        echo '<tr class="bg-white border-b hover:bg-gray-50">';
        echo '<td class="px-4 py-3 font-medium text-gray-900">' . htmlspecialchars($item['nombre']) . '</td>';
        echo '<td class="px-4 py-3">' . htmlspecialchars($item['fecha_asignacion']) . '</td>';
        echo '<td class="px-4 py-3">' . htmlspecialchars($item['numero_whatsapp']) . '</td>';
        
        // Mostrar información de contacto
        echo '<td class="px-4 py-3">';
        if ($item['mismo_numero']) {
            echo htmlspecialchars($item['numero_contacto'] ?: 'Sin número');
        } else {
            echo 'Llamadas: ' . htmlspecialchars($item['numero_llamadas'] ?: 'N/A') . '<br>';
            echo 'WhatsApp: ' . htmlspecialchars($item['numero_whatsapp'] ?: 'N/A');
        }
        echo '</td>';
        
        echo '<td class="px-4 py-3">' . $estado . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}

echo '</div>';
?>