<!DOCTYPE html>
<html lang="es">
<head>
    <title>Iniciar Sesión</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#3b82f6">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Mobiliario">
    <link rel="manifest" href="/sistemas/manifest.json">
    
    <!-- Tailwind y Flowbite -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Estilos optimizados para móvil -->
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --surface-color: #ffffff;
            --background-color: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
        }

        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }

        input, textarea {
            -webkit-user-select: text;
            user-select: text;
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--background-color);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            min-height: 100vh;
            min-height: 100dvh;
            overflow-x: hidden;
        }

        .login-container {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            position: relative;
        }

        .login-card {
            background: var(--surface-color);
            border-radius: 20px;
            box-shadow: 
                0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 10px 15px -3px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(0, 0, 0, 0.02);
            padding: 2rem 1.5rem;
            width: 100%;
            max-width: 400px;
            position: relative;
            backdrop-filter: blur(10px);
        }

        /* Header estilo iOS */
        .header {
            text-align: center;
            margin-bottom: 2rem;
            padding-top: 0.5rem;
        }

        .logo-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .logo {
            height: 72px;
            width: 72px;
            border-radius: 18px;
            object-fit: cover;
            border: 3px solid #e0f2fe;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        }

        .title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            letter-spacing: -0.5px;
        }

        .subtitle {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.4;
        }

        /* Formulario estilo nativo móvil */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .input-container {
            position: relative;
            background: #f8fafc;
            border-radius: 12px;
            border: 2px solid var(--border-color);
            transition: all 0.2s ease;
        }

        .input-container:focus-within {
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            transition: color 0.2s ease;
            pointer-events: none;
        }

        .input-container:focus-within .input-icon {
            color: var(--primary-color);
        }

        .form-input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border: none;
            background: transparent;
            font-size: 16px;
            line-height: 1.5;
            border-radius: 12px;
            outline: none;
            -webkit-appearance: none;
            appearance: none;
        }

        .form-input::placeholder {
            color: #94a3b8;
            font-size: 16px;
        }

        /* Checkbox estilo iOS */
        .checkbox-container {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .checkbox {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid #cbd5e1;
            border-radius: 6px;
            margin-right: 12px;
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .checkbox:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .checkbox:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 14px;
            font-weight: bold;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .checkbox-label {
            font-size: 0.95rem;
            color: var(--text-primary);
            cursor: pointer;
        }

        /* Botón estilo nativo */
        .btn-primary {
            width: 100%;
            padding: 16px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            -webkit-tap-highlight-color: rgba(59, 130, 246, 0.3);
        }

        .btn-primary:active {
            transform: scale(0.98);
            background: var(--primary-hover);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* Footer links */
        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .footer-link {
            text-align: center;
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            padding: 8px;
            border-radius: 8px;
            transition: background-color 0.2s ease;
        }

        .footer-link:active {
            background-color: rgba(59, 130, 246, 0.1);
        }

        /* Estados de carga */
        .btn-loading {
            pointer-events: none;
            position: relative;
        }

        .btn-loading::after {
            content: '';
            width: 18px;
            height: 18px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 8px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Safe areas para iPhone */
        @supports (padding: max(0px)) {
            .login-container {
                padding-left: max(1rem, env(safe-area-inset-left));
                padding-right: max(1rem, env(safe-area-inset-right));
                padding-bottom: max(1rem, env(safe-area-inset-bottom));
            }
        }

        /* Mejoras para desktop */
        @media (min-width: 768px) {
            .login-card {
                padding: 2.5rem 2rem;
                box-shadow: 
                    0 20px 25px -5px rgba(0, 0, 0, 0.1),
                    0 10px 10px -5px rgba(0, 0, 0, 0.04),
                    0 0 0 1px rgba(0, 0, 0, 0.02);
            }

            .btn-primary:hover {
                background: var(--primary-hover);
                transform: translateY(-1px);
            }

            .footer-link:hover {
                background-color: rgba(59, 130, 246, 0.1);
            }
        }

        /* Animaciones suaves */
        .login-card {
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mejoras de accesibilidad */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>
    <?php
    session_start();
    include 'includes/conexionbd.php';

    // Evitar caché
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");

    // Redirigir si ya está autenticado
    if (isset($_SESSION['user_id'])) {
        header('Location: ../sistemas/dashboard/');
        exit();
    }
    ?>

    <div class="login-container">
        <div class="">
            <!-- Header -->
            <div class="header">
                <h1 class="title">Bienvenido</h1>
                <p class="subtitle">Ingresa tus credenciales para continuar</p>
            </div>

            <!-- Formulario -->
            <form id="login-form" action="includes/process_login.php" method="post">
                <!-- Email -->
                <div class="form-group">
                    <div class="input-container">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                        <input type="email" id="email" name="email" placeholder="correo@ejemplo.com" class="form-input" required autocomplete="email">
                    </div>
                </div>

                <!-- Contraseña -->
                <div class="form-group">
                    <div class="input-container">
                        <div class="input-icon">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-6a2 2 0 012 2v4a2 2 0 01-2 2m0-10a2 2 0 00-2 2v4a2 2 0 002 2m0-6v6"/>
                            </svg>
                        </div>
                        <input type="password" id="password" name="password" placeholder="••••••••" class="form-input" required autocomplete="current-password">
                    </div>
                </div>

                <!-- Recordar contraseña -->
                <div class="checkbox-container">
                    <input type="checkbox" id="remember-me" name="remember-me" class="checkbox">
                    <label for="remember-me" class="checkbox-label">Recordar contraseña</label>
                </div>

                <!-- Botón de inicio de sesión -->
                <button type="submit" id="login-btn" class="btn-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Iniciar Sesión
                </button>
            </form>

            <!-- Enlaces del footer -->
            <div class="footer-links">
                <a href="reset_password.php" class="footer-link">¿Olvidaste tu contraseña?</a>
                <a href="registro.php" class="footer-link">Solicita una cuenta</a>
            </div>
        </div>
    </div>

    <script>
        // Manejo del formulario
        document.getElementById('login-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const btn = document.getElementById('login-btn');
            btn.disabled = true;
            btn.classList.add('btn-loading');

            const formData = new FormData(this);

            fetch('includes/process_login.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                btn.classList.remove('btn-loading');
                
                if(data.success) {
                    window.location.href = data.redirect;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message,
                        confirmButtonText: 'Aceptar',
                        customClass: {
                            confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded'
                        },
                        buttonsStyling: false
                    });
                }
            })
            .catch(err => {
                console.error(err);
                btn.disabled = false;
                btn.classList.remove('btn-loading');
            });
        });

        // Service Worker para PWA
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sistemas/service-worker.js')
            .then(reg => console.log('Service Worker registrado', reg))
            .catch(err => console.log('Error al registrar Service Worker', err));
        }

        // Prevenir zoom en inputs en iOS
        document.addEventListener('touchstart', function() {}, { passive: true });
    </script>
</body>
</html>