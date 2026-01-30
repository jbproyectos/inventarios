<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Página No Encontrada</title>
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
        }

        h1 {
            font-size: 8rem;
            color: #ff5e5e;
            margin-bottom: 10px;
            animation: pulse 1.5s infinite alternate;
        }

        @keyframes pulse {
            0% { transform: scale(1); opacity: 0.8; }
            100% { transform: scale(1.1); opacity: 1; }
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

        .illustration {
            width: 100%;
            max-width: 300px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>¡Oops! Página no encontrada</h2>
        <p>La página que buscas no existe o ha sido movida. <br> Vuelve a la página de inicio.</p>
        <a href="/sistemas/" class="btn">Volver al inicio</a>
    </div>
</body>
</html>
