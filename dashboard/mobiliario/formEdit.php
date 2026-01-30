<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<form id="formMobiliario" method="POST" enctype="multipart/form-data" class="space-y-6 p-4 bg-white rounded-xl">
    <input type="hidden" name="id_mobiliario" id="id_edit">

    <div class="grid grid-cols-2 gap-4">
        <!-- Código de Inventario -->
        <div class="hidden">
            <label for="codigo_inventario_edit" class="block mb-2 text-sm font-semibold text-gray-800">Código de Inventario</label>
            <input type="text" name="codigo_inventario" id="codigo_inventario_edit" placeholder="MOB-2024-XXXX"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Descripción -->
        <div class="col-span-2">
            <label for="descripcion_edit" class="block mb-2 text-sm font-semibold text-gray-800">Descripción</label>
            <input type="text" name="descripcion" id="descripcion_edit" placeholder="Describe el artículo..."
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Categoría -->
        <div class="col-span-2">
            <label for="categoria_edit" class="block mb-2 text-sm font-semibold text-gray-800">Categoría</label>
            <select name="categoria" id="categoria_edit" 
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">Selecciona una categoría</option>
                <option value="Silla">Silla</option>
                <option value="Mesa">Mesa</option>
                <option value="Escritorio">Escritorio</option>
                <option value="Banco">Banco</option>
                <option value="Estante">Estante</option>
                <option value="Archivador">Archivador</option>
                <option value="Librero">Librero</option>
                <option value="Gabinete">Gabinete</option>
                <option value="Locker">Locker</option>
                <option value="Pizarrón">Pizarrón</option>
                <option value="Pantalla">Pantalla</option>
                <option value="Monitor">Monitor</option>
                <option value="CPU">CPU</option>
                <option value="Laptop">Laptop</option>
                <option value="Teclado">Teclado</option>
                <option value="Mouse">Mouse</option>
                <option value="Impresora">Impresora</option>
                <option value="Escáner">Escáner</option>
                <option value="Proyector">Proyector</option>
                <option value="Teléfono">Teléfono</option>
                <option value="Router">Router</option>
                <option value="Switch">Switch</option>
                <option value="Regulador">Regulador</option>
                <option value="No-break">No-break</option>
                <option value="Cableado">Cableado</option>
                <option value="Aire acondicionado">Aire acondicionado</option>
                <option value="Ventilador">Ventilador</option>
                <option value="Lámpara">Lámpara</option>
                <option value="Cafetera">Cafetera</option>
                <option value="Refrigerador">Refrigerador</option>
                <option value="Microondas">Microondas</option>
                <option value="Dispensador de agua">Dispensador de agua</option>
                <option value="Sofá">Sofá</option>
                <option value="Sillón">Sillón</option>
                <option value="Butaca">Butaca</option>
                <option value="Cama">Cama</option>
                <option value="Colchón">Colchón</option>
                <option value="Buró">Buró</option>
                <option value="Ropero">Ropero</option>
                <option value="Otro">Otro</option>
            </select>
        </div>

        <!-- Modelo y Marca -->
        <div>
            <label for="modelo_edit" class="block mb-2 text-sm font-semibold text-gray-800">Modelo</label>
            <input type="text" name="modelo" id="modelo_edit" placeholder="Ej. XT-2000"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <div>
            <label for="marca_edit" class="block mb-2 text-sm font-semibold text-gray-800">Marca</label>
            <input type="text" name="marca" id="marca_edit" placeholder="Ej. Samsung"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Número de Serie -->
        <div>
            <label for="numero_serie_edit" class="block mb-2 text-sm font-semibold text-gray-800">Número de Serie</label>
            <input type="text" name="numero_serie" id="numero_serie_edit" placeholder="Ej. SN123456789"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Condición -->
        <div>
            <label for="condicion_edit" class="block mb-2 text-sm font-semibold text-gray-800">Condición</label>
            <select name="condicion" id="condicion_edit"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="nuevo">Nuevo</option>
                <option value="bueno" selected>Bueno</option>
                <option value="regular">Regular</option>
                <option value="malo">Malo</option>
            </select>
        </div>

        <!-- Estado Actual -->
        <div>
            <label for="estado_actual_edit" class="block mb-2 text-sm font-semibold text-gray-800">Estado Actual</label>
            <select name="estado_actual" id="estado_actual_edit"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="activo" selected>Activo</option>
                <option value="almacenado">Almacenado</option>
                <option value="en reparación">En Reparación</option>
                <option value="baja">Baja</option>
                <option value="candidato para venta">Candidato para venta</option>
            </select>
        </div>

        <!-- Total -->
        <div>
            <label for="total_edit" class="block mb-2 text-sm font-semibold text-gray-800">Cantidad</label>
            <input type="number" name="total" id="total_edit" min="1" value="1" placeholder="1"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Responsable -->
        <div>
            <label for="responsable_edit" class="block mb-2 text-sm font-semibold text-gray-800">Responsable</label>
            <input type="text" name="responsable" id="responsable_edit" placeholder="Nombre del responsable"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Costo -->
        <div>
            <label for="costo_edit" class="block mb-2 text-sm font-semibold text-gray-800">Costo</label>
            <input type="number" step="0.01" name="costo" id="costo_edit" placeholder="0.00"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Fechas -->
        <div>
            <label for="fecha_adquisicion_edit" class="block mb-2 text-sm font-semibold text-gray-800">Fecha Adquisición</label>
            <input type="date" name="fecha_adquisicion" id="fecha_adquisicion_edit"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <div>
            <label for="garantia_vencimiento_edit" class="block mb-2 text-sm font-semibold text-gray-800">Vencimiento Garantía</label>
            <input type="date" name="garantia_vencimiento" id="garantia_vencimiento_edit"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Depreciación Anual -->
        <div>
            <label for="depreciacion_anual_edit" class="block mb-2 text-sm font-semibold text-gray-800">Depreciación Anual (%)</label>
            <input type="number" step="0.01" name="depreciacion_anual" id="depreciacion_anual_edit" placeholder="0.00"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
        </div>

        <!-- Disponibilidad -->
        <div>
            <label for="disponibilidad_edit" class="block mb-2 text-sm font-semibold text-gray-800">Disponibilidad</label>
            <select name="disponibilidad" id="disponibilidad_edit"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="disponible" selected>Disponible</option>
                <option value="no disponible">No Disponible</option>
                <option value="prestado">Prestado</option>
            </select>
        </div>

        <!-- Domicilio -->
        <div class="col-span-2">
            <label for="domicilio_edit" class="block mb-2 text-sm font-semibold text-gray-800">Domicilio</label>
            <select id="domicilio_edit" name="id_domicilio"
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                <option value="">Selecciona un domicilio</option>
            </select>
        </div>

        <!-- Notas -->
        <div class="col-span-2">
            <label for="notas_edit" class="block mb-2 text-sm font-semibold text-gray-800">Notas</label>
            <textarea name="notas" id="notas_edit" rows="3" placeholder="Observaciones adicionales..."
                class="w-full p-3 border border-gray-300 rounded-lg text-sm bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500"></textarea>
        </div>

        <!-- Foto -->
        <div class="col-span-2">
            <label for="foto_edit" class="block mb-2 text-sm font-semibold text-gray-800">Foto</label>
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center bg-gray-50 hover:bg-gray-100 transition-colors">
                <input type="file" name="foto" id="foto_edit" 
                    class="w-full text-sm text-gray-900">
                <p class="text-sm text-gray-500 mt-2">Seleccionar nueva imagen (opcional)</p>
            </div>
        </div>
    </div>

    <!-- Botón Actualizar -->
    <button type="button" id="actualizarMobiliario" 
        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors flex items-center justify-center shadow-md">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M7.707 3.293a1 1 0 010 1.414L5.414 7H11a7 7 0 017 7v2a1 1 0 11-2 0v-2a5 5 0 00-5-5H5.414l2.293 2.293a1 1 0 11-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"/>
        </svg>
        Actualizar Mobiliario
    </button>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function() {
            fetch(`domicilio/get_direccion_id.php`)
                .then(response => response.json())
                .then(data => {
                    const select = document.getElementById('domicilio_edit');
                    data.forEach(domicilio => {
                        const option = document.createElement('option');
                        option.value = domicilio.id;
                        option.textContent = domicilio.direccion;
                        select.appendChild(option);
                    });
                })
            .catch(error => console.error('Error cargando domicilios:', error));
        });
</script>