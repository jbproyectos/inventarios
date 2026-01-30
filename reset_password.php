<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    
    <!-- Tailwind y SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --accent-color: #f59e0b;
            --accent-hover: #d97706;
            --success-color: #10b981;
            --success-hover: #059669;
            --error-color: #ef4444;
            --error-hover: #dc2626;
        }
        
        .recovery-container {
            background: linear-gradient(135deg, #fef3c7 0%, #fef7cd 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .recovery-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .recovery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .recovery-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        .recovery-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        
        .form-title {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .form-title h2 {
            font-size: 1.75rem;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }
        
        .form-title p {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        
        .form-label span {
            color: var(--error-color);
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background-color: #f9fafb;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
            background-color: white;
        }
        
        .btn-recovery {
            width: 100%;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-hover) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-recovery:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4);
        }
        
        .btn-recovery:active {
            transform: translateY(0);
        }
        
        .btn-back-login {
            width: 100%;
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-hover) 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .btn-back-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.4);
        }
        
        .btn-loading {
            pointer-events: none;
            opacity: 0.7;
        }
        
        .btn-loading::after {
            content: '';
            width: 16px;
            height: 16px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .success-screen {
            text-align: center;
            padding: 2rem 0;
            display: none;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: var(--success-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        .success-icon svg {
            width: 40px;
            height: 40px;
            color: white;
        }
        
        .success-screen h2 {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--success-color);
            margin-bottom: 0.5rem;
        }
        
        .success-screen p {
            color: #6b7280;
            margin-bottom: 2rem;
            line-height: 1.5;
        }
        
        .info-text {
            font-size: 0.75rem;
            color: #6b7280;
            text-align: center;
            margin-top: 1rem;
        }
        
        .info-text span {
            color: var(--error-color);
        }
        
        @media (max-width: 480px) {
            .recovery-card {
                padding: 2rem 1.5rem;
            }
            
            .recovery-icon {
                width: 60px;
                height: 60px;
            }
            
            .recovery-icon svg {
                width: 30px;
                height: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="recovery-container">
        <div class="recovery-card">
            <!-- Icono de recuperación -->
            <div class="recovery-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0-6a2 2 0 012 2v4a2 2 0 01-2 2m0-10a2 2 0 00-2 2v4a2 2 0 002 2m0-6v6" />
                </svg>
            </div>
            
            <!-- Formulario de recuperación -->
            <div id="recoveryForm">
                <div class="form-title">
                    <h2>Recuperar Contraseña</h2>
                    <p>Ingresa tu correo electrónico para recibir instrucciones de recuperación</p>
                </div>
                
                <form id="passwordRecoveryForm">
                    <div class="form-group">
                        <label for="email3" class="form-label">
                            Correo Electrónico <span>*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email3" 
                            name="email"
                            required 
                            placeholder="correo@dominio.com" 
                            class="form-input"
                        >
                    </div>
                    
                    <button type="submit" class="btn-recovery" id="recoveryBtn">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Enviar Instrucciones
                    </button>
                </form>
                
                <div class="info-text">
                    Los campos marcados con <span>*</span> son obligatorios
                </div>
                
                <button class="btn-back-login" onclick="goToLogin()">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Volver al Inicio de Sesión
                </button>
            </div>
            
            <!-- Pantalla de éxito después del envío -->
            <div class="success-screen" id="successScreen">
                <div class="success-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2>¡Correo Enviado!</h2>
                <p>Te hemos enviado un correo electrónico con las instrucciones para restablecer tu contraseña. Por favor, revisa tu bandeja de entrada.</p>
                <button class="btn-back-login" onclick="goToLogin()">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Volver al Inicio de Sesión
                </button>
            </div>
        </div>
    </div>

    <script>
        // Configuración de colores para SweetAlert
        const SwalTheme = {
            success: {
                color: '#10b981',
                background: '#f0fdf4',
                confirmButton: '#10b981'
            },
            error: {
                color: '#ef4444',
                background: '#fef2f2',
                confirmButton: '#ef4444'
            },
            warning: {
                color: '#f59e0b',
                background: '#fffbeb',
                confirmButton: '#f59e0b'
            },
            info: {
                color: '#3b82f6',
                background: '#eff6ff',
                confirmButton: '#3b82f6'
            }
        };
        
        // Función para ir al login
        function goToLogin() {
            window.location.href = 'index.php'; // Ajusta la ruta según tu estructura
        }
        
        // Manejo del formulario
        document.getElementById('passwordRecoveryForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            
            const email = document.getElementById('email3').value;
            const recoveryBtn = document.getElementById('recoveryBtn');
            
            // Validación básica del email
            if (!email || !validateEmail(email)) {
                Swal.fire({
                    title: 'Email Inválido',
                    text: 'Por favor, ingresa una dirección de correo electrónico válida.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: SwalTheme.warning.confirmButton,
                    background: SwalTheme.warning.background,
                    iconColor: SwalTheme.warning.color
                });
                return;
            }
            
            // Mostrar estado de carga
            recoveryBtn.classList.add('btn-loading');
            recoveryBtn.disabled = true;
            
            // Mostrar SweetAlert de carga
            Swal.fire({
                title: 'Enviando solicitud...',
                text: 'Estamos procesando tu solicitud de recuperación.',
                didOpen: () => {
                    Swal.showLoading();
                },
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false
            });
            
            try {
                const response = await fetch('configuracion/password/recuperar_contrasena.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({ email })
                });
                
                const result = await response.text();
                
                // Cerrar SweetAlert de carga
                Swal.close();
                
                if (result.includes('Correo enviado con éxito')) {
                    // Mostrar pantalla de éxito
                    document.getElementById('recoveryForm').style.display = 'none';
                    document.getElementById('successScreen').style.display = 'block';
                    
                    // También mostrar SweetAlert de confirmación
                    Swal.fire({
                        title: '¡Éxito!',
                        text: 'Te hemos enviado un correo con las instrucciones para restablecer tu contraseña.',
                        icon: 'success',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: SwalTheme.success.confirmButton,
                        background: SwalTheme.success.background,
                        iconColor: SwalTheme.success.color
                    });
                } else {
                    // Mostrar error
                    Swal.fire({
                        title: 'Error',
                        text: result,
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: SwalTheme.error.confirmButton,
                        background: SwalTheme.error.background,
                        iconColor: SwalTheme.error.color
                    });
                }
            } catch (error) {
                // Cerrar SweetAlert de carga
                Swal.close();
                
                Swal.fire({
                    title: 'Error de Conexión',
                    text: 'Hubo un problema al procesar tu solicitud. Por favor, intenta nuevamente.',
                    icon: 'error',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: SwalTheme.error.confirmButton,
                    background: SwalTheme.error.background,
                    iconColor: SwalTheme.error.color
                });
            } finally {
                // Restaurar estado del botón
                recoveryBtn.classList.remove('btn-loading');
                recoveryBtn.disabled = false;
            }
        });
        
        // Función para validar email
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Efecto de interacción para el input
        document.getElementById('email3').addEventListener('focus', function() {
            this.style.borderColor = '#f59e0b';
        });
        
        document.getElementById('email3').addEventListener('blur', function() {
            if (!this.value) {
                this.style.borderColor = '#d1d5db';
            }
        });
    </script>
</body>
</html>