<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../../includes/conexionbd.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Obtener información del teléfono
        $stmt = $conexion->prepare("SELECT * FROM telefonos WHERE id = ?");
        $stmt->execute([$id]);
        $telefono = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$telefono) {
            echo "<p class='text-red-600'>Teléfono no encontrado</p>";
            exit;
        }

        // Obtener SOLO el propietario actual (es_actual = 1) - CAMBIO IMPORTANTE
        $stmtProp = $conexion->prepare("SELECT * FROM historial_propietarios WHERE telefono_id = ? AND es_actual = 1 ORDER BY fecha_asignacion DESC LIMIT 1");
        $stmtProp->execute([$id]);
        $propietarioActual = $stmtProp->fetch(PDO::FETCH_ASSOC);

        // Si no hay propietario actual, crear uno vacío
        if (!$propietarioActual) {
            $propietarioActual = [
                'id' => '',
                'nombre' => '',
                'fecha_asignacion' => date('Y-m-d'),
                'mismo_numero' => 1,
                'numero_contacto' => '',
                'numero_llamadas' => '',
                'numero_whatsapp' => '',
                'es_actual' => 1
            ];
        }

        // Crear un array con solo el propietario actual para la vista
        $propietarios = [$propietarioActual];

        // Obtener cuentas iCloud solo para el propietario actual
        if ($propietarioActual['id']) {
            $stmtCuentas = $conexion->prepare("
                SELECT ci.* 
                FROM cuentas_icloud ci
                WHERE ci.telefono_id = ? AND ci.propietario_id = ?
                ORDER BY ci.id
            ");
            $stmtCuentas->execute([$id, $propietarioActual['id']]);
            $cuentas = $stmtCuentas->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $cuentas = [];
        }

        // Organizar cuentas por propietario (solo para el actual)
        $cuentasPorPropietario = [];
        if ($propietarioActual['id']) {
            $cuentasPorPropietario[$propietarioActual['id']] = $cuentas;
        }

        // Obtener listas para dropdowns
        $oficinas = $conexion->query("SELECT DISTINCT nombre FROM oficina WHERE nombre IS NOT NULL ORDER BY nombre")->fetchAll(PDO::FETCH_COLUMN);
        $departamentos = $conexion->query("SELECT DISTINCT nombre FROM departamentos WHERE nombre IS NOT NULL ORDER BY nombre")->fetchAll(PDO::FETCH_COLUMN);
        $empleados = $conexion->query("SELECT DISTINCT nombre FROM Empleados WHERE nombre IS NOT NULL ORDER BY nombre")->fetchAll(PDO::FETCH_COLUMN);

        $marcas = ['Apple', 'Samsung', 'Xiaomi', 'Huawei', 'Motorola', 'LG', 'Google', 'OnePlus', 'Sony', 'Nokia', 'Oppo', 'Vivo', 'Realme', 'Tecno', 'Infinix', 'Otro'];

?>
        <div id="formEditTelefono" class="space-y-6 max-h-[70vh] overflow-y-auto">
            <input type="hidden" id="editTelefonoId" value="<?= $telefono['id'] ?>">

            <!-- Información básica -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="modelo_edit" class="block mb-2 text-sm font-medium text-gray-900">Modelo *</label>
                    <input type="text" id="modelo_edit" value="<?= htmlspecialchars($telefono['modelo']) ?>" required
                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="marca_edit" class="block mb-2 text-sm font-medium text-gray-900">Marca *</label>
                    <select id="marca_edit" required
                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccionar marca</option>
                        <?php foreach ($marcas as $marca): ?>
                            <option value="<?= htmlspecialchars($marca) ?>" <?= $telefono['marca'] == $marca ? 'selected' : '' ?>>
                                <?= htmlspecialchars($marca) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="bateria_edit" class="block mb-2 text-sm font-medium text-gray-900">Estado de Batería (%) *</label>
                    <div class="flex items-center space-x-4">
                        <input type="range" id="bateria_edit" min="0" max="100" value="<?= $telefono['bateria'] ?>"
                            class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                            oninput="document.getElementById('bateriaValueEdit').textContent = this.value + '%'">
                        <span id="bateriaValueEdit" class="text-lg font-semibold text-gray-700 min-w-[60px]"><?= $telefono['bateria'] ?>%</span>
                    </div>
                    <div class="text-xs text-gray-500 mt-1">
                        <span id="bateriaEstadoEdit"><?php
                                                        if ($telefono['bateria'] < 80) echo 'Malo - Necesita cambio urgente';
                                                        elseif ($telefono['bateria'] < 90) echo 'Regular - Considerar cambio pronto';
                                                        elseif ($telefono['bateria'] < 95) echo 'Bueno - Funciona correctamente';
                                                        else echo 'Excelente - No necesita cambio';
                                                        ?></span>
                    </div>
                </div>

                <div>
                    <label for="imei_edit" class="block mb-2 text-sm font-medium text-gray-900">IMEI *</label>
                    <input type="text" id="imei_edit" value="<?= htmlspecialchars($telefono['imei']) ?>" required
                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Ubicación -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="oficina_edit" class="block mb-2 text-sm font-medium text-gray-900">Oficina</label>
                    <select id="oficina_edit"
                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccionar oficina</option>
                        <?php foreach ($oficinas as $ofi): ?>
                            <option value="<?= htmlspecialchars($ofi) ?>" <?= $telefono['oficina'] == $ofi ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ofi) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="departamento_actual_edit" class="block mb-2 text-sm font-medium text-gray-900">Departamento Actual</label>
                    <select id="departamento_actual_edit"
                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Seleccionar departamento</option>
                        <?php foreach ($departamentos as $depto): ?>
                            <option value="<?= htmlspecialchars($depto) ?>" <?= $telefono['departamento_actual'] == $depto ? 'selected' : '' ?>>
                                <?= htmlspecialchars($depto) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Propietarios - SOLO se muestra el actual -->
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h4 class="text-lg font-medium text-gray-900">Propietario Actual</h4>
                    <button type="button" id="btn-agregar-propietario-edit" class="btn-agregar" style="display: none;">
                        <i class="fas fa-plus mr-1"></i> Agregar Propietario
                    </button>
                </div>

                <div id="propietarios-container-edit" data-prop-index="0">
                    <?php
                    $propIndex = 0;
                    foreach ($propietarios as $prop):
                    ?>
                        <div class="propietario-container propietario-actual mt-4"
                            id="propietario-edit-<?= $propIndex ?>-container" data-prop-id="<?= $prop['id'] ?>">
                            <i class="fas fa-star text-yellow-500 absolute top-2 right-8"></i>

                            <button type="button" class="btn-eliminar" style="display: none;"
                                onclick="eliminarPropietarioEdit(<?= $propIndex ?>)">
                                <i class="fas fa-times"></i>
                            </button>

                            <h5 class="font-medium text-gray-700 mb-4">
                                Propietario Actual *
                            </h5>

                            <input type="hidden" class="propietario-id" value="<?= $prop['id'] ?>">
                            <input type="hidden" class="propietario-es-actual" value="1">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Nombre</label>
                                    <select class="propietario-nombre w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                                        <option value="">Seleccionar empleado</option>
                                        <?php foreach ($empleados as $emp): ?>
                                            <option value="<?= htmlspecialchars($emp) ?>" <?= $prop['nombre'] == $emp ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($emp) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900">Fecha Asignación</label>
                                    <input type="date" class="propietario-fecha w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                        value="<?= $prop['fecha_asignacion'] ?>">
                                </div>
                            </div>

                            <!-- Información de contacto -->
                            <div class="space-y-4 mb-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="mismo_numero_edit_<?= $propIndex ?>"
                                        class="propietario-mismo-numero mr-2" value="1"
                                        <?= $prop['mismo_numero'] == 1 ? 'checked' : '' ?>
                                        onchange="toggleNumerosContactoEdit(<?= $propIndex ?>)">
                                    <label for="mismo_numero_edit_<?= $propIndex ?>" class="text-sm text-gray-700">
                                        Usar el mismo número para llamadas y WhatsApp
                                    </label>
                                </div>

                                <div id="contacto-unico-edit-<?= $propIndex ?>"
                                    class="grid grid-cols-1 gap-4"
                                    style="<?= $prop['mismo_numero'] == 1 ? '' : 'display: none;' ?>">
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Número de Contacto</label>
                                        <input type="text" class="propietario-numero-contacto w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                            value="<?= htmlspecialchars($prop['numero_contacto']) ?>"
                                            placeholder="Ej: 555-123-4567">
                                    </div>
                                </div>

                                <div id="contacto-separado-edit-<?= $propIndex ?>"
                                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                                    style="<?= $prop['mismo_numero'] == 1 ? 'display: none;' : '' ?>">
                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Número para Llamadas</label>
                                        <input type="text" class="propietario-numero-llamadas w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                            value="<?= htmlspecialchars($prop['numero_llamadas']) ?>"
                                            placeholder="Ej: 555-123-4567">
                                    </div>

                                    <div>
                                        <label class="block mb-2 text-sm font-medium text-gray-900">Número para WhatsApp</label>
                                        <input type="text" class="propietario-numero-whatsapp w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                            value="<?= htmlspecialchars($prop['numero_whatsapp']) ?>"
                                            placeholder="Ej: 555-987-6543">
                                    </div>
                                </div>
                            </div>

                            <!-- Cuentas iCloud -->
                            <div class="cuentas-container">
                                <div class="flex items-center justify-between mb-3">
                                    <h6 class="font-medium text-gray-600">Cuentas iCloud</h6>
                                    <button type="button" class="text-xs btn-agregar"
                                        onclick="agregarCuentaIcloudEdit(<?= $propIndex ?>)">
                                        <i class="fas fa-plus mr-1"></i> Agregar Cuenta
                                    </button>
                                </div>
                                <div id="cuentas-icloud-edit-<?= $propIndex ?>" class="space-y-3" data-cuenta-index="<?= isset($cuentasPorPropietario[$prop['id']]) ? count($cuentasPorPropietario[$prop['id']]) : 1 ?>">
                                    <?php
                                    if (isset($cuentasPorPropietario[$prop['id']])) {
                                        $propCuentas = $cuentasPorPropietario[$prop['id']];
                                    } else {
                                        $propCuentas = [];
                                    }
                                    
                                    if (empty($propCuentas)) {
                                        // Mostrar al menos un campo vacío
                                    ?>
                                        <div class="cuenta-container">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-900">Cuenta iCloud</label>
                                                    <input type="email" class="cuenta-icloud w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                                        placeholder="ejemplo@icloud.com">
                                                </div>
                                                <div>
                                                    <label class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                                                    <div class="flex">
                                                        <input type="password" class="cuenta-password w-full p-2.5 border border-gray-300 rounded-l-lg focus:ring-blue-500 focus:border-blue-500">
                                                        <button type="button" class="toggle-password bg-gray-200 px-4 rounded-r-lg hover:bg-gray-300">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-eliminar" onclick="eliminarCuenta(this)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <?php
                                    } else {
                                        foreach ($propCuentas as $cuenta):
                                        ?>
                                            <div class="cuenta-container" data-cuenta-id="<?= $cuenta['id'] ?>">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block mb-2 text-sm font-medium text-gray-900">Cuenta iCloud</label>
                                                        <input type="email" class="cuenta-icloud w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                                            value="<?= htmlspecialchars($cuenta['icloud']) ?>"
                                                            placeholder="ejemplo@icloud.com">
                                                    </div>
                                                    <div>
                                                        <label class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                                                        <div class="flex">
                                                            <input type="password" class="cuenta-password w-full p-2.5 border border-gray-300 rounded-l-lg focus:ring-blue-500 focus:border-blue-500"
                                                                value="<?= htmlspecialchars($cuenta['password']) ?>">
                                                            <button type="button" class="toggle-password bg-gray-200 px-4 rounded-r-lg hover:bg-gray-300">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn-eliminar" onclick="eliminarCuenta(this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                    <?php
                                        endforeach;
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php
                        $propIndex++;
                    endforeach;
                    ?>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="status_edit" class="block mb-2 text-sm font-medium text-gray-900">Status</label>
                    <select id="status_edit" class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="Disponible en almacen" <?= $telefono['status'] == 'Disponible en almacen' ? 'selected' : '' ?>>Disponible en almacen</option>
                        <option value="Asignado a usuario" <?= $telefono['status'] == 'Asignado a usuario' ? 'selected' : '' ?>>Asignado a usuario</option>
                        <option value="Asignacion temporal" <?= $telefono['status'] == 'Asignacion temporal' ? 'selected' : '' ?>>Asignacion temporal</option>
                        <option value="Devuelto" <?= $telefono['status'] == 'Devuelto' ? 'selected' : '' ?>>Devuelto</option>
                        <option value="En revision tecnica" <?= $telefono['status'] == 'En revision tecnica' ? 'selected' : '' ?>>En revision tecnica</option>
                        <option value="Pendiente de formateo" <?= $telefono['status'] == 'Pendiente de formateo' ? 'selected' : '' ?>>Pendiente de formateo</option>
                        <option value="En venta" <?= $telefono['status'] == 'En venta' ? 'selected' : '' ?>>En venta</option>
                        <option value="Baja definitiva" <?= $telefono['status'] == 'Baja definitiva' ? 'selected' : '' ?>>Baja definitiva</option>
                    </select>
                </div>

                <div>
                    <label for="posible_venta_edit" class="block mb-2 text-sm font-medium text-gray-900">Posible Venta</label>
                    <input type="text" id="posible_venta_edit"
                        value="<?= htmlspecialchars($telefono['posible_venta']) ?>"
                        class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ej: 2024, 2025, Próximo año...">
                </div>
            </div>

            <div>
                <label for="observaciones_edit" class="block mb-2 text-sm font-medium text-gray-900">Observaciones</label>
                <textarea id="observaciones_edit" rows="3"
                    class="w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <?= htmlspecialchars($telefono['observaciones'] !== null ? $telefono['observaciones'] : '') ?>

                </textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button type="button" data-modal-toggle="crud-edit" class="px-5 py-2.5 text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">
                    Cancelar
                </button>
                <button type="button" id="btn-actualizar-telefono" class="px-5 py-2.5 text-white bg-blue-700 hover:bg-blue-800 rounded-lg">
                    <i class="fas fa-save mr-2"></i> Actualizar Teléfono
                </button>
            </div>
        </div>

        <script>
            // Variables para edición
            let propietarioCountEdit = 0; // Siempre será 0 porque solo mostramos el actual

            // Ocultar el botón de agregar propietario
            document.getElementById('btn-agregar-propietario-edit').style.display = 'none';

            // Actualizar estado de batería en edición
            document.getElementById('bateria_edit').addEventListener('input', function() {
                const valor = parseInt(this.value);
                const estado = document.getElementById('bateriaEstadoEdit');
                const valorSpan = document.getElementById('bateriaValueEdit');

                valorSpan.textContent = valor + '%';

                if (valor < 80) {
                    estado.textContent = 'Malo - Necesita cambio urgente';
                    estado.style.color = '#dc2626';
                } else if (valor < 90) {
                    estado.textContent = 'Regular - Considerar cambio pronto';
                    estado.style.color = '#f59e0b';
                } else if (valor < 95) {
                    estado.textContent = 'Bueno - Funciona correctamente';
                    estado.style.color = '#10b981';
                } else {
                    estado.textContent = 'Excelente - No necesita cambio';
                    estado.style.color = '#10b981';
                }
            });

            // Funciones para edición (simplificadas ya que solo hay un propietario)
            function toggleNumerosContactoEdit(propietarioId) {
                const checkbox = document.getElementById(`mismo_numero_edit_${propietarioId}`);
                const contactoUnico = document.getElementById(`contacto-unico-edit-${propietarioId}`);
                const contactoSeparado = document.getElementById(`contacto-separado-edit-${propietarioId}`);

                if (checkbox.checked) {
                    contactoUnico.style.display = 'grid';
                    contactoSeparado.style.display = 'none';
                } else {
                    contactoUnico.style.display = 'none';
                    contactoSeparado.style.display = 'grid';
                }
            }

            function agregarCuentaIcloudEdit(propietarioId) {
                const container = document.getElementById(`cuentas-icloud-edit-${propietarioId}`);
                const cuentaIndex = parseInt(container.getAttribute('data-cuenta-index')) || 1;

                const nuevaCuenta = document.createElement('div');
                nuevaCuenta.className = 'cuenta-container';
                nuevaCuenta.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Cuenta iCloud</label>
                            <input type="email" class="cuenta-icloud w-full p-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="ejemplo@icloud.com">
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                            <div class="flex">
                                <input type="password" class="cuenta-password w-full p-2.5 border border-gray-300 rounded-l-lg focus:ring-blue-500 focus:border-blue-500">
                                <button type="button" class="toggle-password bg-gray-200 px-4 rounded-r-lg hover:bg-gray-300">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn-eliminar" onclick="eliminarCuenta(this)">
                        <i class="fas fa-times"></i>
                    </button>
                `;

                container.appendChild(nuevaCuenta);
                container.setAttribute('data-cuenta-index', cuentaIndex + 1);
            }

            // Eliminar cuenta iCloud
            function eliminarCuenta(button) {
                button.closest('.cuenta-container').remove();
            }

            // Toggle para mostrar/ocultar contraseñas
            document.addEventListener('click', function(e) {
                if (e.target.closest('.toggle-password')) {
                    const button = e.target.closest('.toggle-password');
                    const input = button.previousElementSibling;
                    const icon = button.querySelector('i');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.className = 'fas fa-eye-slash';
                    } else {
                        input.type = 'password';
                        icon.className = 'fas fa-eye';
                    }
                }
            });

            // Botón para actualizar teléfono
            document.getElementById('btn-actualizar-telefono').addEventListener('click', function() {
                // Recoger datos del teléfono
                const telefonoData = {
                    id: document.getElementById('editTelefonoId').value,
                    modelo: document.getElementById('modelo_edit').value,
                    marca: document.getElementById('marca_edit').value,
                    bateria: document.getElementById('bateria_edit').value,
                    imei: document.getElementById('imei_edit').value,
                    oficina: document.getElementById('oficina_edit').value,
                    departamento_actual: document.getElementById('departamento_actual_edit').value,
                    status: document.getElementById('status_edit').value,
                    posible_venta: document.getElementById('posible_venta_edit').value,
                    observaciones: document.getElementById('observaciones_edit').value,
                    propietarios: []
                };

                // Validar campos requeridos
                if (!telefonoData.modelo || !telefonoData.marca || !telefonoData.imei) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Los campos Modelo, Marca e IMEI son requeridos',
                        confirmButtonText: 'Cerrar'
                    });
                    return;
                }

                // Recoger SOLO el propietario actual
                const propContainer = document.querySelector('.propietario-container');
                if (propContainer) {
                    const propData = {
                        id: propContainer.querySelector('.propietario-id')?.value || '',
                        es_actual: '1', // Siempre será actual
                        nombre: propContainer.querySelector('.propietario-nombre')?.value || '',
                        fecha_asignacion: propContainer.querySelector('.propietario-fecha')?.value || '',
                        mismo_numero: propContainer.querySelector('.propietario-mismo-numero')?.checked ? '1' : '0',
                        numero_contacto: propContainer.querySelector('.propietario-numero-contacto')?.value || '',
                        numero_llamadas: propContainer.querySelector('.propietario-numero-llamadas')?.value || '',
                        numero_whatsapp: propContainer.querySelector('.propietario-numero-whatsapp')?.value || '',
                        cuentas: []
                    };

                    // Si es mismo número, usar numero_contacto para ambos
                    if (propData.mismo_numero === '1' && propData.numero_contacto) {
                        propData.numero_llamadas = propData.numero_contacto;
                        propData.numero_whatsapp = propData.numero_contacto;
                    }

                    // Recoger cuentas iCloud
                    const cuentaContainers = propContainer.querySelectorAll('.cuenta-container');
                    cuentaContainers.forEach(cuentaContainer => {
                        const cuentaData = {
                            id: cuentaContainer.getAttribute('data-cuenta-id') || '',
                            icloud: cuentaContainer.querySelector('.cuenta-icloud')?.value || '',
                            password: cuentaContainer.querySelector('.cuenta-password')?.value || ''
                        };

                        if (cuentaData.icloud) {
                            propData.cuentas.push(cuentaData);
                        }
                    });

                    telefonoData.propietarios.push(propData);
                }

                // Validar propietario actual
                const propietarioActual = telefonoData.propietarios.find(p => p.es_actual === '1');
                if (!propietarioActual || !propietarioActual.nombre) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Debe haber al menos un Propietario Actual con nombre',
                        confirmButtonText: 'Cerrar'
                    });
                    return;
                }

                // Mostrar loading
                Swal.fire({
                    title: 'Actualizando...',
                    text: 'Por favor espere',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });

                // Enviar datos
                fetch('celular/updateTelefono.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(telefonoData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.close();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Éxito!',
                                text: data.message,
                                confirmButtonText: 'Aceptar'
                            }).then(() => {
                                // Cerrar modal y recargar página
                                const modal = document.getElementById('crud-edit');
                                modal.classList.add('hidden');
                                modal.setAttribute('aria-hidden', 'true');
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                                confirmButtonText: 'Cerrar'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.close();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo actualizar el teléfono',
                            confirmButtonText: 'Cerrar'
                        });
                        console.error('Error:', error);
                    });
            });

            // Inicializar toggle para el propietario actual
            document.getElementById('mismo_numero_edit_0')?.addEventListener('change', function() {
                toggleNumerosContactoEdit(0);
            });
        </script>
<?php

    } catch (PDOException $e) {
        echo "<p class='text-red-600'>Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p class='text-red-600'>ID no especificado</p>";
}