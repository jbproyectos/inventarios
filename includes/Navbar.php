<style>
    .profile-button {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .profile-button span {
        text-align: center;
        line-height: 1.2;
    }
</style>

<header class=" py-2 bg-white dark:bg-gray-900 shadow-sm">
    <div class=" flex items-center justify-between h-full px-6 mx-auto text-purple-600 dark:text-purple-300">
        <div class="flex justify-left flex-1 lg:mr-32">
            <div class="relative w-full max-w-xl mr-6 focus-within:text-purple-500">
                <div class="absolute inset-y-0 flex items-center pl-2">

                </div>
                <a class="hidden md:block text-lg font-bold text-gray-800 dark:text-gray-200" href="#">


                    <?php
                    include "breadcrumb.php";

                    ?>

                </a>
            </div>
        </div>

        <ul class="flex items-center  space-x-2">
            <button id="mobile-menu-button" class="block md:hidden item-left">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="dark:text-white w-6 h-6 mr-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9h16.5m-16.5 6.75h16.5" />
                </svg>
            </button>

            <!-- Theme toggler -->
            <li class="flex">

                <button class="rounded-md focus:outline-none focus:shadow-outline-purple" @click="toggleTheme" aria-label="Toggle color mode">
                    <template x-if="!dark">
                        <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </template>
                    <template x-if="dark">
                        <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
                        </svg>
                    </template>
                </button>
            </li>
            <!-- Notifications menu -->


            <!-- Profile menu -->
            <?php
            $user_id = $_SESSION["user_id"];

            try {
                // Consulta para obtener el nombre del usuario y su rolActual desde 'usuarios' y 'roless'
                $consulta = $conexion->prepare("
        SELECT 
            usuarios.nombre AS nombre_usuario, 
            usuarios.apellido AS apellido_usuario, 
            roless.nombre AS nombre_rol
        FROM 
            usuarios
        JOIN 
            roless ON usuarios.rolActual = roless.id
        WHERE 
            usuarios.Id_Usuario = :user_id
    ");
                $consulta->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $consulta->execute();

                // Obtener el resultado de la consulta
                $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                if ($resultado) {
                    $nombre_usuario = $resultado['nombre_usuario'];
                    $apellido_usuario = $resultado['apellido_usuario'];
                    $nombre_rol = $resultado['nombre_rol'];

                    // Mostrar el nombre del usuario y su rolActual
                    echo '<div class="flex flex-col">';
                    echo '<span class="text-right font-semibold">' . htmlspecialchars($nombre_usuario . ' ' . $apellido_usuario) . '</span>';
                    echo '<span class="text-right text-sm text-gray-600">Rol: 
        <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-gray-700 dark:text-green-400 border border-green-400">
                ' . htmlspecialchars($nombre_rol) . '

        </span>

        </span>';
                    echo '</div>';
                } else {
                    echo '<div class="text-red-500">No se encontró rol asignado para este usuario</div>';
                }
            } catch (PDOException $e) {
                echo '<div class="text-red-500">Error en la consulta: ' . htmlspecialchars($e->getMessage()) . '</div>';
            }
            ?>


            <button class="profile-button rounded-full focus:shadow-outline-purple focus:outline-none"
                @click="toggleProfileMenu" @keydown.escape="closeProfileMenu"
                aria-label="Account" aria-haspopup="true">
            </button>


            <li class="relative z-10">
                <?php
                include 'conexionbd.php';

                $sql = "SELECT nombre FROM usuarios WHERE Id_Usuario = :user_id";
                $stmt = $conexion->prepare($sql);
                $stmt->bindParam(':user_id', $_SESSION['user_id']);
                $stmt->execute();

                $userData = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($userData) {
                    $username = $userData['nombre'];
                } else {
                    $username = "Usuario Desconocido";
                }
                ?>



                <template x-if="isProfileMenuOpen">
                    <ul x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click.away="closeProfileMenu" @keydown.escape="closeProfileMenu" class="absolute right-0 w-72 p-2 mt-2 space-y-2 text-gray-600 bg-white border border-gray-100 rounded-md shadow-md dark:border-gray-700 dark:text-gray-300 dark:bg-gray-700" aria-label="submenu">
                        <li class="flex mb-4 border-b p-2">
                            <a class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200" href="../dashboard/Mi_Perfil">
                                <svg class="w-4 h-4 mr-3" aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor">
                                    <path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="inline-flex">Yo (
                                    <?php

                                    $user_id = $_SESSION["user_id"];

                                    try {
                                        $consulta = $conexion->prepare("SELECT * FROM usuarios WHERE Id_Usuario = :user_id");
                                        $consulta->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                                        $consulta->execute();

                                        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);

                                        if ($resultado) {
                                            $nombre_usuario = $resultado['nombre'];
                                            echo '<div class="flex flex-col">';
                                            echo '<span class="text-right">' . $nombre_usuario . '</span>';
                                            echo '</div>';
                                        } else {
                                            echo 'Usuario no encontrado';
                                        }
                                    } catch (PDOException $e) {
                                        echo 'Error en la consulta: ' . $e->getMessage();
                                    }
                                    ?>)</span>
                            </a>
                        </li>


                        <li class="flex">
                            <a class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200" href="../dashboard/Users">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                </svg>

                                <span>Mis colaboradores</span>
                            </a>
                        </li>
                        <li class="flex border-t mt-4">
                            <a class="inline-flex items-center w-full px-2 py-1 text-sm font-semibold transition-colors duration-150 rounded-md hover:bg-gray-100 hover:text-gray-800 dark:hover:bg-gray-800 dark:hover:text-gray-200" href="../dashboard/login/logout.php">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 mr-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>


                                <span>Cerrar sesión</span>
                            </a>
                        </li>
                    </ul>
                </template>
            </li>
        </ul>
    </div>
</header>


<script>
    var mobileMenuButton = document.getElementById('mobile-menu-button');
    var sideMenu = document.querySelector('.z-20');

    function toggleMobileMenu() {
        sideMenu.classList.toggle('hidden');
    }

    mobileMenuButton.addEventListener('click', toggleMobileMenu);
</script>
<script>
    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    var initials = '<?php echo strtoupper(substr($username, 0, 1)); ?>';

    var randomColor = getRandomColor();

    document.addEventListener('DOMContentLoaded', function() {
        var profileButton = document.querySelector('.profile-button');
        profileButton.style.backgroundColor = randomColor;
        profileButton.innerHTML = '<span class="text-white font-bold">' + initials + '</span>';
    });
</script>