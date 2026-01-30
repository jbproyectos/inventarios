<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center p-6">
        <form id="passwordRecoveryForm" class="w-full max-w-md bg-white p-8 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Recuperar Contraseña</h2>
            
            <div class="mb-6">
                <label for="email" class="block text-gray-700 text-sm font-semibold mb-2">
                    Correo Electrónico <span class="text-red-500">*</span>
                </label>
                <input 
                    type="email" 
                    id="email3" 
                    required 
                    placeholder="correo@dominio.com" 
                    class="w-full px-4 py-3 text-sm text-gray-800 bg-white rounded-lg border border-yellow-300 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-yellow-500 transition-all duration-200"
                >
            </div>
            
            <div class="mb-6">
                <button 
                    type="submit" 
                    class="w-full py-3 text-gray-900 bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-lg hover:from-yellow-500 hover:to-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transform hover:scale-[1.02] transition-all duration-200 font-semibold shadow-lg"
                >
                    Recuperar Contraseña
                </button>
            </div>
            
            <div class="text-center">
                <!-- <p class="text-sm text-gray-600">¿Ya tienes cuenta? <a href="../../" class="text-yellow-600 hover:text-yellow-700 hover:underline font-medium">Inicia sesión</a></p> -->
                <p class="text-xs text-gray-500 mt-2">Los campos marcados con <span class="text-red-500">*</span> son obligatorios</p>
            </div>
        </form>
    </div>

<script>
document.getElementById('passwordRecoveryForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const email = document.getElementById('email3').value;

    // Mostrar carga
    Swal.fire({
        title: 'Cargando...',
        text: 'Estamos procesando tu solicitud.',
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Enviar solicitud de recuperación
    try {
        const response = await fetch('configuracion/password/recuperar_contrasena.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({ email })
        });

        const result = await response.text();

        if (result.includes('Correo enviado con éxito')) {
    Swal.fire({
        title: 'Éxito',
        text: 'Te hemos enviado un correo para restablecer tu contraseña.',
        icon: 'success',
        confirmButtonText: 'OK',
        confirmButtonColor: '#4CAF50', // Color verde para éxito
    });
} else {
    Swal.fire({
        title: 'Error',
        text: result,
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#F44336', // Color rojo para error
    });
}
} catch (error) {
    Swal.fire({
        title: 'Error',
        text: 'Hubo un problema al procesar tu solicitud.',
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#F44336', // Color rojo para error
    });
}

});
</script>

</body>
</html>
