<!-- Botón para abrir el drawer -->
<div class="text-center">
   <button id="chatbotButton" class="relative text-gray-600 dark:text-gray-300 p-3 rounded-full">
       <i class="fas fa-robot"></i>
       <span class="absolute top-0 right-0 inline-block w-2 h-2 bg-red-600 rounded-full"></span>
   </button>
</div>

<!-- Drawer del Chatbot -->
<div id="drawer-right-example" class="fixed top-0 right-0 z-40 h-screen p-4 overflow-y-auto transition-transform translate-x-full bg-white dark:bg-gray-800 w-80 sm:w-96 md:w-1/3 lg:w-1/4 xl:w-1/5" tabindex="-1" aria-labelledby="drawer-right-label">
   <div class="flex justify-between items-center mb-4">
      <h5 id="drawer-right-label" class="inline-flex items-center text-lg font-semibold text-gray-900 dark:text-white">
          <svg class="w-5 h-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18c1.104 0 2-.896 2-2s-.896-2-2-2s-2 .896-2 2s.896 2 2 2Zm0-8c1.104 0 2-.896 2-2s-.896-2-2-2s-2 .896-2 2s.896 2 2 2Z"/>
          </svg>
          Chatbot IA
      </h5>
      <button type="button" data-drawer-hide="drawer-right-example" aria-controls="drawer-right-example" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 right-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white">
          <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
          </svg>
          <span class="sr-only">Close menu</span>
      </button>
   </div>

   <!-- Contenedor de mensajes de chat -->
   <div id="chatbotMessages" class="space-y-4 overflow-y-auto h-[60vh] mb-4 px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg shadow-inner text-gray-900 dark:text-white flex flex-col-reverse">
       <!-- Mensajes del chat se añaden aquí -->
   </div>

   <!-- Input para enviar mensajes -->
   <div class="flex items-center mt-4 fixed bottom-4 left-0 w-full px-4">
       <input type="text" id="chatbotInput" class="flex-1 p-3 bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-white rounded-l-lg focus:ring-2 focus:ring-blue-500 outline-none placeholder-gray-500" placeholder="Escribe un mensaje...">
       <button id="sendChatbotMessage" class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-r-lg transition-all">
           <i class="fas fa-paper-plane"></i>
       </button>
   </div>
</div>

<!-- Agregar la animación y funcionalidad de Drawer -->
<script>
    const chatbotButton = document.getElementById("chatbotButton");
    const drawerRightExample = document.getElementById("drawer-right-example");
    const closeChatbotButton = document.querySelector('[data-drawer-hide="drawer-right-example"]');
    
    // Función para abrir el chatbot (drawer)
    function openChatbot() {
        drawerRightExample.classList.remove("translate-x-full");
        drawerRightExample.classList.add("translate-x-0");
    }

    // Función para cerrar el chatbot (drawer)
    function closeChatbot() {
        drawerRightExample.classList.add("translate-x-full");
        drawerRightExample.classList.remove("translate-x-0");
    }

    // Mostrar el chatbot al hacer clic en el botón
    chatbotButton.addEventListener("click", openChatbot);

    // Cerrar el chatbot al hacer clic en el botón de cerrar
    closeChatbotButton.addEventListener("click", closeChatbot);

    // Añadir mensaje al chat
    document.getElementById("sendChatbotMessage").addEventListener("click", function() {
        const input = document.getElementById("chatbotInput");
        const message = input.value.trim();
        if (message) {
            const messageDiv = document.createElement("div");
            messageDiv.classList.add("flex", "gap-2.5", "p-2", "bg-gray-700", "rounded-lg", "text-white", "mb-2", "items-start");

            // Icono de usuario (perfil)
            const userIcon = document.createElement("img");
            userIcon.classList.add("w-8", "h-8", "rounded-full");
            userIcon.src = "/docs/images/people/profile-picture-3.jpg"; // Cambia la URL con tu imagen

            // Contenedor del mensaje
            const messageContainer = document.createElement("div");
            messageContainer.classList.add("flex", "flex-col", "gap-1", "w-full", "max-w-[320px]", "text-right");

            // Nombre del usuario
            const userName = document.createElement("div");
            userName.classList.add("text-sm", "font-semibold", "text-gray-900", "dark:text-white");
            userName.textContent = "Tú";

            // Hora del mensaje
            const messageTime = document.createElement("div");
            messageTime.classList.add("text-sm", "font-normal", "text-gray-500", "dark:text-gray-400");
            messageTime.textContent = new Date().toLocaleTimeString().slice(0, 5); // Hora actual

            // Cuerpo del mensaje
            const messageBody = document.createElement("div");
            messageBody.classList.add("flex", "flex-col", "leading-1.5", "p-4", "border-gray-200", "bg-gray-100", "rounded-e-xl", "rounded-es-xl", "dark:bg-gray-700");
            const messageText = document.createElement("p");
            messageText.classList.add("text-sm", "font-normal", "text-gray-900", "dark:text-white");
            messageText.textContent = message;
            messageBody.appendChild(messageText);

            // Se añaden los elementos a su contenedor
            messageContainer.appendChild(userName);
            messageContainer.appendChild(messageTime);
            messageContainer.appendChild(messageBody);

            // Finalmente, añadimos todo al contenedor de mensajes
            messageDiv.appendChild(userIcon);
            messageDiv.appendChild(messageContainer);

            // Agregar el mensaje a la lista de mensajes
            document.getElementById("chatbotMessages").appendChild(messageDiv);

            // Limpiar el campo de texto y hacer scroll al final
            input.value = "";
            document.getElementById("chatbotMessages").scrollTop = document.getElementById("chatbotMessages").scrollHeight; // Hacer scroll hasta el final
        }
    });
</script>

<!-- Agregar librería de iconos de FontAwesome -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>
