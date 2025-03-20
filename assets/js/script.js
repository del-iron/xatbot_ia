// Este arquivo contém a lógica de interatividade do chatbot, incluindo eventos e comunicação com o backend.
const chatButton = document.getElementById("chat-button");
const chatContainer = document.getElementById("chat-container");
const closeChat = document.getElementById("close-chat");
const userInput = document.getElementById("user-input");
const sendButton = document.getElementById("send-button");
const chatMessages = document.getElementById("chat-messages");
const chatStatus = document.getElementById("chat-status");

// Adiciona o evento de clique no botão de chat
let isBotTyping = false;

// Inicializa os eventos do chat
function initializeChatEvents() {
    chatButton.addEventListener("click", openChat);
    closeChat.addEventListener("click", closeChatWindow);
    sendButton.addEventListener("click", sendMessage);
    userInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") sendMessage();
    });
}

// Abre o chat e inicia a conversa se necessário
function openChat() {
    chatContainer.style.display = "flex";
    if (chatMessages.children.length === 0) {
        showTypingIndicator();
        fetchInitialMessage();
    }
}

// Fecha o chat
function closeChatWindow() {
    chatContainer.style.display = "none";
}

// Envia a mensagem do usuário
function sendMessage() {
    let message = userInput.value.trim();
    if (message === "" || isBotTyping) return;

    addMessage(message, "user");
    userInput.value = "";
    showTypingIndicator();
    fetchBotResponse(message);
}

// Busca a mensagem inicial do bot
function fetchInitialMessage() {
    setTimeout(() => {
        fetch("../public/chatbot.php", { // Caminho atualizado
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" }
        })
        .then(response => response.text())
        .then(data => {
            hideTypingIndicator();
            displayBotMessageWithTypingEffect(data);
        });
    }, 1000);
}

// Busca a resposta do bot para a mensagem do usuário
function fetchBotResponse(message) {
    fetch("../public/chatbot.php", { // Caminho atualizado
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "message=" + encodeURIComponent(message)
    })
    .then(response => response.text())
    .then(data => {
        setTimeout(() => {
            hideTypingIndicator();
            displayBotMessageWithTypingEffect(data);
        }, 2000);
    });
}

// Adiciona uma mensagem ao chat
function addMessage(text, type) {
    let msg = document.createElement("div");
    msg.classList.add("message", type);

    if (type === "bot") {
        let img = document.createElement("img");
        img.src = "https://i.imgur.com/6RK7NQp.png";
        msg.appendChild(img);
    }

    let span = document.createElement("span");
    span.textContent = text;
    msg.appendChild(span);
    chatMessages.appendChild(msg);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Mostra o indicador de digitação
function showTypingIndicator() {
    isBotTyping = true;
    chatStatus.textContent = "Digitando";
    animateTypingDots();
}

// Esconde o indicador de digitação
function hideTypingIndicator() {
    isBotTyping = false;
    chatStatus.textContent = "Online agora";
}

// Anima os pontos do indicador de digitação
function animateTypingDots() {
    let dots = 0;
    const typingInterval = setInterval(() => {
        if (!isBotTyping) {
            clearInterval(typingInterval);
            return;
        }
        dots = (dots + 1) % 4;
        chatStatus.textContent = "Digitando" + ".".repeat(dots);
    }, 500);
}

// Exibe a mensagem do bot com efeito de digitação
function displayBotMessageWithTypingEffect(text) {
    let msg = document.createElement("div");
    msg.classList.add("message", "bot");

    let img = document.createElement("img");
    img.src = "https://i.imgur.com/6RK7NQp.png";
    msg.appendChild(img);

    let span = document.createElement("span");
    span.textContent = "";
    msg.appendChild(span);

    chatMessages.appendChild(msg);

    let i = 0;
    function typeCharacter() {
        if (i < text.length) {
            span.textContent += text.charAt(i);
            i++;
            chatMessages.scrollTop = chatMessages.scrollHeight;
            let typingSpeed = calculateTypingSpeed(text, i);
            setTimeout(typeCharacter, typingSpeed);
        }
    }
    typeCharacter();
}

// Calcula a velocidade de digitação
function calculateTypingSpeed(text, index) {
    let typingSpeed = Math.random() * 40 + 30; // 30-70ms
    if (index < 5 || index > text.length - 10) {
        typingSpeed += 20; // Mais lento no início e fim
    }
    if (['.', '!', '?', ',', ':'].includes(text.charAt(index - 1))) {
        typingSpeed += 300; // Pausa extra em pontuação
    }
    return typingSpeed;
}

// Inicializa os eventos do chat
initializeChatEvents();
