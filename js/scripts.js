// Mostrar/ocultar el contenedor del chat al hacer clic en la burbuja
document.getElementById('chat-bubble').addEventListener('click', function () {
    var chatContainer = document.getElementById('chat-container');
    if (chatContainer.style.display === 'none' || chatContainer.style.display === '') {
        chatContainer.style.display = 'block';
        document.getElementById('user-input').focus(); // Enfoca el campo de entrada
    } else {
        chatContainer.style.display = 'none';
    }
});
// Cerrar chat con ratón
document.querySelector('.close-button').addEventListener('click', function () {
    document.getElementById('chat-container').style.display = 'none';
});

function sendMessage() {
    var userInput = document.getElementById('user-input').value;
    appendMessage('user', userInput);
    var xhr = new XMLHttpRequest();
    xhr.open('POST', chatbot_ajax.ajax_url, true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                appendMessage('bot', response.response);
            } else {
                appendMessage('bot', 'Error al obtener la respuesta del chatbot.');
            }
        }
    };
    xhr.send('action=get_chatbot_response&message=' + encodeURIComponent(userInput));

    document.getElementById('user-input').value = '';
}
// Enviar con ratón
document.getElementById('send-button').addEventListener('click', function () {
    sendMessage();
});
// Enviar con tecla enter
document.getElementById('user-input').addEventListener('keypress', function (event) {
    if (event.key === 'Enter') {
        sendMessage();
    }
});

// Función para agregar un mensaje al contenedor de mensajes del chat
function appendMessage(sender, message) {
    var chatMessages = document.getElementById('chat-messages');
    var messageElement = document.createElement('div');
    messageElement.className = sender === 'user' ? 'user-message' : 'bot-message';
    messageElement.textContent = message;
    chatMessages.appendChild(messageElement);

    // Desplaza automáticamente el contenedor de mensajes hacia abajo para mostrar el último mensaje
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Función para ajustar el tamaño del chat
function adjustChatSize(increase) {
    var chatContainer = document.getElementById('chat-container');
    var currentWidth = chatContainer.offsetWidth;
    var currentHeight = chatContainer.offsetHeight;
    var newWidth = increase ? currentWidth + 50 : currentWidth - 50;
    var newHeight = increase ? currentHeight + 50 : currentHeight - 50;
    chatContainer.style.width = newWidth + 'px';
    chatContainer.style.height = newHeight + 'px';
    document.getElementById('chat-messages').style.height = `calc(100% - 120px)`;
}
document.getElementById('increase-size').addEventListener('click', function () {
    adjustChatSize(true);
});

document.getElementById('decrease-size').addEventListener('click', function () {
    adjustChatSize(false);
});

// Eventos para manejar el chatbot únicamente con el teclado
document.addEventListener('keydown', function (event) {
    if (event.ctrlKey && event.key === ' ') { // Ctrl + Espacio
        var chatContainer = document.getElementById('chat-container');
        if (chatContainer.style.display === 'none' || chatContainer.style.display === '') {
            chatContainer.style.display = 'block';
            document.getElementById('user-input').focus();
        }
    }
});
document.addEventListener('keydown', function (event) {
    var chatContainer = document.getElementById('chat-container');
    var increaseKey = '.';
    var decreaseKey = ',';

    if (event.ctrlKey && event.altKey && event.key === increaseKey) { // Ctrl + Alt + "."
        adjustChatSize(true);
    } else if (event.ctrlKey && event.altKey && event.key === decreaseKey) { // Ctrl + Alt + ","
        adjustChatSize(false);
    }
});
document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') { // Esc
        document.getElementById('chat-container').style.display = 'none';
    }
});