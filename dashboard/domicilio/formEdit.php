<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<form id="formDomicilio" method="POST" enctype="multipart/form-data" class="space-y-4 p-4">
    <input type="hidden" name="id" id="id_edit">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <label for="direccion_edit" class="block mb-1 text-sm font-semibold text-gray-900">Dirección</label>
            <input type="text" name="direccion" id="direccion_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="empresa1_edit" class="block mb-1 text-sm font-semibold text-gray-900">Empresa 1</label>
            <input type="text" name="empresa1" id="empresa1_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="empresa2_edit" class="block mb-1 text-sm font-semibold text-gray-900">Empresa 2</label>
            <input type="text" name="empresa2" id="empresa2_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="municipio_edit" class="block mb-1 text-sm font-semibold text-gray-900">Municipio</label>
            <input type="text" name="municipio" id="municipio_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="ubicacion_edit" class="block mb-1 text-sm font-semibold text-gray-900">Ubicación</label>
            <input type="text" name="ubicacion" id="ubicacion_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="escritorios_edit" class="block mb-1 text-sm font-semibold text-gray-900">Escritorios</label>
            <input type="number" name="escritorios" id="escritorios_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="sillas_de_escritorios_edit" class="block mb-1 text-sm font-semibold text-gray-900">Sillas de Escritorio</label>
            <input type="number" name="sillas_de_escritorios" id="sillas_de_escritorios_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="sillas_edit" class="block mb-1 text-sm font-semibold text-gray-900">Sillas</label>
            <input type="number" name="sillas" id="sillas_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="mesa_escritorio_edit" class="block mb-1 text-sm font-semibold text-gray-900">Mesa de Escritorio</label>
            <input type="number" name="mesa_escritorio" id="mesa_escritorio_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="sillones_edit" class="block mb-1 text-sm font-semibold text-gray-900">Sillones</label>
            <input type="number" name="sillones" id="sillones_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="mesa_de_centro_edit" class="block mb-1 text-sm font-semibold text-gray-900">Mesa de Centro</label>
            <input type="number" name="mesa_de_centro" id="mesa_de_centro_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="cajoneras_edit" class="block mb-1 text-sm font-semibold text-gray-900">Cajoneras</label>
            <input type="number" name="cajoneras" id="cajoneras_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div>
            <label for="estantes_edit" class="block mb-1 text-sm font-semibold text-gray-900">Estantes</label>
            <input type="number" name="estantes" id="estantes_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>

        <div class="md:col-span-2">
            <label for="otros_edit" class="block mb-1 text-sm font-semibold text-gray-900">Otros</label>
            <textarea name="otros" id="otros_edit" class="w-full p-2 text-sm text-gray-900 border border-gray-300 rounded-lg"></textarea>
        </div>

        <div class="md:col-span-2">
            <label for="foto_edit" class="block mb-1 text-sm font-semibold text-gray-900">Foto</label>
            <input type="file" name="foto" id="foto_edit" class="w-full text-sm text-gray-900 border border-gray-300 rounded-lg">
        </div>
    </div>

    <button type="submit" id="actualizarDomicilio" class="mt-4 w-full bg-blue-700 hover:bg-blue-800 text-white font-medium rounded-lg p-2">
        Actualizar Domicilio
    </button>
</form>

