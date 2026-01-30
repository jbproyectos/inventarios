<?php
// middleware.php
function verificarSesion() {
    // Iniciar la sesión si no se ha hecho
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Verificar si el usuario no está logueado
    if (empty($_SESSION['user_id'])) {
        // Guardar la URL actual antes de redirigir al login
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];

        // Redirigir al login
        header('Location: ./');
        exit();
    }
}
?>
