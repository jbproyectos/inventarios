<?php
include "../includes/conexionbd.php";
require_once '../errores/error_handler.php';

session_start();
?>
<!DOCTYPE html>
<html :class="{ 'theme-dark': dark }" x-data="data()" lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Usuario | Sistema</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/tailwind.output.css" />
    <!--<link rel="stylesheet" href="../assets/css/tailwind.output.css" />-->
    <!-- <link rel="stylesheet" href="../assets/css/tailwind.css" /> -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <script src="../assets/js/init-alpine.js"></script>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css" /> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" defer></script>
    <script src="../assets/js/charts-lines.js" defer></script>
    <script src="../assets/js/charts-pie.js" defer></script>
    <!-- <link rel="stylesheet" href="../assets/css/style.css"> -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>

    <!-- Incluir CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Incluir jQuery (Select2 depende de jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Incluir JS de Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

    <!--//////////////////////// -->
    <link rel="stylesheet" href="../assets/css/multi-select-tag.css">
    <style>
        .toggle-checkbox:checked {
            right: 0;
            border-color: #4CAF50;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #4CAF50;
        }
    </style>

</head>

<body>
    <div class="flex bg-gray-50 dark:bg-gray-900" :class="{ 'overflow-hidden': isSideMenuOpen }">
        <!-- Desktop sidebar -->
        <?php
        include "../includes/menu.php";
        ?>
        <div class="h-screen w-full">
            <?php
            include "../includes/Navbar.php";
            ?>
            <!-- <main class="h-screen flex justify-center items-center bg-gray-100 dark:bg-gray-900"> -->
            <main class=" justify-center items-center  dark:bg-gray-900">
                <div class="container px-2 sm:px-2 mx-auto">
                    <div class="flex h-auto flex-col gap-6 mt-4 mb-8 md:flex-row">
                        <!-- Primer div que abarca dos columnas -->
                        <div class="w-full lg:max-w-screen-sm md:w-1/3 p-4 bg-white rounded-xl shadow-xs dark:bg-gray-800 h-auto md:h-64">
                            <div class="min-w-0 p-4  dark:bg-gray-800">
                                <h4 class="mb-4 font-semibold text-gray-800 dark:text-gray-300">
                                    Usuarios </h4>

                                <div class=" sm:rounded-lg">
                                    <div class="flex items-center justify-between flex-column flex-wrap md:flex-row space-y-4 md:space-y-0 pb-4 bg-white dark:bg-gray-800">

                                        <!-- Modal toggle mod-->
                                        <button data-modal-target="crud-modal" data-modal-toggle="crud-modal" class="inline-flex text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                                            </svg>
                                            Registrar usuario
                                        </button>

                                        <!-- Main modal -->
                                        <div id="crud-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                            <div class="relative p-4 w-full max-w-md max-h-full">
                                                <!-- Modal content -->
                                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                                    <!-- Modal header -->
                                                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                            Crear nuevo usuario
                                                        </h3>
                                                        <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-modal">
                                                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                                            </svg>
                                                            <span class="sr-only">Cerrar ventana</span>
                                                        </button>
                                                    </div>
                                                    <!-- Modal body -->
                                                    <form id="userForm" action="#" class="p-4 md:p-5">
                                                        <div class="grid gap-4 mb-4 grid-cols-2">
                                                            <!-- Nombre -->
                                                            <div class="col-span-2">
                                                                <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nombre</label>
                                                                <input type="text" name="name" id="name" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Juan" required>
                                                            </div>

                                                            <!-- Apellidos -->
                                                            <div class="col-span-2">
                                                                <label for="apellido" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Apellidos</label>
                                                                <input type="text" name="apellido" id="apellido" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Bautista" required>
                                                            </div>

                                                            <!-- Email -->
                                                            <div class="col-span-2">
                                                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-mail</label>
                                                                <input type="email" name="email" id="email" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="dev@kabzo.org" required>
                                                            </div>

                                                            <!-- Contraseña -->
                                                            <div class="col-span-2 sm:col-span-1">
                                                                <label for="contrasena" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contraseña</label>
                                                                <input type="password" name="contrasena" id="contrasena" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="********" required>
                                                            </div>

                                                            <!-- Verificar Contraseña -->
                                                            <div class="col-span-2 sm:col-span-1">
                                                                <label for="verificar" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Verificar Contraseña</label>
                                                                <input type="password" name="verificar" id="verificar" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="********" required>
                                                            </div>

                                                            <!-- Puesto -->
                                                            <?php
                                                            try {
                                                                $consulta = $conexion->query("SELECT * FROM puestos");
                                                                $puestos = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                                            } catch (PDOException $e) {
                                                                die('Error en la consulta: ' . $e->getMessage());
                                                            }
                                                            ?>
                                                            <div class="col-span-2 sm:col-span-1">
                                                                <label for="puestos" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Puesto</label>
                                                                <select name="puestos" id="puestos" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" required>
                                                                    <option value="" selected>Selecciona puesto</option>
                                                                    <?php foreach ($puestos as $puesto): ?>
                                                                        <option value="<?= $puesto['Id_puesto'] ?>"><?= htmlspecialchars($puesto['nombre']) ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>

                                                            <!-- Oficina -->
                                                            <?php
                                                            try {
                                                                $consulta = $conexion->query("SELECT * FROM oficina");
                                                                $oficinas = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                                            } catch (PDOException $e) {
                                                                die('Error en la consulta: ' . $e->getMessage());
                                                            }
                                                            ?>
                                                            <div class="col-span-2 sm:col-span-1">
                                                                <label for="oficina" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Oficina</label>
                                                                <select name="oficina" id="oficina" class="block p-2 text-sm text-gray-900 border border-gray-300 w-full rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" required>
                                                                    <option value="" selected>Selecciona oficina</option>
                                                                    <?php foreach ($oficinas as $oficina): ?>
                                                                        <option value="<?= $oficina['Id_Oficina'] ?>"><?= htmlspecialchars($oficina['nombre']) ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>

                                                            <?php
                                                            // Obtener roles desde la base de datos
                                                            try {
                                                                $consulta = $conexion->query("SELECT * FROM roless");
                                                                $roles = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                                            } catch (PDOException $e) {
                                                                die('Error en la consulta de roles: ' . $e->getMessage());
                                                            }
                                                            ?>

                                                            <div class="col-span-2 sm:col-span-1">
                                                                <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rol</label>
                                                                <select id="role" name="role" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 w-full rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                                    <option value="" selected disabled>Selecciona un rol</option>
                                                                    <?php foreach ($roles as $rol) : ?>
                                                                        <option value="<?= $rol['id'] ?>"><?= htmlspecialchars($rol['nombre']) ?></option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <button type="submit" id="saveenterprise" class="text-white inline-flex items-center w-full mt-4 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                                                            Agregar usuario
                                                        </button>
                                                    </form>

                                                </div>
                                            </div>
                                        </div>

                                        <label for="table-search" class="sr-only">Buscar</label>
                                        <div class="relative">

                                            <input type="text" id="table-search-users" class="w-full block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-80 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Buscar usuario">
                                        </div>
                                    </div>
                                    <div class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400" style="overflow-x: auto;">

                                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                                <tr>
                                                    <th scope="col" class="px-6 py-3">Nombre</th>
                                                    <th scope="col" class="px-6 py-3">Fecha de registro</th>
                                                    <th scope="col" class="px-6 py-3">Status</th>
                                                    <th scope="col" class="px-6 py-3">Puesto</th>
                                                    <th scope="col" class="">Departamento</th>
                                                    <th scope="col" class="">Personal</th>
                                                    <th scope="col" class="px-6 py-3">Oficina</th>
                                                    <th scope="col" class="px-6 py-3">Rol</th> <!-- Columna de Rol -->
                                                    <th scope="col" class="px-6 py-3">Permisos</th> <!-- Columna de Permisos -->
                                                    <th scope="col" class="px-6 py-3">Acciones</th> <!-- Columna de Acciones -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                try {
                                                    $consulta = $conexion->query("SELECT usuarios.*, oficina.nombre AS nombre_oficina, departamentos.nombre AS nombre_departamento, puestos.nombre AS nombre_puesto, 
                                              roless.nombre AS nombre_rol
                                          FROM usuarios
                                          LEFT JOIN oficina ON usuarios.Id_oficina = oficina.Id_oficina
                                          LEFT JOIN departamentos ON usuarios.Id_departamento = departamentos.Id_departamento
                                          LEFT JOIN puestos ON usuarios.Id_puesto = puestos.Id_puesto
                                          LEFT JOIN roles_usuarios ON usuarios.Id_Usuario = roles_usuarios.usuario_id
                                          LEFT JOIN roless ON roles_usuarios.rol_id = roless.id");
                                                    $usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($usuarios as $usuario) {
                                                        // Obtener permisos para el usuario
                                                        $consulta_permisos = $conexion->prepare("SELECT permisos.nombre 
                                                         FROM permisos 
                                                         JOIN permisos_modelos ON permisos.id = permisos_modelos.permiso_id
                                                         JOIN roles_usuarios ON permisos_modelos.rol_id = roles_usuarios.rol_id
                                                         WHERE roles_usuarios.usuario_id = ?");
                                                        $consulta_permisos->execute([$usuario['Id_Usuario']]);
                                                        $permisos = $consulta_permisos->fetchAll(PDO::FETCH_COLUMN);
                                                        $permisos_str = implode(', ', $permisos);

                                                        echo '<tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-900">';
                                                        echo '<th scope="row" class="flex items-center font-medium text-gray-900 dark:text-white">';
                                                        echo '<div class="ps-3">';
                                                        echo '<div class="text-base font-semibold">' . htmlspecialchars($usuario['nombre']) . " " . htmlspecialchars($usuario['apellido']) . '</div>';
                                                        echo '<div class="font-normal text-gray-500">' . htmlspecialchars($usuario['email']) . '</div>';
                                                        echo '</div>';
                                                        echo '</th>';
                                                        echo '<td class="px-6 py-4">' . htmlspecialchars($usuario['fechaRegistro']) . '</td>';
                                                        echo '<td class="px-6 py-4 text-center">
                        <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                            <input 
                                type="checkbox" 
                                id="toggle-' . $usuario['Id_Usuario'] . '" 
                                class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer"
                                ' . ($usuario['estatu'] == 1 ? 'checked' : '') . ' 
                                onclick="cambiarEstado(' . $usuario['Id_Usuario'] . ', this.checked)"
                            />
                            <label for="toggle-' . $usuario['Id_Usuario'] . '" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                        </div>
                    </td>';
                                                        echo '<td class="px-6 py-4">' . htmlspecialchars($usuario['nombre_puesto']) . '</td>';
                                                        echo '<td class="px-6 py-4">' . htmlspecialchars($usuario['nombre_departamento']) . '</td>';
                                                        echo '<td class="px-6 py-4">' . htmlspecialchars($usuario['nombre_oficina']) . '</td>';
                                                        echo '<td class="px-6 py-4">' . htmlspecialchars($usuario['nombre_rol']) . '</td>';
                                                        echo '<td class="px-6 py-4">' . htmlspecialchars($permisos_str) . '</td>';

                                                        // Verificar permisos para acciones
                                                        $acciones = '';
                                                        if (in_array('editar_usuarios', $permisos)) {
                                                            $acciones .= '<button class="btn btn-primary">Editar</button>';
                                                        } else {
                                                            $acciones .= '<button class="btn btn-secondary" disabled>No tienes permiso para editar</button>';
                                                        }

                                                        if (in_array('eliminar_usuarios', $permisos)) {
                                                            $acciones .= '<button class="btn btn-danger">Eliminar</button>';
                                                        } else {
                                                            $acciones .= '<button class="btn btn-secondary" disabled>No tienes permiso para eliminar</button>';
                                                        }

                                                        echo '<td class="px-6 py-4">' . $acciones . '</td>';
                                                        echo '</tr>';
                                                    }
                                                } catch (PDOException $e) {
                                                    die('Error en la consulta: ' . $e->getMessage());
                                                }
                                                ?>
                                            </tbody>
                                        </table>



                                    </div>
                                </div>



                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <!-- <script src="login/doc.js"></script> -->


    <!-- Incluir CSS de Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Incluir jQuery (Select2 depende de jQuery) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Incluir JS de Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>


    <script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@2.0.1/dist/js/multi-select-tag.js"></script>



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function cambiarEstado(userId, isChecked) {
            const nuevoEstado = isChecked ? 1 : 0;

            Swal.fire({
                title: "¿Estás seguro?",
                text: "El estado del usuario será cambiado.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, cambiar",
                cancelButtonText: "Cancelar",
                customClass: {
                    confirmButton: "bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded",
                    cancelButton: "bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded",
                },
                buttonsStyling: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("login/actualizar_estado.php", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                userId,
                                estatu: nuevoEstado
                            }),
                        })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.status === "success") {
                                Swal.fire({
                                    title: "¡Estado cambiado!",
                                    text: "El estado ha sido actualizado correctamente.",
                                    icon: "success",
                                    customClass: {
                                        confirmButton: "bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded",
                                    },
                                    buttonsStyling: false,
                                });
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: "Hubo un problema al cambiar el estado.",
                                    icon: "error",
                                    customClass: {
                                        confirmButton: "bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded",
                                    },
                                    buttonsStyling: false,
                                });
                            }
                        })
                        .catch((error) => {
                            Swal.fire({
                                title: "Error",
                                text: "No se pudo conectar con el servidor.",
                                icon: "error",
                                customClass: {
                                    confirmButton: "bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded",
                                },
                                buttonsStyling: false,
                            });
                        });
                } else {
                    document.getElementById(`toggle-${userId}`).checked = !isChecked;
                }
            });
        }
    </script>

    <script>
        document.getElementById('userForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            fetch('login/addUser.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Éxito!',
                            text: data.message,
                        }).then(() => location.reload());
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message,
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en la solicitud',
                        text: error.message,
                    });
                });
        });
    </script>

    <script>
        function mostrarListaSiguiente() {
            const puestoSeleccionado = document.getElementById('puestos').value;

            if (puestoSeleccionado) {
                // Mostrar el contenedor del siguiente nivel
                document.getElementById('nivel-container').style.display = 'block';

                // Cargar las opciones correspondientes al puesto seleccionado
                cargarNivel(puestoSeleccionado);
            } else {
                // Ocultar y limpiar si no hay selección
                document.getElementById('nivel-container').style.display = 'none';
                limpiarSelect('nivel');
            }
        }

        function cargarNivel(puesto) {
            // Realizar una solicitud AJAX para obtener los datos del siguiente nivel
            $.ajax({
                url: 'login/get_nivel.php', // Este archivo PHP debe manejar la lógica según el puesto
                type: 'GET',
                data: {
                    puesto
                },
                success: function(data) {
                    console.log('Respuesta de get_nivel.php:', data);

                    // Convertir en objeto si es una cadena
                    if (typeof data === 'string') {
                        data = JSON.parse(data);
                    }

                    // Limpiar el select de nivel
                    limpiarSelect('nivel');

                    // Comprobar si se han encontrado datos
                    if (Array.isArray(data) && data.length > 0) {
                        // Agregar las opciones al select
                        data.forEach(function(item) {
                            $('#nivel').append('<option value="' + item.id + '">' + item.nombre + '</option>');
                        });

                        // Aplicar Select2 si es necesario
                        $('#nivel').select2({
                            placeholder: "Selecciona una opción",
                            allowClear: true
                        });
                    } else {
                        // Si no hay datos, mostrar un mensaje
                        $('#nivel').append('<option value="">No hay opciones disponibles</option>');
                    }
                },
                error: function() {
                    alert('Error al cargar los datos');
                }
            });
        }

        function limpiarSelect(id) {
            const select = document.getElementById(id);
            if (select) {
                select.innerHTML = '<option value="">Selecciona</option>';
            }
        }


        // Mapeo de puestos a los títulos y el próximo nivel jerárquico
        const puestoMap = {
            "empresa": {
                label: "CEOs",
                next: "ceos"
            },
            "ceos": {
                label: "Directores",
                next: "directores"
            },
            "director": {
                label: "Supervisores",
                next: "supervisores"
            },
            "supervisor": {
                label: "Líderes",
                next: "lideres"
            },
            "lider": {
                label: "Staff",
                next: "staff"
            }
        };

        // Cuando cambie el puesto, actualizar la etiqueta y cargar los datos
        document.getElementById('puestos').addEventListener('change', function() {
            const puestoId = this.value;

            // Obtener los datos del mapa según el puesto seleccionado
            const puestoData = Object.keys(puestoMap).find(key => key === puestoId.toLowerCase());
            if (puestoData) {
                // Actualizar el texto dinámico
                const label = puestoMap[puestoData].label;
                document.getElementById('select-label').textContent = `Selecciona ${label}`;

                // Mostrar el contenedor
                document.getElementById('select-container').style.display = 'block';

                // Cargar el personal dinámicamente
                cargarPersonal(puestoId);
            } else {
                // Ocultar el select si no hay puesto seleccionado
                document.getElementById('select-container').style.display = 'none';
            }
        });
    </script>

</body>

</html>


<!-- test -->
