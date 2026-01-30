<?php
session_start();
include "../includes/conexionbd.php";

// Obtener la lista de usuarios
$queryUsuarios = "SELECT Id_Usuario, nombre FROM usuarios";
$stmtUsuarios = $conexion->query($queryUsuarios);
$usuarios = $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC);

// Obtener la lista de departamentos
$queryDepartamentos = "SELECT Id_departamento, nombre FROM departamentos";
$stmtDepartamentos = $conexion->query($queryDepartamentos);
$departamentos = $stmtDepartamentos->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignar Departamentos a Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold mb-6">Asignar Departamentos a Usuario</h1>

        <!-- Formulario de Asignación -->
        <form id="formAsignarDepartamentos">
            <!-- Seleccionar Usuario -->
            <div class="mb-4">
                <label for="usuario" class="block text-sm font-medium text-gray-700">Seleccionar Usuario</label>
                <select name="usuario" id="usuario" class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" required>
                    <option value="">Seleccione un usuario</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option value="<?= $usuario['Id_Usuario'] ?>"><?= $usuario['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Seleccionar Departamentos -->
            <div class="mb-4">
                <label for="departamentos" class="block text-sm font-medium text-gray-700">Seleccionar Departamentos</label>
                <select name="departamentos[]" id="departamentos" multiple class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm" required>
                    <?php foreach ($departamentos as $departamento): ?>
                        <option value="<?= $departamento['Id_departamento'] ?>"><?= $departamento['nombre'] ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-sm text-gray-500 mt-1">Mantén presionada la tecla Ctrl (Windows) o Command (Mac) para seleccionar múltiples departamentos.</p>
            </div>

            <!-- Botón de Envío -->
            <div class="mt-6">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Asignar Departamentos</button>
            </div>
        </form>

        <!-- Mensaje de Retroalimentación -->
        <div id="mensaje" class="mt-4 hidden"></div>
    </div>

    <!-- Script para manejar el envío del formulario con Fetch -->
<script>
    document.getElementById('formAsignarDepartamentos').addEventListener('submit', function (event) {
    event.preventDefault();

    const formData = new FormData(this);
    const usuarioId = formData.get('usuario');
    const departamentos = formData.getAll('departamentos[]');

    console.log("Usuario ID:", usuarioId); // Verificar el ID del usuario
    console.log("Departamentos:", departamentos); // Verificar los departamentos seleccionados

    if (!usuarioId || departamentos.length === 0) {
        alert('Por favor, seleccione un usuario y al menos un departamento.');
        return;
    }

    fetch('user/guardar_asignacion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            usuario: usuarioId,
            departamentos: departamentos,
        }),
    })
    .then(response => response.json())
    .then(data => {
        const mensajeDiv = document.getElementById('mensaje');
        mensajeDiv.classList.remove('hidden');
        if (data.success) {
            mensajeDiv.classList.add('bg-green-100', 'border-green-400', 'text-green-700');
            mensajeDiv.textContent = data.message;
        } else {
            mensajeDiv.classList.add('bg-red-100', 'border-red-400', 'text-red-700');
            mensajeDiv.textContent = data.message;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar la solicitud.');
    });
});
</script>
</body>
</html>