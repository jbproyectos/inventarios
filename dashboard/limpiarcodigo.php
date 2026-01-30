<?php
session_start();
include "../includes/conexionbd.php"; // Asegúrate de que la conexión está correctamente configurada

// Obtén el user_id de la sesión
$user_id = $_SESSION["user_id"];

// Obtener el rol del usuario actual
$stmt = $conexion->prepare("SELECT rolActual FROM usuarios WHERE Id_Usuario = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Usuario no encontrado");
}

// Guardamos el rol del usuario
$rolActual = $user['rolActual'];

// Obtener los permisos asociados a ese rol
$stmtPermissions = $conexion->prepare("
    SELECT DISTINCT p.nombre 
    FROM permisos p
    JOIN permisos_modelos pm ON p.id = pm.permiso_id
    WHERE pm.rol_id = :rol_id
");
$stmtPermissions->bindParam(':rol_id', $rolActual, PDO::PARAM_INT);
$stmtPermissions->execute();
$permissions = $stmtPermissions->fetchAll(PDO::FETCH_ASSOC);

// Asegúrate de que los permisos estén correctamente asignados a las variables
$canEdit = in_array('editar', array_column($permissions, 'nombre'));
$canDelete = in_array('eliminar', array_column($permissions, 'nombre'));
$canView = in_array('ver', array_column($permissions, 'nombre'));
$canAdd = in_array('crear', array_column($permissions, 'nombre'));


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Permisos - CRUD</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/tailwind.output.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
</head>
<body>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold">Gestión de Permisos</h1>
        <p>Usuario: <?= htmlspecialchars($user_id); ?> - Rol: <?= htmlspecialchars($rolActual); ?></p>

        <div class="my-4">
            <h2 class="text-xl font-semibold">Listado de Elementos</h2>
            
            <!-- Verificar si el usuario tiene permisos para agregar -->
            <?php if ($canAdd): ?>
                <button class="bg-green-500 text-white px-4 py-2 rounded" onclick="document.getElementById('addForm').classList.toggle('hidden')">Agregar Nuevo Elemento</button>
            <?php else: ?>
                <p class="text-red-500">No tienes permiso para agregar</p>
            <?php endif; ?>

            <div id="addForm" class="hidden mt-4">
                <form action="agregar_elemento.php" method="POST">
                    <input type="text" name="nombre" placeholder="Nombre del Elemento" class="border p-2 mb-2 w-full" required>
                    <textarea name="descripcion" placeholder="Descripción" class="border p-2 mb-2 w-full" required></textarea>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Guardar</button>
                </form>
            </div>

            <table class="min-w-full table-auto mt-4">
                <thead>
                    <tr>
                        <th class="px-4 py-2">Nombre</th>
                        <th class="px-4 py-2">Descripción</th>
                        <th class="px-4 py-2">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obtén la lista de elementos
                    $stmt = $conexion->query("SELECT * FROM computadora");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['marca']) . "</td>";
                        echo "<td class='border px-4 py-2'>" . htmlspecialchars($row['modelo']) . "</td>";
                        echo "<td class='border px-4 py-2'>";

                        // Verificar si el usuario tiene permisos para editar o eliminar
                        if ($canEdit) {
                            echo "<a href='editar_elemento.php?id=" . $row['id'] . "' class='text-blue-500'>Editar</a> ";
                        } else {
                            echo "<span class='text-gray-500'>Editar</span> ";
                        }

                        if ($canDelete) {
                            echo "<a href='eliminar_elemento.php?id=" . $row['id'] . "' class='text-red-500'>Eliminar</a>";
                        } else {
                            echo "<span class='text-gray-500'>Eliminar</span>";
                        }

                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Aquí podrías agregar más funcionalidades, como la validación de formularios o la eliminación de elementos
    </script>
</body>
</html>
