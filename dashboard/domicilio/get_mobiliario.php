<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json; charset=utf-8');

include '../../includes/conexionbd.php';

$id_domicilio = isset($_GET['id_domicilio']) ? intval($_GET['id_domicilio']) : 0;

if ($id_domicilio <= 0) {
    echo "<p class='text-red-600 p-4'>ID de domicilio no válido.</p>";
    exit;
}

$stmt = $conexion->prepare("SELECT codigo_inventario, descripcion, categoria, marca, modelo, estado_actual
                       FROM mobiliario
                       WHERE id_domicilio = ?");
$stmt->execute([$id_domicilio]);

$muebles = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$muebles) {
    echo "<div class='p-4 text-center'>
            <p class='text-gray-600 mb-4'>No hay mobiliario registrado para este domicilio.</p>
            <a href='inv_mobiliario.php' class='inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors'>
                <svg class='w-4 h-4 mr-2' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                    <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'></path>
                </svg>
                Ver Inventario Completo
            </a>
          </div>";
    exit;
}

$num_muebles = count($muebles);
?>

<div class="bg-white rounded-lg shadow-sm border">
    <!-- Header con contador y botón -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 border-b bg-gray-50 rounded-t-lg gap-3">
        <div class="flex items-center space-x-2">
            <h3 class="text-lg font-semibold text-gray-800">Mobiliario del Domicilio</h3>
            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">
                <?php echo $num_muebles; ?> items
            </span>
        </div>
        <a href="inv_mobiliario.php" 
           class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white px-3 py-1.5 rounded text-sm transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Inventario Completo
        </a>
    </div>

    <!-- Tabla responsive -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th class="px-4 py-3 border-b">Código</th>
                    <th class="px-4 py-3 border-b">Descripción</th>
                    <th class="px-4 py-3 border-b hidden sm:table-cell">Categoría</th>
                    <th class="px-4 py-3 border-b hidden md:table-cell">Marca</th>
                    <th class="px-4 py-3 border-b hidden lg:table-cell">Modelo</th>
                    <th class="px-4 py-3 border-b">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($muebles as $m): 
                    $estado_class = 'bg-gray-100 text-gray-800';
                    if ($m['estado_actual'] == 'Excelente') $estado_class = 'bg-green-100 text-green-800';
                    if ($m['estado_actual'] == 'Bueno') $estado_class = 'bg-blue-100 text-blue-800';
                    if ($m['estado_actual'] == 'Regular') $estado_class = 'bg-yellow-100 text-yellow-800';
                    if ($m['estado_actual'] == 'Malo') $estado_class = 'bg-red-100 text-red-800';
                ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-3 font-mono text-xs border-b"><?php echo htmlspecialchars($m['codigo_inventario']); ?></td>
                    <td class="px-4 py-3 border-b">
                        <div class="font-medium"><?php echo htmlspecialchars($m['descripcion']); ?></div>
                        <div class="text-xs text-gray-500 sm:hidden mt-1">
                            <span class="inline-block bg-gray-100 px-1 rounded mr-2"><?php echo htmlspecialchars($m['categoria']); ?></span>
                            <span class="inline-block bg-gray-100 px-1 rounded"><?php echo htmlspecialchars($m['marca']); ?></span>
                        </div>
                    </td>
                    <td class="px-4 py-3 border-b hidden sm:table-cell"><?php echo htmlspecialchars($m['categoria']); ?></td>
                    <td class="px-4 py-3 border-b hidden md:table-cell"><?php echo htmlspecialchars($m['marca']); ?></td>
                    <td class="px-4 py-3 border-b hidden lg:table-cell"><?php echo htmlspecialchars($m['modelo']); ?></td>
                    <td class="px-4 py-3 border-b">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $estado_class; ?>">
                            <?php echo htmlspecialchars($m['estado_actual']); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="p-3 border-t bg-gray-50 text-xs text-gray-600 rounded-b-lg">
        <div class="flex flex-col sm:flex-row justify-between items-center space-y-2 sm:space-y-0">
            <div class="text-center sm:text-left">
                Mostrando <span class="font-semibold"><?php echo $num_muebles; ?></span> items de mobiliario
            </div>
            <div class="flex items-center space-x-4">
                <span class="hidden sm:inline">Para más detalles:</span>
                <a href="inv_mobiliario.php" 
                   class="text-blue-600 hover:text-blue-800 font-medium underline transition-colors">
                    Ver inventario completo
                </a>
            </div>
        </div>
    </div>
</div>