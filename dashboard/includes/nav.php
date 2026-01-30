<nav class="bg-white shadow-lg p-4 dark:bg-gray-800 flex justify-between items-center sticky top-0 z-30 border-b border-gray-200 dark:border-gray-700">
    
    <!-- Lado izquierdo: Logo y funciones de sistema -->
    <div class="flex items-center space-x-6">
        <!-- Logo y título -->
        <div class="flex items-center space-x-3">
            <div class="relative">
                <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 3H6a2 2 0 0 0-2 2v7a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2z"/>
                    <path d="M18 8h1a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2h-7a2 2 0 0 1-2-2v-1"/>
                </svg>
                <!-- Indicador de actividad en tiempo real -->
                <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full animate-ping"></div>
                <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full"></div>
            </div>
            <div class="text-xl font-bold text-gray-800 dark:text-white">Inventario AI</div>
        </div>

        <!-- Quick Actions -->
        <div class="hidden md:flex items-center space-x-2">
            <!-- Scanner QR rápido -->
            <button onclick="openQRScanner()" class="flex items-center space-x-1 px-3 py-1 text-sm bg-green-50 text-green-700 dark:bg-green-900/20 dark:text-green-400 rounded-lg border border-green-200 dark:border-green-800 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span>Escanear QR</span>
            </button>

            <!-- Búsqueda por voz -->
            <button onclick="startVoiceSearch()" class="flex items-center space-x-1 px-3 py-1 text-sm bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg border border-blue-200 dark:border-blue-800 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                </svg>
                <span>Búsqueda por voz</span>
            </button>
        </div>
    </div>

    <!-- Lado derecho: Funciones de usuario y sistema -->
    <div class="flex items-center space-x-4">
        

        <ul class="flex items-center space-x-3">
            
            <!-- Asistente IA -->
            <li class="relative">
                <button onclick="toggleAIAssistant()" class="p-2 text-gray-600 hover:text-purple-600 dark:text-gray-400 dark:hover:text-purple-400 relative transition-colors duration-200 group">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-purple-500 rounded-full animate-pulse"></span>
                </button>
            </li>



            <!-- Pantalla completa -->
            <li>
                <button class="p-2 text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors duration-200" 
                        onclick="toggleFullScreen()"
                        aria-label="Toggle fullscreen">
                    <svg id="fullscreen-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v4.5m0-4.5h4.5m-4.5 0L9 9M3.75 20.25v-4.5m0 4.5h4.5m-4.5 0L9 15M20.25 3.75h-4.5m4.5 0v4.5m0-4.5L15 9m5.25 11.25h-4.5m4.5 0v-4.5m0 4.5L15 15"/>
                    </svg>
                </button>
            </li>

           
        </ul>
    </div>
</nav>

<!-- Modal de Asistente IA Mejorado -->
<div id="ai-assistant-modal" class="fixed inset-0 hidden bg-black bg-opacity-60 flex items-center justify-center z-50 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl mx-4 border border-gray-200 dark:border-gray-700 transform transition-all duration-300 scale-95">
        
        <!-- Header con gradiente -->
        <div class="relative bg-gradient-to-r from-purple-600 to-blue-600 rounded-t-2xl p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white"></div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Asistente IA</h3>
                        <p class="text-purple-100 text-sm">Inteligencia artificial para tu inventario</p>
                    </div>
                </div>
                <button onclick="toggleAIAssistant()" 
                        class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-xl flex items-center justify-center text-white transition-all duration-200 hover:scale-110 backdrop-blur-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="p-6 max-h-[70vh] overflow-y-auto">
            
            <!-- Estado de conexión -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-3">
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-green-600 dark:text-green-400">Conectado</span>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400">IA lista para ayudarte</span>
                </div>
                <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Powered by AI</span>
                </div>
            </div>

            <!-- Área de chat -->
            <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-6 min-h-[200px] max-h-[300px] overflow-y-auto border border-gray-200 dark:border-gray-700">
                <div class="space-y-4">
                    
                    <!-- Mensaje del asistente -->
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="bg-white dark:bg-gray-700 rounded-2xl rounded-tl-none px-4 py-3 shadow-sm border border-gray-200 dark:border-gray-600">
                                <p class="text-gray-800 dark:text-gray-200 text-sm leading-relaxed">
                                    ¡Hola! Soy tu asistente de IA especializado en gestión de inventario. 
                                    Puedo ayudarte a:
                                </p>
                                <ul class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                    <li class="flex items-center space-x-2">
                                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                                        <span>Analizar tendencias de tu inventario</span>
                                    </li>
                                    <li class="flex items-center space-x-2">
                                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                                        <span>Predecir necesidades de mantenimiento</span>
                                    </li>
                                    <li class="flex items-center space-x-2">
                                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                                        <span>Optimizar distribución de equipos</span>
                                    </li>
                                    <li class="flex items-center space-x-2">
                                        <div class="w-1.5 h-1.5 bg-blue-500 rounded-full"></div>
                                        <span>Generar reportes inteligentes</span>
                                    </li>
                                </ul>
                                <p class="mt-3 text-sm text-purple-600 dark:text-purple-400 font-medium">
                                    ¿En qué puedo ayudarte hoy?
                                </p>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">Ahora mismo</span>
                        </div>
                    </div>

                    <!-- Respuesta del usuario (placeholder) -->
                    <div id="ai-response" class="hidden">
                        <!-- Las respuestas de IA aparecerán aquí -->
                    </div>

                </div>
            </div>

            <!-- Ejemplos de preguntas -->
            <div class="mb-6">
                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 flex items-center space-x-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span>Preguntas sugeridas:</span>
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    <button onclick="setAIPrompt(this)" 
                            class="text-left p-3 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-500 transition-all duration-200 hover:shadow-md group">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                ¿Qué equipos necesitan mantenimiento?
                            </span>
                        </div>
                    </button>
                    <button onclick="setAIPrompt(this)" 
                            class="text-left p-3 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-500 transition-all duration-200 hover:shadow-md group">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                Generar reporte de inventario
                            </span>
                        </div>
                    </button>
                    <button onclick="setAIPrompt(this)" 
                            class="text-left p-3 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-500 transition-all duration-200 hover:shadow-md group">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-orange-500 rounded-full"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                Optimizar distribución de equipos
                            </span>
                        </div>
                    </button>
                    <button onclick="setAIPrompt(this)" 
                            class="text-left p-3 bg-white dark:bg-gray-700 rounded-xl border border-gray-200 dark:border-gray-600 hover:border-purple-300 dark:hover:border-purple-500 transition-all duration-200 hover:shadow-md group">
                        <div class="flex items-center space-x-2">
                            <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-purple-600 dark:group-hover:text-purple-400">
                                Predecir necesidades futuras
                            </span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Input de consulta mejorado -->
            <div class="space-y-4">
                <div class="relative">
                    <textarea id="ai-prompt" 
                              rows="3"
                              placeholder="Describe lo que necesitas... Ej: 'Analiza el estado actual del inventario y sugiere mejoras'"
                              class="w-full px-4 py-4 pr-12 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400"
                              oninput="autoResize(this)"></textarea>
                    
                    <!-- Botones de acción en el input -->
                    <div class="absolute right-3 bottom-3 flex items-center space-x-2">
                        <button onclick="startVoiceInput()" 
                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-purple-600 dark:text-gray-400 dark:hover:text-purple-400 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                            </svg>
                        </button>
                        <button onclick="clearAIPrompt()" 
                                class="w-8 h-8 flex items-center justify-center text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Presiona Enter para enviar</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="clearAIPrompt()" 
                                class="px-6 py-2.5 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition-all duration-200 font-medium">
                            Limpiar
                        </button>
                        <button onclick="processAIRequest()" 
                                id="ai-send-button"
                                class="px-8 py-2.5 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center space-x-2">
                            <span>Enviar Consulta</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer con información -->
        <div class="border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 rounded-b-2xl px-6 py-4">
            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center space-x-4">
                    <span>🔒 Consultas seguras</span>
                    <span>⚡ Procesamiento rápido</span>
                    <span>🎯 Respuestas precisas</span>
                </div>
                <span>v2.1 • AI Inventory Assistant</span>
            </div>
        </div>
    </div>
</div>

<script>
// Funciones mejoradas para el modal de IA
function toggleAIAssistant() {
    const modal = document.getElementById('ai-assistant-modal');
    modal.classList.toggle('hidden');
    
    if (!modal.classList.contains('hidden')) {
        // Animación de entrada
        setTimeout(() => {
            modal.querySelector('.transform').classList.remove('scale-95');
            modal.querySelector('.transform').classList.add('scale-100');
        }, 50);
        
        // Enfocar el textarea
        document.getElementById('ai-prompt').focus();
    }
}

function setAIPrompt(button) {
    const promptText = button.querySelector('span').textContent;
    document.getElementById('ai-prompt').value = promptText;
    document.getElementById('ai-prompt').focus();
}

function clearAIPrompt() {
    document.getElementById('ai-prompt').value = '';
    document.getElementById('ai-response').innerHTML = '';
    document.getElementById('ai-response').classList.add('hidden');
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = textarea.scrollHeight + 'px';
}

function startVoiceInput() {
    if ('webkitSpeechRecognition' in window) {
        const recognition = new webkitSpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        recognition.lang = 'es-ES';
        
        recognition.onstart = function() {
            showVoiceStatus('Escuchando... Habla ahora.');
        };
        
        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            document.getElementById('ai-prompt').value = transcript;
            autoResize(document.getElementById('ai-prompt'));
            showVoiceStatus('Texto reconocido correctamente');
        };
        
        recognition.onerror = function(event) {
            showVoiceStatus('Error en reconocimiento de voz');
        };
        
        recognition.start();
    } else {
        alert('El reconocimiento de voz no es compatible con tu navegador');
    }
}

function showVoiceStatus(message) {
    // Podrías implementar un toast o notificación aquí
    console.log(message);
}

function processAIRequest() {
    const prompt = document.getElementById('ai-prompt').value.trim();
    const sendButton = document.getElementById('ai-send-button');
    
    if (!prompt) {
        alert('Por favor, escribe tu consulta');
        return;
    }
    
    // Mostrar estado de carga
    sendButton.innerHTML = '<span>Procesando...</span><div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>';
    sendButton.disabled = true;
    
    // Simular procesamiento IA (en producción, aquí iría la llamada a la API)
    setTimeout(() => {
        // Respuesta simulada de IA
        const response = `
            <div class="flex items-start space-x-3">
                <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="bg-white dark:bg-gray-700 rounded-2xl rounded-tl-none px-4 py-3 shadow-sm border border-gray-200 dark:border-gray-600">
                        <p class="text-gray-800 dark:text-gray-200 text-sm leading-relaxed">
                            He analizado tu consulta sobre <strong>"${prompt}"</strong>. Basándome en los datos del inventario:
                        </p>
                        <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                📊 <strong>Análisis completado:</strong> He procesado la información y estoy listo para proporcionarte insights detallados sobre tu inventario.
                            </p>
                        </div>
                        <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                            En una implementación completa con IA real, aquí verías análisis predictivos, recomendaciones específicas y visualizaciones de datos.
                        </p>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 mt-1 block">Justo ahora</span>
                </div>
            </div>
        `;
        
        document.getElementById('ai-response').innerHTML = response;
        document.getElementById('ai-response').classList.remove('hidden');
        
        // Restaurar botón
        sendButton.innerHTML = '<span>Enviar Consulta</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/></svg>';
        sendButton.disabled = false;
        
        // Limpiar input y scroll hacia la respuesta
        document.getElementById('ai-prompt').value = '';
        document.getElementById('ai-response').scrollIntoView({ behavior: 'smooth' });
        
    }, 2000);
}

// Permitir enviar con Enter
document.addEventListener('DOMContentLoaded', function() {
    const promptInput = document.getElementById('ai-prompt');
    promptInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            processAIRequest();
        }
    });
});
</script>

<script>
// Controlador principal del navbar
function navbarController() {
    return {
        isMainMenuOpen: false,
        isSystemAlertsOpen: false,
        dark: localStorage.getItem('dark') === 'true',
        
        toggleMainMenu() {
            this.isMainMenuOpen = !this.isMainMenuOpen;
            this.isSystemAlertsOpen = false;
        },
        
        toggleSystemAlerts() {
            this.isSystemAlertsOpen = !this.isSystemAlertsOpen;
            this.isMainMenuOpen = false;
        },
        
        toggleTheme() {
            this.dark = !this.dark;
            localStorage.setItem('dark', this.dark);
            document.documentElement.classList.toggle('dark', this.dark);
        },
        
        init() {
            document.documentElement.classList.toggle('dark', this.dark);
            this.startLiveUpdates();
        },
        
        startLiveUpdates() {
            // Simular actualizaciones en tiempo real
            setInterval(() => {
                this.updateStats();
            }, 30000);
        },
        
        updateStats() {
            // Aquí iría la lógica para actualizar estadísticas
            console.log('Actualizando estadísticas...');
        }
    }
}



function openQRScanner() {
    // Simulación de scanner QR
    alert('Abriendo scanner QR...\n\nEn una implementación real, esto activaría la cámara para escanear códigos QR de equipos.');
}

function startVoiceSearch() {
    if ('webkitSpeechRecognition' in window) {
        const recognition = new webkitSpeechRecognition();
        recognition.continuous = false;
        recognition.interimResults = false;
        
        recognition.onstart = function() {
            alert('Escuchando... Habla ahora.');
        };
        
        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            alert(`Búsqueda por voz: "${transcript}"`);
            // Aquí procesarías la búsqueda
        };
        
        recognition.start();
    } else {
        alert('La búsqueda por voz no es compatible con tu navegador');
    }
}

function generateReport() {
    alert('Generando reporte automático...\n\nEsta función generaría un reporte PDF/Excel del inventario actual.');
}

function exportData() {
    alert('Preparando exportación de datos...\n\nExportaría los datos a CSV, Excel o PDF.');
}

function showShortcuts() {
    const shortcuts = `
Atajos de teclado disponibles:

• Ctrl + F - Buscar
• Ctrl + N - Nuevo equipo
• Ctrl + E - Exportar
• Ctrl + / - Mostrar atajos
• F11 - Pantalla completa
• Esc - Cerrar modales
    `;
    alert(shortcuts);
}

// Inicializar Alpine
document.addEventListener('alpine:init', () => {
    Alpine.data('navbar', navbarController);
});

// Pantalla completa
function toggleFullScreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => {
            console.log(`Error attempting to enable fullscreen: ${err.message}`);
        });
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}
</script>