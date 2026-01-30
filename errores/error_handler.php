<?php
// error_handler.php

// Función personalizada para manejar los errores
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // Mostrar error solo si el tipo de error está habilitado en error_reporting
    if (!(error_reporting() & $errno)) {
        return;
    }

    // Crear el mensaje de error personalizado
    $error_message = "<strong>Tipo de error: </strong>" . htmlspecialchars($errno) . "<br>";
    $error_message .= "<strong>Mensaje: </strong>" . htmlspecialchars($errstr) . "<br>";
    $error_message .= "<strong>Archivo: </strong>" . htmlspecialchars($errfile) . "<br>";
    $error_message .= "<strong>Línea: </strong>" . htmlspecialchars($errline) . "<br>";

    // Mostrar el error de forma flotante sobre el contenido
    echo "<div class='fixed top-0 left-0 right-0 bg-red-500 text-white text-center p-4 z-50'>
            <strong>Error Detectado:</strong> $error_message
          </div>";
}

// Función personalizada para manejar las excepciones
function customExceptionHandler($exception) {
    $exception_message = "<strong>Excepción: </strong>" . htmlspecialchars($exception->getMessage()) . "<br>";
    $exception_message .= "<strong>Archivo: </strong>" . htmlspecialchars($exception->getFile()) . "<br>";
    $exception_message .= "<strong>Línea: </strong>" . htmlspecialchars($exception->getLine()) . "<br>";

    // Mostrar la excepción de forma flotante
    echo "<div class='fixed top-0 left-0 right-0 bg-red-500 text-white text-center p-4 z-50'>
            <strong>Excepción Detectada:</strong> $exception_message
          </div>";
}

// Registra el manejador de errores personalizado
set_error_handler('customErrorHandler');

// Registra el manejador de excepciones personalizado
set_exception_handler('customExceptionHandler');
?>
