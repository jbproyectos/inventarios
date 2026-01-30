<?php
include "includes/conexionbd.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Usuario</title>
    
    <!-- Tailwind y Flowbite -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-hover: #2563eb;
            --accent-color: #f59e0b;
            --accent-hover: #d97706;
        }
        
        .registration-container {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .registration-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            position: relative;
        }
        
        .form-title {
            text-align: center;
            margin-bottom: 2rem;
            color: #1f2937;
        }
        
        .form-title h1 {
            font-size: 1.875rem;
            font-weight: bold;
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
        
        .form-select, .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            background-color: #f9fafb;
        }
        
        .form-select:focus, .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            background-color: white;
        }
        
        .form-input:read-only {
            background-color: #f3f4f6;
            color: #6b7280;
            cursor: not-allowed;
        }
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .password-strength.weak {
            background-color: #ef4444;
            width: 33%;
        }
        
        .password-strength.medium {
            background-color: #f59e0b;
            width: 66%;
        }
        
        .password-strength.strong {
            background-color: #10b981;
            width: 100%;
        }
        
        .password-match {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
        }
        
        .password-match.valid {
            color: #10b981;
        }
        
        .password-match.invalid {
            color: #ef4444;
        }
        
        .btn-register {
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
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4);
        }
        
        .btn-register:active {
            transform: translateY(0);
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
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }
        
        .step {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #d1d5db;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        
        .step.active {
            background-color: var(--accent-color);
            width: 30px;
            border-radius: 5px;
        }
        
        .form-step {
            display: none;
        }
        
        .form-step.active {
            display: block;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-navigation {
            display: flex;
            justify-content: space-between;
            margin-top: 1.5rem;
        }
        
        .btn-nav {
            padding: 0.5rem 1rem;
            border: 1.5px solid #d1d5db;
            border-radius: 6px;
            background: white;
            color: #374151;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-nav:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
        }
        
        .btn-next {
            background: var(--primary-color);
            color: white;
            border: none;
        }
        
        .btn-next:hover {
            background: var(--primary-hover);
            color: white;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <div class="registration-card">
            <div class="form-title">
                <h1>Registrar Usuario</h1>
                <p>Complete la información para crear una nueva cuenta</p>
            </div>
            
            <!-- Indicador de pasos -->
            <div class="step-indicator">
                <div class="step active" data-step="1"></div>
                <div class="step" data-step="2"></div>
                <div class="step" data-step="3"></div>
            </div>
            
            <form id="newuser" action="includes/process_register.php" method="POST">
                <!-- Paso 1: Información de la organización -->
                <div class="form-step active" id="step1">
                    <div class="form-group">
                        <label for="departamento" class="form-label">Departamento</label>
                        <select id="departamento" name="departamento" class="form-select" required>
                            <option value="" selected disabled>Selecciona Departamento</option>
                            <?php
                            try {
                                $consulta = $conexion->query("SELECT * FROM departamentos");
                                $puestos = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($puestos as $puesto) : ?>
                                    <option value="<?= htmlspecialchars($puesto['Id_departamento']) ?>"><?= htmlspecialchars($puesto['nombre']) ?></option>
                            <?php endforeach;
                            } catch (PDOException $e) {
                                die('Error en la consulta: ' . $e->getMessage());
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="oficina" class="form-label">Oficina</label>
                        <select id="oficina" name="oficina" class="form-select" required>
                            <option value="" selected disabled>Selecciona Oficina</option>
                            <?php
                            try {
                                $consulta = $conexion->query("SELECT * FROM oficina");
                                $oficina = $consulta->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($oficina as $oficinas) : ?>
                                    <option value="<?= htmlspecialchars($oficinas['Id_Oficina']) ?>"><?= htmlspecialchars($oficinas['nombre']) ?></option>
                            <?php endforeach;
                            } catch (PDOException $e) {
                                die('Error en la consulta: ' . $e->getMessage());
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-navigation">
                        <div></div> <!-- Espaciador -->
                        <button type="button" class="btn-nav btn-next" onclick="nextStep(1)">Siguiente</button>
                    </div>
                </div>
                
                <!-- Paso 2: Información del empleado -->
                <div class="form-step" id="step2">
                    <div class="form-group">
                        <label for="nombre" class="form-label">Empleado</label>
                        <select id="nombre" name="nombre" class="form-select" required>
                            <option value="" selected disabled>Selecciona un empleado</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="puesto" class="form-label">Puesto</label>
                        <input id="puesto" name="puesto" type="text" class="form-input" readonly>
                        <input type="hidden" id="puesto_id" name="puesto_id">
                    </div>
                    
                    <div class="form-navigation">
                        <button type="button" class="btn-nav" onclick="prevStep(2)">Anterior</button>
                        <button type="button" class="btn-nav btn-next" onclick="nextStep(2)">Siguiente</button>
                    </div>
                </div>
                
                <!-- Paso 3: Credenciales de acceso -->
                <div class="form-step" id="step3">
                    <div class="form-group">
                        <label for="email2" class="form-label">Email</label>
                        <input id="email2" name="email" type="email" placeholder="ejemplo@organizacion.com" class="form-input" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password2" class="form-label">Contraseña</label>
                        <input id="password2" name="password" type="password" class="form-input" required>
                        <div id="password-strength" class="password-strength"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="verificacionContrasena" class="form-label">Confirmar Contraseña</label>
                        <input id="verificacionContrasena" name="verificacionContrasena" type="password" class="form-input" required>
                        <div id="password-match" class="password-match"></div>
                    </div>
                    
                    <div class="form-navigation">
                        <button type="button" class="btn-nav" onclick="prevStep(3)">Anterior</button>
                        <button type="submit" class="btn-register">
                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Registrar Usuario
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Variables globales
        let currentStep = 1;
        const totalSteps = 3;
        
        // Navegación entre pasos
        function nextStep(step) {
            if (validateStep(step)) {
                document.getElementById(`step${step}`).classList.remove('active');
                document.querySelector(`.step[data-step="${step}"]`).classList.remove('active');
                
                currentStep = step + 1;
                document.getElementById(`step${currentStep}`).classList.add('active');
                document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
            }
        }
        
        function prevStep(step) {
            document.getElementById(`step${step}`).classList.remove('active');
            document.querySelector(`.step[data-step="${step}"]`).classList.remove('active');
            
            currentStep = step - 1;
            document.getElementById(`step${currentStep}`).classList.add('active');
            document.querySelector(`.step[data-step="${currentStep}"]`).classList.add('active');
        }
        
        // Validación de pasos
        function validateStep(step) {
            if (step === 1) {
                const departamento = $('#departamento').val();
                const oficina = $('#oficina').val();
                
                if (!departamento || !oficina) {
                    Swal.fire({
                        title: 'Campos requeridos',
                        text: 'Por favor, selecciona un departamento y una oficina.',
                        icon: 'warning',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#f59e0b'
                    });
                    return false;
                }
                return true;
            }
            
            if (step === 2) {
                const nombre = $('#nombre').val();
                
                if (!nombre) {
                    Swal.fire({
                        title: 'Campo requerido',
                        text: 'Por favor, selecciona un empleado.',
                        icon: 'warning',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#f59e0b'
                    });
                    return false;
                }
                return true;
            }
            
            return true;
        }
        
        // Validación de contraseña en tiempo real
        $(document).ready(function() {
            $('#password2').on('input', function() {
                const password = $(this).val();
                const strengthBar = $('#password-strength');
                
                // Remover clases anteriores
                strengthBar.removeClass('weak medium strong');
                
                if (password.length === 0) {
                    strengthBar.css('width', '0');
                    return;
                }
                
                // Calcular fortaleza
                let strength = 0;
                if (password.length >= 8) strength++;
                if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
                if (password.match(/\d/)) strength++;
                if (password.match(/[^a-zA-Z\d]/)) strength++;
                
                // Aplicar clases según la fortaleza
                if (strength <= 1) {
                    strengthBar.addClass('weak');
                } else if (strength <= 3) {
                    strengthBar.addClass('medium');
                } else {
                    strengthBar.addClass('strong');
                }
            });
            
            // Validación de coincidencia de contraseñas
            $('#verificacionContrasena').on('input', function() {
                const password = $('#password2').val();
                const confirmPassword = $(this).val();
                const matchIndicator = $('#password-match');
                
                if (confirmPassword.length === 0) {
                    matchIndicator.text('').removeClass('valid invalid');
                    return;
                }
                
                if (password === confirmPassword) {
                    matchIndicator.text('✓ Las contraseñas coinciden').addClass('valid').removeClass('invalid');
                } else {
                    matchIndicator.text('✗ Las contraseñas no coinciden').addClass('invalid').removeClass('valid');
                }
            });
            
            // Capturar el evento de envío del formulario
            $('#newuser').on('submit', function(e) {
                e.preventDefault();
                
                // Validar contraseñas
                const password = $('#password2').val();
                const confirmPassword = $('#verificacionContrasena').val();
                
                if (password !== confirmPassword) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Las contraseñas no coinciden. Por favor, verifica e intenta nuevamente.',
                        icon: 'error',
                        confirmButtonText: 'Entendido',
                        confirmButtonColor: '#dc3545'
                    });
                    return;
                }
                
                // Obtener el valor del campo nombre completo
                var nombreCompleto = $('#nombre option:selected').text().trim();
                var oficina = $('#oficina').val().trim();
                var departamento = $('#departamento').val();

                // Separar el nombre y los apellidos
                var partes = nombreCompleto.split(' ');
                var nombre = partes.shift(); // La primera palabra será el nombre
                var apellido = partes.join(' '); // El resto será el apellido

                // Mostrar indicador de carga
                const submitBtn = $('.btn-register');
                submitBtn.addClass('btn-loading').prop('disabled', true);

                // Capturar los datos del formulario después de procesar el nombre completo
                var formData = {
                    email: $('#email2').val(),
                    password: $('#password2').val(),
                    verificacionContrasena: $('#verificacionContrasena').val(),
                    nombre: nombre,
                    apellido: apellido,
                    departamento: departamento,
                    oficina: oficina,
                    puesto: $('#puesto_id').val()
                };

                // Realizar la solicitud Ajax
                $.ajax({
                    type: 'POST',
                    url: 'includes/process_register.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        submitBtn.removeClass('btn-loading').prop('disabled', false);
                        
                        if (response.status == 'success') {
                            Swal.fire({
                                title: '¡Éxito!',
                                text: response.message,
                                icon: 'success',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#10b981'
                            }).then(() => {
                                // Limpiar formulario o redirigir
                                $('#newuser')[0].reset();
                                currentStep = 1;
                                $('.form-step').removeClass('active');
                                $('#step1').addClass('active');
                                $('.step').removeClass('active');
                                $('.step[data-step="1"]').addClass('active');
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function() {
                        submitBtn.removeClass('btn-loading').prop('disabled', false);
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un problema al registrar el usuario.',
                            icon: 'error',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            });

            // Listener para el cambio en el departamento
            $('#departamento').on('change', function() {
                var departamento = $('#departamento option:selected').text();

                if (departamento !== "Selecciona Departamento") {
                    $.ajax({
                        url: 'includes/get_empleados.php',
                        method: 'POST',
                        data: { departamento: departamento },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                var empleados = response.data;
                                $('#nombre').empty();
                                $('#nombre').append(new Option('Selecciona Nombre', '', true, true));
                                empleados.forEach(function(empleado) {
                                    $('#nombre').append(new Option(empleado.nombre, empleado.id));
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'No se pudo obtener la lista de empleados.',
                                icon: 'error',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });

            // Listener para el cambio en el nombre
            $('#nombre').on('change', function() {
                const empleadoId = $('#nombre').val();

                if (empleadoId) {
                    $.ajax({
                        url: 'includes/get_puesto.php',
                        method: 'POST',
                        data: { id: empleadoId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                const puesto = response.data.puesto;
                                const puestoId = response.data.puesto_id;
                                $('#puesto').val(puesto);
                                $('#puesto_id').val(puestoId);
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    confirmButtonText: 'Aceptar',
                                    confirmButtonColor: '#dc3545'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: 'Error',
                                text: 'No se pudo obtener el puesto del empleado.',
                                icon: 'error',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>