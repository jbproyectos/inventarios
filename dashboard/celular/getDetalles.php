<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include '../../includes/conexionbd.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Obtener información completa del teléfono
        $stmt = $conexion->prepare("SELECT * FROM telefonos WHERE id = ?");
        $stmt->execute([$id]);
        $telefono = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$telefono) {
            echo '<div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <p class="text-red-700">Teléfono no encontrado</p>
                    </div>
                  </div>';
            exit;
        }

        // Obtener propietario actual
        $stmtProp = $conexion->prepare("
            SELECT * FROM historial_propietarios 
            WHERE telefono_id = ? AND es_actual = 1 
            ORDER BY fecha_asignacion DESC LIMIT 1
        ");
        $stmtProp->execute([$id]);
        $propietarioActual = $stmtProp->fetch(PDO::FETCH_ASSOC);

        // Obtener cuentas iCloud actuales
        $stmtCuentas = $conexion->prepare("
            SELECT ci.* 
            FROM cuentas_icloud ci
            WHERE ci.telefono_id = ? AND ci.es_actual = 1
            ORDER BY ci.created_at DESC
        ");
        $stmtCuentas->execute([$id]);
        $cuentas = $stmtCuentas->fetchAll(PDO::FETCH_ASSOC);

        // Determinar color de batería
        $bateriaColor = '#10b981'; // Verde
        $bateriaIcon = 'fas fa-battery-full';

        if ($telefono['bateria'] < 60) {
            $bateriaColor = '#ef4444';
            $bateriaIcon = 'fas fa-battery-empty';
        } elseif ($telefono['bateria'] < 80) {
            $bateriaColor = '#f97316';
            $bateriaIcon = 'fas fa-battery-quarter';
        } elseif ($telefono['bateria'] < 90) {
            $bateriaColor = '#f59e0b';
            $bateriaIcon = 'fas fa-battery-half';
        } elseif ($telefono['bateria'] < 95) {
            $bateriaColor = '#34d399';
            $bateriaIcon = 'fas fa-battery-three-quarters';
        }

        // Función para determinar estado del teléfono
        function getStatusBadge($status)
        {
            $estados = [
                'ACTIVO' => 'bg-green-100 text-green-800',
                'ASIGNADO A USUARIO' => 'bg-blue-100 text-blue-800',
                'DEVUELTO' => 'bg-yellow-100 text-yellow-800',
                'EN REPARACION' => 'bg-orange-100 text-orange-800',
                'PENDIENTE DE FORMATEO' => 'bg-purple-100 text-purple-800',
                'EN VENTA' => 'bg-indigo-100 text-indigo-800',
                'BAJA DEFINITIVA' => 'bg-red-100 text-red-800',
                'DISPONIBLE EN ALMACEN' => 'bg-gray-100 text-gray-800',
                'ASIGNACION TEMPORAL' => 'bg-cyan-100 text-cyan-800',
                'EN REVISION TECNICA' => 'bg-amber-100 text-amber-800'
            ];

            return $estados[$status] ?? 'bg-gray-100 text-gray-800';
        }

?>
        <div class="space-y-4 max-h-[65vh] overflow-y-auto pr-2">
            <!-- Header principal -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-lg border border-blue-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-white rounded-lg border border-blue-200 flex items-center justify-center">
                            <?php if (stripos($telefono['marca'], 'apple') !== false): ?>
                                <i class="fab fa-apple text-2xl text-gray-800"></i>
                            <?php else: ?>
                                <i class="fas fa-mobile-alt text-2xl text-blue-600"></i>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg"><?= htmlspecialchars($telefono['modelo']) ?></h3>
                            <p class="text-gray-600 text-sm"><?= htmlspecialchars($telefono['marca']) ?> • ID: #<?= $telefono['id'] ?></p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium <?= getStatusBadge($telefono['status']) ?>">
                        <?= htmlspecialchars($telefono['status']) ?>
                    </span>
                </div>
            </div>

            <!-- Información básica en grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Información técnica -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-100">Información Técnica</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">IMEI</span>
                            <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($telefono['imei']) ?></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Batería</span>
                            <div class="flex items-center space-x-2">
                                <i class="<?= $bateriaIcon ?>" style="color: <?= $bateriaColor ?>"></i>
                                <span class="font-medium" style="color: <?= $bateriaColor ?>"><?= $telefono['bateria'] ?>%</span>
                            </div>
                        </div>

                        <?php if ($telefono['costo']): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Costo</span>
                                <span class="font-medium text-gray-900">$<?= number_format($telefono['costo'], 2) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($telefono['puk']): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">PUK</span>
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($telefono['puk']) ?></span>
                            </div>
                        <?php endif; ?>

                        <?php if ($telefono['pin']): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">PIN</span>
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded"><?= htmlspecialchars($telefono['pin']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Propietario actual -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-100">Propietario Actual</h4>
                    <?php if ($propietarioActual): ?>
                        <div class="flex items-center space-x-3 mb-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 truncate"><?= htmlspecialchars($propietarioActual['nombre']) ?></p>
                                <p class="text-xs text-gray-500">Asignado: <?= date('d/m/Y', strtotime($propietarioActual['fecha_asignacion'])) ?></p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <?php if ($propietarioActual['mismo_numero'] == 1 && $propietarioActual['numero_contacto']): ?>
                                <div class="flex items-center text-sm text-gray-700">
                                    <i class="fas fa-phone text-green-600 mr-2 w-4"></i>
                                    <span class="truncate"><?= htmlspecialchars($propietarioActual['numero_contacto']) ?></span>
                                    <span class="ml-2 text-xs bg-green-100 text-green-800 px-1 py-0.5 rounded whitespace-nowrap">Único</span>
                                </div>
                            <?php else: ?>
                                <?php if ($propietarioActual['numero_llamadas']): ?>
                                    <div class="flex items-center text-sm text-gray-700">
                                        <i class="fas fa-phone text-blue-600 mr-2 w-4"></i>
                                        <span class="truncate"><?= htmlspecialchars($propietarioActual['numero_llamadas']) ?></span>
                                    </div>
                                <?php endif; ?>
                                <?php if ($propietarioActual['numero_whatsapp']): ?>
                                    <div class="flex items-center text-sm text-gray-700">
                                        <i class="fab fa-whatsapp text-green-600 mr-2 w-4"></i>
                                        <span class="truncate"><?= htmlspecialchars($propietarioActual['numero_whatsapp']) ?></span>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-3">
                            <i class="fas fa-user-slash text-2xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500 text-sm">Sin propietario asignado</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Ubicación y venta -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-100">Ubicación</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Oficina</span>
                            <span class="font-medium text-gray-900"><?= htmlspecialchars($telefono['oficina'] ?: 'No asignada') ?></span>
                        </div>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Departamento</span>
                            <span class="font-medium text-gray-900"><?= htmlspecialchars($telefono['departamento_actual'] ?: 'No asignado') ?></span>
                        </div>

                        <?php if ($telefono['posible_venta']): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Posible Venta</span>
                                <span class="font-medium text-gray-900"><?= htmlspecialchars($telefono['posible_venta']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Información adicional -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-100">Información Adicional</h4>
                    <div class="space-y-3">
                        <?php if (!empty($telefono['created_at'])): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Fecha de registro</span>
                                <span class="font-medium text-gray-900 text-xs"><?= date('d/m/Y', strtotime($telefono['created_at'])) ?></span>
                            </div>
                        <?php endif; ?>


                        <?php if ($telefono['updated_at'] && $telefono['updated_at'] != $telefono['created_at']): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Última actualización</span>
                                <span class="font-medium text-gray-900 text-xs"><?= date('d/m/Y', strtotime($telefono['updated_at'])) ?></span>
                            </div>
                        <?php endif; ?>

                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Cuentas iCloud</span>
                            <span class="font-medium text-gray-900"><?= count($cuentas) ?> activa(s)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cuentas iCloud -->
            <?php if (!empty($cuentas)): ?>
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-100">Cuentas iCloud Activas</h4>
                    <div class="space-y-3">
                        <?php foreach ($cuentas as $cuenta): ?>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex items-center space-x-2">
                                        <i class="fab fa-apple text-gray-700"></i>
                                        <span class="font-medium text-gray-900 text-sm truncate" title="<?= htmlspecialchars($cuenta['icloud']) ?>">
                                            <?= htmlspecialchars($cuenta['icloud']) ?>
                                        </span>
                                    </div>
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Activa</span>
                                </div>

                                <?php if (!empty($cuenta['password'])): ?>
                                    <div class="mt-2">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs text-gray-500">Contraseña</span>
                                            <button type="button"
                                                class="toggle-password text-xs text-blue-600 hover:text-blue-800"
                                                data-password="<?= htmlspecialchars($cuenta['password']) ?>">
                                                <i class="fas fa-eye mr-1"></i>
                                                <span>Mostrar</span>
                                            </button>
                                        </div>
                                        <div class="mt-1 flex items-center">
                                            <input type="password"
                                                value="<?= htmlspecialchars($cuenta['password']) ?>"
                                                class="password-field w-full px-2 py-1 text-xs bg-white border rounded font-mono"
                                                readonly>
                                            <button type="button" class="copy-password ml-2 text-gray-500 hover:text-gray-700 text-sm"
                                                data-password="<?= htmlspecialchars($cuenta['password']) ?>"
                                                title="Copiar contraseña">
                                                <i class="far fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Observaciones -->
            <?php if (!empty($telefono['observaciones'])): ?>
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h4 class="font-semibold text-gray-900 mb-3 pb-2 border-b border-gray-100">Observaciones</h4>
                    <div class="p-3 bg-amber-50 border border-amber-200 rounded">
                        <div class="flex items-start">
                            <i class="fas fa-sticky-note text-amber-600 mt-1 mr-2"></i>
                            <p class="text-gray-700 text-sm whitespace-pre-wrap"><?= nl2br(htmlspecialchars($telefono['observaciones'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <script>
            // Mostrar/ocultar contraseñas
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function() {
                    const password = this.getAttribute('data-password');
                    const icon = this.querySelector('i');
                    const textSpan = this.querySelector('span');
                    const inputField = this.closest('.mt-2').querySelector('.password-field');

                    if (inputField.type === 'password') {
                        inputField.type = 'text';
                        icon.className = 'fas fa-eye-slash mr-1';
                        textSpan.textContent = 'Ocultar';
                    } else {
                        inputField.type = 'password';
                        icon.className = 'fas fa-eye mr-1';
                        textSpan.textContent = 'Mostrar';
                    }
                });
            });

            // Copiar contraseña al portapapeles
            document.querySelectorAll('.copy-password').forEach(button => {
                button.addEventListener('click', function() {
                    const password = this.getAttribute('data-password');

                    navigator.clipboard.writeText(password).then(() => {
                        // Cambiar icono temporalmente
                        const icon = this.querySelector('i');
                        icon.className = 'fas fa-check text-green-500';

                        setTimeout(() => {
                            icon.className = 'far fa-copy';
                        }, 2000);
                    });
                });
            });

            // Mejorar scroll del modal
            const modalContent = document.querySelector('.max-h-\\[65vh\\]');
            if (modalContent) {
                // Añadir estilos para scrollbar personalizada
                modalContent.style.scrollbarWidth = 'thin';
                modalContent.style.scrollbarColor = '#cbd5e1 #f8fafc';
            }
        </script>
<?php

    } catch (PDOException $e) {
        echo '<div class="p-3 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    <p class="text-red-700">Error: ' . htmlspecialchars($e->getMessage()) . '</p>
                </div>
              </div>';
    }
} else {
    echo '<div class="p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-yellow-500 mr-2"></i>
                <p class="text-yellow-700">ID no especificado</p>
            </div>
          </div>';
}
?>