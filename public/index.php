<!DOCTYPE html>
<html lang="pt">
<head>
    <!-- Este arquivo é o frontend principal do chatbot, contendo a estrutura HTML e links para CSS e JavaScript. -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot Moodle</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Caminho atualizado -->
</head>
<body>
    <button id="chat-button">💬</button>
    <div id="chat-container">
        <div id="chat-header">
            <div id="chat-header-left">
                <img src="https://i.imgur.com/6RK7NQp.png" alt="Bot">
                <div id="chat-title">
                    <div>Toinha Moodle</div>
                    <span id="chat-status">Online agora</span>
                </div>
            </div>
            <button id="close-chat">✖</button>
        </div>
        <div id="chat-messages"></div>
        <div id="chat-input">
            <input type="text" id="user-input" placeholder="Digite sua pergunta...">
            <button id="send-button">➤</button>
        </div>
    </div>
    <script src="../assets/js/script.js"></script> <!-- Caminho atualizado -->
</body>
</html>
