<?php
// Habilitar la visualización de errores para desarrollo
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL); // Mostrar todos los errores

// Inicia el almacenamiento de errores
ob_start();

// Verifica si hay algún error y lo captura
$error_message = '';

// Captura el último error PHP
$error = error_get_last();
if ($error) {
    $error_message = "Error: " . htmlspecialchars($error['message']) . "<br>";
    $error_message .= "Archivo: " . $error['file'] . "<br>";
    $error_message .= "Línea: " . $error['line'] . "<br>";
} else {
    // Si no hay error, un mensaje genérico
    $error_message = 'Hubo un error en el servidor. Por favor, intenta más tarde.';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Error Interno del Servidor</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f8f8;
            text-align: center;
            color: #333;
        }

        .container {
            max-width: 600px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 6rem;
            color: #ff5e5e;
            margin-bottom: 10px;
            animation: shake 1s infinite alternate;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-3px); }
            50% { transform: translateX(3px); }
            75% { transform: translateX(-3px); }
            100% { transform: translateX(3px); }
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            padding: 12px 25px;
            font-size: 1rem;
            color: white;
            background: #ff5e5e;
            text-decoration: none;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn:hover {
            background: #e04e4e;
        }

        .error-details {
            margin-top: 30px;
            padding: 15px;
            background-color: #f1f1f1;
            border-radius: 5px;
            color: #333;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>500</h1>
        <h2>¡Oops! Algo salió mal</h2>
        <p>El servidor encontró un problema y no pudo completar tu solicitud. <br> Inténtalo de nuevo más tarde.</p>
        <a href="/sistemas/" class="btn">Volver al inicio</a>

        <!-- Mostrar detalles del error -->
        <div class="error-details">
            <strong>Detalles del error:</strong>
            <p><?php echo $error_message; ?></p>
        </div>
    </div>
</body>
</html>
